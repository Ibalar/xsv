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
            ->with('category') // если нужно
            ->take(12)         // количество слайдов, можно менять
            ->get();

        return view('home', compact('featuredProducts'));
    }
}
