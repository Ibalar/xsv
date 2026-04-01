<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    // Показ страницы оформления заказа
    public function show()
    {
        return view('checkout');
    }

    // Сохранение заказа или быстрой заявки
    public function store(Request $request)
    {
        // Валидация данных
        $data = $request->validate([
            'products' => 'required|json',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'agree' => 'required|accepted',
            'comment' => 'nullable|string|max:1000',
        ], [
            'products.required' => 'Корзина пуста',
            'products.json' => 'Неверный формат данных корзины',
            'name.required' => 'Пожалуйста, укажите ваше имя',
            'name.max' => 'Имя не должно превышать 255 символов',
            'phone.required' => 'Пожалуйста, укажите ваш телефон',
            'phone.max' => 'Телефон не должен превышать 50 символов',
            'agree.required' => 'Необходимо согласие на обработку персональных данных',
            'agree.accepted' => 'Необходимо согласие на обработку персональных данных',
            'comment.max' => 'Комментарий не должен превышать 1000 символов',
        ]);

        // Декодируем товары из JSON
        $products = json_decode($data['products'], true);

        if (empty($products) || !is_array($products)) {
            return back()
                ->with('error', 'Корзина пуста')
                ->withInput();
        }

        // Валидируем структуру товаров
        foreach ($products as $product) {
            if (!isset($product['id'], $product['name'], $product['price'], $product['quantity'])) {
                return back()
                    ->with('error', 'Неверный формат данных товаров')
                    ->withInput();
            }
        }

        // Создание заказа в базе
        $order = Order::create([
            'products' => $products,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'comment' => $data['comment'] ?? null,
            'agree' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Отправка в Telegram
        try {
            $this->sendTelegram($order);
        } catch (\Exception $e) {
            \Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }

        // Редирект на страницу успеха
        return redirect()
            ->route('orders.success', $order)
            ->with('success', 'Заявка успешно отправлена!');
    }

    // Страница успешного оформления заказа
    public function success(Order $order)
    {
        return view('orders.success', compact('order'));
    }

    // Метод для отправки данных в Telegram
    protected function sendTelegram(Order $order)
    {
        $token = config('services.telegram.bot_token');
        $chat_id = config('services.telegram.chat_id');

        // Если не настроены настройки Telegram - пропускаем
        if (empty($token) || empty($chat_id)) {
            return;
        }

        $productsText = '';
        $totalAmount = 0;

        foreach ($order->products as $p) {
            $itemTotal = $p['quantity'] * $p['price'];
            $totalAmount += $itemTotal;
            $productsText .= "• {$p['name']}\n";
            $productsText .= "  {$p['quantity']} × {$p['price']} BYN = {$itemTotal} BYN\n\n";
        }

        $message = "<b>🆕 Новая заявка #{$order->id}</b>\n\n";
        $message .= "<b>👤 Имя:</b> {$order->name}\n";
        $message .= "<b>📞 Телефон:</b> {$order->phone}\n";

        if ($order->comment) {
            $message .= "<b>📝 Комментарий:</b> {$order->comment}\n";
        }

        $message .= "\n<b>📦 Товары:</b>\n\n";
        $message .= $productsText;
        $message .= "<b>💰 Итого:</b> {$totalAmount} BYN\n";
        $message .= "\n<i>" . now()->format('d.m.Y H:i') . "</i>";

        // Отправка через Telegram Bot API
        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        // Расширенное логирование ответа
        \Log::info("Telegram API response for order #{$order->id}:", [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        // Проверка успешности отправки и обновление полей
        if ($response->successful() && $response->json('ok')) {
            $updated = $order->forceFill([
                'telegram_sent' => true,
                'telegram_sent_at' => now(),
            ])->save();

            \Log::info("Database update for order #{$order->id} (telegram_sent): " . ($updated ? 'success' : 'failed'));
        } else {
            \Log::error("Telegram notification failed for order #{$order->id}", [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
        }
    }
}
