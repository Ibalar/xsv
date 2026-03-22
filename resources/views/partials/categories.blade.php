<div class="w-100 border rounded p-3 p-xl-4 mb-3 mb-xl-4">
    <h4 class="h6 mb-2">Разделы каталога</h4>
    <ul class="list-unstyled d-block m-0">
        @foreach($rootCategories as $cat)
            <li class="nav d-block pt-2 mt-1">
                <a class="nav-link animate-underline fw-normal p-0" href="{{ route('catalog.show', $cat->getFullPath()) }}">
                    <span class="animate-target text-truncate me-3">{{ $cat->name }}</span>
                    <span class="text-body-secondary fs-xs ms-auto">{{ $cat->products_count }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
