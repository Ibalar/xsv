<!-- Navigation bar (Page header) -->
<header class="navbar navbar-expand navbar-sticky sticky-top d-block bg-body z-fixed py-1 py-lg-0 py-xl-1 px-0" data-sticky-element>
    <div class="container justify-content-start py-2 py-lg-3">

        <!-- Offcanvas menu toggler (Hamburger) -->
        <button type="button" class="navbar-toggler d-block flex-shrink-0 me-3 me-sm-4" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar brand (Logo) -->
        @if(request()->routeIs('home'))
            <span class="navbar-brand fs-2 p-0 pe-lg-2 pe-xxl-0 me-0 me-sm-3 me-md-4 me-xxl-5">
                XSV.BY
            </span>
        @else
            <a class="navbar-brand fs-2 p-0 pe-lg-2 pe-xxl-0 me-0 me-sm-3 me-md-4 me-xxl-5"
               href="{{ route('home') }}">
                XSV.BY
            </a>
        @endif


        <!-- Categories dropdown visible on screens > 991px wide (lg breakpoint) -->
        <div id="headerCatalogDropdown" class="dropdown d-none d-lg-block w-100 me-4" style="max-width: 200px">
            <button type="button" class="btn btn-lg btn-success w-100 border-0 rounded-pill fs-5" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="ci-grid fs-lg me-2 ms-n1"></i>
                Каталог
                <i class="ci-chevron-down fs-lg me-2 ms-auto me-n1"></i>
            </button>
            <div id="headerCatalogDropdownMenu" class="dropdown-menu rounded-4 p-4 header-catalog-dropdown-menu" style="margin-left: -195px">
                <div id="headerCatalogDropdownScroll" class="header-catalog-dropdown-scroll d-flex gap-4">

                    @foreach($menuColumns as $column)
                        <div style="min-width: 200px">

                            @foreach($column as $category)

                                {{-- Заголовок --}}
                                <div class="h6">
                                    <a href="{{ route('catalog.show', $category->getFullPath()) }}">
                                        {{ $category->name }}
                                    </a>
                                </div>

                                {{-- Подкатегории --}}
                                @if($category->childrenRecursive->count())
                                    <ul class="nav flex-column gap-2 mt-n2 mb-3">

                                        @foreach($category->childrenRecursive as $child)
                                            @include('components.category-menu', ['category' => $child, 'path' => $category->slug])
                                        @endforeach

                                            <li class="pt-1">
                                                <a class="nav-link p-0"
                                                   href="{{ route('catalog.show', $category->slug) }}">
                                                    Смотреть все
                                                </a>
                                            </li>

                                    </ul>
                                @endif

                            @endforeach

                        </div>
                    @endforeach

                </div>
            </div>

        </div>

        <!-- Search bar visible on screens > 768px wide (md breakpoint) -->
        <div class="position-relative w-100 d-none d-md-block me-3 me-xl-4 search-box">

            <input
                type="search"
                name="q"
                id="search"
                class="form-control form-control-lg rounded-pill"
                placeholder="Поиск по сайту"
                autocomplete="off"
            >

            <button
                type="button"
                aria-label="Search button"
                class="btn btn-icon btn-ghost fs-lg btn-secondary border-0 position-absolute top-0 end-0 rounded-circle mt-1 me-1"
            >
                <i class="ci-search"></i>
            </button>

            <!-- Результаты -->
            <div id="search-results" class="search-results shadow"></div>

        </div>



        <!-- Button group -->
        <div class="d-flex align-items-center gap-md-1 gap-lg-2 ms-auto">

            <!-- Theme switcher (light/dark/auto) -->
            <div class="dropdown">
                <button type="button" class="theme-switcher btn btn-icon btn-outline-secondary fs-lg border-0 rounded-circle animate-scale" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Toggle theme (light)">
              <span class="theme-icon-active d-flex animate-target">
                <i class="ci-sun"></i>
              </span>
                </button>
                <ul class="dropdown-menu" style="--cz-dropdown-min-width: 9rem">
                    <li>
                        <button type="button" class="dropdown-item active" data-bs-theme-value="light" aria-pressed="true">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-sun"></i>
                  </span>
                            <span class="theme-label">Светлая</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="dark" aria-pressed="false">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-moon"></i>
                  </span>
                            <span class="theme-label">Темная</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="auto" aria-pressed="false">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-auto"></i>
                  </span>
                            <span class="theme-label">Авто</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Search toggle button visible on screens < 768px wide (md breakpoint) -->
            <button type="button" class="btn btn-icon fs-xl btn-outline-secondary border-0 rounded-circle animate-shake d-md-none" data-bs-toggle="collapse" data-bs-target="#searchBar" aria-controls="searchBar" aria-label="Toggle search bar">
                <i class="ci-search animate-target"></i>
            </button>

            <!-- Cart button -->
            <button type="button" class="btn btn-icon fs-xl btn-outline-secondary position-relative border-0 rounded-circle animate-scale" data-bs-toggle="offcanvas" data-bs-target="#shoppingCart" aria-controls="shoppingCart" aria-label="Shopping cart" title="Заявка на заказ">
                <span id="cart-badge" class="position-absolute top-0 start-100 badge fs-xs text-bg-primary rounded-pill ms-n3 z-2" style="--cz-badge-padding-y: .25em; --cz-badge-padding-x: .42em; display: none;">0</span>
                <i class="ci-file-text animate-target"></i>
            </button>
        </div>
    </div>

    <!-- Search collapse available on screens < 768px wide (md breakpoint) -->
    <div class="collapse d-md-none" id="searchBar">
        <div class="container pt-2 pb-3">
            <div class="position-relative">
                <i class="ci-search position-absolute top-50 translate-middle-y d-flex fs-lg ms-3"></i>
                <input
                    type="search"
                    name="q"
                    id="search-mobile"
                    class="form-control form-icon-start rounded-pill"
                    placeholder="Поиск по сайту"
                    data-autofocus="collapse"
                    autocomplete="off"
                >

                <!-- Результаты поиска для мобильных -->
                <div id="search-results-mobile" class="search-results shadow"></div>
            </div>
        </div>
    </div>
</header>

<section class="header-categories-bar border-top">
    <div class="container py-lg-1">
        <div class="overflow-auto" data-simplebar>
            <div class="nav flex-nowrap justify-content-between gap-4 py-2">
                @foreach($headerCategories as $category)
                    @php
                        $categoryImage = $category->image
                            ? asset('storage/' . ltrim($category->image, '/'))
                            : null;
                    @endphp

                    <a class="nav-link align-items-center animate-underline gap-2 p-0"
                       href="{{ route('catalog.show', $category->getFullPath()) }}">
                        <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle overflow-hidden flex-shrink-0"
                              style="width: 40px; height: 40px">
                            @if($categoryImage)
                                <img src="{{ $categoryImage }}"
                                     width="30"
                                     height="30"
                                     alt="{{ $category->name }}"
                                     loading="lazy"
                                     class="object-fit-cover">
                            @else
                                <i class="ci-grid text-primary fs-xl"></i>
                            @endif
                        </span>
                        <span class="d-block animate-target fw-semibold text-nowrap ms-1">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropdown = document.getElementById('headerCatalogDropdown');
            const dropdownMenu = document.getElementById('headerCatalogDropdownMenu');
            const dropdownScroll = document.getElementById('headerCatalogDropdownScroll');

            if (!dropdown || !dropdownMenu || !dropdownScroll) {
                return;
            }

            const isDesktopViewport = () => window.innerWidth >= 992;
            const getOverlayTop = () => {
                const selectors = ['.top-bar', 'header[data-sticky-element]', '.header-categories-bar'];

                return selectors.reduce((maxBottom, selector) => {
                    const element = document.querySelector(selector);

                    if (!element) {
                        return maxBottom;
                    }

                    const rect = element.getBoundingClientRect();
                    return Math.max(maxBottom, rect.bottom);
                }, 0);
            };

            const lockPageScroll = () => {
                if (!isDesktopViewport() || document.body.classList.contains('desktop-catalog-lock')) {
                    return;
                }

                const scrollY = window.scrollY || window.pageYOffset || 0;
                document.body.style.top = `-${scrollY}px`;
                document.body.style.setProperty('--catalog-overlay-top', `${Math.max(0, getOverlayTop())}px`);
                document.body.dataset.desktopCatalogScrollY = String(scrollY);
                document.body.classList.add('desktop-catalog-lock');
                document.body.classList.add('catalog-overlay-active');
            };

            const unlockPageScroll = () => {
                if (!document.body.classList.contains('desktop-catalog-lock')) {
                    return;
                }

                const scrollY = parseInt(document.body.dataset.desktopCatalogScrollY || '0', 10);
                document.body.classList.remove('desktop-catalog-lock');
                document.body.classList.remove('catalog-overlay-active');
                document.body.style.top = '';
                document.body.style.removeProperty('--catalog-overlay-top');
                delete document.body.dataset.desktopCatalogScrollY;
                window.scrollTo(0, scrollY);
            };

            const syncDropdownHeight = () => {
                if (!isDesktopViewport() || !dropdownMenu.classList.contains('show')) {
                    dropdownScroll.style.removeProperty('--desktop-catalog-max-height');
                    return;
                }

                const rect = dropdownScroll.getBoundingClientRect();
                const maxHeight = Math.max(220, window.innerHeight - rect.top - 16);
                dropdownScroll.style.setProperty('--desktop-catalog-max-height', `${maxHeight}px`);
            };

            dropdown.addEventListener('shown.bs.dropdown', () => {
                lockPageScroll();
                syncDropdownHeight();
            });

            dropdown.addEventListener('hidden.bs.dropdown', () => {
                dropdownScroll.style.removeProperty('--desktop-catalog-max-height');
                unlockPageScroll();
            });

            window.addEventListener('resize', () => {
                if (!dropdownMenu.classList.contains('show')) {
                    return;
                }

                if (!isDesktopViewport()) {
                    dropdownScroll.style.removeProperty('--desktop-catalog-max-height');
                    unlockPageScroll();
                    return;
                }

                document.body.style.setProperty('--catalog-overlay-top', `${Math.max(0, getOverlayTop())}px`);
                syncDropdownHeight();
            });
        });
    </script>
@endpush
