@extends('layouts.main')

@section('title', 'Товар')

@section('meta_description', ' ')
@section('meta_keywords', ' ')

@section('content')

    <!-- Breadcrumb -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Product gallery + Product details -->
    <section class="container pt-md-4 pb-5 mt-md-2 mt-lg-3 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
        <div class="row align-items-start">

            <!-- Product gallery -->
            <div class="col-md-6 col-lg-7 sticky-md-top z-1 mb-4 mb-md-0" style="margin-top: -120px">
                <div class="d-flex" style="padding-top: 120px">

                    <!-- Thumbnails -->
                    <div class="swiper swiper-load swiper-thumbs d-none d-lg-block w-100 me-xl-3" id="thumbs"
                         data-swiper='{
                "direction": "vertical",
                "spaceBetween": 12,
                "slidesPerView": 4,
                "watchSlidesProgress": true
             }' style="max-width: 96px; height: 420px;">
                        <div class="swiper-wrapper flex-column">
                            @foreach($images as $img)
                                <div class="swiper-slide swiper-thumb">
                                    <div class="ratio ratio-1x1" style="max-width: 94px">
                                        <img src="{{ asset('storage/products/' . $img) }}" class="swiper-thumb-img" alt="{{ $product->name }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preview (Large image) -->
                    <div class="swiper w-100" data-swiper='{
                "loop": false,
                "thumbs": {
                  "swiper": "#thumbs"
                },
                "pagination": {
                  "el": ".swiper-pagination",
                  "clickable": true
                }
              }'>
                        <div class="swiper-wrapper">
                            @foreach($images as $img)
                                <div class="swiper-slide">
                                    <a class="ratio ratio-1x1 d-block cursor-zoom-in"
                                       href="{{ asset('storage/products/' . $img) }}"
                                       data-glightbox
                                       data-gallery="product-gallery">
                                        <img src="{{ asset('storage/products/' . $img) }}" alt="{{ $product->name }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <!-- Slider pagination (Bullets) visible on screens > 991px wide (lg breakpoint) -->
                        <div class="swiper-pagination mb-n3 d-lg-none"></div>
                    </div>
                </div>
            </div>


            <!-- Product details -->
            <div class="col-md-6 col-lg-5 position-relative">
                <div class="ps-xxl-3">
                    <h1 class="h5 mb-2">{{ $product->name }}</h1>

                    <div class="h3">{{ number_format($product->price, 2) }} BYN</div>
                    @if($product->wholesale_price)
                        <div class="border rounded-pill px-4 py-2 my-4">
                            <div class="text-dark-emphasis fs-sm py-1">
                                Оптовая стоимость:
                                <span class="text-dark-emphasis fs-5 fw-medium ms-1">
                                    {{ $product->wholesale_price }} BYN
                                </span>
                                при заказе от <span class="fs-6 fw-medium">{{ $product->wholesale_min_quantity }}</span> шт.
                            </div>
                        </div>
                    @endif
                    <div class="d-flex gap-3 mb-4">
                        <button type="button"
                                class="product-card-button btn btn-lg btn-primary rounded-pill w-100"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-image="{{ $product->image }}"
                                data-slug="{{ $product->slug }}"
                        >в Лист заказа</button>
                        <button type="button"
                                class="quick-order-button btn btn-lg btn-dark rounded-pill w-100"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-image="{{ $product->image }}"
                                data-slug="{{ $product->slug }}"
                        >Быстрая заявка</button>
                    </div>
                    <ul class="list-unstyled gap-3 pb-3 pb-lg-4 mb-3">
                        @if($product->supplier?->name)
                        <li class="d-flex flex-wrap fs-sm">
                          <span class="d-flex align-items-center fw-medium text-dark-emphasis me-2">
                            <i class="ci-delivery fs-base me-2"></i>
                            Поставщик:
                          </span>
                            {{ $product->supplier?->name ?? '-' }}
                            <span class="d-block fs-xs">{{ $product->supplier?->description ?? '-' }}</span>
                        </li>
                        @endif
                        @if($product->country?->name)
                        <li class="d-flex flex-wrap fs-sm">
                          <span class="d-flex align-items-center fw-medium text-dark-emphasis me-2">
                            <i class="ci-globe fs-base me-2"></i>
                            Страна производства:
                          </span>
                            {{ $product->country?->name ?? '-' }}
                        </li>
                        @endif

                    </ul>
                    @if($product->short_description)
                        <p class="fs-sm mb-4">{{ $product->short_description }}</p>
                    @endif

                    <!-- Product info accordion -->
                    <div class="accordion accordion-alt-icon py-2 mb-4" id="productAccordion">
                        @if($attributes->isNotEmpty())
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="headingProductIngredients">
                                    <button type="button" class="accordion-button animate-underline collapsed" data-bs-toggle="collapse" data-bs-target="#productIngredients" aria-expanded="false" aria-controls="productIngredients">
                                        <span class="animate-target me-2">Характеристики</span>
                                    </button>
                                </h3>
                                <div class="accordion-collapse collapse" id="productIngredients" aria-labelledby="headingProductIngredients" data-bs-parent="#productAccordion">
                                    <div class="accordion-body">
                                        <ul class="list-unstyled">
                                            @foreach($attributes as $attrName => $values)
                                                <li>
                                                    <strong>{{ $attrName }}:</strong>
                                                    {{ $values->pluck('value')->implode(', ') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($product->description)
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="headingProductInfo">
                                <button type="button" class="accordion-button animate-underline collapsed" data-bs-toggle="collapse" data-bs-target="#productInfo" aria-expanded="false" aria-controls="productInfo">
                                    <span class="animate-target me-2">Полное описание</span>
                                </button>
                            </h3>
                            <div class="accordion-collapse collapse show" id="productInfo" aria-labelledby="headingProductInfo" data-bs-parent="#productAccordion">
                                <div class="accordion-body">{!! $product->description !!}</div>
                            </div>
                        </div>
                        @endif

                    </div>


                    <!-- Related products -->
                    <h2 class="h5 pt-5">Похожие товары</h2>
                    <div class="d-flex border rounded-5 px-2 mb-4">
                            @foreach($relatedProducts as $related)
                                <div class="w-50">
                                    <div class="card product-card h-100 bg-transparent border-0 shadow-none">
                                        <div class="position-relative z-2">
                                            <a class="d-block p-2 p-lg-3" href="{{ route('products.show', $related->slug) }}">
                                                <div class="ratio" style="--cz-aspect-ratio: calc(160 / 191 * 100%)">
                                                    @php
                                                        $mainImage = $related->image ?? ($related->gallery[0] ?? null);
                                                    @endphp
                                                    @if($mainImage)
                                                        <img src="{{ asset('storage/products/' . $mainImage) }}" alt="{{ $related->name }}">
                                                    @endif
                                                </div>
                                            </a>
                                            <div class="position-absolute w-100 start-0 bottom-0">
                                                <div class="d-flex justify-content-end px-2 px-lg-3 pb-2 pb-lg-3">
                                                    <div class="count-input count-input-collapsible collapsed justify-content-between w-100 bg-transparent border-0 rounded-2">
                                                        <button type="button" class="btn btn-icon btn-sm btn-primary" data-decrement aria-label="Decrement quantity">
                                                            <i class="ci-minus fs-sm"></i>
                                                        </button>
                                                        <input type="number" class="form-control form-control-sm bg-primary text-white w-100" value="0" min="0" readonly>
                                                        <button type="button"
                                                                class="product-card-button btn btn-icon btn-sm btn-secondary ms-auto"
                                                                data-id="{{ $related->id }}"
                                                                data-name="{{ $related->name }}"
                                                                data-price="{{ $related->price }}"
                                                                data-image="{{ $related->image }}"
                                                                data-slug="{{ $related->slug }}"
                                                                data-increment
                                                                aria-label="Increment quantity">
                                                            <span data-count-input-value></span>
                                                            <i class="ci-plus fs-sm"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0 px-1 px-md-2 px-lg-3 pb-2">
                                            <div class="h6 mb-2">{{ $related->price }} руб.</div>
                                            <h3 class="fs-sm lh-base mb-0">
                                                <a class="hover-effect-underline fw-normal" href="{{ route('products.show', $related->slug) }}">
                                                    {{ $related->name }}
                                                </a>
                                            </h3>
                                        </div>
                                        @if($related->weight ?? false)
                                            <div class="fs-xs text-body-secondary px-1 px-md-2 px-lg-3 pb-2 pb-md-3">{{ $related->weight }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                    </div>


                </div>
            </div>
        </div>
    </section>

    <!-- Quick Order Modal -->
    <div class="modal fade" id="quickOrderModal" tabindex="-1" aria-labelledby="quickOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickOrderModalLabel">Быстрая заявка</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Product Info -->
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                        <img src="" alt="" id="quick-order-image" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                        <div class="ms-3">
                            <h6 class="mb-1" id="quick-order-name"></h6>
                            <div class="text-primary fw-medium" id="quick-order-price"></div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form id="quick-order-form">
                        @csrf
                        <input type="hidden" id="quick-order-product-id" name="product_id">

                        <div class="mb-3">
                            <label for="quick-order-name-input" class="form-label">Ваше имя <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="quick-order-name-input"
                                   name="name"
                                   required
                                   placeholder="Введите ваше имя">
                        </div>

                        <div class="mb-3">
                            <label for="quick-order-phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                            <input type="tel"
                                   class="form-control"
                                   id="quick-order-phone"
                                   name="phone"
                                   required
                                   placeholder="+375 (XX) XXX-XX-XX">
                        </div>

                        <div class="mb-3">
                            <label for="quick-order-comment" class="form-label">Комментарий</label>
                            <textarea class="form-control"
                                      id="quick-order-comment"
                                      name="comment"
                                      rows="2"
                                      placeholder="Удобное время для звонка..."></textarea>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="quick-order-agree"
                                       name="agree"
                                       value="1"
                                       required>
                                <label class="form-check-label" for="quick-order-agree">
                                    Я согласен на обработку персональных данных <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="quick-order-submit">
                            Отправить заявку
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
