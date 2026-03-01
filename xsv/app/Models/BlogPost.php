<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'is_active',
        'published_at',
        'seo_title',
        'seo_h1',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = self::generateUniqueSlug($model->title);
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 0;

        while (true) {
            $query = self::where('slug', $slug);
            if ($ignoreId !== null) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                break;
            }

            $counter++;
            $slug = $originalSlug . '-' . $counter;
        }

        return $slug;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->where('published_at', '<=', now());
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    public function isPublished(): bool
    {
        return $this->is_active && $this->published_at !== null && $this->published_at->isPast();
    }

    public function getExcerpt(int $length = 150): string
    {
        if (!empty($this->excerpt)) {
            return $this->excerpt;
        }

        $text = strip_tags($this->content ?? '');
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . '...';
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
        return $this->seo_description ?: $this->excerpt;
    }
}
