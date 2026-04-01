<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /** @var array<int, array<int, Category>> */
    protected static array $requestAncestors = [];

    /** @var array<int, string> */
    protected static array $requestPaths = [];

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

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withTimestamps();
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
        $ancestors = $this->getAncestorsAndSelf();
        array_pop($ancestors);

        return $ancestors;
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        // Главная
        $breadcrumbs[] = [
            'name' => 'Главная',
            'url' => route('home'),
        ];

        // Каталог (если есть)
        $breadcrumbs[] = [
            'name' => 'Каталог',
            'url' => route('catalog.index'),
        ];

        $categories = $this->getAncestorsAndSelf();

        foreach ($categories as $category) {
            $breadcrumbs[] = [
                'name' => $category->name,
                'url' => route('catalog.show', $category->getFullPath()),
            ];
        }

        return $breadcrumbs;
    }

    public function getFullPath(): string
    {
        if (isset(static::$requestPaths[$this->id])) {
            return static::$requestPaths[$this->id];
        }

        $ancestors = $this->getAncestorsAndSelf();
        $path = '';

        foreach ($ancestors as $cat) {
            $path = $path === '' ? $cat->slug : $path . '/' . $cat->slug;
            static::$requestPaths[$cat->id] = $path;
        }

        return static::$requestPaths[$this->id];
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

    /**
     * Get all descendant category IDs including self.
     *
     * @return list<int>
     */
    public function getAllDescendantIds(): array
    {
        return Cache::remember(
            "category_descendants_{$this->id}",
            now()->addHours(24),
            fn (): array => $this->loadDescendantIds()
        );
    }

    /**
     * Load all descendant category IDs including self without caching.
     *
     * @return list<int>
     */
    protected function loadDescendantIds(): array
    {
        $ids = [$this->id];

        $children = $this->children;
        foreach ($children as $child) {
            $ids = array_merge($ids, $child->loadDescendantIds());
        }

        return $ids;
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Возвращает массив всех родителей до корня (от корня к текущей категории)
     */
    public function getAncestorsAndSelf(): array
    {
        if (isset(static::$requestAncestors[$this->id])) {
            return static::$requestAncestors[$this->id];
        }

        $categories = [];
        $current = $this;

        while ($current) {
            if (isset(static::$requestAncestors[$current->id])) {
                $categories = array_merge(static::$requestAncestors[$current->id], $categories);
                break;
            }

            array_unshift($categories, $current);
            $current = $current->parent;
        }

        // Заполняем кэш для всей цепочки
        $chain = [];
        foreach ($categories as $cat) {
            $chain[] = $cat;
            static::$requestAncestors[$cat->id] = $chain;
        }

        return static::$requestAncestors[$this->id];
    }

}
