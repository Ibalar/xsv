<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'attribute_id' => 'integer',
        ];
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Связь с таблицей product_attribute_values для SELECT типа атрибутов
     */
    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_value_id');
    }
}
