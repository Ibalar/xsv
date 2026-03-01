<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory;

    public const TYPE_TEXT = 'text';
    public const TYPE_NUMBER = 'number';
    public const TYPE_SELECT = 'select';
    public const TYPE_BOOLEAN = 'boolean';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_filterable',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_filterable' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get available attribute types for dropdown.
     *
     * @return array<string, string>
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_TEXT => 'Текст',
            self::TYPE_NUMBER => 'Число',
            self::TYPE_SELECT => 'Список',
            self::TYPE_BOOLEAN => 'Да/Нет',
        ];
    }

    /**
     * Scope for filterable attributes.
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope for ordered attributes.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $attribute) {
            if (empty($attribute->slug)) {
                $attribute->slug = static::generateUniqueSlug($attribute->name);
            }
        });

        static::updating(function (self $attribute) {
            if ($attribute->isDirty('name') && empty($attribute->slug)) {
                $attribute->slug = static::generateUniqueSlug($attribute->name);
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
}
