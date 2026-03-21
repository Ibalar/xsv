<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_value_id',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'attribute_value_id' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(static function (self $item) {
            if ($item->attributeValue && empty($item->value)) {
                $item->value = $item->attributeValue->value;
            }
        });

        static::saved(static function (self $item) {
            // Update the attribute_id column in the database to maintain the relationship
            if ($item->attributeValue) {
                $item->update(['attribute_id' => $item->attributeValue->attribute_id]);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class);
    }

    public function attribute(): Attribute
    {
        return $this->attributeValue->attribute ?? new Attribute();
    }
}
