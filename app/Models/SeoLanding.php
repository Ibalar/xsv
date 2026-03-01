<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SeoLanding extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'filter_payload',
        'seo_title',
        'seo_h1',
        'seo_description',
        'image',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filter_payload' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $landing) {
            if (empty($landing->slug)) {
                $landing->slug = static::generateUniqueSlug($landing->title);
            }
        });

        static::updating(function (self $landing) {
            if ($landing->isDirty('title') && empty($landing->slug)) {
                $landing->slug = static::generateUniqueSlug($landing->title);
            }
        });
    }

    protected static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFilterPayload(): array
    {
        return is_array($this->filter_payload) ? $this->filter_payload : [];
    }

    public function applyFilter(Builder $query): Builder
    {
        $payload = $this->getFilterPayload();

        $attributes = $payload['attributes'] ?? null;
        if (is_array($attributes)) {
            foreach ($attributes as $slug => $value) {
                if ($value === null || $value === '' || $value === []) {
                    continue;
                }

                $query->whereHas('attributeValues', function (Builder $attributeQuery) use ($slug, $value) {
                    $attributeQuery->whereHas('attribute', function (Builder $attribute) use ($slug) {
                        $attribute->where('slug', $slug);
                    });

                    if (is_array($value)) {
                        $attributeQuery->whereIn('value', $value);
                    } else {
                        $attributeQuery->where('value', $value);
                    }
                });
            }
        }

        $supplierIds = $payload['supplier_id'] ?? null;
        if ($supplierIds !== null) {
            if (is_array($supplierIds)) {
                $query->whereIn('supplier_id', $supplierIds);
            } else {
                $query->where('supplier_id', $supplierIds);
            }
        }

        $countryIds = $payload['country_id'] ?? null;
        if ($countryIds !== null) {
            if (is_array($countryIds)) {
                $query->whereIn('country_id', $countryIds);
            } else {
                $query->where('country_id', $countryIds);
            }
        }

        $price = $payload['price'] ?? null;
        if (is_array($price)) {
            $min = $price['min'] ?? $price[0] ?? null;
            $max = $price['max'] ?? $price[1] ?? null;

            if ($min !== null && $max !== null) {
                $query->whereBetween('price', [$min, $max]);
            } elseif ($min !== null) {
                $query->where('price', '>=', $min);
            } elseif ($max !== null) {
                $query->where('price', '<=', $max);
            }
        }

        return $query;
    }

    public function getSeoTitle(): string
    {
        return $this->seo_title ?: $this->title;
    }

    public function getSeoH1(): string
    {
        return $this->seo_h1 ?: $this->title;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seo_description ?: $this->description;
    }
}
