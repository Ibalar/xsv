document.addEventListener('DOMContentLoaded', function () {

    function getCart() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }

    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartBadge();
    }

    function addToCart(product) {
        let cart = getCart();

        let existing = cart.find(item => item.id == product.id);

        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: 1
            });
        }

        saveCart(cart);
        showAddedMessage(product.name);
    }

    function removeFromCart(productId) {
        let cart = getCart();
        const removedItem = cart.find(item => item.id == productId);
        cart = cart.filter(item => item.id != productId);
        saveCart(cart);

        if (removedItem) {
            showRemovedMessage(removedItem.name);
        }
    }

    function updateQuantity(productId, quantity) {
        let cart = getCart();
        let item = cart.find(item => item.id == productId);
        if (item) {
            if (quantity <= 0) {
                removeFromCart(productId);
            } else {
                item.quantity = quantity;
                saveCart(cart);
            }
        }
    }

    function clearCart() {
        localStorage.removeItem('cart');
        updateCartBadge();
    }

    function getTotalQuantity() {
        return getCart().reduce((sum, item) => sum + item.quantity, 0);
    }

    function getTotalAmount() {
        return getCart().reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    function updateCartBadge() {
        const badge = document.querySelector('#cart-badge');
        if (badge) {
            const total = getTotalQuantity();
            badge.textContent = total;
            badge.style.display = total > 0 ? 'block' : 'none';
        }
    }

    // Toast уведомления на основе Bootstrap
    function getToastContainer() {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        return toastContainer;
    }

    function showCartMessage(message, type = 'success') {
        const toastContainer = getToastContainer();

        const typeClasses = {
            success: 'bg-success',
            danger: 'bg-danger',
            info: 'bg-info',
            warning: 'bg-warning'
        };

        const typeIcons = {
            success: 'ci-check-circle',
            danger: 'ci-trash',
            info: 'ci-info-circle',
            warning: 'ci-exclamation-triangle'
        };

        const toastHtml = `
            <div class="toast align-items-center text-white ${typeClasses[type] || typeClasses.success} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${typeIcons[type] || typeIcons.success} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = toastContainer.lastElementChild;

        const toast = new bootstrap.Toast(toastElement, {
            delay: 3000,
            autohide: true
        });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    function showAddedMessage(name) {
        showCartMessage(`Добавлено в заявку: ${name}`, 'success');
    }

    function showRemovedMessage(name) {
        showCartMessage(`Удалено из заявки: ${name}`, 'danger');
    }

    // Обработка всех кнопок добавления в корзину
    document.querySelectorAll('.product-card-button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const product = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                image: this.dataset.image
            };

            addToCart(product);
        });
    });

    // Обработка кнопок быстрой заявки
    document.querySelectorAll('.quick-order-button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const product = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                image: this.dataset.image
            };

            // Добавляем товар в корзину и редиректим на checkout
            addToCart(product);
            window.location.href = '/checkout';
        });
    });

    // Обновляем бейдж при загрузке страницы
    updateCartBadge();

    // Слушаем изменения localStorage из других вкладок
    window.addEventListener('storage', function(e) {
        if (e.key === 'cart') {
            updateCartBadge();
        }
    });

    // Глобальные функции для использования в других скриптах
    window.cart = {
        getCart,
        saveCart,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        getTotalQuantity,
        getTotalAmount,
        updateCartBadge
    };

});
