@extends('layouts.main')

@section('title', 'XSV.BY - Главная')

@section('meta_description', ' ')
@section('meta_keywords', ' ')

@section('content')

    <!-- Hero banner with hotspots -->
    <section class="container pt-2 pt-lg-3">
        <div class="position-relative rounded-5 overflow-hidden">
            <div class="row align-items-center position-relative z-2">
                <div class="col-md-5 col-lg-4 offset-lg-1 text-center text-md-start mb-4 mb-md-0">
                    <div class="pt-5 pt-md-0 px-4 px-sm-5 pe-md-0 ps-md-5 ps-lg-0">
                        <p class="fs-xl mb-lg-4">Большой ассортимент. Доставка по всей Беларуси. Посадка.</p>
                        <h1 class="display-6 text-uppercase mb-4 mb-lg-5">Питомник растений <span class="text-nowrap">"Сказочный сад"</span></h1>
                        <a class="btn btn-lg btn-dark" href="/">
                            В Каталог
                            <i class="ci-arrow-up-right fs-lg ms-2 me-n1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-7 mt-md-n5">
                    <div class="d-flex justify-content-center justify-content-md-end">
                        <div class="position-relative w-100 rtl-flip" style="max-width: 636px">
                            <div class="ratio" style="--cz-aspect-ratio: calc(648 / 636 * 100%)">
                                <img src="{{ asset('assets/img/hero/image-hero.webp') }}" alt="XSV.BY">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span class="position-absolute top-0 start-0 w-100 h-100 d-none-dark rtl-flip" style="background: linear-gradient(102deg, #dad4ec 0%, #f3e7e9 80.43%)"></span>
            <span class="position-absolute top-0 start-0 w-100 h-100 d-none d-block-dark rtl-flip" style="background: linear-gradient(102deg, #2a2735 0%, #312a2b 80.43%)"></span>
        </div>
    </section>

    <!-- Features -->
    <section class="container pt-5 mt-1 mt-sm-3 mt-lg-4">
        <div class="row row-cols-2 row-cols-md-4 g-4">

            <!-- Item -->
            <div class="col">
                <div class="d-flex flex-column flex-xxl-row align-items-center">
                    <div class="d-flex text-dark-emphasis bg-body-tertiary rounded-circle p-4 mb-3 mb-xxl-0 ">
                        <i class="ci-delivery fs-2 m-xxl-1"></i>
                    </div>
                    <div class="text-center text-xxl-start ps-xxl-3">
                        <h3 class="h6 mb-1">Доставка</h3>
                        <p class="fs-sm mb-0">по всей Беларуси</p>
                    </div>
                </div>
            </div>

            <!-- Item -->
            <div class="col">
                <div class="d-flex flex-column flex-xxl-row align-items-center">
                    <div class="d-flex text-dark-emphasis bg-body-tertiary rounded-circle p-4 mb-3 mb-xxl-0">
                        <i class="ci-gift fs-2 m-xxl-1"></i>
                    </div>
                    <div class="text-center text-xxl-start ps-xxl-3">
                        <h3 class="h6 mb-1">ПОДАРКИ</h3>
                        <p class="fs-sm mb-0">и выгодные акции</p>
                    </div>
                </div>
            </div>

            <!-- Item -->
            <div class="col">
                <div class="d-flex flex-column flex-xxl-row align-items-center">
                    <div class="d-flex text-dark-emphasis bg-body-tertiary rounded-circle p-4 mb-3 mb-xxl-0">
                        <i class="ci-check-shield fs-2 m-xxl-1"></i>
                    </div>
                    <div class="text-center text-xxl-start ps-xxl-3">
                        <h3 class="h6 mb-1">Гарантия качества</h3>
                        <p class="fs-sm mb-0">на все товары</p>
                    </div>
                </div>
            </div>

            <!-- Item -->
            <div class="col">
                <div class="d-flex flex-column flex-xxl-row align-items-center">
                    <div class="d-flex text-dark-emphasis bg-body-tertiary rounded-circle p-4 mb-3 mb-xxl-0">
                        <i class="ci-sunrise fs-2 m-xxl-1"></i>
                    </div>
                    <div class="text-center text-xxl-start ps-xxl-3">
                        <h3 class="h6 mb-1">Высадка в почву</h3>
                        <p class="fs-sm mb-0">с гарантией приживаемости</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Special offers (Carousel) -->
    @if($featuredProducts->count())
    <section class="container pt-5 mt-2 mt-sm-3 mt-lg-4">

        <!-- Heading + Countdown -->
        <div class="d-flex align-items-start align-items-md-center justify-content-between border-bottom pb-3 pb-md-4">
            <div class="d-md-flex align-items-center">
                <h2 class="h3 pe-3 me-3 mb-md-0">Хиты продаж</h2>

            </div>
            <div class="nav ms-3">
                <a class="nav-link animate-underline px-0 py-2" href="/">
                    <span class="animate-target text-nowrap">Смотреть все</span>
                    <i class="ci-chevron-right fs-base ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Product carousel -->
        <div class="position-relative mx-md-1">

            <!-- Навигация слайдера -->
            <button type="button" class="offers-prev btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-start position-absolute top-40 start-0 z-2 translate-middle-y ms-n1 d-none d-sm-inline-flex" aria-label="Предыдущий">
                <i class="ci-chevron-left fs-lg animate-target"></i>
            </button>
            <button type="button" class="offers-next btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-end position-absolute top-40 end-0 z-2 translate-middle-y me-n1 d-none d-sm-inline-flex" aria-label="Следующий">
                <i class="ci-chevron-right fs-lg animate-target"></i>
            </button>

            <!-- Swiper -->
            <div class="swiper py-4 px-sm-3" data-swiper='{
                "slidesPerView": 2,
                "spaceBetween": 24,
                "loop": {{ $featuredProducts->count() >= 4 ? 'true' : 'false' }},
                "navigation": {
                  "prevEl": ".offers-prev",
                  "nextEl": ".offers-next"
                },
                "breakpoints": {
                  "768": {"slidesPerView": 3},
                  "992": {"slidesPerView": 4}
                }
            }'>
                <div class="swiper-wrapper">

                    @foreach($featuredProducts as $product)
                        <div class="swiper-slide">
                            <div class="product-card animate-underline hover-effect-opacity bg-body rounded">
                                <div class="position-relative">
                                    <!-- Wishlist / Compare кнопки -->
                                    <div class="position-absolute top-0 end-0 z-2 hover-effect-target opacity-0 mt-3 me-3">
                                        <div class="d-flex flex-column gap-2">
                                            <button type="button" class="btn btn-icon btn-secondary animate-pulse d-none d-lg-inline-flex">
                                                <i class="ci-heart fs-base animate-target"></i>
                                            </button>
                                            <button type="button" class="btn btn-icon btn-secondary animate-rotate d-none d-lg-inline-flex">
                                                <i class="ci-refresh-cw fs-base animate-target"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Mobile dropdown -->
                                    <div class="dropdown d-lg-none position-absolute top-0 end-0 z-2 mt-2 me-2">
                                        <button type="button" class="btn btn-icon btn-sm btn-secondary bg-body" data-bs-toggle="dropdown">
                                            <i class="ci-more-vertical fs-lg"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end fs-xs p-2">
                                            <li><a class="dropdown-item" href="#"><i class="ci-heart fs-sm ms-n1 me-2"></i> Wishlist</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="ci-refresh-cw fs-sm ms-n1 me-2"></i> Compare</a></li>
                                        </ul>
                                    </div>

                                    <!-- Изображение товара -->
                                    <a class="d-block rounded-top overflow-hidden p-3 p-sm-4"
                                       href="{{ route('products.show', $product->slug ?? '#') }}">
                                        @if($product->is_featured)
                                            <span class="badge bg-info position-absolute top-0 start-0 mt-2 ms-2 mt-lg-3 ms-lg-3">Хит</span>
                                        @endif
                                        <div class="ratio" style="--cz-aspect-ratio: calc(240 / 258 * 100%)">
                                            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                                        </div>
                                    </a>
                                </div>

                                <!-- Название и цена -->
                                <div class="w-100 min-w-0 px-1 pb-2 px-sm-3 pb-sm-3">
                                    <h3 class="pb-1 mb-2">
                                        <a class="d-block fs-sm fw-medium text-truncate" href="{{ route('products.show', $product->slug ?? '#') }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    <div class="d-flex align-items-center justify-content-between pb-2 mb-1">
                                        <div class="h5 lh-1 mb-0">
                                            {{ number_format($product->price, 2) }} BYN
                                            @if($product->old_price)
                                                <del class="text-body-tertiary fs-sm fw-normal">{{ number_format($product->old_price, 2) }} BYN</del>
                                            @endif
                                        </div>
                                        <button type="button" class="product-card-button btn btn-icon btn-secondary animate-slide-end ms-2" title="Добавить в заявку">
                                            <i class="ci-chat fs-base animate-target"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- Мобильная навигация -->
            <div class="d-flex justify-content-center gap-2 mt-n2 mb-3 pb-1 d-sm-none">
                <button type="button" class="offers-prev btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-start me-1" aria-label="Назад">
                    <i class="ci-chevron-left fs-lg animate-target"></i>
                </button>
                <button type="button" class="offers-next btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-end" aria-label="Вперед">
                    <i class="ci-chevron-right fs-lg animate-target"></i>
                </button>
            </div>
        </div>
    </section>
    @endif

    <!-- Special offers (Carousel) -->
    @if($newProducts->count())
    <section class="container pt-5 mt-2 mt-sm-3 mt-lg-4">

        <!-- Heading + Countdown -->
        <div class="d-flex align-items-start align-items-md-center justify-content-between border-bottom pb-3 pb-md-4">
            <div class="d-md-flex align-items-center">
                <h2 class="h3 pe-3 me-3 mb-md-0">Последние поступления</h2>

            </div>
            <div class="nav ms-3">
                <a class="nav-link animate-underline px-0 py-2" href="/">
                    <span class="animate-target text-nowrap">Смотреть все</span>
                    <i class="ci-chevron-right fs-base ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Product carousel -->
        <div class="position-relative mx-md-1">

            <!-- Навигация слайдера -->
            <button type="button" class="offers-prev-two btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-start position-absolute top-40 start-0 z-2 translate-middle-y ms-n1 d-none d-sm-inline-flex" aria-label="Предыдущий">
                <i class="ci-chevron-left fs-lg animate-target"></i>
            </button>
            <button type="button" class="offers-next-two btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-end position-absolute top-40 end-0 z-2 translate-middle-y me-n1 d-none d-sm-inline-flex" aria-label="Следующий">
                <i class="ci-chevron-right fs-lg animate-target"></i>
            </button>

            <!-- Swiper -->
            <div class="swiper py-4 px-sm-3" data-swiper='{
                "slidesPerView": 2,
                "spaceBetween": 24,
                "loop": {{ $newProducts->count() >= 4 ? 'true' : 'false' }},
                "navigation": {
                  "prevEl": ".offers-prev-two",
                  "nextEl": ".offers-next-two"
                },
                "breakpoints": {
                  "768": {"slidesPerView": 3},
                  "992": {"slidesPerView": 4}
                }
            }'>
                <div class="swiper-wrapper">

                    @foreach($newProducts as $product)
                        <div class="swiper-slide">
                            <div class="product-card animate-underline hover-effect-opacity bg-body rounded">
                                <div class="position-relative">
                                    <!-- Wishlist / Compare кнопки -->
                                    <div class="position-absolute top-0 end-0 z-2 hover-effect-target opacity-0 mt-3 me-3">
                                        <div class="d-flex flex-column gap-2">
                                            <button type="button" class="btn btn-icon btn-secondary animate-pulse d-none d-lg-inline-flex">
                                                <i class="ci-heart fs-base animate-target"></i>
                                            </button>
                                            <button type="button" class="btn btn-icon btn-secondary animate-rotate d-none d-lg-inline-flex">
                                                <i class="ci-refresh-cw fs-base animate-target"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Mobile dropdown -->
                                    <div class="dropdown d-lg-none position-absolute top-0 end-0 z-2 mt-2 me-2">
                                        <button type="button" class="btn btn-icon btn-sm btn-secondary bg-body" data-bs-toggle="dropdown">
                                            <i class="ci-more-vertical fs-lg"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end fs-xs p-2">
                                            <li><a class="dropdown-item" href="#"><i class="ci-heart fs-sm ms-n1 me-2"></i> Wishlist</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="ci-refresh-cw fs-sm ms-n1 me-2"></i> Compare</a></li>
                                        </ul>
                                    </div>

                                    <!-- Изображение товара -->
                                    <a class="d-block rounded-top overflow-hidden p-3 p-sm-4"
                                       href="{{ route('products.show', $product->slug ?? '#') }}">
                                        @if($product->is_new)
                                            <span class="badge bg-danger position-absolute top-0 start-0 mt-2 ms-2 mt-lg-3 ms-lg-3">Новинка</span>
                                        @endif
                                        <div class="ratio" style="--cz-aspect-ratio: calc(240 / 258 * 100%)">
                                            <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}">
                                        </div>
                                    </a>
                                </div>

                                <!-- Название и цена -->
                                <div class="w-100 min-w-0 px-1 pb-2 px-sm-3 pb-sm-3">
                                    <h3 class="pb-1 mb-2">
                                        <a class="d-block fs-sm fw-medium text-truncate" href="{{ route('products.show', $product->slug ?? '#') }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    <div class="d-flex align-items-center justify-content-between pb-2 mb-1">
                                        <div class="h5 lh-1 mb-0">
                                            {{ number_format($product->price, 2) }} BYN
                                            @if($product->old_price)
                                                <del class="text-body-tertiary fs-sm fw-normal">{{ number_format($product->old_price, 2) }} BYN</del>
                                            @endif
                                        </div>
                                        <button type="button" class="product-card-button btn btn-icon btn-secondary animate-slide-end ms-2" title="Добавить в заявку">
                                            <i class="ci-chat fs-base animate-target"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- Мобильная навигация -->
            <div class="d-flex justify-content-center gap-2 mt-n2 mb-3 pb-1 d-sm-none">
                <button type="button" class="offers-prev btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-start me-1" aria-label="Назад">
                    <i class="ci-chevron-left fs-lg animate-target"></i>
                </button>
                <button type="button" class="offers-next btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-end" aria-label="Вперед">
                    <i class="ci-chevron-right fs-lg animate-target"></i>
                </button>
            </div>
        </div>
    </section>
    @endif

    <!-- Trending products (Grid) -->
    @if($inStockProducts->count())
    <section class="container pt-5 mt-2 mt-sm-3 mt-lg-4">

        <!-- Heading -->
        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 pb-md-4">
            <h2 class="h3 mb-0">Растения в наличии</h2>
            <div class="nav ms-3">
                <a class="nav-link animate-underline px-0 py-2" href="/">
                    <span class="animate-target">Смотреть все</span>
                    <i class="ci-chevron-right fs-base ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Product grid -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 pt-4">

            @foreach($inStockProducts as $product)
                @php
                    $price = $product->price;
                    $oldPrice = $product->old_price;
                @endphp

                <div class="col">
                    <div class="product-card animate-underline hover-effect-opacity bg-body rounded">

                        <!-- IMAGE -->
                        <div class="position-relative">

                            <!-- Wishlist / Compare кнопки -->
                            <div class="position-absolute top-0 end-0 z-2 hover-effect-target opacity-0 mt-3 me-3">
                                <div class="d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-icon btn-secondary animate-pulse d-none d-lg-inline-flex">
                                        <i class="ci-heart fs-base animate-target"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-secondary animate-rotate d-none d-lg-inline-flex">
                                        <i class="ci-refresh-cw fs-base animate-target"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Mobile dropdown -->
                            <div class="dropdown d-lg-none position-absolute top-0 end-0 z-2 mt-2 me-2">
                                <button type="button" class="btn btn-icon btn-sm btn-secondary bg-body" data-bs-toggle="dropdown">
                                    <i class="ci-more-vertical fs-lg"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end fs-xs p-2">
                                    <li><a class="dropdown-item" href="#"><i class="ci-heart fs-sm ms-n1 me-2"></i> Wishlist</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ci-refresh-cw fs-sm ms-n1 me-2"></i> Compare</a></li>
                                </ul>
                            </div>

                            <a class="d-block rounded-top overflow-hidden p-3 p-sm-4"
                               href="{{ route('products.show', $product->slug ?? '#') }}">

                                {{-- бейдж --}}
                                @if($product->old_price)
                                    <span class="badge bg-danger position-absolute top-0 start-0 mt-2 ms-2">
                                    -{{ round(100 - ($price / $product->old_price * 100)) }}%
                                </span>
                                @elseif($product->is_new)
                                    <span class="badge bg-info position-absolute top-0 start-0 mt-2 ms-2">
                                    Новинка
                                </span>
                                @endif

                                <div class="ratio" style="--cz-aspect-ratio: calc(240 / 258 * 100%)">
                                    <img src="{{ asset('storage/products/' . $product->image) }}"
                                         alt="{{ $product->name }}">
                                </div>
                            </a>
                        </div>

                        <!-- CONTENT -->
                        <div class="w-100 min-w-0 px-1 pb-2 px-sm-3 pb-sm-3">

                            <h3 class="pb-1 mb-2">
                                <a class="d-block fs-sm fw-medium text-truncate"
                                   href="{{ route('products.show', $product->slug ?? '#') }}">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <div class="d-flex align-items-center justify-content-between">

                                <div class="h5 lh-1 mb-0">
                                    {{ number_format($price, 2) }} BYN

                                    @if($product->old_price)
                                        <del class="text-body-tertiary fs-sm fw-normal">
                                            {{ number_format($product->old_price, 2) }} BYN
                                        </del>
                                    @endif
                                </div>

                                <button type="button"
                                        class="product-card-button btn btn-icon btn-secondary">
                                    <i class="ci-chat fs-base"></i>
                                </button>

                            </div>
                        </div>

                    </div>
                </div>
            @endforeach


        </div>
    </section>
    @endif

    <div class="container py-5 mb-2 mt-n2 mt-sm-1 my-md-3 my-lg-4 mb-xl-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="h2 pb-2 pb-sm-3 pb-lg-4">XSV.BY (Питомник растений "Сказочный сад")</h1>
                <hr class="mt-0">

                <p>Мы производим и продаем: многолетние растения, хвойные деревья и кустарники, а также лиственные деревья в контейнерах и в земле. Мы прилагаем все усилия, чтобы деревья и растения, которые мы продаем, были здоровыми и красивыми в течение всего года.</p>
                <h2 class="h4 pt-3 pt-lg-4">Сотрудничать с нами легко и выгодно:</h2>

                <ul class="gap-3">
                    <li>Мы находимся <strong>рядом с г. Минском (д. Зеленый сад),</strong> поэтому можем своевременно доставлять наш товар в любые уголки нашей республики и столицы.</li>
                    <li>Наши специалисты подробно <strong>ответят на все интересующие вас вопросы,</strong> а также организуют быструю и своевременную доставку.</li>
                    <li>На сайте предусмотрена <strong>удобная система поиска,</strong> благодаря которой процесс выбора посадочных материалов не станет утомительным.</li>
                    <li>Мы можем предложить Вам <strong>услуги по разгрузке</strong> нашего товара.</li>
                    <li>В нашем питомнике Вы всегда найдете <strong>акционные товары</strong> по привлекательной цене.</li>
                    <li>Бесплатная доставка по Минску при заказе <strong>свыше 1000 рублей.</strong></li>
                    <li>Мы предлагаем <strong>услуги по высадке растений в почву</strong> с гарантией приживаемости.</li>
                </ul>
            </div>
        </div>
    </div>

@endsection
