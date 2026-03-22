# Быстрый старт: Система заказов

Краткое руководство по началу реализации системы заказов.

---

## ШАГ 1: Создание базы данных (15 минут)

### Выполните команды:

```bash
# Создайте миграцию для заказов
php artisan make:migration create_orders_table

# Создайте миграцию для товаров в заказе
php artisan make:migration create_order_items_table

# Создайте миграцию для истории статусов
php artisan make:migration create_order_status_history_table

# Создайте миграцию для корзины
php artisan make:migration create_carts_table

# Создайте миграцию для товаров в корзине
php artisan make:migration create_cart_items_table

# Создайте миграцию для купонов
php artisan make:migration create_coupons_table

# Создайте миграцию для методов доставки
php artisan make:migration create_shipping_methods_table

# Создайте миграцию для методов оплаты
php artisan make:migration create_payment_methods_table
```

**Воспользуйтесь готовым кодом миграций из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 2: Создание моделей (10 минут)

### Выполните команды:

```bash
php artisan make:model Order
php artisan make:model OrderItem
php artisan make:model OrderStatusHistory
php artisan make:model Cart
php artisan make:model CartItem
php artisan make:model Coupon
php artisan make:model ShippingMethod
php artisan make:model PaymentMethod
```

**Воспользуйтесь готовым кодом моделей из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 3: Применение миграций (1 минута)

```bash
php artisan migrate
```

---

## ШАГ 4: Создание Seeders для базовых данных (5 минут)

```bash
php artisan make:seeder ShippingMethodSeeder
php artisan make:seeder PaymentMethodSeeder
```

### Пример ShippingMethodSeeder:

```php
<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        ShippingMethod::create([
            'name' => 'Самовывоз',
            'description' => 'Бесплатно заберите заказ в нашем магазине',
            'cost' => 0,
            'estimated_days' => 0,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ShippingMethod::create([
            'name' => 'Курьер по Минску',
            'description' => 'Доставка курьером по г. Минск',
            'cost' => 15.00,
            'estimated_days' => 1,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        ShippingMethod::create([
            'name' => 'Доставка по Беларуси',
            'description' => 'Доставка по всей Беларуси',
            'cost' => 25.00,
            'estimated_days' => 3,
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
```

### Пример PaymentMethodSeeder:

```php
<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::create([
            'name' => 'Наличными при получении',
            'description' => 'Оплата наличными при получении заказа',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        PaymentMethod::create([
            'name' => 'Карточкой при получении',
            'description' => 'Оплата банковской картой при получении',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        PaymentMethod::create([
            'name' => 'EriPay',
            'description' => 'Онлайн-оплата через EriPay',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
```

### Запустите seeders:

```bash
php artisan db:seed --class=ShippingMethodSeeder
php artisan db:seed --class=PaymentMethodSeeder
```

---

## ШАГ 5: Создание сервисов (10 минут)

```bash
php artisan make:service CartService
php artisan make:service OrderService
```

**Воспользуйтесь готовым кодом сервисов из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 6: Создание контроллеров (10 минут)

```bash
php artisan make:controller CartController
php artisan make:controller OrderController
php artisan make:controller CheckoutController
```

**Воспользуйтесь готовым кодом контроллеров из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 7: Добавление маршрутов (5 минут)

### Добавьте в routes/web.php:

```php
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Orders routes
Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.index')->middleware('auth');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('auth');
Route::get('/order/success/{order}', [OrderController::class, 'success'])->name('orders.success');
```

### Добавьте в routes/api.php:

```php
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

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

## ШАГ 8: Создание представлений (20 минут)

### Создайте директории:

```bash
mkdir -p resources/views/cart
mkdir -p resources/views/checkout
mkdir -p resources/views/orders
mkdir -p resources/views/modals
```

### Создайте файлы:

1. `resources/views/cart/index.blade.php`
2. `resources/views/checkout/index.blade.php`
3. `resources/views/orders/success.blade.php`
4. `resources/views/orders/my-orders.blade.php`
5. `resources/views/orders/show.blade.php`
6. `resources/views/modals/cart-modal.blade.php`

**Воспользуйтесь готовым кодом из файла `ORDER_SYSTEM_CODE_EXAMPLES.md` для модалки корзины**

---

## ШАГ 9: Создание JavaScript (10 минут)

### Создайте файл:

```bash
touch resources/js/cart.js
```

**Воспользуйтесь готовым кодом из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

### Подключите JavaScript в layout:

```blade
<script src="{{ asset('assets/js/cart.js') }}"></script>
```

---

## ШАГ 10: Создание MoonShine ресурсов (15 минут)

```bash
php artisan make:moonshine-resource OrderResource
php artisan make:moonshine-resource CouponResource
php artisan make:moonshine-resource ShippingMethodResource
php artisan make:moonshine-resource PaymentMethodResource
```

**Воспользуйтесь готовым кодом из файла `ORDER_SYSTEM_CODE_EXAMPLES.md` для OrderResource**

---

## ШАГ 11: Настройка Telegram уведомлений (10 минут)

### Установка пакета:

```bash
composer require laravel-notification-channels/telegram
```

### Создайте конфиг:

```bash
php artisan vendor:publish --provider="NotificationChannels\Telegram\TelegramServiceProvider"
```

### Создайте notification:

```bash
php artisan make:notification NewOrderNotification
```

**Воспользуйтесь готовым кодом из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 12: Добавьте кнопку "В корзину" на страницу товара (5 минут)

### В файле `resources/views/products/show.blade.php` найдите кнопку и добавьте атрибуты:

```blade
<button
    data-add-to-cart
    data-product-id="{{ $product->id }}"
    data-quantity="{{ $quantity ?? 1 }}"
    class="btn btn-primary btn-lg w-100"
>
    В корзину
</button>
```

### Добавьте виджет корзины в header:

```blade
<a href="#" data-bs-toggle="offcanvas" data-bs-target="#cartModal" class="btn btn-outline-dark ms-3">
    <i class="ci-shopping-bag"></i>
    <span class="badge bg-primary rounded-pill ms-1" id="cart-count">0</span>
</a>
```

---

## ШАГ 13: Тестирование (15 минут)

### 1. Проверьте миграции:

```bash
php artisan migrate:status
```

### 2. Проверьте базовые данные:

```bash
php artisan tinker
>>> ShippingMethod::count()
>>> PaymentMethod::count()
```

### 3. Протестируйте добавление в корзину:

- Откройте страницу товара
- Нажмите "В корзину"
- Проверьте обновление виджета
- Откройте модалку корзины

### 4. Протестируйте оформление заказа:

- Откройте корзину
- Нажмите "Оформить заказ"
- Заполните форму
- Отправьте заказ

### 5. Проверьте в админке:

- Откройте `/admin`
- Перейдите в раздел "Заказы"
- Проверьте созданный заказ

---

## ПРОВЕРОЧНЫЙ СПИСОК

Перед запуском убедитесь, что:

- [ ] Все миграции применены
- [ ] Модели созданы с правильными отношениями
- [ ] Seeders запущены
- [ ] Сервисы созданы и подключены
- [ ] Контроллеры созданы
- [ ] Маршруты добавлены
- [ ] Представления созданы
- [ ] JavaScript подключен
- [ ] MoonShine ресурсы созданы
- [ ] Telegram настроен (опционально)
- [ ] Кнопки на странице товара работают
- [ ] Виджет корзины отображается
- [ ] Модалка корзины открывается

---

## СЛЕДУЮЩИЕ ШАГИ

После базовой реализации:

1. **Доработайте оформление заказа:**
   - Многошаговая форма
   - Валидация
   - Сохранение прогресса

2. **Добавьте систему купонов:**
   - Создайте купоны в админке
   - Реализуйте логику скидок
   - Добавьте поле для ввода купона

3. **Настройте уведомления:**
   - Telegram уведомления о новых заказах
   - Email уведомления клиентам
   - SMS уведомления (опционально)

4. **Добавьте дополнительный функционал:**
   - История заказов
   - Повторение заказа
   - PDF чеки
   - Экспорт заказов

5. **Оптимизация:**
   - Кэширование
   - Очереди для уведомлений
   - Индексы в БД

---

## ПОЛЕЗНЫЕ КОМАНДЫ

```bash
# Очистка кэша
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Создание symlink для storage
php artisan storage:link

# Запуск серверов
npm run dev
php artisan serve

# Тестирование
php artisan test
```

---

## ССЫЛКИ НА ДОКУМЕНТАЦИЮ

- Полный план реализации: `ORDER_SYSTEM_IMPLEMENTATION_PLAN.md`
- Чек-лист: `ORDER_SYSTEM_CHECKLIST.md`
- Примеры кода: `ORDER_SYSTEM_CODE_EXAMPLES.md`

---

## ПОДДЕРЖКА

Если возникли проблемы:

1. Проверьте логи: `storage/logs/laravel.log`
2. Включите режим отладки в `.env`: `APP_DEBUG=true`
3. Используйте `php artisan tinker` для проверки данных
4. Проверьте SQL запросы через `DB::enableQueryLog()`

---

**Примерное время реализации базовой функциональности: 2-3 часа**

**Полная реализация с дополнительным функционалом: 8-12 часов**
