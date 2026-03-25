@extends('layouts.main')

@section('title', 'Категория')

@section('meta_description', ' ')
@section('meta_keywords', ' ')

@section('content')

    <!-- Breadcrumb -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Page title -->
    <h1 class="h3 container pb-2 pb-md-3 pb-lg-4">{{ $category->name }}</h1>


    <!-- Products grid + Sidebar with filters -->
    <section class="container pb-5 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
        <div class="row">

            <!-- Filter sidebar that turns into offcanvas on screens < 992px wide (lg breakpoint) -->
            <aside class="col-lg-3">
                <div class="offcanvas-lg offcanvas-start pe-lg-4" id="filterSidebar">
                    <div class="offcanvas-header py-3">
                        <h5 class="offcanvas-title">Фильтр товаров</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#filterSidebar" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body flex-column pt-2 py-lg-0">

                        <!-- Categories -->
                        @include('partials.categories')

                        <form method="GET" action="{{ route('catalog.show', $category->getFullPath()) }}">

                            <!-- Filters -->
                            <div class="accordion border-top mb-4">

                                <!-- Price -->
                                <div class="accordion-item">
                                    <h4 class="accordion-header" id="headingPrice">
                                        <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#price">
                                            Стоимость
                                        </button>
                                    </h4>

                                    <div class="accordion-collapse collapse show" id="price">
                                        <div class="accordion-body">

                                            <div class="range-slider ps-1"
                                                 data-range-slider='{
                                                "startMin": {{ $minPrice ?? $minPriceAll }},
                                                "startMax": {{ $maxPrice ?? $maxPriceAll }},
                                                "min": {{ $minPriceAll }},
                                                "max": {{ $maxPriceAll }},
                                                "step": 1,
                                                "tooltipSuffix": " р."
                                                }'>

                                                <div class="range-slider-ui"></div>

                                                <div class="d-flex align-items-center">
                                                    <div class="position-relative w-50">
                                                        <input type="number"
                                                               class="form-control"
                                                               name="min_price"
                                                               value="{{ $minPrice ?? $minPriceAll }}"
                                                               data-range-slider-min>
                                                    </div>

                                                    <i class="ci-minus mx-2"></i>

                                                    <div class="position-relative w-50">
                                                        <input type="number"
                                                               class="form-control"
                                                               name="max_price"
                                                               value="{{ $maxPrice ?? $maxPriceAll }}"
                                                               data-range-slider-max>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Attributes -->
                                @foreach($filterAttributes as $attribute)
                                    <div class="accordion-item">
                                        <h4 class="accordion-header">
                                            <button type="button"
                                                    class="accordion-button collapsed"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#attr-{{ $attribute->id }}">
                                                {{ $attribute->name }}
                                            </button>
                                        </h4>

                                        <div id="attr-{{ $attribute->id }}"
                                             class="accordion-collapse collapse show">

                                            <div class="accordion-body">
                                                <div class="d-flex flex-column gap-2">

                                                    @foreach($attribute->attributeValues as $value)
                                                        <div class="form-check mb-0">

                                                            <input type="checkbox"
                                                                   class="form-check-input"
                                                                   id="attr-{{ $value->id }}"
                                                                   name="{{ $attribute->slug }}[]"
                                                                   value="{{ $value->slug }}"
                                                                @checked(in_array($value->slug, request($attribute->slug, [])))
                                                            >

                                                            <label for="attr-{{ $value->id }}"
                                                                   class="form-check-label text-body-emphasis">
                                                                {{ $value->value }}
                                                            </label>

                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach


                                {{-- 🔥 АКТИВНЫЕ ФИЛЬТРЫ --}}
                                @if(!empty($filters) || request('min_price') || request('max_price'))
                                    <div class="mt-3 mb-2">
                                        <small class="text-muted d-block mb-2">Активные фильтры:</small>

                                        <div class="d-flex flex-wrap gap-2">

                                            {{-- Цена --}}
                                            @if(request('min_price') || request('max_price'))
                                                @php
                                                    $query = request()->except(['min_price', 'max_price']);
                                                @endphp

                                                <a href="{{ route('catalog.show', $category->getFullPath()) }}?{{ http_build_query($query) }}"
                                                   class="badge bg-secondary text-decoration-none">

                                                    Цена:
                                                    {{ request('min_price') ? 'от ' . request('min_price') : '' }}
                                                    {{ request('max_price') ? ' до ' . request('max_price') : '' }}
                                                    ✕
                                                </a>
                                            @endif


                                            {{-- Атрибуты --}}
                                            @foreach($filterAttributes as $attribute)
                                                @foreach(request($attribute->slug, []) as $valueSlug)

                                                    @php
                                                        $value = $attribute->attributeValues->firstWhere('slug', $valueSlug);

                                                        $query = request()->query();
                                                        $values = $query[$attribute->slug] ?? [];

                                                        $newValues = array_diff($values, [$valueSlug]);

                                                        if (empty($newValues)) {
                                                            unset($query[$attribute->slug]);
                                                        } else {
                                                            $query[$attribute->slug] = $newValues;
                                                        }
                                                    @endphp

                                                    @if($value)
                                                        <a href="{{ route('catalog.show', $category->getFullPath()) }}?{{ http_build_query($query) }}"
                                                           class="badge bg-secondary text-decoration-none">

                                                            {{ $attribute->name }}: {{ $value->value }} ✕
                                                        </a>
                                                    @endif

                                                @endforeach
                                            @endforeach

                                        </div>
                                    </div>
                                @endif


                                {{-- КНОПКИ --}}
                                <div class="d-flex gap-3 mb-4 justify-content-between">
                                    <button type="submit" class="btn btn-sm btn-primary mt-2">
                                        Применить
                                    </button>

                                    @if(!empty($filters) || request('min_price') || request('max_price'))
                                        <a href="{{ route('catalog.show', $category->getFullPath()) }}?sort={{ request('sort') }}"
                                           class="btn btn-sm btn-secondary mt-2">
                                            ✕ Очистить
                                        </a>
                                    @endif
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </aside>


            <!-- Product grid -->
            <div class="col-lg-9">

                <!-- Sorting -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div class="fs-sm text-body-emphasis text-nowrap mb-2 mb-sm-0">
                        Всего товаров: <span class="fw-semibold">{{ $products->total() }}</span>
                    </div>
                    <div class="d-flex align-items-center text-nowrap mb-3">
                        <label class="form-label fw-semibold mb-0 me-2">Сортировать:</label>
                        <div style="width: 200px">
                            <form method="GET" id="sortForm">
                                <select name="sort" class="form-select rounded-pill" onchange="document.getElementById('sortForm').submit()">
                                    <option value="Relevance" {{ $sort === 'Relevance' ? 'selected' : '' }}>Релевантность</option>
                                    <option value="Alphabet" {{ $sort === 'Alphabet' ? 'selected' : '' }}>Название А-Я</option>
                                    <option value="Price: Low to High" {{ $sort === 'Price: Low to High' ? 'selected' : '' }}>Сначала дешевле</option>
                                    <option value="Price: High to Low" {{ $sort === 'Price: High to Low' ? 'selected' : '' }}>Сначала дороже</option>
                                    <option value="Popularity" {{ $sort === 'Popularity' ? 'selected' : '' }}>Только хиты</option>
                                    <option value="Newest Arrivals" {{ $sort === 'Newest Arrivals' ? 'selected' : '' }}>Только новинки</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                @if($subcategories->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach($subcategories as $sub)
                            <a href="{{ route('catalog.show', $sub->getFullPath()) }}" class="btn btn-sm btn-secondary">
                                <i class="ci-corner-down-right fs-sm ms-n1 me-1"></i>
                                {{ $sub->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Grid -->
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-3 row-cols-xl-4 g-4">
                    @forelse ($products as $product)
                        <div class="col">
                            <div class="card product-card h-100 bg-transparent border-0 shadow-none">
                                <div class="position-relative z-2">
                                    @if($product->discount)
                                        <span class="badge text-bg-danger position-absolute top-0 start-0 z-2 mt-1 mt-sm-2 ms-1 ms-sm-2">
                                            {{ $product->discount }}
                                        </span>
                                    @endif

                                    <button type="button" class="btn btn-icon btn-sm btn-secondary animate-pulse fs-sm bg-body border-0 position-absolute top-0 end-0 z-2 mt-1 mt-sm-2 me-1 me-sm-2" aria-label="Add to Wishlist">
                                        <i class="ci-heart animate-target"></i>
                                    </button>

                                    <a class="d-block p-2 p-lg-3" href="{{ route('products.show', $product->slug) }}">
                                        <div class="ratio" style="--cz-aspect-ratio: calc(160 / 191 * 100%)">
                                            <img src="{{ asset('storage/products/' . $product->image) ?? 'assets/img/placeholder.png' }}" alt="{{ $product->name }}">
                                        </div>
                                    </a>

                                    <div class="position-absolute w-100 start-0 bottom-0">
                                        <div class="d-flex justify-content-end mt-1 mt-sm-2 me-1 me-sm-2">
                                            <div class="count-input count-input-collapsible collapsed justify-content-between w-100 bg-transparent border-0 rounded-2">
                                                <button type="button" class="btn btn-icon btn-sm btn-primary" data-decrement aria-label="Decrement quantity">
                                                    <i class="ci-minus fs-sm"></i>
                                                </button>
                                                <input type="number" class="form-control form-control-sm bg-primary text-white w-100" value="0" min="0" readonly>
                                                <button type="button"
                                                        class="product-card-button btn btn-icon btn-sm btn-secondary ms-auto"
                                                        data-id="{{ $product->id }}"
                                                        data-name="{{ $product->name }}"
                                                        data-price="{{ $product->price }}"
                                                        data-image="{{ $product->image }}"
                                                        data-increment
                                                        aria-label="Increment quantity">
                                                    <span data-count-input-value></span>
                                                    <i class="ci-chat fs-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body pt-0 px-1 px-md-2 px-lg-3 pb-2">
                                    <div class="h6 mb-2">
                                        {{ number_format($product->price, 2) }} BYN
                                        @if($product->price_old)
                                            <del class="fs-sm fw-normal text-body-tertiary ms-1">{{ number_format($product->price_old, 2) }} BYN</del>
                                        @endif
                                    </div>

                                    <h3 class="fs-sm lh-base mb-0">
                                        <a class="hover-effect-underline fw-normal" href="{{ route('products.show', $product->slug) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                </div>

                                @if($product->weight)
                                    <div class="fs-xs text-body-secondary px-1 px-md-2 px-lg-3 pb-2 pb-md-3">
                                        {{ $product->weight }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col">
                            <p>Товары в этой категории отсутствуют.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Кастомная пагинация --}}
                @if($products->hasPages())
                    <nav class="border-top mt-4 pt-3" aria-label="Catalog pagination">
                        <ul class="pagination pagination-lg pt-2 pt-md-3">
                            {{-- Previous --}}
                            <li class="page-item {{ $products->onFirstPage() ? 'disabled me-auto' : 'me-auto' }}">
                                <a class="page-link d-flex align-items-center h-100 fs-lg px-2" href="{{ $products->previousPageUrl() }}" aria-label="Previous page">
                                    <i class="ci-chevron-left mx-1"></i>
                                </a>
                            </li>

                            {{-- Links --}}
                            @foreach ($products->links()->elements[0] ?? [] as $page => $url)
                                @if ($page == $products->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }} <span class="visually-hidden">(current)</span></span>
                                    </li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            <li class="page-item {{ $products->hasMorePages() ? 'ms-auto' : 'disabled ms-auto' }}">
                                <a class="page-link d-flex align-items-center h-100 fs-lg px-2" href="{{ $products->nextPageUrl() }}" aria-label="Next page">
                                    <i class="ci-chevron-right mx-1"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                @endif

            </div>
        </div>
    </section>



@endsection
