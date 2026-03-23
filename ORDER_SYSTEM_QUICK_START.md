# Быстрый старт: Упрощённая система заказов

Краткое руководство по началу реализации упрощённой системы заказов.

---

## ШАГ 1: Создание базы данных (10 минут)

### Выполните команды:

```bash
# Создайте миграцию для заказов
php artisan make:migration create_orders_table

# Создайте миграцию для товаров в заказе
php artisan make:migration create_order_items_table
```

**Воспользуйтесь готовым кодом миграций из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 2: Создание моделей (5 минут)

### Выполните команды:

```bash
php artisan make:model Order
php artisan make:model OrderItem
```

**Воспользуйтесь готовым кодом моделей из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 3: Применение миграций (1 минута)

```bash
php artisan migrate
```

---

## ШАГ 4: Создание контроллеров (10 минут)

```bash
php artisan make:controller OrderController
php artisan make:controller ProductController
```

**Воспользуйтесь готовым кодом контроллеров из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 5: Создание сервиса для заказов (5 минут)

```bash
php artisan make:service OrderService
```

**Воспользуйтесь готовым кодом сервиса из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 6: Добавление маршрутов (5 минут)

### Добавьте в routes/web.php:

```php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Orders
Route::get('/order/success/{order}', [OrderController::class, 'success'])->name('orders.success');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
```

### Добавьте в routes/api.php:

```php
use App\Http\Controllers\OrderController;

// Orders API
Route::post('/orders', [OrderController::class, 'store']);
```

---

## ШАГ 7: Создание представлений (20 минут)

### Создайте директории:

```bash
mkdir -p resources/views/products
mkdir -p resources/views/orders
```

### Создайте файлы:

1. `resources/views/products/index.blade.php` - список товаров
2. `resources/views/products/show.blade.php` - страница товара с формой заявки
3. `resources/views/orders/success.blade.php` - страница успешного заказа

**Воспользуйтесь готовым кодом из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 8: Создание JavaScript (10 минут)

### Создайте файл:

```bash
touch resources/js/order.js
```

**Воспользуйтесь готовым кодом из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

### Подключите JavaScript в layout:

```blade
<script src="{{ asset('assets/js/order.js') }}"></script>
```

---

## ШАГ 9: Создание MoonShine ресурса (15 минут)

```bash
php artisan make:moonshine-resource OrderResource
```

**Воспользуйтесь готовым кодом из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 10: Настройка Telegram уведомлений (10 минут)

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

## ШАГ 11: Создание сервиса Telegram (5 минут)

```bash
php artisan make:service TelegramService
```

**Воспользуйтесь готовым кодом из файла `ORDER_SYSTEM_CODE_EXAMPLES.md`**

---

## ШАГ 12: Тестирование (15 минут)

### 1. Проверьте миграции:

```bash
php artisan migrate:status
```

### 2. Протестируйте создание заказа:

- Откройте страницу товара
- Заполните форму заявки (имя, телефон, согласие)
- Отправьте заявку
- Проверьте страницу успешного заказа

### 3. Проверьте в админке:

- Откройте `/admin`
- Перейдите в раздел "Заказы"
- Проверьте созданный заказ

### 4. Проверьте Telegram уведомления:

- Проверьте, что уведомление пришло в Telegram
- Проверьте форматирование сообщения

---

## ПРОВЕРОЧНЫЙ СПИСОК

Перед запуском убедитесь, что:

- [ ] Все миграции применены
- [ ] Модели созданы с правильными отношениями
- [ ] Сервисы созданы и подключены
- [ ] Контроллеры созданы
- [ ] Маршруты добавлены
- [ ] Представления созданы
- [ ] JavaScript подключен
- [ ] MoonShine ресурс создан
- [ ] Telegram настроен
- [ ] Форма заявки работает
- [ ] Уведомления приходят в Telegram

---

## СЛЕДУЮЩИЕ ШАГИ

После базовой реализации:

1. **Доработайте каталог товаров:**
   - Фильтры по категориям
   - Поиск товаров
   - Сортировка

2. **Добавьте историю заказов:**
   - Сохранение в localStorage
   - Быстрый повтор заказа

3. **Настройте дополнительные уведомления:**
   - SMS уведомления (опционально)
   - Email уведомления (опционально)

4. **Добавьте аналитику:**
   - Статистика заказов в админке
   - Графики продаж
   - ТОП товаров

5. **Оптимизация:**
   - Кэширование товаров
   - Оптимизация запросов к БД

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

# Проверка маршрутов
php artisan route:list

# Тестирование уведомлений
php artisan tinker
>>> Order::first()->notify(new NewOrderNotification(Order::first()))
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
5. Проверьте настройки Telegram в `.env`

---

**Примерное время реализации базовой функциональности: 1-2 часа**

**Полная реализация с дополнительным функционалом: 4-6 часов**
