<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\SiteSetting;
use App\Services\TelegramNotifier;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }

    public function contacts()
    {
        $contacts = SiteSetting::contacts();

        return view('pages.contacts', compact('contacts'));
    }

    public function sendContactForm(Request $request, TelegramNotifier $telegramNotifier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'message' => 'required|string|max:2000',
            'agree' => 'required|accepted',
        ], [
            'name.required' => 'Пожалуйста, укажите ваше имя',
            'name.max' => 'Имя не должно превышать 255 символов',
            'phone.required' => 'Пожалуйста, укажите ваш телефон',
            'phone.max' => 'Телефон не должен превышать 50 символов',
            'email.email' => 'Пожалуйста, введите корректный email',
            'email.max' => 'Email не должен превышать 255 символов',
            'message.required' => 'Пожалуйста, введите сообщение',
            'message.max' => 'Сообщение не должно превышать 2000 символов',
            'agree.required' => 'Необходимо согласие на обработку персональных данных',
            'agree.accepted' => 'Необходимо согласие на обработку персональных данных',
        ]);

        try {
            $telegramNotifier->sendContactForm($data);
        } catch (\Throwable $e) {
            // Silently fail. The form should still be accepted.
        }

        return redirect()
            ->route('contacts')
            ->with('success', 'Ваше сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.');
    }
}
