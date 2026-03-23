# План реализации упрощённой системы заказов

## Обзор

Детальный план создания упрощённой системы заказов для интернет-магазина на базе Laravel 12 + MoonShine v4.8.

**Ключевые особенности:**
- ✅ Список товаров (хранение в localStorage)
- ✅ Быстрая заявка с товара
- ✅ Оформление: имя + телефон + согласие
- ✅ Telegram уведомления
- ✅ MoonShine админка
- ❌ Без скидок
- ❌ Без купонов
- ❌ Без доставки
- ❌ Без онлайн-оплаты
- ❌ Без регистрации пользователей

---

## 1. БАЗА ДАННЫХ (Database)

### 1.1. Миграция для таблицы `orders`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_orders_table.php`

**Поля:**
- `id` - первичный ключ
- `order_number` - уникальный номер заказа (формат: ORD-YYYYMMDD-XXXX)
- `status` - статус заказа
- `total_amount` - итоговая сумма (decimal 10,2)
- `customer_name` - имя клиента
- `customer_phone` - телефон клиента
- `consent` - согласие на обработку данных (boolean)
- `product_id` - ID товара (для быстрой заявки, nullable)
- `product_name` - название товара (на момент заказа)
- `product_price` - цена товара (decimal 10,2, nullable)
- `quantity` - количество (integer, default 1, nullable)
- `notes` - заметки клиента (text, nullable)
- `ip_address` - IP адрес клиента (string, nullable)
- `user_agent` - User Agent браузера (text, nullable)
- `telegram_sent` - отправлено ли уведомление в Telegram (boolean, default false)
- `telegram_sent_at` - дата отправки уведомления (datetime, nullable)
- `created_at` / `updated_at`

**Статусы заказа:**
- `pending` - Новая заявка
- `confirmed` - Подтверждена
- `completed` - Выполнена
- `cancelled` - Отменена

### 1.2. Миграция для таблицы `order_items`
**Файл:** `database/migrations/YYYY_MM_DD_HHMMSS_create_order_items_table.php`

**Поля:**
- `id` - первичный ключ
- `order_id` - ID заказа (foreign key)
- `product_id` - ID товара (foreign key, nullable)
- `product_name` - название товара (на момент заказа)
- `product_sku` - артикул товара (string, nullable)
- `quantity` - количество (integer)
- `price` - цена за единицу (decimal 10,2)
- `total_price` - общая цена (decimal 10,2)
- `product_snapshot` - JSON с данными товара на момент заказа (json, nullable)
- `created_at` / `updated_at`

**Примечание:** Таблица order_items используется для будущих расширений, когда будут поддерживаться заказы с несколькими товарами. В базовой версии заказы создаются с одним товаром.

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
- `STATUS_COMPLETED = 'completed'`
- `STATUS_CANCELLED = 'cancelled'`

**Fillable поля:**
- Все поля кроме id, created_at, updated_at

**Casts:**
- `total_amount`, `product_price` → `decimal:2`
- `telegram_sent_at` → `datetime`
- `consent`, `telegram_sent` → `boolean`
- `quantity` → `integer`

**Отношения:**
- `items()` - HasMany OrderItem
- `product()` - BelongsTo Product (если быстрой заявки)

**Методы:**
- `generateOrderNumber()` - генерация уникального номера заказа
- `scopeByStatus($status)` - фильтр по статусу
- `scopePending()` - новые заявки
- `scopeNew()` - новые заявки (alias)
- `scopeCompleted()` - выполненные
- `getStatusLabel()` - получение названия статуса
- `markAsConfirmed()`, `markAsCompleted()`, `markAsCancelled()` - методы смены статуса
- `isQuickOrder()` - является ли быстрой заявкой
- `isBulkOrder()` - является ли заказом с несколькими товарами

**Observers:**
- Автоматическая генерация order_number при создании
- Отправка уведомления в Telegram при создании

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

---

## 3. КОНТРОЛЛЕРЫ (Controllers)

### 3.1. OrderController
**Файл:** `app/Http/Controllers/OrderController.php`

**Методы:**
- `store(Request $request)` - создание заказа (быстрой заявки или корзины)
- `success(Order $order)` - страница успешного заказа
- `show(Order $order)` - страница заказа (для админа)

**Логика метода store:**
1. Валидация данных (имя, телефон, согласие)
2. Определение типа заказа (быстрая заявка или из корзины)
3. Создание заказа
4. Создание order_items
5. Отправка уведомления в Telegram
6. Редирект на страницу успеха

### 3.2. ProductController
**Файл:** `app/Http/Controllers/ProductController.php`

**Методы:**
- `index()` - список товаров с фильтрами
- `show(Product $product)` - страница товара с формой заявки

---

## 4. API (API Routes)

### 4.1. API Routes
**Файл:** `routes/api.php`

**Endpoints:**

#### Заказы (Orders API)
- `POST /api/orders` - создание заказа (быстрой заявки или из корзины)
- `GET /api/orders/{order}` - просмотр заказа (для админа)

#### Корзина (Cart API - для localStorage)
- `POST /api/cart/validate` - валидация товаров в корзине (проверка наличия, актуальности цен)

---

## 5. FRONTEND

### 5.1. Компонент корзины (localStorage)

**Файл:** `resources/js/cart.js`

**Функционал:**
- Добавление товара в корзину (localStorage)
- Удаление товара из корзины
- Изменение количества
- Очистка корзины
- Подсчет итоговой суммы
- Сохранение в localStorage

**Структура данных в localStorage:**
```javascript
{
  "cart": [
    {
      "id": 1,
      "name": "Товар",
      "price": 100,
      "quantity": 2,
      "image": "/path/to/image.jpg"
    }
  ],
  "timestamp": 1234567890
}
```

### 5.2. Страница списка товаров
**Файл:** `resources/views/products/index.blade.php`

**Блоки:**
- Сетка товаров с:
  - Изображением
  - Названием
  - Ценой
  - Кнопкой "В корзину"
  - Кнопкой "Купить сейчас" (быстрая заявка)
- Фильтры по категориям (опционально)
- Поиск товаров (опционально)
- Сортировка (опционально)

### 5.3. Страница товара
**Файл:** `resources/views/products/show.blade.php`

**Блоки:**
- Изображение товара (галерея)
- Название и описание
- Цена
- Характеристики (опционально)
- Кнопка "В корзину"
- Кнопка "Купить сейчас" (быстрая заявка)
- Форма быстрой заявки:
  - Имя (required)
  - Телефон (required)
  - Чекбокс согласия (required)
  - Кнопка "Оформить заявку"

### 5.4. Корзина (localStorage)
**Может быть реализована как виджет или модалка**

**Блоки:**
- Список товаров в корзине
- Изменение количества (+/-)
- Удаление товаров
- Итоговая сумма
- Кнопка "Оформить заказ"
- Форма оформления:
  - Имя (required)
  - Телефон (required)
  - Чекбокс согласия (required)
  - Кнопка "Отправить заказ"

### 5.5. Страница успешного заказа
**Файл:** `resources/views/orders/success.blade.php`

**Блоки:**
- Номер заказа
- Сообщение благодарности
- Детали заказа (если авторизован админ)
- Кнопки:
  - "Продолжить покупки"
  - "В каталог"

### 5.6. JavaScript компоненты

**Файл:** `resources/js/order.js`

**Функции:**
- Работа с localStorage корзиной
- Добавление товара
- Удаление товара
- Изменение количества
- Отправка заказа (AJAX)
- Валидация формы
- Показ уведомлений

---

## 6. УВЕДОМЛЕНИЯ TELEGRAM

### 6.1. Создание Notification класса
**Файл:** `app/Notifications/NewOrderNotification.php`

**Методы:**
- `toTelegram($notifiable)` - форматирование сообщения для Telegram
- `toArray($notifiable)` - форматирование для других каналов

**Содержание сообщения:**
- 🆕 Новая заявка #{номер}
- 📦 Товар: {название}
- 💰 Сумма: {total}
- 📞 Телефон: {phone}
- 👤 Имя: {name}
- 📝 Комментарий: {notes}
- 📍 IP: {ip_address}

Для заказов из корзины:
- 📦 Количество товаров: {count}
- 💰 Сумма: {total}

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

### 6.4. Сервис для отправки уведомлений
**Файл:** `app/Services/TelegramService.php`

**Методы:**
- `sendOrderNotification(Order $order)` - отправка уведомления о заказе
- `sendOrderUpdatedNotification(Order $order)` - отправка уведомления об изменении статуса
- `formatOrderMessage(Order $order)` - форматирование сообщения

### 6.5. Отправка уведомления
**В OrderObserver при создании заказа:**
```php
$order->notify(new NewOrderNotification($order));
$order->update(['telegram_sent' => true, 'telegram_sent_at' => now()]);
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
- Сумма
- Клиент
- Телефон
- Тип заявки (быстрая/корзина)

**Поля в форме:**
- Информация о заказе (ID, номер, дата)
- Статус (select)
- Данные клиента (только чтение)
- Товары заказа (таблица)
- Заметки клиента (только чтение)
- Заметки администратора (textarea)
- Флаг "Отправлено в Telegram" (switcher, только чтение)

**Actions:**
- "Отправить повторно в Telegram"
- "Позвонить клиенту" (ссылка на tel:)
- "Отправить WhatsApp" (если настроено)

**Filters:**
- По статусу
- По дате (диапазон)
- По телефону клиента
- По номеру заказа

**Scopes:**
- `scopePending()` - новые заявки
- `scopeProcessing()` - в работе
- `scopeCompleted()` - выполненные
- `scopeCancelled()` - отмененные

### 7.2. Кастомные страницы MoonShine

**Dashboard widget:**
- Количество новых заявок сегодня
- Общая сумма заказов сегодня
- Количество заявок в работе
- График заявок за неделю

---

## 8. ДОПОЛНИТЕЛЬНЫЕ КОМПОНЕНТЫ

### 8.1. Services

#### OrderService
**Файл:** `app/Services/OrderService.php`

**Методы:**
- `createQuickOrder($data, $product)` - создание быстрой заявки
- `createOrderFromCart($data, $cartItems)` - создание заказа из корзины
- `updateOrderStatus($orderId, $status, $comment)` - обновление статуса
- `cancelOrder($orderId)` - отмена заявки
- `calculateOrderTotal($order)` - пересчет суммы

#### TelegramService
**Файл:** `app/Services/TelegramService.php`

**Методы:**
- `sendOrderNotification(Order $order)` - отправка уведомления
- `sendOrderStatusNotification(Order $order)` - уведомление о смене статуса
- `formatOrderMessage(Order $order)` - форматирование сообщения

### 8.2. Form Requests

#### StoreOrderRequest
**Файл:** `app/Http/Requests/StoreOrderRequest.php`

**Правила валидации:**
- `customer_name` - required, string, max:255
- `customer_phone` - required, regex:/^\+?\d{10,15}$/
- `consent` - required, accepted
- `notes` - nullable, string, max:1000
- `product_id` - nullable, exists:products,id (для быстрой заявки)
- `cart_items` - nullable, array (для заказа из корзины)

---

## 9. ПОРЯДОК РЕАЛИЗАЦИИ

### Этап 1: База данных и модели (Приоритет: Высокий)
1. Создать миграцию для orders
2. Создать миграцию для order_items
3. Создать модели Order и OrderItem
4. Настроить отношения
5. Настроить observers

### Этап 2: Базовый функционал (Приоритет: Высокий)
1. Создать OrderService
2. Создать OrderController
3. Создать Form Requests
4. Реализовать API для заказов
5. Создать JavaScript для работы с корзиной (localStorage)
6. Тестирование

### Этап 3: Frontend (Приоритет: Высокий)
1. Создать страницу товаров
2. Создать страницу товара с формой заявки
3. Реализовать JavaScript компонент корзины
4. Создать страницу успешного заказа
5. Тестирование

### Этап 4: Админка MoonShine (Приоритет: Средний)
1. Создать OrderResource
2. Добавить поля в список и форму
3. Добавить фильтры и scopes
4. Добавить виджеты на Dashboard
5. Тестирование админки

### Этап 5: Уведомления Telegram (Приоритет: Средний)
1. Установить пакет telegram notifications
2. Создать конфигурацию
3. Создать TelegramService
4. Создать Notification класс
5. Настроить отправку уведомлений
6. Тестирование уведомлений

### Этап 6: Дополнительный функционал (Приоритет: Низкий)
1. Фильтры и поиск товаров
2. История заказов в localStorage
3. Email уведомления (опционально)
4. SMS уведомления (опционально)
5. Статистика и отчеты

### Этап 7: Тестирование и оптимизация (Приоритет: Высокий)
1. Unit тесты для моделей
2. Feature тесты для API
3. Browser тесты для оформления
4. Оптимизация запросов к БД
5. Кэширование товаров

---

## 10. ТЕХНИЧЕСКИЕ ТРЕБОВАНИЯ

### Безопасность:
- Все формы с CSRF токеном
- Валидация всех входных данных
- Проверка согласия на обработку данных
- Защита от SQL injection (Eloquent ORM)
- Rate limiting для API
- Маскирование телефона в админке

### Производительность:
- Ленивая загрузка отношений (eager loading)
- Оптимизация запросов к БД
- Кэширование товаров (Redis/File)
- Индексы в БД для частых запросов

### UX/UI:
- AJAX обновления без перезагрузки
- Понятные сообщения об ошибках
- Сохранение данных в localStorage
- Адаптивный дизайн (mobile-first)
- Быстрая заявка в один клик

### Логирование:
- Логирование всех заказов
- Логирование уведомлений
- Audit trail для изменений статусов

---

## 11. КОНФИГУРАЦИЯ

### Файлы конфигурации:
- `config/orders.php` - настройки заказов
- `config/telegram.php` - настройки Telegram

### Переменные окружения (.env):
```env
# Telegram
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id
TELEGRAM_ENABLED=true

# Orders
ORDER_NUMBER_PREFIX=ORD
```

---

## ПРИМЕЧАНИЯ

1. **Без авторизации:** Все заказы создаются без регистрации пользователя
2. **localStorage корзина:** Корзина хранится в браузере клиента
3. **Быстрая заявка:** Заказ создается напрямую со страницы товара
4. **Цены:** Фиксировать цены в момент заказа (product_snapshot)
5. **Кэширование:** Кэшировать цены и наличие товаров
6. **Маскирование данных:** В админке показывать телефон в маскированном виде

---

## ЗАВИСИМОСТИ

### Необходимые пакеты:
```bash
# Telegram notifications
composer require laravel-notification-channels/telegram
```

### Установленные пакеты (уже есть):
- Laravel 12
- MoonShine v4.8
- Laravel framework components

---

## ДАТАСЕТ ДЛЯ ТЕСТИРОВАНИЯ

### Тестовые заказы:
- Создать 5-10 тестовых заказов с разными статусами
- Несколько быстрых заявок
- Несколько заказов из корзины с несколькими товарами

### Тестовые товары:
- 10-20 товаров с разными категориями
- Товары с изображениями
- Товары с характеристиками

---

Этот план предоставляет полную структуру для реализации упрощённой системы заказов. Реализацию можно делать поэтапно, начиная с базового функционала и постепенно добавляя дополнительные возможности.
