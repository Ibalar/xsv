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
                    <div class="card-footer bg-transparent py-3" id="cart-footer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-cart">
                                <i class="ci-trash me-2"></i>Очистить заявку
                            </button>
                            <div class="text-end">
                                <span class="text-muted me-2">Итого:</span>
                                <span class="h4 mb-0" id="cart-total">0 BYN</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order form (right column) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 1;">
                    <div class="card-header bg-transparent py-3">
                        <h2 class="h5 mb-0">Контактные данные</h2>
                    </div>
                    <div class="card-body">
                        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Ваше имя <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required
                                       placeholder="Введите ваше имя">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                                <input type="tel"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       required
                                       placeholder="+375 (XX) XXX-XX-XX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Мы перезвоним вам для подтверждения заказа</div>
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label">Комментарий</label>
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
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input @error('agree') is-invalid @enderror"
                                           id="agree"
                                           name="agree"
                                           value="1"
                                           required
                                           {{ old('agree') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="agree">
                                        Я согласен на обработку персональных данных <span class="text-danger">*</span>
                                    </label>
                                    @error('agree')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Hidden field for cart products -->
                            <input type="hidden" name="products" id="products-input">

                            <button type="submit" class="btn btn-primary w-100 btn-lg" id="submit-order" disabled>
                                Отправить заявку
                            </button>
                        </form>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="ci-shield-check me-2 fs-lg"></i>
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
    .cart-item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }

    .quantity-input {
        width: 60px;
        text-align: center;
    }

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

        let html = '<div class="table-responsive"><table class="table table-borderless align-middle mb-0">';
        html += '<tbody>';

        let total = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            html += `
                <tr class="cart-item" data-id="${item.id}">
                    <td style="width: 100px;">
                        <img src="/storage/products/${item.image || 'placeholder.png'}"
                             alt="${item.name}"
                             class="cart-item-image">
                    </td>
                    <td>
                        <h3 class="h6 mb-1">${item.name}</h3>
                        <div class="text-muted small">${item.price.toFixed(2)} BYN / шт.</div>
                    </td>
                    <td style="width: 120px;">
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-icon btn-sm btn-outline-secondary quantity-decrease" data-id="${item.id}">
                                <i class="ci-minus fs-sm"></i>
                            </button>
                            <input type="number"
                                   class="form-control form-control-sm quantity-input mx-2"
                                   value="${item.quantity}"
                                   min="1"
                                   max="99"
                                   readonly>
                            <button type="button" class="btn btn-icon btn-sm btn-outline-secondary quantity-increase" data-id="${item.id}">
                                <i class="ci-plus fs-sm"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-end" style="width: 120px;">
                        <div class="h6 mb-0">${itemTotal.toFixed(2)} BYN</div>
                    </td>
                    <td style="width: 50px;">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger remove-item" data-id="${item.id}">
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
