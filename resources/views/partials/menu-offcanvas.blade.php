
<!-- Site menu offcanvas -->
<nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
    <div class="offcanvas-header py-3">
        <h5 class="offcanvas-title" id="navbarNavLabel">Основные разделы</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0 pb-3">

        <!-- Navbar nav -->
        <div class="accordion" id="navigation">

            <!-- Categories collapse visible on screens < 992px wide (lg breakpoint) -->
            <div class="accordion-item border-0 d-lg-none">
                <div class="accordion-header" id="headingCategories">
                    <button type="button"
                            class="accordion-button collapsed py-2"
                            data-bs-toggle="collapse"
                            data-bs-target="#categoriesMenu">
                        <i class="ci-grid fs-lg me-2"></i>
                        <span class="py-1">Каталог</span>
                    </button>
                </div>

                <div id="categoriesMenu"
                     class="accordion-collapse collapse"
                     data-bs-parent="#navigation">

                    <div class="accordion-body pb-3">

                        <div class="d-flex flex-column gap-4">

                            @foreach($headerCategories as $category)
                                <div>

                                    {{-- Заголовок категории --}}
                                    <div class="h6">
                                        <a href="{{ route('catalog.show', $category->getFullPath()) }}">
                                            {{ $category->name }}
                                        </a>
                                    </div>

                                    {{-- Подкатегории --}}
                                    @if($category->childrenRecursive->count())
                                        <ul class="nav flex-column gap-2 mt-n2">
                                            @foreach($category->childrenRecursive as $child)
                                                @include('components.mobile-category', [
                                                    'category' => $child
                                                ])
                                            @endforeach

                                        </ul>
                                    @endif

                                </div>
                            @endforeach

                        </div>

                    </div>
                </div>
            </div>

            <!-- Rest of the menu -->
            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingHome">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#home" aria-expanded="false" aria-controls="home">
                        <span class="d-block animate-target py-1">Home</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="home" aria-labelledby="headingHome" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            <li class="hover-effect-opacity px-2 mx-n2">
                                <a class="dropdown-item d-block mb-0" href="home-electronics.html">
                                    <span class="fw-medium">Electronics Store</span>
                                    <span class="d-block fs-xs text-body-secondary">Megamenu + Hero slider</span>
                                </a>
                            </li>
                            <li class="hover-effect-opacity px-2 mx-n2">
                                <a class="dropdown-item d-block mb-0" href="home-fashion-v1.html">
                                    <span class="fw-medium">Fashion Store v.1</span>
                                    <span class="d-block fs-xs text-body-secondary">Hero promo slider</span>
                                </a>
                            </li>
                            <li class="hover-effect-opacity px-2 mx-n2">
                                <a class="dropdown-item d-block mb-0" href="home-fashion-v2.html">
                                    <span class="fw-medium">Fashion Store v.2</span>
                                    <span class="d-block fs-xs text-body-secondary">Hero banner with hotspots</span>
                                </a>
                            </li>
                            <li class="hover-effect-opacity px-2 mx-n2">
                                <a class="dropdown-item d-block mb-0" href="home-furniture.html">
                                    <span class="fw-medium">Furniture Store</span>
                                    <span class="d-block fs-xs text-body-secondary">Fancy product carousel</span>
                                </a>
                            </li>
                            <li class="hover-effect-opacity px-2 mx-n2">
                                <a class="dropdown-item d-block mb-0" href="home-grocery.html">
                                    <span class="fw-medium">Grocery Store</span>
                                    <span class="d-block fs-xs text-body-secondary">Hero slider + Category cards</span>
                                </a>
                            </li>
                            <li class="hover-effect-opacity px-2 mx-n2">
                                <a class="dropdown-item d-block mb-0" href="home-marketplace.html">
                                    <span class="fw-medium">Marketplace</span>
                                    <span class="d-block fs-xs text-body-secondary">Multi-vendor, digital goods</span>
                                </a>
                            </li>
                            <li class="hover-effect-opacity px-2 mx-n2">
                                <a class="dropdown-item d-block mb-0" href="home-single-store.html">
                                    <span class="fw-medium">Single Product Store</span>
                                    <span class="d-block fs-xs text-body-secondary">Single product / mono brand</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingShop">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#shop" aria-expanded="false" aria-controls="shop">
                        <span class="d-block animate-target py-1">Shop</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="shop" aria-labelledby="headingShop" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <div class="dropdown-menu show position-static shadow-none p-4">
                            <div class="d-flex flex-column gap-4">
                                <div style="min-width: 190px">
                                    <div class="h6 mb-2">Electronics Store</div>
                                    <ul class="nav flex-column gap-2 mt-0">
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-categories-electronics.html">Categories Page</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-catalog-electronics.html">Catalog with Side Filters</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-product-general-electronics.html">Product General Info</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-product-details-electronics.html">Product Details</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-product-reviews-electronics.html">Product Reviews</a>
                                        </li>
                                    </ul>
                                    <div class="h6 pt-4 mb-2">Fashion Store</div>
                                    <ul class="nav flex-column gap-2 mt-0">
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-catalog-fashion.html">Catalog with Side Filters</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-product-fashion.html">Product Page</a>
                                        </li>
                                    </ul>
                                    <div class="h6 pt-4 mb-2">Furniture Store</div>
                                    <ul class="nav flex-column gap-2 mt-0">
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-catalog-furniture.html">Catalog with Top Filters</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-product-furniture.html">Product Page</a>
                                        </li>
                                    </ul>
                                </div>
                                <div style="min-width: 190px">
                                    <div class="h6 mb-2">Grocery Store</div>
                                    <ul class="nav flex-column gap-2 mt-0">
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-catalog-grocery.html">Catalog with Side Filters</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-product-grocery.html">Product Page</a>
                                        </li>
                                    </ul>
                                    <div class="h6 pt-4 mb-2">Marketplace</div>
                                    <ul class="nav flex-column gap-2 mt-0">
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-catalog-marketplace.html">Catalog with Top Filters</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="shop-product-marketplace.html">Digital Product Page</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-marketplace.html">Cart / Checkout</a>
                                        </li>
                                    </ul>
                                    <div class="h6 pt-4 mb-2">Checkout v.1</div>
                                    <ul class="nav flex-column gap-2 mt-0">
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v1-cart.html">Shopping Cart</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v1-delivery-1.html">Delivery Info (Step 1)</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v1-delivery-2.html">Delivery Info (Step 2)</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v1-shipping.html">Shipping Address</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v1-payment.html">Payment</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v1-thankyou.html">Thank You Page</a>
                                        </li>
                                    </ul>
                                </div>
                                <div style="min-width: 190px">
                                    <div class="h6 mb-2">Checkout v.2</div>
                                    <ul class="nav flex-column gap-2 mt-0">
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v2-cart.html">Shopping Cart</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v2-delivery.html">Delivery Info</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v2-pickup.html">Pickup from Store</a>
                                        </li>
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="checkout-v2-thankyou.html">Thank You Page</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingAccount">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#account" aria-expanded="false" aria-controls="account">
                        <span class="d-block animate-target py-1">Account</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="account" aria-labelledby="headingAccount" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            <li class="dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">Auth Pages</a>
                                <ul class="dropdown-menu position-static transform-none shadow-none">
                                    <li><a class="dropdown-item" href="account-signin.html">Sign In</a></li>
                                    <li><a class="dropdown-item" href="account-signup.html">Sign Up</a></li>
                                    <li><a class="dropdown-item" href="account-password-recovery.html">Password Recovery</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">Shop User</a>
                                <ul class="dropdown-menu position-static transform-none shadow-none">
                                    <li><a class="dropdown-item" href="account-orders.html">Orders History</a></li>
                                    <li><a class="dropdown-item" href="account-wishlist.html">Wishlist</a></li>
                                    <li><a class="dropdown-item" href="account-payment.html">Payment Methods</a></li>
                                    <li><a class="dropdown-item" href="account-reviews.html">My Reviews</a></li>
                                    <li><a class="dropdown-item" href="account-info.html">Personal Info</a></li>
                                    <li><a class="dropdown-item" href="account-addresses.html">Addresses</a></li>
                                    <li><a class="dropdown-item" href="account-notifications.html">Notifications</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">Marketplace User</a>
                                <ul class="dropdown-menu position-static transform-none shadow-none">
                                    <li><a class="dropdown-item" href="account-marketplace-dashboard.html">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="account-marketplace-products.html">Products</a></li>
                                    <li><a class="dropdown-item" href="account-marketplace-sales.html">Sales</a></li>
                                    <li><a class="dropdown-item" href="account-marketplace-payouts.html">Payouts</a></li>
                                    <li><a class="dropdown-item" href="account-marketplace-purchases.html">Purchases</a></li>
                                    <li><a class="dropdown-item" href="account-marketplace-favorites.html">Favorites</a></li>
                                    <li><a class="dropdown-item" href="account-marketplace-settings.html">Settings</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingPages">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages" aria-expanded="false" aria-controls="pages">
                        <span class="d-block animate-target py-1">Pages</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="pages" aria-labelledby="headingPages" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            <li class="dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">About</a>
                                <ul class="dropdown-menu position-static transform-none shadow-none">
                                    <li><a class="dropdown-item" href="about-v1.html">About v.1</a></li>
                                    <li><a class="dropdown-item" href="about-v2.html">About v.2</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">Blog</a>
                                <ul class="dropdown-menu position-static transform-none shadow-none">
                                    <li><a class="dropdown-item" href="blog-grid-v1.html">Grid View v.1</a></li>
                                    <li><a class="dropdown-item" href="blog-grid-v2.html">Grid View v.2</a></li>
                                    <li><a class="dropdown-item" href="blog-list.html">List View</a></li>
                                    <li><a class="dropdown-item" href="blog-single-v1.html">Single Post v.1</a></li>
                                    <li><a class="dropdown-item" href="blog-single-v2.html">Single Post v.2</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">Contact</a>
                                <ul class="dropdown-menu position-static transform-none shadow-none">
                                    <li><a class="dropdown-item" href="contact-v1.html">Contact v.1</a></li>
                                    <li><a class="dropdown-item" href="contact-v2.html">Contact v.2</a></li>
                                    <li><a class="dropdown-item" href="contact-v3.html">Contact v.3</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">Help Center</a>
                                <ul class="dropdown-menu position-static transform-none shadow-none">
                                    <li><a class="dropdown-item" href="help-topics-v1.html">Help Topics v.1</a></li>
                                    <li><a class="dropdown-item" href="help-topics-v2.html">Help Topics v.2</a></li>
                                    <li><a class="dropdown-item" href="help-single-article-v1.html">Help Single Article v.1</a></li>
                                    <li><a class="dropdown-item" href="help-single-article-v2.html">Help Single Article v.2</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">404 Error</a>
                                <ul class="dropdown-menu position-static transform-none shadow-none">
                                    <li><a class="dropdown-item" href="404-electronics.html">404 Electronics</a></li>
                                    <li><a class="dropdown-item" href="404-fashion.html">404 Fashion</a></li>
                                    <li><a class="dropdown-item" href="404-furniture.html">404 Furniture</a></li>
                                    <li><a class="dropdown-item" href="404-grocery.html">404 Grocery</a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="terms-and-conditions.html">Terms &amp; Conditions</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="h6 fw-medium py-1 mb-0">
            <a class="d-block animate-underline py-1" href="docs/installation.html">
                <span class="d-inline-block animate-target py-1">Docs</span>
            </a>
        </div>
        <div class="h6 fw-medium py-1 mb-0">
            <a class="d-block animate-underline py-1" href="docs/typography.html">
                <span class="d-inline-block animate-target py-1">Components</span>
            </a>
        </div>
    </div>

    <!-- Account button visible on screens < 768px wide (md breakpoint) -->
    <div class="offcanvas-header flex-column align-items-start d-md-none">
        <a class="btn btn-lg btn-outline-secondary w-100 rounded-pill" href="account-signin.html">
            <i class="ci-user fs-lg ms-n1 me-2"></i>
            Account
        </a>
    </div>
</nav>
