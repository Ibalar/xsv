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
        'legacy_url',
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withTimestamps();
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

    public function scopeInCategories($query, array $categoryIds)
    {
        if ($categoryIds === []) {
            return $query;
        }

        return $query->where(function ($builder) use ($categoryIds): void {
            $builder
                ->whereIn('category_id', $categoryIds)
                ->orWhereHas('categories', function ($relation) use ($categoryIds): void {
                    $relation->whereIn('categories.id', $categoryIds);
                });
        });
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

    public function setLegacyUrlAttribute($value): void
    {
        if (blank($value)) {
            $this->attributes['legacy_url'] = null;

            return;
        }

        $value = trim((string) $value);
        $path = parse_url($value, PHP_URL_PATH);

        if (\is_string($path) && $path !== '') {
            $value = $path;
        }

        $this->attributes['legacy_url'] = '/' . ltrim($value, '/');
    }

    public function setImageAttribute($value): void
    {
        $normalized = self::normalizeImagePath($value);
        if (is_array($normalized)) {
            $this->attributes['image'] = json_encode($normalized, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['image'] = $normalized;
        }
    }

    public function setGalleryAttribute($value): void
    {
        $this->attributes['gallery'] = json_encode(
            self::normalizeImagePath(\is_array($value) ? $value : []),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function getNameAttribute($value): string
    {
        return html_entity_decode($value);
    }

    public function getMainImageUrl(): string
    {
        $image = $this->image;

        // если в БД JSON-массив
        if (is_string($image) && str_starts_with($image, '[')) {
            $decoded = json_decode($image, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $image = $decoded;
            }
        }

        // если массив, берем первый элемент
        if (is_array($image)) {
            $image = reset($image);
        }

        // если пусто, берем первую картинку из галереи
        $gallery = $this->gallery;
        if (empty($image) && is_array($gallery)) {
            $image = reset($gallery);
        }

        // если всё ещё пусто, fallback
        if (empty($image)) {
            return asset('no-image.jpg');
        }

        // ✅ здесь добавляем путь к storage/products/
        return asset("storage/products/{$image}");
    }

    public static function normalizeImagePath(null|string|array $path): null|string|array
    {
        if (\is_array($path)) {
            return array_values(array_filter(array_map(
                static fn (mixed $item): ?string => self::normalizeImagePath(
                    \is_string($item) ? $item : null
                ),
                $path
            )));
        }

        if (blank($path)) {
            return null;
        }

        return basename((string) $path);
    }

    protected static function booted(): void
    {
        static::saving(function ($product) {
            if ($product->image) {
                if (is_array($product->image)) {
                    $product->image = array_map(static fn ($img) => is_string($img) ? basename($img) : $img, $product->image);
                } else if (is_string($product->image) && !str_starts_with($product->image, '[')) {
                    $product->image = basename($product->image);
                }
            }
        });
    }


}
