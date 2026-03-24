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
        return view('checkout'); // подключи свой Blade-шаблон checkout.blade.php
    }

    // Сохранение заказа или быстрой заявки
    public function store(Request $request)
    {
        // Валидация данных
        $data = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer',
            'products.*.name' => 'required|string',
            'products.*.price' => 'required|numeric',
            'products.*.quantity' => 'required|integer|min:1',
            'name' => 'required|string',
            'phone' => 'required|string',
            'agree' => 'accepted',
        ]);

        // Создание заказа в базе
        $order = Order::create([
            'products' => $data['products'], // JSON
            'name' => $data['name'],
            'phone' => $data['phone'],
            'agree' => true,
        ]);

        // Отправка в Telegram
        $this->sendTelegram($order);

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    // Метод для отправки данных в Telegram
    protected function sendTelegram(Order $order)
    {
        $token = config('services.telegram.bot_token'); // твой токен
        $chat_id = config('services.telegram.chat_id'); // ID чата

        $productsText = '';
        foreach ($order->products as $p) {
            $productsText .= "{$p['name']} — {$p['quantity']} × {$p['price']} BYN\n";
        }

        $message = "Новый заказ:\n\n"
            . "Имя: {$order->name}\n"
            . "Телефон: {$order->phone}\n"
            . "Согласие на обработку данных: Да\n\n"
            . "Товары:\n{$productsText}";

        // Отправка через Telegram Bot API
        Http::get("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
    }
}
