<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function redirectLegacy(string $legacyPath)
    {
        $normalizedPath = '/katalog/' . ltrim($legacyPath, '/');
        $legacyCandidates = [
            $normalizedPath,
            ltrim($normalizedPath, '/'),
        ];

        $product = Product::query()
            ->active()
            ->whereIn('legacy_url', $legacyCandidates)
            ->firstOrFail();

        return redirect()->route('products.show', $product->slug, 301);
    }

    public function show(string $slug)
    {
        $product = Product::with([
            'category',
            'productAttributeValues.attributeValue.attribute'
        ])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $product->incrementViews();

        // Галерея
        $images = [];

        if ($product->image) {
            $mainImage = is_array($product->image)
                ? $product->image[0] ?? null
                : $product->image;

            if ($mainImage) {
                $images[] = $mainImage;
            }
        }

        if (!empty($product->gallery) && is_array($product->gallery)) {
            $images = array_merge($images, $product->gallery);
        }

        // Категории
        $categoryIds = [];

        if ($product->category) {
            $ancestors = $product->category->getAncestorsAndSelf();

            foreach ($ancestors as $cat) {
                $categoryIds = array_merge($categoryIds, $cat->getAllDescendantIds());
            }

            $categoryIds = array_unique($categoryIds);
        }

        // Связанные товары
        $relatedProducts = Product::active()
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(2)
            ->get();

        // Атрибуты
        $attributes = $product->productAttributeValues
            ->filter(fn($pav) => $pav->attributeValue?->attribute)
            ->groupBy(fn($pav) => $pav->attributeValue->attribute->name);

        return view('products.show', compact('product', 'images', 'relatedProducts', 'attributes'));
    }
}
