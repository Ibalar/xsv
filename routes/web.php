<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PageController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin',
        'Disallow: /search',
        'Disallow: /checkout',
        'Disallow: /orders/success/',
        '',
        'Sitemap: ' . route('sitemap'),
    ];

    return response(implode("\n", $lines) . "\n")
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');
Route::get('/sitemap.xml', function (SitemapService $sitemapService) {
    return response()
        ->view('sitemap.xml', ['urls' => $sitemapService->build()])
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/katalog/{legacyPath}', [ProductController::class, 'redirectLegacy'])
    ->where('legacyPath', '.*\.html')
    ->name('products.legacy');

Route::get('/katalog/{path}', [CatalogController::class, 'show'])
    ->where('path', '.*')
    ->name('catalog.show');

Route::get('/product/{slug}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/search', SearchController::class);

// Страница корзины / оформления заказа
Route::get('/checkout', [OrderController::class, 'show'])->name('checkout.show');

// Отправка заказа и быстрой заявки
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
Route::post('/quick-order', [OrderController::class, 'store'])->name('quick-order.store');

// Страница успешного оформления заказа
Route::get('/orders/success/{order}', [OrderController::class, 'success'])->name('orders.success');

// Политика конфиденциальности
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

// Страница контактов
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::post('/contacts', [PageController::class, 'sendContactForm'])->name('contacts.send');

// Динамические страницы
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('pages.show');
