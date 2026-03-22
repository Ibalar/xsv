# План реализации системы заказов

## Обзор

Детальный план создания полноценной системы заказов для интернет-магазина на базе Laravel 12 + MoonShine v4.8.

---

## 1. БАЗА ДАННЫХ (Database)

### 1.1. Миграция для таблицы `orders`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_orders_table.php`

**Поля:**
- `id` - первичный ключ
- `user_id` - ID пользователя (nullable для гостевых заказов)
- `order_number` - уникальный номер заказа (формат: ORD-YYYYMMDD-XXXX)
- `status` - статус заказа
- `total_amount` - итоговая сумма (decimal 10,2)
- `subtotal` - сумма товаров без скидки (decimal 10,2)
- `discount_amount` - сумма скидки (decimal 10,2, nullable)
- `coupon_code` - код купона (string, nullable)
- `customer_name` - имя клиента
- `customer_email` - email клиента
- `customer_phone` - телефон клиента
- `shipping_address` - адрес доставки (json или отдельные поля)
- `billing_address` - адрес для оплаты (json или отдельные поля)
- `shipping_method` - метод доставки
- `shipping_cost` - стоимость доставки (decimal 10,2)
- `payment_method` - метод оплаты
- `payment_status` - статус оплаты
- `paid_at` - дата оплаты (datetime, nullable)
- `notes` - заметки клиента
- `admin_notes` - заметки администратора
- `ip_address` - IP адрес клиента
- `user_agent` - User Agent браузера
- `telegram_sent` - отправлено ли уведомление в Telegram (boolean)
- `telegram_sent_at` - дата отправки уведомления (datetime, nullable)
- `created_at` / `updated_at`

**Статусы заказа:**
- `pending` - Ожидает обработки
- `confirmed` - Подтвержден
- `processing` - В обработке
- `shipped` - Отправлен
- `delivered` - Доставлен
- `cancelled` - Отменен
- `refunded` - Возврат

**Статусы оплаты:**
- `pending` - Ожидает оплаты
- `paid` - Оплачен
- `failed` - Ошибка оплаты
- `refunded` - Возвращен

### 1.2. Миграция для таблицы `order_items`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_order_items_table.php`

**Поля:**
- `id` - первичный ключ
- `order_id` - ID заказа (foreign key)
- `product_id` - ID товара (foreign key)
- `product_name` - название товара (на момент заказа)
- `product_sku` - артикул товара (на момент заказа)
- `quantity` - количество
- `price` - цена за единицу (decimal 10,2)
- `total_price` - общая цена (decimal 10,2)
- `product_snapshot` - JSON с данными товара на момент заказа
- `created_at` / `updated_at`

### 1.3. Миграция для таблицы `order_status_history`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_order_status_history_table.php`

**Поля:**
- `id` - первичный ключ
- `order_id` - ID заказа (foreign key)
- `from_status` - предыдущий статус
- `to_status` - новый статус
- `comment` - комментарий
- `user_id` - ID пользователя/админа, изменившего статус (nullable)
- `created_at`

### 1.4. Миграция для таблицы `carts`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_carts_table.php`

**Поля:**
- `id` - первичный ключ
- `user_id` - ID пользователя (nullable)
- `session_id` - ID сессии для гостевой корзины (string)
- `created_at` / `updated_at`

### 1.5. Миграция для таблицы `cart_items`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_cart_items_table.php`

**Поля:**
- `id` - первичный ключ
- `cart_id` - ID корзины (foreign key)
- `product_id` - ID товара (foreign key)
- `quantity` - количество
- `price` - цена на момент добавления (decimal 10,2)
- `created_at` / `updated_at`

### 1.6. Миграция для таблицы `coupons`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_coupons_table.php`

**Поля:**
- `id` - первичный ключ
- `code` - уникальный код купона
- `type` - тип скидки (percentage/fixed)
- `value` - значение скидки (decimal 10,2)
- `min_order_amount` - минимальная сумма заказа (decimal 10,2, nullable)
- `max_discount_amount` - максимальная сумма скидки (decimal 10,2, nullable)
- `usage_limit` - лимит использования (integer, nullable)
- `used_count` - количество использований (integer, default 0)
- `valid_from` - дата начала действия (datetime, nullable)
- `valid_until` - дата окончания действия (datetime, nullable)
- `is_active` - активен ли (boolean)
- `description` - описание
- `created_at` / `updated_at`

### 1.7. Миграция для таблицы `shipping_methods`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_shipping_methods_table.php`

**Поля:**
- `id` - первичный ключ
- `name` - название метода доставки
- `description` - описание
- `cost` - стоимость доставки (decimal 10,2)
- `estimated_days` - ориентировочное количество дней (integer)
- `is_active` - активен ли (boolean)
- `sort_order` - порядок сортировки
- `created_at` / `updated_at`

### 1.8. Миграция для таблицы `payment_methods`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_payment_methods_table.php`

**Поля:**
- `id` - первичный ключ
- `name` - название метода оплаты
- `description` - описание
- `is_active` - активен ли (boolean)
- `sort_order` - порядок сортировки
- `settings` - настройки (json, nullable)
- `created_at` / `updated_at`

---

## 2. МОДЕЛИ (Models)

### 2.1. Модель `Order`
**Файл:** `app/Models/Order.php`

**Классы и трейты:**
- `HasFactory`
- `SoftDeletes`

**Константы статусов:**
- `STATUS_PENDING = 'pending'`
- `STATUS_CONFIRMED = 'confirmed'`
- `STATUS_PROCESSING = 'processing'`
- `STATUS_SHIPPED = 'shipped'`
- `STATUS_DELIVERED = 'delivered'`
- `STATUS_CANCELLED = 'cancelled'`
- `STATUS_REFUNDED = 'refunded'`

**Константы статусов оплаты:**
- `PAYMENT_STATUS_PENDING = 'pending'`
- `PAYMENT_STATUS_PAID = 'paid'`
- `PAYMENT_STATUS_FAILED = 'failed'`
- `PAYMENT_STATUS_REFUNDED = 'refunded'`

**Fillable поля:**
- Все поля кроме id, created_at, updated_at

**Casts:**
- `total_amount`, `subtotal`, `discount_amount`, `shipping_cost` → `decimal:2`
- `paid_at`, `telegram_sent_at` → `datetime`
- `shipping_address`, `billing_address`, `user_agent` → `array`
- `telegram_sent` → `boolean`

**Отношения:**
- `user()` - BelongsTo User
- `items()` - HasMany OrderItem
- `statusHistory()` - HasMany OrderStatusHistory
- `coupon()` - BelongsTo Coupon (если купон применен)

**Методы:**
- `generateOrderNumber()` - генерация уникального номера заказа
- `scopeByStatus($status)` - фильтр по статусу
- `scopeByPaymentStatus($status)` - фильтр по статусу оплаты
- `getStatusLabel()` - получение названия статуса
- `getPaymentStatusLabel()` - получение названия статуса оплаты
- `markAsConfirmed()`, `markAsProcessing()`, etc. - методы смены статуса
- `markAsPaid()` - отметить как оплаченный
- `calculateTotal()` - пересчет итоговой суммы
- `canBeCancelled()` - можно ли отменить заказ
- `getCustomerName()`, `getCustomerEmail()`, `getCustomerPhone()` - получение данных клиента (из заказа или пользователя)

**Observers:**
- Создание записи в `order_status_history` при изменении статуса

### 2.2. Модель `OrderItem`
**Файл:** `app/Models/OrderItem.php`

**Fillable поля:**
- `order_id`, `product_id`, `product_name`, `product_sku`, `quantity`, `price`, `total_price`, `product_snapshot`

**Casts:**
- `price`, `total_price` → `decimal:2`
- `quantity` → `integer`
- `product_snapshot` → `array`

**Отношения:**
- `order()` - BelongsTo Order
- `product()` - BelongsTo Product

**Методы:**
- `calculateTotal()` - пересчет суммы

### 2.3. Модель `OrderStatusHistory`
**Файл:** `app/Models/OrderStatusHistory.php`

**Fillable поля:**
- `order_id`, `from_status`, `to_status`, `comment`, `user_id`

**Отношения:**
- `order()` - BelongsTo Order
- `user()` - BelongsTo User/MoonShineUser

### 2.4. Модель `Cart`
**Файл:** `app/Models/Cart.php`

**Fillable поля:**
- `user_id`, `session_id`

**Отношения:**
- `user()` - BelongsTo User
- `items()` - HasMany CartItem

**Методы:**
- `getTotal()` - получение итоговой суммы
- `getTotalQuantity()` - получение общего количества товаров
- `mergeWithCart(Cart $otherCart)` - объединение корзин
- `clear()` - очистка корзины
- `addItem($productId, $quantity)` - добавление товара
- `updateItem($productId, $quantity)` - обновление количества
- `removeItem($productId)` - удаление товара

**Scopes:**
- `scopeForUser($user)` - корзина пользователя
- `scopeForSession($sessionId)` - корзина сессии

### 2.5. Модель `CartItem`
**Файл:** `app/Models/CartItem.php`

**Fillable поля:**
- `cart_id`, `product_id`, `quantity`, `price`

**Casts:**
- `price` → `decimal:2`
- `quantity` → `integer`

**Отношения:**
- `cart()` - BelongsTo Cart
- `product()` - BelongsTo Product

**Методы:**
- `getSubtotal()` - сумма позиции

### 2.6. Модель `Coupon`
**Файл:** `app/Models/Coupon.php`

**Константы типов:**
- `TYPE_PERCENTAGE = 'percentage'`
- `TYPE_FIXED = 'fixed'`

**Fillable поля:**
- `code`, `type`, `value`, `min_order_amount`, `max_discount_amount`, `usage_limit`, `used_count`, `valid_from`, `valid_until`, `is_active`, `description`

**Casts:**
- `value`, `min_order_amount`, `max_discount_amount` → `decimal:2`
- `usage_limit`, `used_count` → `integer`
- `valid_from`, `valid_until` → `datetime`
- `is_active` → `boolean`

**Методы:**
- `isValid()` - проверка валидности купона
- `calculateDiscount($orderTotal)` - расчет скидки
- `incrementUsage()` - увеличение счетчика использований
- `isExpired()` - проверка срока действия
- `hasReachedLimit()` - проверка лимита использований

### 2.7. Модель `ShippingMethod`
**Файл:** `app/Models/ShippingMethod.php`

**Fillable поля:**
- `name`, `description`, `cost`, `estimated_days`, `is_active`, `sort_order`

**Casts:**
- `cost` → `decimal:2`
- `estimated_days` → `integer`
- `is_active` → `boolean`

**Scopes:**
- `scopeActive()` - только активные

### 2.8. Модель `PaymentMethod`
**Файл:** `app/Models/PaymentMethod.php`

**Fillable поля:**
- `name`, `description`, `is_active`, `sort_order`, `settings`

**Casts:**
- `is_active` → `boolean`
- `settings` → `array`

**Scopes:**
- `scopeActive()` - только активные

---

## 3. КОНТРОЛЛЕРЫ (Controllers)

### 3.1. CartController
**Файл:** `app/Http/Controllers/CartController.php`

**Методы:**
- `index()` - отображение страницы корзины
- `store(Request $request)` - добавление товара в корзину
- `update(Request $request, $itemId)` - обновление количества товара
- `destroy($itemId)` - удаление товара из корзины
- `clear()` - очистка корзины
- `getCachedCart()` - получение корзины из кэша/сессии
- `updateQuantity(Request $request)` - AJAX обновление количества
- `getCartSummary()` - AJAX получение сводки корзины (JSON)

### 3.2. OrderController
**Файл:** `app/Http/Controllers/OrderController.php`

**Методы:**
- `checkout()` - страница оформления заказа
- `store(Request $request)` - создание заказа
- `success(Order $order)` - страница успешного заказа
- `show(Order $order)` - страница заказа (для пользователя)
- `myOrders()` - список заказов пользователя

### 3.3. CheckoutController
**Файл:** `app/Http/Controllers/CheckoutController.php`

**Методы:**
- `index()` - первый шаг оформления (данные клиента)
- `shipping()` - второй шаг (выбор доставки)
- `payment()` - третий шаг (выбор оплаты)
- `review()` - четвертый шаг (просмотр и подтверждение)
- `process()` - финальная обработка заказа

---

## 4. API (API Routes)

### 4.1. API Routes
**Файл:** `routes/api.php`

**Endpoints:**

#### Корзина (Cart API)
- `GET /api/cart` - получение корзины
- `POST /api/cart/items` - добавление товара
- `PUT /api/cart/items/{id}` - обновление количества
- `DELETE /api/cart/items/{id}` - удаление товара
- `DELETE /api/cart` - очистка корзины
- `POST /api/cart/merge` - объединение корзин (при авторизации)

#### Купоны (Coupons API)
- `POST /api/coupons/apply` - применение купона
- `DELETE /api/coupons` - удаление купона

#### Оформление заказа (Checkout API)
- `GET /api/checkout/summary` - получение сводки заказа
- `POST /api/checkout/validate` - валидация данных
- `POST /api/orders` - создание заказа

---

## 5. FRONTEND

### 5.1. Компоненты корзины

#### Виджет корзины в хедере
**Файл:** `resources/views/partials/cart-widget.blade.php`

**Функционал:**
- Отображение количества товаров
- Отображение общей суммы
- Клик - открытие модалки корзины
- Обновление через AJAX при изменениях

#### Модалка корзины
**Файл:** `resources/views/modals/cart-modal.blade.php`

**Функционал:**
- Список товаров в корзине
- Изменение количества (+/- кнопки)
- Удаление товаров
- Итоговая сумма
- Кнопка "Оформить заказ"
- Кнопка "Продолжить покупки"
- AJAX обновление без перезагрузки

### 5.2. Страница корзины
**Файл:** `resources/views/cart/index.blade.php`

**Блоки:**
- Таблица товаров с:
  - Изображением
  - Названием
  - Ценой
  - Количество (+/-)
  - Суммой
  - Удалением
- Блок "Промокод"
- Блок сводки:
  - Подытог
  - Скидка (если есть)
  - Доставка
  - Итого
- Кнопка "Оформить заказ"

### 5.3. Страницы оформления заказа

#### Шаг 1: Данные клиента
**Файл:** `resources/views/checkout/step-customer.blade.php`

**Поля:**
- Имя (required)
- Телефон (required)
- Email (required)
- Комментарий к заказу (optional)
- Автозаполнение из профиля если авторизован

#### Шаг 2: Доставка
**Файл:** `resources/views/checkout/step-shipping.blade.php`

**Функционал:**
- Выбор метода доставки (radio buttons)
- Отображение стоимости и сроков доставки
- Форма адреса доставки:
  - Город
  - Улица
  - Дом
  - Квартира
  - Индекс

#### Шаг 3: Оплата
**Файл:** `resources/views/checkout/step-payment.blade.php`

**Функционал:**
- Выбор метода оплаты (radio buttons)
- Описание методов оплаты
- Поля данных для оплаты (если нужно)

#### Шаг 4: Просмотр и подтверждение
**Файл:** `resources/views/checkout/step-review.blade.php`

**Блоки:**
- Информация о клиенте
- Информация о доставке
- Информация об оплате
- Список товаров
- Сводка по суммам
- Кнопка "Подтвердить заказ"

### 5.4. Страница успешного заказа
**Файл:** `resources/views/orders/success.blade.php`

**Блоки:**
- Номер заказа
- Сообщение благодарности
- Детали заказа
- Инструкция по оплате (если выбран метод с оплатой)
- Кнопки:
  - "Мои заказы"
  - "На главную"

### 5.5. Страница "Мои заказы"
**Файл:** `resources/views/orders/my-orders.blade.php`

**Функционал:**
- Список заказов с:
  - Номером заказа
  - Датой
  - Статусом
  - Суммой
- Детали заказа по клику (модалка или отдельная страница)
- Фильтры по статусам

### 5.6. Страница деталей заказа
**Файл:** `resources/views/orders/show.blade.php`

**Блоки:**
- Информация о заказе (номер, дата, статус)
- Информация о клиенте
- Информация о доставке и оплате
- Список товаров
- История статусов
- Суммы заказа

### 5.7. JavaScript компоненты

**Файл:** `resources/js/cart.js`

**Функции:**
- Добавление в корзину (с анимацией)
- Обновление количества
- Удаление товара
- Обновление виджета корзины
- Открытие/закрытие модалки корзины
- Применение промокода

**Файл:** `resources/js/checkout.js`

**Функции:**
- Валидация шагов оформления
- Расчет итоговой суммы в реальном времени
- Обновление стоимости доставки при выборе метода
- AJAX отправка заказа
- Обработка ошибок

---

## 6. УВЕДОМЛЕНИЯ TELEGRAM

### 6.1. Создание Notification класса
**Файл:** `app/Notifications/NewOrderNotification.php`

**Методы:**
- `toTelegram($notifiable)` - форматирование сообщения для Telegram
- `toArray($notifiable)` - форматирование для других каналов

**Содержание сообщения:**
- 🆕 Новый заказ #{номер}
- 📦 Количество товаров: {count}
- 💰 Сумма: {total}
- 👤 Клиент: {name}
- 📞 Телефон: {phone}
- 📧 Email: {email}
- 🚚 Доставка: {method}
- 💳 Оплата: {method}
- 📝 Комментарий: {notes}

### 6.2. Настройка Telegram Bot
**Файл:** `config/telegram.php` (создать)

**Настройки:**
- `bot_token` - токен бота из BotFather
- `chat_id` - ID чата для отправки уведомлений
- `enabled` - включены ли уведомления

### 6.3. Установка пакета для Telegram
**Пакет:** `laravel-notification-channels/telegram`

**Установка:**
```bash
composer require laravel-notification-channels/telegram
```

### 6.4. Модель Notifiable
**Добавить в User модель:**
- `routeNotificationForTelegram()` - метод для получения chat_id

**Или использовать отдельный класс:**
```php
app/Models/TelegramRecipient.php
```

### 6.5. Отправка уведомления
**В OrderObserver при создании заказа:**
```php
$order->notify(new NewOrderNotification($order));
```

**Или в OrderController после создания:**
```php
$order->notify(new NewOrderNotification($order));
$order->update(['telegram_sent' => true, 'telegram_sent_at' => now()]);
```

### 6.6. Команды для управления

**Команда:**
```bash
php artisan telegram:set-webhook
php artisan telegram:send-test
```

---

## 7. АДМИНКА MOONSHINE

### 7.1. OrderResource
**Файл:** `app/MoonShine/Resources/OrderResource/OrderResource.php`

**Поля в списке:**
- ID
- Номер заказа
- Дата создания
- Статус (select with colors)
- Статус оплаты
- Сумма
- Клиент
- Количество товаров
- Метод доставки
- Метод оплаты

**Поля в форме:**
- Информация о заказе (ID, номер, дата)
- Статус (select)
- Статус оплаты (select)
- Данные клиента (только чтение)
- Данные доставки (редактируемые)
- Данные оплаты (редактируемые)
- Товары заказа (таблица, только чтение)
- Заметки клиента (только чтение)
- Заметки администратора (textarea)
- Флаг "Отправлено в Telegram" (switcher, только чтение)

**Actions:**
- "Отправить повторно в Telegram"
- "Скачать PDF чек"
- "Отправить письмо клиенту"

**Filters:**
- По статусу
- По статусу оплаты
- По дате (диапазон)
- По email клиента
- По номеру заказа

**Scopes:**
- `scopePending()` - новые заказы
- `scopeProcessing()` - в обработке
- `scopeCompleted()` - завершенные
- `scopeCancelled()` - отмененные

### 7.2. OrderItemResource (embedded в OrderResource)
**Поля:**
- Товар (связь с Product)
- Название товара
- Артикул
- Количество
- Цена
- Сумма

### 7.3. CouponResource
**Файл:** `app/MoonShine/Resources/CouponResource/CouponResource.php`

**Поля:**
- Код
- Тип скидки
- Значение
- Минимальная сумма заказа
- Максимальная скидка
- Лимит использований
- Использовано
- Период действия
- Активен
- Описание

### 7.4. ShippingMethodResource
**Файл:** `app/MoonShine/Resources/ShippingMethodResource/ShippingMethodResource.php`

**Поля:**
- Название
- Описание
- Стоимость
- Срок доставки
- Активен
- Порядок сортировки

### 7.5. PaymentMethodResource
**Файл:** `app/MoonShine/Resources/PaymentMethodResource/PaymentMethodResource.php`

**Поля:**
- Название
- Описание
- Активен
- Порядок сортировки
- Настройки (json editor)

### 7.6. OrderStatusHistoryResource (embedded в OrderResource)
**Поля:**
- Дата/Время
- От статуса
- К статусу
- Пользователь
- Комментарий

### 7.7. Кастомные страницы MoonShine

**Dashboard widget:**
- Количество новых заказов за сегодня
- Общая сумма заказов за сегодня
- Количество заказов в работе
- График продаж за неделю

**Страница статистики заказов:**
- Графики продаж
- ТОП товаров
- Статистика по методам доставки
- Статистика по методам оплаты

---

## 8. ДОПОЛНИТЕЛЬНЫЕ КОМПОНЕНТЫ

### 8.1. Services

#### CartService
**Файл:** `app/Services/CartService.php`

**Методы:**
- `getCart()` - получение текущей корзины
- `addToCart($productId, $quantity)` - добавление товара
- `updateCartItem($itemId, $quantity)` - обновление
- `removeFromCart($itemId)` - удаление
- `clearCart()` - очистка
- `mergeCarts($sessionCart, $userCart)` - объединение
- `getCartTotal()` - общая сумма
- `getCartItems()` - список товаров

#### OrderService
**Файл:** `app/Services/OrderService.php`

**Методы:**
- `createOrderFromCart($data)` - создание заказа из корзины
- `updateOrderStatus($orderId, $status, $comment)` - обновление статуса
- `cancelOrder($orderId)` - отмена заказа
- `calculateOrderTotal($order)` - пересчет суммы
- `applyCoupon($order, $couponCode)` - применение купона
- `removeCoupon($order)` - удаление купона

#### NotificationService
**Файл:** `app/Services/NotificationService.php`

**Методы:**
- `sendOrderNotification($order)` - отправка уведомления о заказе
- `sendStatusChangeNotification($order)` - уведомление о смене статуса
- `sendCustomerEmail($order, $template)` - отправка email клиенту

### 8.2. Jobs

#### SendTelegramNotificationJob
**Файл:** `app/Jobs/SendTelegramNotificationJob.php`

Использовать queue для отправки уведомлений в Telegram.

#### SendOrderEmailJob
**Файл:** `app/Jobs/SendOrderEmailJob.php`

Использовать queue для отправки email уведомлений.

### 8.3. Form Requests

#### StoreOrderRequest
**Файл:** `app/Http/Requests/StoreOrderRequest.php`

**Правила валидации:**
- `customer_name` - required, string, max:255
- `customer_email` - required, email
- `customer_phone` - required, regex:/^\+?\d{10,15}$/
- `shipping_method_id` - required, exists:shipping_methods,id
- `payment_method_id` - required, exists:payment_methods,id
- `shipping_address` - required, array
- `notes` - nullable, string, max:1000

#### UpdateCartRequest
**Файл:** `app/Http/Requests/UpdateCartRequest.php`

**Правила валидации:**
- `quantity` - required, integer, min:1

#### ApplyCouponRequest
**Файл:** `app/Http/Requests/ApplyCouponRequest.php`

**Правила валидации:**
- `code` - required, string, exists:coupons,code

### 8.4. Events и Listeners

#### Events:
- `OrderCreated` - заказ создан
- `OrderStatusChanged` - статус заказа изменен
- `OrderPaid` - заказ оплачен
- `OrderCancelled` - заказ отменен

#### Listeners:
- `SendTelegramNotification` - отправка в Telegram
- `SendEmailToCustomer` - отправка email клиенту
- `UpdateInventory` - обновление остатков на складе
- `CreateOrderStatusHistory` - запись истории

### 8.5. Mail

#### OrderCreatedMail
**Файл:** `app/Mail/OrderCreatedMail.php`

**Содержание:**
- Детали заказа
- Список товаров
- Сумма к оплате
- Информация о доставке и оплате

#### OrderStatusChangedMail
**Файл:** `app/Mail/OrderStatusChangedMail.php`

**Содержание:**
- Номер заказа
- Новый статус
- Комментарий (если есть)

---

## 9. ПОРЯДОК РЕАЛИЗАЦИИ

### Этап 1: База данных и модели (Приоритет: Высокий)
1. Создать все миграции
2. Создать модели
3. Настроить отношения
4. Создать seeders для тестовых данных

### Этап 2: Базовый функционал корзины (Приоритет: Высокий)
1. Создать CartService
2. Создать CartController
3. Реализовать API для корзины
4. Создать frontend компоненты корзины (виджет, модалка)
5. Тестирование корзины

### Этап 3: Оформление заказа (Приоритет: Высокий)
1. Создать OrderService
2. Создать OrderController
3. Создать Form Requests
4. Реализовать страницы оформления заказа
5. Создать JavaScript для checkout
6. Тестирование оформления заказа

### Этап 4: Админка MoonShine (Приоритет: Средний)
1. Создать OrderResource
2. Создать CouponResource
3. Создать ShippingMethodResource
4. Создать PaymentMethodResource
5. Добавить виджеты на Dashboard
6. Тестирование админки

### Этап 5: Уведомления Telegram (Приоритет: Средний)
1. Установить пакет telegram notifications
2. Создать конфигурацию
3. Создать Notification класс
4. Настроить отправку уведомлений
5. Тестирование уведомлений

### Этап 6: Дополнительный функционал (Приоритет: Низкий)
1. Система купонов
2. Email уведомления клиентам
3. История заказов для клиентов
4. PDF чеки
5. Экспорт заказов
6. Статистика и отчеты

### Этап 7: Тестирование и оптимизация (Приоритет: Высокий)
1. Unit тесты для моделей
2. Feature тесты для API
3. Browser тесты для checkout
4. Оптимизация запросов к БД
5. Кэширование
6. Настройка очередей

---

## 10. ТЕХНИЧЕСКИЕ ТРЕБОВАНИЯ

### Безопасность:
- Все формы с CSRF токеном
- Валидация всех входных данных
- Проверка прав доступа к заказам пользователей
- Защита от SQL injection (Eloquent ORM)
- Rate limiting для API

### Производительность:
- Кэширование корзины (Redis/Session)
- Ленивая загрузка отношений (eager loading)
- Оптимизация запросов к БД
- Использование очередей для уведомлений
- Индексы в БД для частых запросов

### UX/UI:
- AJAX обновления без перезагрузки
- Анимации для действий с корзиной
- Понятные сообщения об ошибках
- Сохранение данных при ошибках checkout
- Адаптивный дизайн (mobile-first)

### Логирование:
- Логирование всех заказов
- Логирование ошибок оплаты
- Логирование уведомлений
- Audit trail для изменений статусов

---

## 11. СИДЕРЫ ДЛЯ ТЕСТОВЫХ ДАННЫХ

### Database Seeders:
- `ShippingMethodSeeder` - методы доставки
- `PaymentMethodSeeder` - методы оплаты
- `CouponSeeder` - тестовые купоны
- `OrderSeeder` - тестовые заказы (опционально)

---

## 12. КОНФИГУРАЦИЯ

### Файлы конфигурации:
- `config/cart.php` - настройки корзины
- `config/orders.php` - настройки заказов
- `config/telegram.php` - настройки Telegram
- `config/payment.php` - настройки платежных систем (будущее)

---

## ПРИМЕЧАНИЯ

1. **Гостевые заказы:** Поддержка заказов без регистрации (хранение в сессии)
2. **Объединение корзин:** При авторизации объединять гостевую корзину с пользовательской
3. **Цены:** Фиксировать цены в момент заказа (product_snapshot)
4. **Остатки:** Проверять наличие товаров при добавлении в корзину
5. **Кэширование:** Кэшировать цены товаров для быстрого расчета
6. **Валюты:** Подумать о мульти-валютности (если нужно в будущем)
7. **Налоги:** Учесть возможность добавления налогов (НДС)
8. **Интеграция:** Подготовить архитектуру для интеграции с платежными шлюзами

---

## ЗАВИСИМОСТИ

### Необходимые пакеты:
```bash
# Telegram notifications
composer require laravel-notification-channels/telegram

# PDF generation (для чеков)
composer require barryvdh/laravel-dompdf

# Excel export (для экспорта заказов)
composer require maatwebsite/excel
```

### Установленные пакеты (уже есть):
- Laravel 12
- MoonShine v4.8
- Laravel framework components

---

## ДАТАСЕТ ДЛЯ ТЕСТИРОВАНИЯ

### Тестовые заказы:
- Создать 5-10 тестовых заказов с разными статусами
- Несколько заказов с несколькими товарами
- Тестовые купоны

### Тестовые пользователи:
- Админ с правами управления заказами
- Обычный пользователь с заказами
- Гостевой заказ (без пользователя)

---

Этот план предоставляет полную структуру для реализации системы заказов. Реализацию можно делать поэтапно, начиная с базового функционала и постепенно добавляя дополнительные возможности.
