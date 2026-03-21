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

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'attribute_id' => 'integer',
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
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
