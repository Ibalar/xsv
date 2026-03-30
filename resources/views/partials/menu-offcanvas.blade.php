
<!-- Site menu offcanvas -->
<nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
    <div class="offcanvas-header py-3">
        <h5 class="offcanvas-title" id="navbarNavLabel">Основные разделы</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0 pb-3">

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
                     class="accordion-collapse collapse"
                     data-bs-parent="#navigation">

                    <div class="accordion-body pb-3">

                        <div class="d-flex flex-column gap-4">

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
                                                    'category' => $child
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
        <div class="h6 fw-medium py-1 mb-0">
            <a class="d-block animate-underline py-1" href="{{ route('home') }}">
                <span class="d-inline-block animate-target py-1">Главная</span>
            </a>
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
