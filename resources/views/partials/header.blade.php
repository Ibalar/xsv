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
        <div class="dropdown d-none d-lg-block w-100 me-4" style="max-width: 200px">
            <button type="button" class="btn btn-lg btn-secondary w-100 border-0 rounded-pill" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="ci-grid fs-lg me-2 ms-n1"></i>
                Каталог
                <i class="ci-chevron-down fs-lg me-2 ms-auto me-n1"></i>
            </button>
            <div class="dropdown-menu rounded-4 p-4" style="margin-left: -195px">
                <div class="d-flex gap-4">

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
                                            @include('components.category-menu', ['category' => $child])
                                        @endforeach

                                            <li class="pt-1">
                                                <a class="nav-link p-0"
                                                   href="{{ route('catalog.show', $category->getFullPath()) }}">
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
        <div class="position-relative w-100 d-none d-md-block me-3 me-xl-4">
            <input type="search" class="form-control form-control-lg rounded-pill" placeholder="Поиск по сайту" aria-label="Search">
            <button type="button" class="btn btn-icon btn-ghost fs-lg btn-secondary text-bo border-0 position-absolute top-0 end-0 rounded-circle mt-1 me-1" aria-label="Search button">
                <i class="ci-search"></i>
            </button>
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
                            <span class="theme-label">Light</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="dark" aria-pressed="false">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-moon"></i>
                  </span>
                            <span class="theme-label">Dark</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="auto" aria-pressed="false">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-auto"></i>
                  </span>
                            <span class="theme-label">Auto</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Search toggle button visible on screens < 768px wide (md breakpoint) -->
            <button type="button" class="btn btn-icon fs-xl btn-outline-secondary border-0 rounded-circle animate-shake d-md-none" data-bs-toggle="collapse" data-bs-target="#searchBar" aria-controls="searchBar" aria-label="Toggle search bar">
                <i class="ci-search animate-target"></i>
            </button>

            <!-- Delivery options button visible on screens < 1200px wide (xl breakpoint) -->
            <button type="button" class="btn btn-icon fs-lg btn-outline-secondary border-0 rounded-circle animate-scale d-xl-none" data-bs-toggle="offcanvas" data-bs-target="#deliveryOptions" aria-controls="deliveryOptions" aria-label="Toggle delivery options offcanvas">
                <i class="ci-map-pin animate-target"></i>
            </button>

            <!-- Account button visible on screens > 768px wide (md breakpoint) -->
            <a class="btn btn-icon fs-lg btn-outline-secondary border-0 rounded-circle animate-shake d-none d-md-inline-flex" href="account-signin.html">
                <i class="ci-user animate-target"></i>
                <span class="visually-hidden">Account</span>
            </a>

            <!-- Cart button -->
            <button type="button" class="btn btn-icon fs-xl btn-outline-secondary position-relative border-0 rounded-circle animate-scale" data-bs-toggle="offcanvas" data-bs-target="#shoppingCart" aria-controls="shoppingCart" aria-label="Shopping cart" title="Заявка на заказ">
                <span class="position-absolute top-0 start-100 badge fs-xs text-bg-primary rounded-pill ms-n3 z-2" style="--cz-badge-padding-y: .25em; --cz-badge-padding-x: .42em">0</span>
                <i class="ci-file-text animate-target"></i>
            </button>
        </div>
    </div>

    <!-- Search collapse available on screens < 768px wide (md breakpoint) -->
    <div class="collapse d-md-none" id="searchBar">
        <div class="container pt-2 pb-3">
            <div class="position-relative">
                <i class="ci-search position-absolute top-50 translate-middle-y d-flex fs-lg ms-3"></i>
                <input type="search" class="form-control form-icon-start rounded-pill" placeholder="Search for products" data-autofocus="collapse">
            </div>
        </div>
    </div>
</header>
