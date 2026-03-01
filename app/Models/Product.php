<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'country_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'image',
        'gallery',
        'price',
        'old_price',
        'wholesale_price',
        'wholesale_min_quantity',
        'stock',
        'in_stock',
        'is_active',
        'is_featured',
        'is_new',
        'is_bestseller',
        'sort_order',
        'views',
        'seo_title',
        'seo_h1',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'in_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'is_bestseller' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = static::generateUniqueSku();
            }
        });

        static::updating(function (self $product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        static::saved(function (self $product) {
            $product->syncAttributeValues();
        });
    }

    protected static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    protected static function generateUniqueSku(): string
    {
        do {
            $sku = strtoupper(Str::random(8));
        } while (static::where('sku', $sku)->exists());

        return $sku;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function attributeValueOptions(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values', 'product_id', 'attribute_value_id')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function syncAttributeValues(): void
    {
        if (! $this->exists) {
            return;
        }

        $data = request()->input('attribute_value_options', []);

        if (empty($data)) {
            $this->productAttributeValues()->delete();

            return;
        }

        $syncData = [];

        foreach ($data as $attributeValueId) {
            if (empty($attributeValueId)) {
                continue;
            }

            $attributeValue = AttributeValue::find($attributeValueId);

            if (! $attributeValue) {
                continue;
            }

            $syncData[$attributeValueId] = [
                'attribute_id' => $attributeValue->attribute_id,
                'value' => $attributeValue->value,
            ];
        }

        $this->productAttributeValues()->delete();

        foreach ($syncData as $attributeValueId => $pivotData) {
            $this->productAttributeValues()->create([
                'attribute_id' => $pivotData['attribute_id'],
                'attribute_value_id' => $attributeValueId,
                'value' => $pivotData['value'],
            ]);
        }
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values', 'product_id', 'attribute_id')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function leadRequests(): HasMany
    {
        return $this->hasMany(LeadRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('views');
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function getEffectivePriceFor(int $quantity): string
    {
        if (
            $this->wholesale_price !== null &&
            $this->wholesale_min_quantity !== null &&
            $quantity >= $this->wholesale_min_quantity
        ) {
            return $this->wholesale_price;
        }

        return $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->old_price !== null && $this->old_price > $this->price;
    }

    public function getDiscountPercent(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        return (int) round((1 - $this->price / $this->old_price) * 100);
    }

    public function getSeoTitle(): string
    {
        return $this->seo_title ?: $this->name;
    }

    public function getSeoH1(): string
    {
        return $this->seo_h1 ?: $this->name;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seo_description ?: $this->short_description;
    }
}
