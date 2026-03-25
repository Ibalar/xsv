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
    <div id="cart-offcanvas-items" class="offcanvas-body d-flex flex-column gap-4 pt-2">
        <!-- Dynamic content will be loaded here -->
    </div>

    <!-- Empty state -->
    <div id="cart-offcanvas-empty" class="offcanvas-body d-flex flex-column align-items-center justify-content-center text-center py-5 d-none">
        <div class="mb-4">
            <i class="ci-shopping-cart fs-1 text-body-tertiary"></i>
        </div>
        <h5 class="h6 mb-3">Ваша заявка пуста</h5>
        <p class="fs-sm text-body-secondary mb-4">Вы еще не добавили ни одного товара в свою заявку.</p>
        <a class="btn btn-sm btn-primary rounded-pill" href="{{ route('catalog.index') }}">Перейти в каталог</a>
    </div>

    <!-- Footer -->
    <div id="cart-offcanvas-footer" class="offcanvas-header flex-column align-items-start pt-4">
        <div class="card border-0 bg-body-tertiary w-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-body-tertiary">Итого:</span>
                    <span id="cart-offcanvas-total" class="h4 mb-0 fw-bold text-primary">0.00 BYN</span>
                </div>
                <a class="btn btn-lg btn-primary w-100 rounded-pill" href="{{ route('checkout.show') }}">
                    Оформить заказ
                    <i class="ci-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>
