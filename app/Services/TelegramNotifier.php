<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class TelegramNotifier
{
    public function isConfigured(): bool
    {
        return filled(config('services.telegram.bot_token'))
            && filled(config('services.telegram.chat_id'));
    }

    public function sendOrder(Order $order): bool
    {
        $productsText = '';
        $totalAmount = 0;

        foreach ($order->products as $product) {
            $quantity = (int) ($product['quantity'] ?? 0);
            $price = (float) ($product['price'] ?? 0);
            $itemTotal = $quantity * $price;

            $totalAmount += $itemTotal;
            $productsText .= "• " . $this->escape($product['name'] ?? 'Товар') . "\n";
            $productsText .= "  {$quantity} × {$price} BYN = {$itemTotal} BYN\n\n";
        }

        $message = "<b>🆕 Новая заявка #{$order->id}</b>\n\n";
        $message .= "<b>👤 Имя:</b> " . $this->escape($order->name) . "\n";
        $message .= "<b>📞 Телефон:</b> " . $this->escape($order->phone) . "\n";

        if ($order->comment) {
            $message .= "<b>📝 Комментарий:</b> " . $this->escape($order->comment) . "\n";
        }

        $message .= "\n<b>📦 Товары:</b>\n\n{$productsText}";
        $message .= "<b>💰 Итого:</b> {$totalAmount} BYN\n";
        $message .= "\n<i>" . now()->format('d.m.Y H:i') . '</i>';

        return $this->sendMessage($message);
    }

    public function sendContactForm(array $data): bool
    {
        $message = "<b>📩 Новое сообщение с формы контактов</b>\n\n";
        $message .= "<b>👤 Имя:</b> " . $this->escape((string) $data['name']) . "\n";
        $message .= "<b>📞 Телефон:</b> " . $this->escape((string) $data['phone']) . "\n";

        if (! empty($data['email'])) {
            $message .= "<b>✉️ Email:</b> " . $this->escape((string) $data['email']) . "\n";
        }

        $message .= "<b>💬 Сообщение:</b>\n" . $this->escape((string) $data['message']) . "\n";
        $message .= "\n<i>" . now()->format('d.m.Y H:i') . '</i>';

        return $this->sendMessage($message);
    }

    protected function sendMessage(string $message): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $response = Http::post(
            'https://api.telegram.org/bot' . config('services.telegram.bot_token') . '/sendMessage',
            [
                'chat_id' => config('services.telegram.chat_id'),
                'text' => $message,
                'parse_mode' => 'HTML',
            ]
        );

        return $response->successful() && $response->json('ok');
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
