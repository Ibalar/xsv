@extends('layouts.main')

@php
    $seo = [
        'title' => 'Заявка успешно отправлена - ' . config('app.name', 'XSV.BY'),
        'description' => 'Ваша заявка успешно отправлена. Мы свяжемся с вами в ближайшее время.',
        'canonical' => isset($order) ? route('orders.success', $order) : url()->current(),
        'schema_type' => 'WebPage',
        'robots' => 'noindex,follow',
    ];
@endphp

@section('title', 'Заявка успешно отправлена')

@section('meta_description', 'Ваша заявка успешно отправлена. Мы свяжемся с вами в ближайшее время.')
@section('meta_keywords', 'заказ оформлен, успех, питомник растений')

@section('content')

    <!-- Success section -->
    <section class="container py-5 my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 text-center">

                <!-- Success icon -->
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle" style="width: 120px; height: 120px;">
                        <i class="ci-check text-success" style="font-size: 60px;"></i>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="h2 mb-3">Спасибо за вашу заявку!</h1>

                <!-- Order number -->
                @if(isset($order))
                <div class="mb-4">
                    <p class="text-muted mb-2">Номер заявки:</p>
                    <span class="badge bg-primary fs-5 px-4 py-2">#{{ $order->id }}</span>
                </div>
                @endif

                <!-- Description -->
                <p class="text-muted mb-4 fs-5">
                    Мы получили вашу заявку и свяжемся с вами в ближайшее время для подтверждения заказа.
                </p>

                <!-- Info cards -->
                <div class="row g-3 mb-5">
                    <div class="col-sm-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <i class="ci-phone fs-2 text-primary mb-2"></i>
                                <p class="small text-muted mb-0">Перезвоним вам для подтверждения</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <i class="ci-delivery fs-2 text-primary mb-2"></i>
                                <p class="small text-muted mb-0">Доставка по всей Беларуси</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <i class="ci-check-shield fs-2 text-primary mb-2"></i>
                                <p class="small text-muted mb-0">Гарантия качества на все товары</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="ci-home me-2"></i>На главную
                    </a>
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="ci-grid me-2"></i>В каталог
                    </a>
                </div>

                <!-- Contact info -->
                <div class="mt-5 pt-4 border-top">
                    <p class="text-muted mb-2">Есть вопросы? Свяжитесь с нами:</p>
                    <div class="d-flex justify-content-center gap-4">
                        <a href="tel:+375291234567" class="text-decoration-none">
                            <i class="ci-phone me-1"></i>+375 (29) 123-45-67
                        </a>
                        <a href="mailto:info@xsv.by" class="text-decoration-none">
                            <i class="ci-mail me-1"></i>info@xsv.by
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
// Clear cart after successful order
document.addEventListener('DOMContentLoaded', function() {
    localStorage.removeItem('cart');

    // Update badge
    if (window.cart && window.cart.updateCartBadge) {
        window.cart.updateCartBadge();
    }
});
</script>
@endpush
