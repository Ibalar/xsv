<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'is_featured',
        'featured_sort_order',
        'is_active',
        'sort_order',
        'seo_title',
        'seo_h1',
        'seo_description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });

        static::updating(function (self $category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeFeaturedOrdered($query)
    {
        return $query->orderBy('featured_sort_order')->orderBy('id');
    }

    public function ancestors(): array
    {
        $ancestors = [];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($ancestors, $parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        foreach ($this->ancestors() as $ancestor) {
            $breadcrumbs[] = [
                'name' => $ancestor->name,
                'url' => $ancestor->slug,
            ];
        }

        $breadcrumbs[] = [
            'name' => $this->name,
            'url' => $this->slug,
        ];

        return $breadcrumbs;
    }

    public function getFullPath(): string
    {
        $slugs = collect($this->ancestors())
            ->pluck('slug')
            ->push($this->slug);

        return $slugs->implode('/');
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
        return $this->seo_description ?: $this->description;
    }
}
