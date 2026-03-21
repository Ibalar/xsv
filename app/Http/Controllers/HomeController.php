<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Получаем популярные товары
        $featuredProducts = Product::query()
            ->where('is_featured', true)
            ->where('in_stock', true)
            ->with('category')
            ->take(12)
            ->get();

        $newProducts = Product::query()
            ->where('is_new', true)
            ->where('in_stock', true)
            ->where('is_active', true)
            ->with('category')
            ->take(12)
            ->get();

        $inStockProducts = Product::query()
            ->where('is_active', true)
            ->where('in_stock', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('featuredProducts', 'newProducts', 'inStockProducts'));
    }
}
