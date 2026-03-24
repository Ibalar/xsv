<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'products',
        'name',
        'phone',
        'comment',
        'agree',
        'ip_address',
        'user_agent',
        'telegram_sent',
        'telegram_sent_at',
    ];

    protected $casts = [
        'products' => 'array',
        'agree' => 'boolean',
        'telegram_sent' => 'boolean',
        'telegram_sent_at' => 'datetime',
    ];

    /**
     * Получить общую сумму заказа
     */
    public function getTotalAmount(): float
    {
        return collect($this->products)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });
    }

    /**
     * Получить общее количество товаров
     */
    public function getTotalQuantity(): int
    {
        return collect($this->products)->sum('quantity');
    }
}
