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
        cart = cart.filter(item => item.id != productId);
        saveCart(cart);
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

    function showAddedMessage(name) {
        let el = document.createElement('div');
        el.className = 'cart-toast';
        el.innerText = `Добавлено: ${name}`;
        el.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(el);

        setTimeout(() => {
            el.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => el.remove(), 300);
        }, 2000);
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
