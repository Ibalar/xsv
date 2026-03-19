<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function show(string $path)
    {
        // разбиваем путь: parent/child/subchild
        $slugs = explode('/', $path);

        // ищем категорию по последнему slug
        $category = Category::query()
            ->where('slug', end($slugs))
            ->with(['children' => function ($q) {
                $q->active()->ordered();
            }])
            ->firstOrFail();

        // 🔥 проверка полного пути (важно для SEO)
        if ($category->getFullPath() !== $path) {
            abort(404);
        }

        // получаем все id (категория + потомки)
        $categoryIds = $category->getAllDescendantIds();

        // если есть товары (пример)
        $products = \App\Models\Product::query()
            ->whereIn('category_id', $categoryIds)
            ->latest()
            ->paginate(12);

        return view('catalog.show', [
            'category' => $category,
            'products' => $products,
            'breadcrumbs' => $category->getBreadcrumbs(),
        ]);
    }
}
