<li class="d-flex flex-column w-100 pt-1">

    <a class="nav-link p-0"
       href="{{ route('catalog.show', $category->getFullPath()) }}">
        {{ $category->name }}
    </a>

    @if($category->childrenRecursive->count())
        <ul class="nav flex-column ms-3 mt-1">
            @foreach($category->childrenRecursive as $child)
                @include('components.mobile-category', ['category' => $child])
            @endforeach
        </ul>
    @endif

</li>
