
<!-- Site menu offcanvas -->
<nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
    <div class="offcanvas-header py-3">
        <h5 class="offcanvas-title" id="navbarNavLabel">Основные разделы</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0 pb-3">

        <div class="h6 fw-medium py-1 mb-0">
            <a class="d-block animate-underline py-1" href="{{ route('home') }}">
                <span class="d-inline-block animate-target py-1">Главная</span>
            </a>
        </div>
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
                     class="accordion-collapse collapse mobile-catalog-collapse"
                     data-bs-parent="#navigation">

                    <div class="accordion-body pb-3">

                        <div id="mobileCatalogScroll" class="mobile-catalog-scroll d-flex flex-column gap-4">

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
                                                    'category' => $child,
                                                    'path' => $category->slug
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

        </div>




        @foreach($menuPages as $page)
            <div class="h6 fw-medium py-1 mb-0">
                <a href="{{ route('pages.show', $page->slug) }}" class="d-block animate-underline py-1">
                    <span class="d-inline-block animate-target py-1">{{ $page->title }}</span>
                </a>
            </div>
        @endforeach


    </div>
</nav>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const offcanvas = document.getElementById('navbarNav');
            const categoriesMenu = document.getElementById('categoriesMenu');
            const mobileCatalogScroll = document.getElementById('mobileCatalogScroll');

            if (!offcanvas || !categoriesMenu || !mobileCatalogScroll) {
                return;
            }

            let scrollY = 0;

            const isMobileViewport = () => window.innerWidth < 992;
            const getOverlayTop = () => {
                const selectors = ['.top-bar', 'header[data-sticky-element]', '.header-categories-bar'];

                return selectors.reduce((maxBottom, selector) => {
                    const element = document.querySelector(selector);

                    if (!element) {
                        return maxBottom;
                    }

                    const rect = element.getBoundingClientRect();
                    return Math.max(maxBottom, rect.bottom);
                }, 0);
            };

            const lockPageScroll = () => {
                if (!isMobileViewport() || document.body.classList.contains('mobile-catalog-lock')) {
                    return;
                }

                scrollY = window.scrollY || window.pageYOffset || 0;
                document.body.style.top = `-${scrollY}px`;
                document.body.style.setProperty('--catalog-overlay-top', `${Math.max(0, getOverlayTop())}px`);
                document.body.classList.add('mobile-catalog-lock');
                document.body.classList.add('catalog-overlay-active');
                offcanvas.classList.add('mobile-catalog-active');
            };

            const unlockPageScroll = () => {
                if (!document.body.classList.contains('mobile-catalog-lock')) {
                    offcanvas.classList.remove('mobile-catalog-active');
                    return;
                }

                const bodyTop = parseInt(document.body.style.top || '0', 10);

                document.body.classList.remove('mobile-catalog-lock');
                document.body.classList.remove('catalog-overlay-active');
                document.body.style.top = '';
                document.body.style.removeProperty('--catalog-overlay-top');
                offcanvas.classList.remove('mobile-catalog-active');
                window.scrollTo(0, Math.abs(bodyTop));
            };

            const syncCatalogHeight = () => {
                if (!isMobileViewport() || !categoriesMenu.classList.contains('show')) {
                    mobileCatalogScroll.style.removeProperty('--mobile-catalog-max-height');
                    return;
                }

                const rect = mobileCatalogScroll.getBoundingClientRect();
                const maxHeight = Math.max(160, window.innerHeight - rect.top - 16);
                mobileCatalogScroll.style.setProperty('--mobile-catalog-max-height', `${maxHeight}px`);
            };

            categoriesMenu.addEventListener('shown.bs.collapse', () => {
                lockPageScroll();
                syncCatalogHeight();
            });

            categoriesMenu.addEventListener('hidden.bs.collapse', () => {
                mobileCatalogScroll.style.removeProperty('--mobile-catalog-max-height');
                unlockPageScroll();
            });

            offcanvas.addEventListener('shown.bs.offcanvas', () => {
                if (categoriesMenu.classList.contains('show')) {
                    lockPageScroll();
                    syncCatalogHeight();
                }
            });

            offcanvas.addEventListener('hidden.bs.offcanvas', () => {
                mobileCatalogScroll.style.removeProperty('--mobile-catalog-max-height');
                unlockPageScroll();
            });

            window.addEventListener('resize', () => {
                if (!categoriesMenu.classList.contains('show')) {
                    return;
                }

                if (!isMobileViewport()) {
                    mobileCatalogScroll.style.removeProperty('--mobile-catalog-max-height');
                    unlockPageScroll();
                    return;
                }

                document.body.style.setProperty('--catalog-overlay-top', `${Math.max(0, getOverlayTop())}px`);
                syncCatalogHeight();
            });
        });
    </script>
@endpush
