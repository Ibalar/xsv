<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" data-pwa="false">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', config('app.name', 'XSV.BY'))</title>
    <meta name="description" content="@yield('meta_description', ' ')">
    <meta name="keywords" content="@yield('meta_keywords', ' ')">
    <meta name="author" content="WebArt.by">

    <!-- Webmanifest + Favicon -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/app-icons/icon-32x32.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('assets/app-icons/icon-180x180.png') }}">

    <!-- Theme switcher (color modes) -->
    <script src="{{ asset('assets/js/theme-switcher.js') }}"></script>



    <!-- Font icons -->
    <link rel="preload" href="{{ asset('assets/icons/cartzilla-icons.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('assets/icons/cartzilla-icons.min.css') }}">

    <!-- Vendor styles -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/choices/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/nouislider/nouislider.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/glightbox/glightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/search.css') }}">

    <!-- Bootstrap + Theme styles -->
    <link rel="preload" href="{{ asset('assets/css/theme.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}" id="theme-styles">



    @stack('styles')
</head>
<body>
    @include('partials.menu-offcanvas')

    @include('partials.top-bar')
    <!-- Header -->
    @include('partials.header')

    <!-- Main content -->
    <main class="content-wrapper">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('partials.footer')

    @isset($category)
        <!-- Filter offcanvas toggle visible on screens < 992px -->
        <button type="button"
                class="fixed-bottom z-sticky w-100 btn btn-lg btn-dark border-0 border-top border-light border-opacity-10 rounded-0 pb-4 d-lg-none"
                data-bs-toggle="offcanvas"
                data-bs-target="#filterSidebar"
                aria-controls="filterSidebar"
                data-bs-theme="light">
            <i class="ci-filter fs-base me-2"></i>
            Фильтр товаров
        </button>
    @endisset

    <!-- Back to top button -->
    <div class="floating-buttons position-fixed top-50 end-0 z-sticky me-3 me-xl-4 pb-4">
        <a class="btn-scroll-top btn btn-sm bg-body border-0 rounded-pill shadow animate-slide-end" href="#top">
            Top
            <i class="ci-arrow-right fs-base ms-1 me-n1 animate-target"></i>
            <span class="position-absolute top-0 start-0 w-100 h-100 border rounded-pill z-0"></span>
            <svg class="position-absolute top-0 start-0 w-100 h-100 z-1" viewBox="0 0 62 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x=".75" y=".75" width="60.5" height="30.5" rx="15.25" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"/>
            </svg>
        </a>
    </div>

    <!-- Vendor scripts -->
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/choices/choices.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/js/search.js') }}"></script>
    <script src="{{ asset('assets/js/cart.js') }}"></script>
    <script src="{{ asset('assets/js/cookie-consent.js') }}"></script>


    <!-- Bootstrap + Theme scripts -->
    <script src="{{ asset('assets/js/theme.min.js') }}"></script>

    @stack('scripts')

    <!-- Cookie consent -->
    <div class="alert alert-dark alert-dismissible fade position-fixed bottom-0 start-0 end-0 m-3"
         role="alert"
         id="cookieConsent"
         style="z-index: 9999; display: none;">
        <div class="container">
            <div class="d-flex align-items-start">
                <i class="ci-info fs-3 mt-1 me-3 text-primary"></i>
                <div class="w-100">
                    <h6 class="alert-heading mb-2">Мы используем cookies</h6>
                    <p class="mb-3 fs-sm">
                        Этот сайт использует cookies для улучшения работы сайта и персонализации.
                        Продолжая использовать сайт, вы соглашаетесь с нашей
                        <a href="{{ route('privacy-policy') }}" class="alert-link text-decoration-underline">политикой конфиденциальности</a>.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary btn-sm" id="acceptCookies">
                            <i class="ci-check me-2"></i>Согласен
                        </button>
                        <a href="{{ route('privacy-policy') }}" class="btn btn-outline-light btn-sm">
                            Подробнее
                        </a>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
</body>
</html>
