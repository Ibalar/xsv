# Примеры кода для упрощённой системы заказов

Этот файл содержит примеры реализации ключевых компонентов упрощённой системы заказов.

---

## 1. МИГРАЦИИ

### create_orders_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->boolean('consent')->default(false);
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name')->nullable();
            $table->decimal('product_price', 10, 2)->nullable();
            $table->integer('quantity')->default(1)->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('telegram_sent')->default(false);
            $table->timestamp('telegram_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('customer_phone');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

### create_order_items_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->json('product_snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
```

---

## 2. МОДЕЛИ

### Order.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING => 'Новая заявка',
        self::STATUS_CONFIRMED => 'Подтверждена',
        self::STATUS_COMPLETED => 'Выполнена',
        self::STATUS_CANCELLED => 'Отменена',
    ];

    protected $fillable = [
        'order_number',
        'status',
        'total_amount',
        'customer_name',
        'customer_phone',
        'consent',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
        'notes',
        'ip_address',
        'user_agent',
        'telegram_sent',
        'telegram_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'product_price' => 'decimal:2',
            'quantity' => 'integer',
            'telegram_sent_at' => 'datetime',
            'consent' => 'boolean',
            'telegram_sent' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
            $order->ip_address = request()->ip();
            $order->user_agent = request()->userAgent();
        });

        static::created(function (self $order) {
            // Отправка уведомления в Telegram
            try {
                $order->notify(new \App\Notifications\NewOrderNotification($order));
                $order->update([
                    'telegram_sent' => true,
                    'telegram_sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send Telegram notification: ' . $e->getMessage());
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = static::where('order_number', 'like', "ORD-{$date}-%")
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) str_replace("ORD-{$date}-", '', $lastOrder->order_number);
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }

        return "ORD-{$date}-" . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [self::STATUS_COMPLETED]);
    }

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function markAsConfirmed(): void
    {
        $this->update(['status' => self::STATUS_CONFIRMED]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function isQuickOrder(): bool
    {
        return !empty($this->product_id);
    }

    public function isBulkOrder(): bool
    {
        return $this->items()->count() > 1;
    }

    public function getMaskedPhone(): string
    {
        $phone = $this->customer_phone;
        $length = strlen($phone);
        if ($length > 6) {
            return substr($phone, 0, 3) . '***' . substr($phone, -3);
        }
        return $phone;
    }
}
```

### OrderItem.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'price',
        'total_price',
        'product_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'quantity' => 'integer',
            'product_snapshot' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotal(): void
    {
        $this->total_price = $this->price * $this->quantity;
        $this->save();
    }
}
```

---

## 3. SERVICES

### OrderService.php

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createQuickOrder(array $data, Product $product): Order
    {
        return DB::transaction(function () use ($data, $product) {
            $quantity = $data['quantity'] ?? 1;
            $totalAmount = $product->price * $quantity;

            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'consent' => $data['consent'] ?? false,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'quantity' => $quantity,
                'total_amount' => $totalAmount,
                'notes' => $data['notes'] ?? null,
            ]);

            // Создаем order_item для совместимости
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku ?? null,
                'quantity' => $quantity,
                'price' => $product->price,
                'total_price' => $totalAmount,
                'product_snapshot' => [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'image' => $product->image,
                ],
            ]);

            return $order->load('items.product');
        });
    }

    public function createOrderFromCart(array $data, array $cartItems): Order
    {
        return DB::transaction(function () use ($data, $cartItems) {
            $totalAmount = collect($cartItems)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'consent' => $data['consent'] ?? false,
                'total_amount' => $totalAmount,
                'notes' => $data['notes'] ?? null,
            ]);

            // Создаем order_items
            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                    'product_snapshot' => [
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $product->price,
                        'image' => $product->image,
                    ],
                ]);
            }

            return $order->load('items.product');
        });
    }

    public function updateOrderStatus(int $orderId, string $status, ?string $comment = null): Order
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);

        // Можно добавить логирование истории статусов здесь

        return $order;
    }

    public function cancelOrder(int $orderId, ?string $reason = null): Order
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => Order::STATUS_CANCELLED]);

        if ($reason) {
            $order->update(['notes' => ($order->notes ?? '') . "\nОтмена: $reason"]);
        }

        return $order;
    }

    public function calculateOrderTotal(Order $order): float
    {
        return $order->items->sum('total_price');
    }
}
```

### TelegramService.php

```php
<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function sendOrderNotification(Order $order): void
    {
        try {
            $order->notify(new \App\Notifications\NewOrderNotification($order));

            $order->update([
                'telegram_sent' => true,
                'telegram_sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }

    public function formatOrderMessage(Order $order): string
    {
        $message = "*🆕 Новая заявка #{$order->order_number}*\n\n";

        if ($order->isQuickOrder()) {
            $message .= "*📦 Товар:* {$order->product_name}\n";
            $message .= "*💰 Цена:* {$order->product_price} руб.\n";
            $message .= "*📊 Количество:* {$order->quantity}\n";
        } else {
            $itemsText = $order->items->map(function ($item) {
                return "• {$item->product_name} x{$item->quantity} = {$item->total_price} руб.";
            })->implode("\n");

            $message .= "*📦 Товары:*\n{$itemsText}\n";
        }

        $message .= "\n*💰 Итого:* {$order->total_amount} руб.";
        $message .= "\n*👤 Имя:* {$order->customer_name}";
        $message .= "\n*📞 Телефон:* `{$order->customer_phone}`";

        if ($order->notes) {
            $message .= "\n\n*📝 Комментарий:* {$order->notes}";
        }

        $message .= "\n*📍 IP:* {$order->ip_address}";
        $message .= "\n_" . now()->format('d.m.Y H:i') . "_";

        return $message;
    }
}
```

---

## 4. CONTROLLERS

### OrderController.php

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $data = $request->validated();

            // Определяем тип заказа: быстрая заявка или из корзины
            if (!empty($data['product_id'])) {
                // Быстрая заявка
                $product = \App\Models\Product::findOrFail($data['product_id']);
                $order = $this->orderService->createQuickOrder($data, $product);
            } elseif (!empty($data['cart_items'])) {
                // Заказ из корзины
                $order = $this->orderService->createOrderFromCart($data, $data['cart_items']);
            } else {
                return back()->with('error', 'Ошибка: не указаны товары для заказа');
            }

            return redirect()->route('orders.success', $order)
                ->with('success', 'Заявка успешно отправлена!');
        } catch (\Exception $e) {
            \Log::error('Order creation failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Ошибка при создании заказа: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function success(Order $order)
    {
        return view('orders.success', compact('order'));
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return view('orders.show', compact('order'));
    }
}
```

### ProductController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::active()->orderBy('name')->paginate(20);
        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
```

---

## 5. FORM REQUESTS

### StoreOrderRequest.php

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|regex:/^\+?\d{10,15}$/',
            'consent' => 'required|accepted',
            'notes' => 'nullable|string|max:1000',

            // Для быстрой заявки
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:100',

            // Для заказа из корзины
            'cart_items' => 'nullable|array|min:1',
            'cart_items.*.id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1|max:100',
            'cart_items.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Пожалуйста, укажите ваше имя',
            'customer_phone.required' => 'Пожалуйста, укажите ваш телефон',
            'customer_phone.regex' => 'Некорректный формат телефона',
            'consent.required' => 'Необходимо согласие на обработку персональных данных',
            'consent.accepted' => 'Необходимо согласие на обработку персональных данных',
        ];
    }
}
```

---

## 6. API ROUTES

```php
<?php

// routes/api.php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Orders API
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
```

---

## 7. TELEGRAM NOTIFICATION

```php
<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $message = TelegramMessage::create()
            ->to(config('telegram.chat_id'))
            ->content("*🆕 Новая заявка #{$this->order->order_number}*\n\n");

        if ($this->order->isQuickOrder()) {
            $message->line("*📦 Товар:* {$this->order->product_name}")
                ->line("*💰 Цена:* {$this->order->product_price} руб.")
                ->line("*📊 Количество:* {$this->order->quantity}");
        } else {
            $itemsText = $this->order->items->map(function ($item) {
                return "• {$item->product_name} x{$item->quantity} = {$item->total_price} руб.";
            })->implode("\n");

            $message->line("*📦 Товары:*\n{$itemsText}");
        }

        $message->line("\n*💰 Итого:* {$this->order->total_amount} руб.")
            ->line("*👤 Имя:* {$this->order->customer_name}")
            ->line("*📞 Телефон:* `{$this->order->customer_phone}`");

        if ($this->order->notes) {
            $message->line("\n*📝 Комментарий:* {$this->order->notes}");
        }

        $message->line("\n*📍 IP:* {$this->order->ip_address}")
            ->line("_" . now()->format('d.m.Y H:i') . "_");

        $message->button(
            '📦 Заказ #'.$this->order->order_number,
            config('app.url') . '/admin/resource/order-resource/' . $this->order->id
        );

        $message->button(
            '📞 Позвонить',
            'tel:' . $this->order->customer_phone
        );

        return $message;
    }
}
```

---

## 8. MOONSHINE RESOURCE

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Order;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Fields\Switcher;

class OrderResource extends ModelResource
{
    protected string $model = Order::class;

    protected string $column = 'order_number';

    protected array $with = ['product', 'items', 'items.product'];

    public function getTitle(): string
    {
        return 'Заказы';
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Номер', 'order_number')->badge('primary'),
            Date::make('Дата', 'created_at')->format('d.m.Y H:i')->sortable(),
            Select::make('Статус', 'status')
                ->options(Order::STATUSES)
                ->badge(fn($value) => match($value) {
                    'pending' => 'warning',
                    'confirmed' => 'info',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    default => 'gray',
                }),
            Number::make('Сумма', 'total_amount'),
            Text::make('Имя', 'customer_name'),
            Text::make('Телефон', 'customer_phone')->badge(fn($value) => 'secondary'),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Grid::make([
                Column::make([
                    Box::make('Информация о заявке', [
                        ID::make(),
                        Text::make('Номер', 'order_number')->readonly(),
                        Date::make('Дата создания', 'created_at')->readonly(),
                        Select::make('Статус', 'status')
                            ->options(Order::STATUSES)
                            ->required(),
                        Switcher::make('Отправлено в Telegram', 'telegram_sent')->readonly(),
                    ]),
                ])->columnSpan(6),

                Column::make([
                    Box::make('Данные клиента', [
                        Text::make('Имя', 'customer_name')->readonly(),
                        Text::make('Телефон', 'customer_phone')->readonly(),
                    ]),
                ])->columnSpan(6),
            ]),

            Box::make('Товары', [
                HasMany::make('Товары', 'items')->fields([
                    Text::make('Товар', 'product_name')->readonly(),
                    Text::make('Артикул', 'product_sku')->readonly(),
                    Number::make('Количество', 'quantity')->readonly(),
                    Number::make('Цена', 'price')->readonly(),
                    Number::make('Сумма', 'total_price')->readonly(),
                ]),
            ]),

            Box::make('Примечания', [
                Textarea::make('Комментарий клиента', 'notes')->readonly(),
                Textarea::make('Заметки администратора', 'admin_notes'),
            ]),

            Box::make('Дополнительная информация', [
                Text::make('IP адрес', 'ip_address')->readonly(),
                Switcher::make('Согласие на обработку', 'consent')->readonly(),
            ]),
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->formFields();
    }

    protected function filters(): iterable
    {
        return [
            Select::make('Статус', 'status')->options(Order::STATUSES),
            Text::make('Телефон клиента', 'customer_phone'),
            Text::make('Номер заказа', 'order_number'),
        ];
    }
}
```

---

## 9. FRONTEND EXAMPLES

### Products List (resources/views/products/index.blade.php)

```blade
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Каталог товаров</h1>

    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                @if($product->image)
                <img src="{{ $product->image }}" class="card-img-top" alt="{{ $product->name }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                    <h4 class="card-text text-primary">{{ number_format($product->price, 0, ',', ' ') }} руб.</h4>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="d-grid gap-2">
                        <button
                            type="button"
                            class="btn btn-primary"
                            data-add-to-cart
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}"
                            data-product-price="{{ $product->price }}"
                            data-product-image="{{ $product->image }}"
                        >
                            <i class="bi bi-cart-plus"></i> В корзину
                        </button>
                        <a
                            href="{{ route('products.show', $product) }}"
                            class="btn btn-outline-primary"
                        >
                            <i class="bi bi-lightning"></i> Купить сейчас
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{ $products->links() }}
</div>

@endsection
```

### Product Page with Quick Order (resources/views/products/show.blade.php)

```blade
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            @if($product->image)
            <img src="{{ $product->image }}" class="img-fluid rounded" alt="{{ $product->name }}">
            @endif
        </div>
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            <h2 class="text-primary mb-4">{{ number_format($product->price, 0, ',', ' ') }} руб.</h2>
            <p>{{ $product->description }}</p>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Быстрая заявка</h5>
                    <p class="text-muted small">Оставьте заявку и мы перезвоним вам</p>

                    <form action="{{ route('orders.store') }}" method="POST" id="quick-order-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_name" value="{{ $product->name }}">
                        <input type="hidden" name="product_price" value="{{ $product->price }}">
                        <input type="hidden" name="quantity" value="1">

                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Ваше имя *</label>
                            <input
                                type="text"
                                class="form-control"
                                id="customer_name"
                                name="customer_name"
                                required
                                placeholder="Иван Иванов"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Телефон *</label>
                            <input
                                type="tel"
                                class="form-control"
                                id="customer_phone"
                                name="customer_phone"
                                required
                                placeholder="+375 (29) 123-45-67"
                            >
                            <small class="text-muted">Мы позвоним вам в течение 15 минут</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="consent"
                                    name="consent"
                                    value="1"
                                    required
                                >
                                <label class="form-check-label" for="consent">
                                    Я согласен на обработку персональных данных
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Комментарий</label>
                            <textarea
                                class="form-control"
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="Удобное время для звонка, вопросы..."
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-lightning"></i> Оставить заявку
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### Success Page (resources/views/orders/success.blade.php)

```blade
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center">
        <div class="mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
        </div>
        <h1 class="mb-4">Спасибо за вашу заявку!</h1>
        <p class="lead mb-4">
            Номер заявки: <strong>#{{ $order->order_number }}</strong>
        </p>
        <p class="mb-4">
            Мы перезвоним вам в течение 15 минут для подтверждения заказа.
        </p>

        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="bi bi-shop"></i> Продолжить покупки
            </a>
        </div>
    </div>
</div>
@endsection
```

---

## 10. JAVASCRIPT

### Cart with LocalStorage (resources/js/cart.js)

```javascript
class Cart {
    constructor() {
        this.storageKey = 'shop_cart';
        this.cart = this.loadCart();
        this.init();
    }

    init() {
        this.setupAddToCartButtons();
        this.updateCartWidget();
    }

    loadCart() {
        try {
            const data = localStorage.getItem(this.storageKey);
            return data ? JSON.parse(data) : [];
        } catch (e) {
            console.error('Error loading cart:', e);
            return [];
        }
    }

    saveCart() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.cart));
            this.updateCartWidget();
        } catch (e) {
            console.error('Error saving cart:', e);
            alert('Ошибка сохранения корзины');
        }
    }

    addToCart(product) {
        const existingItem = this.cart.find(item => item.id === product.id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                quantity: 1,
                image: product.image || ''
            });
        }

        this.saveCart();
        this.showNotification('Товар добавлен в корзину');
    }

    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        this.updateCartWidget();
    }

    updateQuantity(productId, quantity) {
        const item = this.cart.find(item => item.id === productId);

        if (item) {
            if (quantity <= 0) {
                this.removeFromCart(productId);
            } else {
                item.quantity = quantity;
                this.saveCart();
            }
        }
    }

    clearCart() {
        this.cart = [];
        this.saveCart();
    }

    getTotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    getTotalQuantity() {
        return this.cart.reduce((sum, item) => sum + item.quantity, 0);
    }

    setupAddToCartButtons() {
        document.querySelectorAll('[data-add-to-cart]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const product = {
                    id: parseInt(button.dataset.productId),
                    name: button.dataset.productName,
                    price: parseFloat(button.dataset.productPrice),
                    image: button.dataset.productImage
                };
                this.addToCart(product);
            });
        });
    }

    updateCartWidget() {
        const countElement = document.getElementById('cart-count');
        const totalElement = document.getElementById('cart-total');

        if (countElement) {
            countElement.textContent = this.getTotalQuantity();
        }

        if (totalElement) {
            totalElement.textContent = this.getTotal().toLocaleString('ru-RU') + ' руб.';
        }
    }

    showNotification(message) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed bottom-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.cart = new Cart();
});
```

### Order Form Handler (resources/js/order.js)

```javascript
class OrderForm {
    constructor() {
        this.init();
    }

    init() {
        this.setupPhoneInput();
        this.setupForms();
    }

    setupPhoneInput() {
        const phoneInput = document.getElementById('customer_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    if (value[0] === '7' || value[0] === '8') {
                        value = '+375' + value.slice(1);
                    } else if (!value.startsWith('+375')) {
                        value = '+375' + value;
                    }
                }
                e.target.value = value;
            });
        }
    }

    setupForms() {
        const forms = document.querySelectorAll('[data-order-form]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        });
    }

    async handleSubmit(e) {
        e.preventDefault();
        const form = e.target;

        // Проверяем, есть ли товары в корзине
        if (window.cart && window.cart.getTotalQuantity() > 0) {
            // Формируем данные заказа из корзины
            const formData = new FormData(form);
            const cartItems = window.cart.cart;

            formData.append('cart_items', JSON.stringify(cartItems));
        }

        // Отправляем форму
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new OrderForm();
});
```

---

Эти примеры кода помогут в реализации упрощённой системы заказов. Каждый компонент можно адаптировать под конкретные требования проекта.
