document.addEventListener('DOMContentLoaded', function () {

    function getCart() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }

    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
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

    function showAddedMessage(name) {
        let el = document.createElement('div');
        el.className = 'cart-toast';
        el.innerText = `Добавлено: ${name}`;
        document.body.appendChild(el);

        setTimeout(() => el.remove(), 2000);
    }

    // 🔥 обработка всех кнопок
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function () {

            const product = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: this.dataset.price,
                image: this.dataset.image
            };

            addToCart(product);
        });
    });

});
