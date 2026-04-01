@props(['category', 'path' => null])

@php
    $currentPath = $path === null ? $category->slug : $path . '/' . $category->slug;
@endphp

<li class="d-flex flex-column w-100 pt-1">

    <a class="nav-link p-0"
       href="{{ route('catalog.show', $currentPath) }}">
        {{ $category->name }}
    </a>

    @if($category->childrenRecursive->count())
        <ul class="nav flex-column ms-3 mt-1">
            @foreach($category->childrenRecursive as $child)
                @include('components.mobile-category', ['category' => $child, 'path' => $currentPath])
            @endforeach
        </ul>
    @endif

</li>
