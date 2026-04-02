<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\TelegramNotifier;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show()
    {
        return view('checkout');
    }

    public function store(Request $request, TelegramNotifier $telegramNotifier)
    {
        $data = $request->validate([
            'products' => 'required|json',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'agree' => 'required|accepted',
            'comment' => 'nullable|string|max:1000',
        ], [
            'products.required' => 'Пожалуйста, добавьте товары в заявку',
            'products.json' => 'Неверный формат корзины',
            'name.required' => 'Пожалуйста, укажите ваше имя',
            'name.max' => 'Имя не должно превышать 255 символов',
            'phone.required' => 'Пожалуйста, укажите ваш телефон',
            'phone.max' => 'Телефон не должен превышать 50 символов',
            'agree.required' => 'Необходимо согласие на обработку персональных данных',
            'agree.accepted' => 'Необходимо согласие на обработку персональных данных',
            'comment.max' => 'Комментарий не должен превышать 1000 символов',
        ]);

        $products = json_decode($data['products'], true);

        if (empty($products) || ! is_array($products)) {
            return back()
                ->with('error', 'Корзина пуста')
                ->withInput();
        }

        foreach ($products as $product) {
            if (! isset($product['id'], $product['name'], $product['price'], $product['quantity'])) {
                return back()
                    ->with('error', 'Неверный формат данных товаров')
                    ->withInput();
            }
        }

        $order = Order::create([
            'products' => $products,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'comment' => $data['comment'] ?? null,
            'agree' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            if ($telegramNotifier->sendOrder($order)) {
                $order->forceFill([
                    'telegram_sent' => true,
                    'telegram_sent_at' => now(),
                ])->save();
            }
        } catch (\Throwable $e) {
            // Silently fail. The order is already stored.
        }

        return redirect()
            ->route('orders.success', $order)
            ->with('success', 'Заявка успешно отправлена!');
    }

    public function success(Order $order)
    {
        return view('orders.success', compact('order'));
    }
}
