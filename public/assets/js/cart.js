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

            // Открываем модальное окно быстрой заявки
            openQuickOrderModal(product);
        });
    });

    // Модальное окно быстрой заявки
    let currentQuickOrderProduct = null;

    function openQuickOrderModal(product) {
        currentQuickOrderProduct = product;

        // Заполняем данные товара
        const modal = document.getElementById('quickOrderModal');
        if (!modal) return;

        const imageEl = document.getElementById('quick-order-image');
        const nameEl = document.getElementById('quick-order-name');
        const priceEl = document.getElementById('quick-order-price');
        const productIdEl = document.getElementById('quick-order-product-id');

        if (imageEl) {
            imageEl.src = '/storage/products/' + (product.image || 'placeholder.png');
            imageEl.alt = product.name;
        }
        if (nameEl) nameEl.textContent = product.name;
        if (priceEl) priceEl.textContent = product.price.toFixed(2) + ' BYN';
        if (productIdEl) productIdEl.value = product.id;

        // Открываем модальное окно
        const bsModal = bootstrap.Modal.getOrCreateInstance(modal);
        bsModal.show();
    }

    // Маска для телефона
    const phoneInput = document.getElementById('quick-order-phone');
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

    // Обработка отправки формы быстрой заявки
    const quickOrderForm = document.getElementById('quick-order-form');
    if (quickOrderForm) {
        quickOrderForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!currentQuickOrderProduct) {
                showCartMessage('Ошибка: товар не найден', 'danger');
                return;
            }

            // Валидация
            const name = document.getElementById('quick-order-name-input').value.trim();
            const phone = document.getElementById('quick-order-phone').value.trim();
            const agree = document.getElementById('quick-order-agree').checked;

            if (!name) {
                showCartMessage('Пожалуйста, укажите ваше имя', 'warning');
                return;
            }

            if (!phone) {
                showCartMessage('Пожалуйста, укажите ваш телефон', 'warning');
                return;
            }

            if (!agree) {
                showCartMessage('Необходимо согласие на обработку персональных данных', 'warning');
                return;
            }

            // Подготавливаем данные
            const comment = document.getElementById('quick-order-comment').value.trim();
            const products = [{
                id: currentQuickOrderProduct.id,
                name: currentQuickOrderProduct.name,
                price: currentQuickOrderProduct.price,
                image: currentQuickOrderProduct.image,
                quantity: 1
            }];

            const formData = {
                products: JSON.stringify(products),
                name: name,
                phone: phone,
                comment: comment,
                agree: '1'
            };

            // Отправляем запрос
            const submitBtn = document.getElementById('quick-order-submit');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Отправка...';

            fetch('/quick-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (response.redirected) {
                    // Сервер сделал редирект - заявка успешно создана
                    return { success: true };
                }
                if (!response.ok) {
                    throw new Error('Ошибка сервера');
                }
                return response.json().catch(() => ({ success: true }));
            })
            .then(data => {
                // Закрываем модальное окно
                const modal = document.getElementById('quickOrderModal');
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();

                // Показываем сообщение об успехе
                showCartMessage('Заявка успешно отправлена! Мы перезвоним вам.', 'success');

                // Сбрасываем форму
                quickOrderForm.reset();
                currentQuickOrderProduct = null;
            })
            .catch(error => {
                showCartMessage('Ошибка при отправке заявки. Попробуйте позже.', 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Отправить заявку';
            });
        });
    }

    // Сброс формы при закрытии модального окна
    const quickOrderModal = document.getElementById('quickOrderModal');
    if (quickOrderModal) {
        quickOrderModal.addEventListener('hidden.bs.modal', function() {
            if (quickOrderForm) quickOrderForm.reset();
            currentQuickOrderProduct = null;

            const submitBtn = document.getElementById('quick-order-submit');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Отправить заявку';
            }
        });
    }

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
