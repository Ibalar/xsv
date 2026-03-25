<!-- Shopping cart offcanvas -->
<div class="offcanvas offcanvas-end pb-sm-2 px-sm-2" id="shoppingCart" tabindex="-1" aria-labelledby="shoppingCartLabel" style="width: 500px">

    <!-- Header -->
    <div class="offcanvas-header flex-column align-items-start py-3 pt-lg-4">
        <div class="d-flex align-items-center justify-content-between w-100 mb-3 mb-lg-4">
            <h4 class="offcanvas-title" id="shoppingCartLabel">Лист заказа</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>

    <!-- Items -->
    <div class="offcanvas-body d-flex flex-column gap-4 pt-2">

        <!-- Item -->
        <div class="d-flex align-items-center">
            <a class="position-relative flex-shrink-0" href="shop-product-grocery.html">
                <span class="badge text-bg-danger position-absolute top-0 start-0 z-2 mt-0 ms-0">-$2.79</span>
                <img src="assets/img/shop/grocery/thumbs/01.png" width="110" alt="Thumbnail">
            </a>
            <div class="w-100 ps-3">
                <h5 class="fs-sm fw-medium lh-base mb-2">
                    <a class="hover-effect-underline" href="shop-product-grocery.html">Fresh orange Klementina, Spain</a>
                </h5>
                <div class="h6 pb-1 mb-2">$3.12</div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="count-input rounded-pill">
                        <button type="button" class="btn btn-icon btn-sm" data-decrement aria-label="Decrement quantity">
                            <i class="ci-minus"></i>
                        </button>
                        <input type="number" class="form-control form-control-sm" value="3" readonly>
                        <button type="button" class="btn btn-icon btn-sm" data-increment aria-label="Increment quantity">
                            <i class="ci-plus"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-close fs-sm" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" data-bs-title="Remove" aria-label="Remove from cart"></button>
                </div>
            </div>
        </div>

        <!-- Item -->
        <div class="d-flex align-items-center">
            <a class="flex-shrink-0" href="shop-product-grocery.html">
                <img src="assets/img/shop/grocery/thumbs/02.png" width="110" alt="Thumbnail">
            </a>
            <div class="w-100 ps-3">
                <h5 class="fs-sm fw-medium lh-base mb-2">
                    <a class="hover-effect-underline" href="shop-product-grocery.html">Pesto sauce Barilla with basil, Italy</a>
                </h5>
                <div class="h6 pb-1 mb-2">$3.95</div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="count-input rounded-pill">
                        <button type="button" class="btn btn-icon btn-sm" data-decrement aria-label="Decrement quantity">
                            <i class="ci-minus"></i>
                        </button>
                        <input type="number" class="form-control form-control-sm" value="1" readonly>
                        <button type="button" class="btn btn-icon btn-sm" data-increment aria-label="Increment quantity">
                            <i class="ci-plus"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-close fs-sm" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" data-bs-title="Remove" aria-label="Remove from cart"></button>
                </div>
            </div>
        </div>

        <!-- Item -->
        <div class="d-flex align-items-center">
            <a class="flex-shrink-0" href="shop-product-grocery.html">
                <img src="assets/img/shop/grocery/thumbs/03.png" width="110" alt="Thumbnail">
            </a>
            <div class="w-100 ps-3">
                <h5 class="fs-sm fw-medium lh-base mb-2">
                    <a class="hover-effect-underline" href="shop-product-grocery.html">Steak salmon fillet with rosmary</a>
                </h5>
                <div class="h6 pb-1 mb-2">$27.00</div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="count-input rounded-pill">
                        <button type="button" class="btn btn-icon btn-sm" data-decrement aria-label="Decrement quantity">
                            <i class="ci-minus"></i>
                        </button>
                        <input type="number" class="form-control form-control-sm" value="2" readonly>
                        <button type="button" class="btn btn-icon btn-sm" data-increment aria-label="Increment quantity">
                            <i class="ci-plus"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-close fs-sm" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" data-bs-title="Remove" aria-label="Remove from cart"></button>
                </div>
            </div>
        </div>

        <!-- Item -->
        <div class="d-flex align-items-center">
            <a class="flex-shrink-0" href="shop-product-grocery.html">
                <img src="assets/img/shop/grocery/thumbs/04.png" width="110" alt="Thumbnail">
            </a>
            <div class="w-100 ps-3">
                <h5 class="fs-sm fw-medium lh-base mb-2">
                    <a class="hover-effect-underline" href="shop-product-grocery.html">Sprite soda lemon lime, can</a>
                </h5>
                <div class="h6 pb-1 mb-2">$0.80</div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="count-input rounded-pill">
                        <button type="button" class="btn btn-icon btn-sm" data-decrement aria-label="Decrement quantity">
                            <i class="ci-minus"></i>
                        </button>
                        <input type="number" class="form-control form-control-sm" value="2" readonly>
                        <button type="button" class="btn btn-icon btn-sm" data-increment aria-label="Increment quantity">
                            <i class="ci-plus"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-close fs-sm" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" data-bs-title="Remove" aria-label="Remove from cart"></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="offcanvas-header flex-column align-items-start">
        <div class="d-flex align-items-center justify-content-between w-100 mb-3 mb-md-4">
            <span class="text-light-emphasis">Subtotal:</span>
            <span class="h6 mb-0">$68.91</span>
        </div>
        <div class="d-flex w-100 gap-3">
            <a class="btn btn-lg btn-primary w-100 rounded-pill" href="{{ route('checkout.show') }}">Оформить</a>
        </div>
    </div>
</div>
