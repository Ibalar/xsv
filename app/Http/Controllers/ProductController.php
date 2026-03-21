<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        // Загружаем продукт с атрибутами
        $product = Product::with(['productAttributeValues.attributeValue.attribute'])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $product->incrementViews();

        // Галерея
        $images = [];
        if ($product->image) {
            $images[] = $product->image;
        }
        if (!empty($product->gallery) && is_array($product->gallery)) {
            $images = array_merge($images, $product->gallery);
        }

        // Связанные товары
        $categoryIds = [];
        if ($product->category) {
            $ancestors = $product->category->getAncestorsAndSelf();
            foreach ($ancestors as $cat) {
                $categoryIds = array_merge($categoryIds, $cat->getAllDescendantIds());
            }
            $categoryIds = array_unique($categoryIds);
        }

        $relatedProducts = Product::active()
            ->whereIn('category_id', $categoryIds)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(2)
            ->get();

        // Группировка атрибутов
        $attributes = $product->productAttributeValues
            ->filter(fn($pav) => $pav->attributeValue && $pav->attributeValue->attribute) // убираем пустые
            ->groupBy(fn($pav) => $pav->attributeValue->attribute->name);

        return view('products.show', compact('product', 'images', 'relatedProducts', 'attributes'));
    }
}
