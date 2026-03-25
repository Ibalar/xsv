@extends('layouts.main')

@section('title', 'Оформление заявки')

@section('meta_description', 'Оформление заявки на покупку растений')
@section('meta_keywords', 'заказ, оформление, питомник растений')

@section('content')

    <!-- Breadcrumb -->
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Оформление заявки</li>
            </ol>
        </nav>
    </div>

    <!-- Page title -->
    <div class="container pb-4">
        <h1 class="h3 mb-0">Оформление заявки</h1>
    </div>

    <!-- Checkout content -->
    <section class="container pb-5 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
        <div class="row">

            <!-- Cart items (left column) -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent py-3">
                        <h2 class="h5 mb-0">Товары в заявке</h2>
                    </div>
                    <div class="card-body p-0" id="cart-items-container">
                        <!-- Cart items will be loaded here via JavaScript -->
                        <div class="text-center py-5 text-muted" id="empty-cart-message">
                            <i class="ci-shopping-bag fs-1 mb-3 d-block"></i>
                            <p class="mb-3">Ваша заявка пуста</p>
                            <a href="{{ route('catalog.index') }}" class="btn btn-primary">
                                Перейти в каталог
                            </a>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent py-4" id="cart-footer" style="display: none;">
                        <div class="row align-items-center g-3">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100 w-md-auto" id="clear-cart">
                                    <i class="ci-trash me-2"></i>Очистить заявку
                                </button>
                            </div>
                            <div class="col-md-8 text-md-end">
                                <div class="d-inline-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center">
                                        <span class="text-body-tertiary me-2">Итого:</span>
                                        <span class="h4 mb-0 fw-bold" id="cart-total">0 BYN</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order form (right column) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 1;">
                    <div class="card-header bg-transparent py-4 border-0">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ci-user-circle text-primary fs-5"></i>
                            <h2 class="h5 mb-0">Контактные данные</h2>
                        </div>
                        <p class="text-body-tertiary small mb-0">Заполните форму для оформления заявки</p>
                    </div>
                    <div class="card-body pt-0">
                        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">Ваше имя <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-end-0">
                                        <i class="ci-user text-body-tertiary"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control border-start-0 @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required
                                           placeholder="Введите ваше имя">
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label fw-medium">Телефон <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-end-0">
                                        <i class="ci-phone text-body-tertiary"></i>
                                    </span>
                                    <input type="tel"
                                           class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           required
                                           placeholder="+375 (XX) XXX-XX-XX">
                                </div>
                                <div class="form-text">Мы перезвоним вам для подтверждения заявки</div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="comment" class="form-label fw-medium">Комментарий</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror"
                                          id="comment"
                                          name="comment"
                                          rows="3"
                                          placeholder="Удобное время для звонка, вопросы...">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check bg-body-tertiary p-3 rounded">
                                    <input type="checkbox"
                                           class="form-check-input @error('agree') is-invalid @enderror"
                                           id="agree"
                                           name="agree"
                                           value="1"
                                           required
                                           {{ old('agree') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="agree">
                                        <span class="fw-medium">Я согласен</span> на обработку персональных данных <span class="text-danger">*</span>
                                    </label>
                                    @error('agree')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Hidden field for cart products -->
                            <input type="hidden" name="products" id="products-input">

                            <button type="submit" class="btn btn-primary w-100 btn-lg fw-medium" id="submit-order" disabled>
                                <i class="ci-paper-plane me-2"></i>
                                Отправить заявку
                            </button>
                        </form>
                    </div>
                    <div class="card-footer bg-body-tertiary py-3 border-0">
                        <div class="d-flex align-items-center text-body-tertiary small">
                            <i class="ci-shield-check me-2 fs-5 text-success"></i>
                            <span>Ваши данные защищены и не будут переданы третьим лицам</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

@endsection

@push('styles')
<style>
    /* Cart item image styling */
    .cart-item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid var(--cz-border-color, rgba(0,0,0,0.08));
    }

    /* Quantity input styling */
    .quantity-input {
        width: 50px;
        text-align: center;
        font-weight: 500;
        border: none;
        background: transparent;
    }

    /* Cart item row styling */
    .cart-item {
        border-bottom: 1px solid var(--cz-border-color, rgba(0,0,0,0.08));
        transition: background-color 0.2s ease;
    }

    .cart-item:hover {
        background-color: var(--cz-body-tertiary-bg, rgba(0,0,0,0.02));
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    /* Product name styling */
    .product-name {
        font-weight: 600;
        color: var(--cz-body-color);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .product-name:hover {
        color: var(--cz-primary);
    }

    /* Quantity controls styling */
    .quantity-control {
        display: inline-flex;
        align-items: center;
        background: var(--cz-body-bg);
        border: 1px solid var(--cz-border-color, rgba(0,0,0,0.12));
        border-radius: 8px;
        padding: 2px;
        transition: all 0.2s ease;
    }

    .quantity-control:hover {
        border-color: var(--cz-primary);
    }

    .quantity-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        border-radius: 6px;
        color: var(--cz-body-color);
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .quantity-btn:hover {
        background-color: var(--cz-primary);
        color: white;
    }

    .quantity-btn:active {
        transform: scale(0.95);
    }

    /* Remove button styling */
    .remove-item-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--cz-danger-bg, rgba(220, 53, 69, 0.1));
        border: none;
        border-radius: 8px;
        color: var(--cz-danger, #dc3545);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .remove-item-btn:hover {
        background: var(--cz-danger, #dc3545);
        color: white;
    }

    /* Price styling */
    .product-price {
        font-weight: 600;
        color: var(--cz-body-color);
    }

    .item-total {
        font-weight: 700;
        font-size: 1.125rem;
        color: var(--cz-primary);
    }

    /* Table styling */
    .cart-table {
        margin-bottom: 0;
    }

    .cart-table td {
        vertical-align: middle;
        padding: 1.25rem 1rem;
    }

    .cart-table td:first-child {
        padding-left: 1.5rem;
    }

    .cart-table td:last-child {
        padding-right: 1.5rem;
    }

    /* Product info column */
    .product-info {
        min-width: 200px;
    }

    /* Quantity column */
    .quantity-column {
        min-width: 140px;
        text-align: center;
    }

    /* Total column */
    .total-column {
        min-width: 120px;
        text-align: right;
    }

    /* Actions column */
    .actions-column {
        min-width: 60px;
        text-align: right;
    }

    /* Animations */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .cart-item-image {
            width: 80px;
            height: 80px;
        }

        .cart-table td {
            padding: 1rem 0.75rem;
        }

        .cart-table td:first-child {
            padding-left: 0.75rem;
        }

        .cart-table td:last-child {
            padding-right: 0.75rem;
        }

        .item-total {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .cart-item-image {
            width: 60px;
            height: 60px;
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
        }

        .quantity-input {
            width: 40px;
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Phone mask
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');

            // Belarus phone format
            if (value.length > 0) {
                if (value.startsWith('375')) {
                    value = '+' + value;
                } else if (value.startsWith('80')) {
                    value = '+375' + value.substring(2);
                } else if (!value.startsWith('+')) {
                    value = '+375' + value;
                }

                // Format: +375 (XX) XXX-XX-XX
                let formatted = value;
                if (value.length > 4) {
                    formatted = value.substring(0, 4) + ' (' + value.substring(4, 6);
                }
                if (value.length > 6) {
                    formatted += ') ' + value.substring(6, 9);
                }
                if (value.length > 9) {
                    formatted += '-' + value.substring(9, 11);
                }
                if (value.length > 11) {
                    formatted += '-' + value.substring(11, 13);
                }

                e.target.value = formatted;
            }
        });
    }

    // Render cart items
    function renderCart() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const container = document.getElementById('cart-items-container');
        const emptyMessage = document.getElementById('empty-cart-message');
        const cartFooter = document.getElementById('cart-footer');
        const submitBtn = document.getElementById('submit-order');
        const productsInput = document.getElementById('products-input');

        if (cart.length === 0) {
            // Не удаляем emptyMessage из DOM, просто скрываем/показываем
            const cartTable = container.querySelector('.table-responsive');
            if (cartTable) {
                cartTable.remove();
            }
            emptyMessage.style.display = 'block';
            cartFooter.style.display = 'none';
            submitBtn.disabled = true;
            productsInput.value = '';
            return;
        }

        emptyMessage.style.display = 'none';
        cartFooter.style.display = 'block';
        submitBtn.disabled = false;

        // Set products data for form submission
        productsInput.value = JSON.stringify(cart);

        let html = '<div class="table-responsive"><table class="table cart-table align-middle mb-0">';
        html += '<tbody>';

        let total = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            const productUrl = item.slug ? `/product/${item.slug}` : `/product/${item.id}`;

            html += `
                <tr class="cart-item" data-id="${item.id}">
                    <td class="product-info">
                        <div class="d-flex align-items-center gap-3">
                            <a href="${productUrl}">
                                <img src="/storage/products/${item.image || 'placeholder.png'}"
                                     alt="${item.name}"
                                     class="cart-item-image">
                            </a>
                            <div>
                                <h3 class="h6 mb-1">
                                    <a href="${productUrl}" class="product-name text-decoration-none">${item.name}</a>
                                </h3>
                                <div class="product-price text-body-tertiary small">${item.price.toFixed(2)} BYN / шт.</div>
                            </div>
                        </div>
                    </td>
                    <td class="quantity-column">
                        <div class="d-flex justify-content-center">
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn quantity-decrease" data-id="${item.id}" aria-label="Уменьшить количество">
                                    <i class="ci-minus fs-sm"></i>
                                </button>
                                <input type="number"
                                       class="quantity-input form-control bg-transparent"
                                       value="${item.quantity}"
                                       min="1"
                                       max="99"
                                       readonly
                                       aria-label="Количество">
                                <button type="button" class="quantity-btn quantity-increase" data-id="${item.id}" aria-label="Увеличить количество">
                                    <i class="ci-plus fs-sm"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td class="total-column">
                        <div class="item-total">${itemTotal.toFixed(2)} BYN</div>
                    </td>
                    <td class="actions-column">
                        <button type="button" class="remove-item-btn" data-id="${item.id}" aria-label="Удалить из заявки">
                            <i class="ci-trash fs-sm"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table></div>';

        // Удаляем старую таблицу если есть
        const existingTable = container.querySelector('.table-responsive');
        if (existingTable) {
            existingTable.remove();
        }

        // Добавляем HTML после emptyMessage
        emptyMessage.insertAdjacentHTML('afterend', html);

        // Update total
        document.getElementById('cart-total').textContent = total.toFixed(2) + ' BYN';

        // Add event listeners
        container.querySelectorAll('.quantity-decrease').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                updateQuantity(id, -1);
            });
        });

        container.querySelectorAll('.quantity-increase').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                updateQuantity(id, 1);
            });
        });

        container.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                removeItem(id);
            });
        });
    }

    function updateQuantity(id, delta) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const item = cart.find(item => item.id == id);

        if (item) {
            item.quantity += delta;
            if (item.quantity <= 0) {
                cart = cart.filter(item => item.id != id);
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();

            // Update badge
            if (window.cart && window.cart.updateCartBadge) {
                window.cart.updateCartBadge();
            }
        }
    }

    function removeItem(id) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        cart = cart.filter(item => item.id != id);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();

        // Update badge
        if (window.cart && window.cart.updateCartBadge) {
            window.cart.updateCartBadge();
        }
    }

    // Clear cart button
    document.getElementById('clear-cart')?.addEventListener('click', function() {
        if (confirm('Вы уверены, что хотите очистить заявку?')) {
            localStorage.removeItem('cart');
            renderCart();

            // Update badge
            if (window.cart && window.cart.updateCartBadge) {
                window.cart.updateCartBadge();
            }
        }
    });

    // Form validation
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');

        if (cart.length === 0) {
            e.preventDefault();
            alert('Добавьте товары в заявку перед отправкой');
            return false;
        }

        // Update products input before submit
        document.getElementById('products-input').value = JSON.stringify(cart);
    });

    // Initial render
    renderCart();

    // Listen for storage changes
    window.addEventListener('storage', function(e) {
        if (e.key === 'cart') {
            renderCart();
        }
    });
});
</script>
@endpush
