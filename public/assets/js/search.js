document.addEventListener('DOMContentLoaded', function () {

    let timeout = null;

    // --- Универсальная функция подсветки ---
    function highlight(text, query) {
        try {
            const reg = new RegExp(`(${query})`, 'gi');
            return text.replace(reg, '<mark>$1</mark>');
        } catch (e) {
            return text;
        }
    }

    // --- Функция инициализации поиска для поля и контейнера ---
    function initSearch(inputId, resultsId) {
        const input = document.getElementById(inputId);
        const resultsBox = document.getElementById(resultsId);

        if (!input || !resultsBox) return;

        input.addEventListener('input', function () {
            clearTimeout(timeout);

            const query = this.value.trim();

            if (query.length < 2) {
                resultsBox.innerHTML = '';
                resultsBox.style.display = 'none';
                return;
            }

            timeout = setTimeout(() => {
                fetch('/search?q=' + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        resultsBox.innerHTML = '';

                        if (!data.length) {
                            resultsBox.innerHTML = '<div class="no-results">Ничего не найдено</div>';
                            resultsBox.style.display = 'block';
                            return;
                        }

                        data.forEach(item => {
                            const el = document.createElement('div');
                            el.classList.add('search-item');

                            el.innerHTML = `
                                <div class="search-item-inner">
                                    <img src="${item.image ? `${item.image}` : '/no-image.jpg'}" alt="${item.name}" width="40" height="40">
                                    <div>
                                        <div class="title">${highlight(item.name, query)}</div>
                                        <div class="price">${item.price} BYN</div>
                                    </div>
                                </div>
                            `;

                            el.onclick = () => {
                                window.location.href = '/product/' + item.slug;
                            };

                            resultsBox.appendChild(el);
                        });

                        resultsBox.style.display = 'block';
                    })
                    .catch(() => {
                        resultsBox.style.display = 'none';
                    });

            }, 300);
        });

        // Enter → страница поиска
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                window.location.href = '/search-page?q=' + encodeURIComponent(this.value);
            }
        });
    }

    // --- Инициализация для десктопа ---
    initSearch('search', 'search-results');

    // --- Инициализация для мобильного поиска ---
    initSearch('search-mobile', 'search-results-mobile');

    // --- Кнопка поиска для десктопа ---
    const btn = document.querySelector('.btn[aria-label="Search button"]');
    if (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById('search');
            if (input) {
                window.location.href = '/search-page?q=' + encodeURIComponent(input.value);
            }
        });
    }

    // --- Закрытие при клике вне ---
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-box') && !e.target.closest('#searchBar')) {
            const desktopResults = document.getElementById('search-results');
            const mobileResults = document.getElementById('search-results-mobile');

            if (desktopResults) desktopResults.style.display = 'none';
            if (mobileResults) mobileResults.style.display = 'none';
        }
    });

});
