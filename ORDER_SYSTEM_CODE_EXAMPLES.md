# Примеры кода для системы заказов

Этот файл содержит примеры реализации ключевых компонентов системы заказов.

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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'refunded',
            ])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->foreignId('shipping_method_id')->nullable()->constrained();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->foreignId('payment_method_id')->nullable()->constrained();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('telegram_sent')->default(false);
            $table->timestamp('telegram_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('payment_status');
            $table->index('customer_email');
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

### create_carts_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
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
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    public const STATUSES = [
        self::STATUS_PENDING => 'Ожидает обработки',
        self::STATUS_CONFIRMED => 'Подтвержден',
        self::STATUS_PROCESSING => 'В обработке',
        self::STATUS_SHIPPED => 'Отправлен',
        self::STATUS_DELIVERED => 'Доставлен',
        self::STATUS_CANCELLED => 'Отменен',
        self::STATUS_REFUNDED => 'Возврат',
    ];

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_STATUS_PENDING => 'Ожидает оплаты',
        self::PAYMENT_STATUS_PAID => 'Оплачен',
        self::PAYMENT_STATUS_FAILED => 'Ошибка оплаты',
        self::PAYMENT_STATUS_REFUNDED => 'Возврат',
    ];

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'billing_address',
        'shipping_method_id',
        'shipping_cost',
        'payment_method_id',
        'payment_status',
        'paid_at',
        'notes',
        'admin_notes',
        'ip_address',
        'user_agent',
        'telegram_sent',
        'telegram_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'paid_at' => 'datetime',
            'telegram_sent_at' => 'datetime',
            'shipping_address' => 'array',
            'billing_address' => 'array',
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
        });

        static::updating(function (self $order) {
            if ($order->isDirty('status')) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'from_status' => $order->getOriginal('status'),
                    'to_status' => $order->status,
                    'user_id' => auth()->id(),
                ]);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
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
        return $query->whereIn('status', [self::STATUS_SHIPPED, self::STATUS_DELIVERED]);
    }

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabel(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    public function getCustomerName(): string
    {
        if ($this->user && empty($this->customer_name)) {
            return $this->user->name;
        }

        return $this->customer_name;
    }

    public function getCustomerEmail(): string
    {
        if ($this->user && empty($this->customer_email)) {
            return $this->user->email;
        }

        return $this->customer_email;
    }

    public function getCustomerPhone(): string
    {
        if ($this->user && empty($this->customer_phone)) {
            return $this->user->phone ?? '';
        }

        return $this->customer_phone;
    }

    public function markAsConfirmed(): void
    {
        $this->update(['status' => self::STATUS_CONFIRMED]);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    public function markAsShipped(): void
    {
        $this->update(['status' => self::STATUS_SHIPPED]);
    }

    public function markAsDelivered(): void
    {
        $this->update(['status' => self::STATUS_DELIVERED]);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => self::PAYMENT_STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PROCESSING,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function calculateTotal(): void
    {
        $subtotal = $this->items->sum('total_price');
        $discount = $this->discount_amount ?? 0;
        $shipping = $this->shipping_cost ?? 0;

        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal - $discount + $shipping;
        $this->save();
    }
}
```

### Cart.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeForUser($query, $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function getTotal(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function getTotalQuantity(): int
    {
        return $this->items->sum('quantity');
    }

    public function addItem(int $productId, int $quantity = 1): CartItem
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            $item->refresh();
        } else {
            $product = Product::find($productId);
            if (!$product) {
                throw new \Exception('Product not found');
            }

            $item = $this->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return $item;
    }

    public function updateItem(int $productId, int $quantity): void
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item) {
            if ($quantity > 0) {
                $item->update(['quantity' => $quantity]);
            } else {
                $item->delete();
            }
        }
    }

    public function removeItem(int $productId): void
    {
        $this->items()->where('product_id', $productId)->delete();
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public function mergeWithCart(Cart $otherCart): void
    {
        foreach ($otherCart->items as $item) {
            $existingItem = $this->items()->where('product_id', $item->product_id)->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $item->quantity);
            } else {
                $this->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }
        }

        $otherCart->delete();
    }
}
```

---

## 3. SERVICES

### CartService.php

```php
<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected ?Cart $cart = null;

    public function getCart(): Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        if (Auth::check()) {
            $this->cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);
        } else {
            $sessionId = Session::getId();
            $this->cart = Cart::firstOrCreate([
                'session_id' => $sessionId,
            ]);
        }

        return $this->cart;
    }

    public function addToCart(int $productId, int $quantity = 1): CartItem
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);

        if (!$product->in_stock || $product->stock < $quantity) {
            throw new \Exception('Product is out of stock');
        }

        return $cart->addItem($productId, $quantity);
    }

    public function updateCartItem(int $itemId, int $quantity): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);

        $product = $item->product;
        if (!$product->in_stock || $product->stock < $quantity) {
            throw new \Exception('Insufficient stock');
        }

        $cart->updateItem($item->product_id, $quantity);
    }

    public function removeFromCart(int $itemId): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);
        $cart->removeItem($item->product_id);
    }

    public function clearCart(): void
    {
        $cart = $this->getCart();
        $cart->clear();
    }

    public function mergeCarts(string $sessionId): void
    {
        $sessionCart = Cart::where('session_id', $sessionId)->first();

        if ($sessionCart && Auth::check()) {
            $userCart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $userCart->mergeWithCart($sessionCart);
        }
    }

    public function getCartTotal(): float
    {
        return $this->getCart()->getTotal();
    }

    public function getCartItemsCount(): int
    {
        return $this->getCart()->getTotalQuantity();
    }

    public function getCartItems(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getCart()->items()->with('product')->get();
    }
}
```

### OrderService.php

```php
<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrderFromCart(array $data): Order
    {
        $cart = app(CartService::class)->getCart();

        if ($cart->items->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        return DB::transaction(function () use ($cart, $data) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'billing_address' => $data['billing_address'] ?? null,
                'shipping_method_id' => $data['shipping_method_id'],
                'payment_method_id' => $data['payment_method_id'],
                'notes' => $data['notes'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Calculate totals
            $subtotal = 0;
            foreach ($cart->items as $item) {
                $subtotal += $item->price * $item->quantity;
            }

            $shippingMethod = ShippingMethod::find($data['shipping_method_id']);
            $shippingCost = $shippingMethod ? $shippingMethod->cost : 0;

            $discountAmount = 0;
            if (!empty($data['coupon_code'])) {
                $discountAmount = $this->applyCouponToOrder($order, $data['coupon_code'], $subtotal);
            }

            $totalAmount = $subtotal + $shippingCost - $discountAmount;

            $order->update([
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
            ]);

            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                    'product_snapshot' => [
                        'name' => $item->product->name,
                        'sku' => $item->product->sku,
                        'price' => $item->product->price,
                        'image' => $item->product->image,
                    ],
                ]);

                // Update product stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Clear cart
            $cart->clear();

            return $order->load('items.product');
        });
    }

    public function applyCouponToOrder(Order $order, string $couponCode, float $subtotal): float
    {
        $coupon = Coupon::where('code', $couponCode)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            throw new \Exception('Invalid coupon code');
        }

        if (!$coupon->isValid()) {
            throw new \Exception('Coupon is not valid');
        }

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            throw new \Exception('Minimum order amount not met');
        }

        $discount = $coupon->calculateDiscount($subtotal);

        if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
            $discount = $coupon->max_discount_amount;
        }

        $order->update([
            'coupon_code' => $coupon->code,
            'discount_amount' => $discount,
        ]);

        $coupon->incrementUsage();

        return $discount;
    }

    public function updateOrderStatus(int $orderId, string $status, ?string $comment = null): Order
    {
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => $status,
        ]);

        if ($comment) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $order->getOriginal('status'),
                'to_status' => $status,
                'comment' => $comment,
                'user_id' => auth()->id(),
            ]);
        }

        return $order;
    }

    public function cancelOrder(int $orderId, ?string $reason = null): Order
    {
        $order = Order::findOrFail($orderId);

        if (!$order->canBeCancelled()) {
            throw new \Exception('Order cannot be cancelled');
        }

        // Return stock
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        return $this->updateOrderStatus($orderId, Order::STATUS_CANCELLED, $reason);
    }

    public function calculateOrderTotal(Order $order): float
    {
        $subtotal = $order->items->sum('total_price');
        $shippingCost = $order->shipping_cost ?? 0;
        $discountAmount = $order->discount_amount ?? 0;

        return $subtotal + $shippingCost - $discountAmount;
    }
}
```

---

## 4. CONTROLLERS

### CartController.php

```php
<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCart();
        $items = $this->cartService->getCartItems();
        $total = $this->cartService->getCartTotal();

        return view('cart.index', compact('cart', 'items', 'total'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:100',
        ]);

        try {
            $quantity = $request->input('quantity', 1);
            $item = $this->cartService->addToCart($request->product_id, $quantity);

            return response()->json([
                'success' => true,
                'message' => 'Товар добавлен в корзину',
                'count' => $this->cartService->getCartItemsCount(),
                'total' => $this->cartService->getCartTotal(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, int $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        try {
            $this->cartService->updateCartItem($itemId, $request->quantity);

            return response()->json([
                'success' => true,
                'count' => $this->cartService->getCartItemsCount(),
                'total' => $this->cartService->getCartTotal(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(int $itemId): JsonResponse
    {
        try {
            $this->cartService->removeFromCart($itemId);

            return response()->json([
                'success' => true,
                'count' => $this->cartService->getCartItemsCount(),
                'total' => $this->cartService->getCartTotal(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Корзина очищена',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getSummary(): JsonResponse
    {
        return response()->json([
            'count' => $this->cartService->getCartItemsCount(),
            'total' => $this->cartService->getCartTotal(),
            'items' => $this->cartService->getCartItems(),
        ]);
    }
}
```

---

## 5. API ROUTES

```php
// routes/api.php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Cart API
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'getSummary']);
    Route::post('/items', [CartController::class, 'store']);
    Route::put('/items/{id}', [CartController::class, 'update']);
    Route::delete('/items/{id}', [CartController::class, 'destroy']);
    Route::delete('/', [CartController::class, 'clear']);
});

// Orders API
Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->middleware('auth');
    Route::get('/{order}', [OrderController::class, 'show'])->middleware('auth');
    Route::post('/', [OrderController::class, 'store']);
});
```

---

## 6. TELEGRAM NOTIFICATION

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
            ->content("*🆕 Новый заказ #{$this->order->order_number}*\n\n");

        $itemsText = $this->order->items->map(function ($item) {
            return "• {$item->product_name} x{$item->quantity} = {$item->total_price} руб.";
        })->implode("\n");

        $message->line("*📦 Товары:*\n{$itemsText}")
            ->line("\n*💰 Итого:* {$this->order->total_amount} руб.")
            ->line("*👤 Клиент:* {$this->order->customer_name}")
            ->line("*📞 Телефон:* {$this->order->customer_phone}")
            ->line("*📧 Email:* {$this->order->customer_email}");

        if ($this->order->shippingMethod) {
            $message->line("*🚚 Доставка:* {$this->order->shippingMethod->name} ({$this->order->shipping_cost} руб.)");
        }

        if ($this->order->paymentMethod) {
            $message->line("*💳 Оплата:* {$this->order->paymentMethod->name}");
        }

        if ($this->order->notes) {
            $message->line("\n*📝 Комментарий:* {$this->order->notes}");
        }

        $message->line("\n_" . now()->format('d.m.Y H:i') . "_");

        return $message->button('Просмотр в админке', config('app.url') . '/admin/resource/order-resource/' . $this->order->id);
    }
}
```

---

## 7. MOONSHINE RESOURCE

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
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

    protected array $with = ['user', 'items', 'shippingMethod', 'paymentMethod'];

    public function getTitle(): string
    {
        return 'Заказы';
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Номер заказа', 'order_number')->badge('primary'),
            Date::make('Дата', 'created_at')->format('d.m.Y H:i')->sortable(),
            Select::make('Статус', 'status')
                ->options(Order::STATUSES)
                ->badge(fn($value) => match($value) {
                    'pending' => 'warning',
                    'confirmed' => 'info',
                    'processing' => 'primary',
                    'shipped' => 'success',
                    'delivered' => 'success',
                    'cancelled' => 'danger',
                    default => 'gray',
                }),
            Select::make('Оплата', 'payment_status')
                ->options(Order::PAYMENT_STATUSES)
                ->badge(fn($value) => match($value) {
                    'paid' => 'success',
                    'pending' => 'warning',
                    'failed' => 'danger',
                    default => 'gray',
                }),
            Number::make('Сумма', 'total_amount'),
            Text::make('Клиент', 'customer_name'),
            Text::make('Телефон', 'customer_phone'),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Grid::make([
                Column::make([
                    Box::make('Информация о заказе', [
                        ID::make(),
                        Text::make('Номер заказа', 'order_number')->readonly(),
                        Date::make('Дата создания', 'created_at')->readonly(),
                        Select::make('Статус', 'status')
                            ->options(Order::STATUSES)
                            ->required(),
                        Select::make('Статус оплаты', 'payment_status')
                            ->options(Order::PAYMENT_STATUSES)
                            ->required(),
                    ]),
                ])->columnSpan(6),

                Column::make([
                    Box::make('Данные клиента', [
                        Text::make('Имя', 'customer_name')->readonly(),
                        Text::make('Email', 'customer_email')->readonly(),
                        Text::make('Телефон', 'customer_phone')->readonly(),
                    ]),
                ])->columnSpan(6),
            ]),

            Box::make('Адрес доставки', [
                Json::make('Адрес', 'shipping_address')->readonly(),
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
                Textarea::make('Заметки клиента', 'notes')->readonly(),
                Textarea::make('Заметки администратора', 'admin_notes'),
                Switcher::make('Отправлено в Telegram', 'telegram_sent')->readonly(),
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
            Select::make('Статус оплаты', 'payment_status')->options(Order::PAYMENT_STATUSES),
            Text::make('Email клиента', 'customer_email'),
            Text::make('Номер заказа', 'order_number'),
        ];
    }
}
```

---

## 8. FRONTEND EXAMPLES

### Cart Modal (resources/views/modals/cart-modal.blade.php)

```blade
<div class="offcanvas offcanvas-end" id="cartModal" tabindex="-1">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Корзина</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div id="cart-items">
            <!-- Items will be loaded here via AJAX -->
        </div>
        <div id="cart-empty" class="text-center py-5 d-none">
            <i class="ci-shopping-bag display-4 text-muted"></i>
            <p class="mt-3">Корзина пуста</p>
        </div>
    </div>
    <div class="offcanvas-footer border-top p-3" id="cart-footer">
        <div class="d-flex justify-content-between mb-2">
            <span>Товары:</span>
            <span id="cart-subtotal">0 руб.</span>
        </div>
        <a href="{{ route('cart.index') }}" class="btn btn-primary w-100">
            Оформить заказ
        </a>
    </div>
</div>
```

### Cart JavaScript (resources/js/cart.js)

```javascript
class Cart {
    constructor() {
        this.cartCount = document.getElementById('cart-count');
        this.cartTotal = document.getElementById('cart-total');
        this.init();
    }

    init() {
        this.updateCartWidget();
        this.setupAddToCartButtons();
    }

    async addToCart(productId, quantity = 1) {
        try {
            const response = await fetch('/api/cart/items', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartWidget();
                this.showNotification(data.message);
                this.openCartModal();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Ошибка при добавлении в корзину');
        }
    }

    async updateQuantity(itemId, quantity) {
        try {
            const response = await fetch(`/api/cart/items/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ quantity })
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartWidget();
                this.updateCartModal();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async removeItem(itemId) {
        if (!confirm('Удалить товар из корзины?')) {
            return;
        }

        try {
            const response = await fetch(`/api/cart/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartWidget();
                this.updateCartModal();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async updateCartWidget() {
        try {
            const response = await fetch('/api/cart');
            const data = await response.json();

            if (this.cartCount) {
                this.cartCount.textContent = data.count;
            }
            if (this.cartTotal) {
                this.cartTotal.textContent = data.total + ' руб.';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async updateCartModal() {
        const response = await fetch('/cart');
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const cartItems = doc.getElementById('cart-items');

        if (cartItems) {
            document.getElementById('cart-items').innerHTML = cartItems.innerHTML;
        }
    }

    openCartModal() {
        const cartModal = new bootstrap.Offcanvas(document.getElementById('cartModal'));
        cartModal.show();
    }

    setupAddToCartButtons() {
        document.querySelectorAll('[data-add-to-cart]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.dataset.productId;
                const quantity = button.dataset.quantity || 1;
                this.addToCart(parseInt(productId), parseInt(quantity));
            });
        });
    }

    showNotification(message) {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'toast show position-fixed bottom-0 end-0 m-3';
        toast.innerHTML = `
            <div class="toast-body">
                ${message}
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    showError(message) {
        alert(message);
    }
}

// Initialize cart
document.addEventListener('DOMContentLoaded', () => {
    window.cart = new Cart();
});
```

---

Эти примеры кода помогут в реализации системы заказов. Каждый компонент можно адаптировать под конкретные требования проекта.
