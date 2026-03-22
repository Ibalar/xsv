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
        'attribute_id',
        'attribute_value_id',
        'value',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'attribute_id' => 'integer',
        'attribute_value_id' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // При создании или обновлении подставляем attribute_id и value
        static::saving(function (self $item) {
            if ($item->attributeValue) {
                // Подставляем attribute_id из attributeValue
                $item->attribute_id = $item->attributeValue->attribute_id;

                // Если value пустое, берём из attributeValue
                if (empty($item->value)) {
                    $item->value = $item->attributeValue->value;
                }
            }
        });
    }

    /**
     * Атрибут к которому относится значение
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * Значение атрибута
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }

    /**
     * Товар, к которому привязана характеристика
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
