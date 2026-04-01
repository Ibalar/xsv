<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim($request->get('q'));

        if (!$q || mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('description', 'LIKE', "%{$q}%");
            })
            ->orderByRaw("
            CASE
                WHEN name LIKE ? THEN 1
                WHEN name LIKE ? THEN 2
                ELSE 3
            END
        ", ["{$q}%", "%{$q}%"])
            ->limit(10)
            ->get(['slug', 'name', 'price', 'image']);

        // Формируем полный путь к изображению
        $products->transform(function ($product) {
            $product->image = $product->getMainImageUrl();
            return $product;
        });

        return response()->json($products);
    }
}
