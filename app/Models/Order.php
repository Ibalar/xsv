<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'products',
        'name',
        'phone',
        'status',
        'comment',
        'admin_notes',
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

    protected $attributes = [
        'status' => self::STATUS_NEW,
    ];

    /**
     * Получить варианты статусов с человекочитаемыми названиями
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_NEW => 'Новый',
            self::STATUS_PROCESSING => 'В обработке',
            self::STATUS_COMPLETED => 'Выполнен',
            self::STATUS_CANCELLED => 'Отменён',
        ];
    }

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
