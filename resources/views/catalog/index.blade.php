@extends('layouts.main')

@section('title', 'Каталог товаров')

@section('content')

    <!-- Breadcrumb -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Page title -->
    <div class="container pb-2 pb-md-3 pb-lg-4">
        <h1 class="h3 mb-0">Каталог товаров</h1>
    </div>

    <!-- Categories grid -->
    <section class="container pb-5 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($categories as $category)
                <div class="col">
                    <div class="card h-100 bg-transparent border-0">
                        <div class="position-relative hover-effect-opacity mb-3">
                            <div class="ratio rounded-5 overflow-hidden" style="--cz-aspect-ratio: calc(240 / 212 * 100%)">
                                <img src="{{ $category->image ? asset('storage/' . $category->image) : asset('assets/img/placeholder.png') }}" alt="{{ $category->name }}">
                            </div>
                            <div class="hover-effect-target position-absolute top-0 start-0 w-100 h-100 bg-black bg-opacity-25 d-flex align-items-center justify-content-center opacity-0 transition-all">
                                <i class="ci-arrow-right fs-2 text-white"></i>
                            </div>
                            <a href="{{ route('catalog.show', $category->getFullPath()) }}" class="stretched-link"></a>
                        </div>
                        <h3 class="h5 mb-2">
                            <a class="nav-link animate-underline p-0" href="{{ route('catalog.show', $category->getFullPath()) }}">
                                <span class="animate-target">{{ $category->name }}</span>
                            </a>
                        </h3>
                        <ul class="nav flex-column gap-2 fs-sm">
                            @foreach($category->children->take(5) as $child)
                                <li class="nav-item">
                                    <a class="nav-link d-inline-block p-0 fw-normal text-truncate" href="{{ route('catalog.show', $child->getFullPath()) }}">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @endforeach
                            @if($category->children_count > 5)
                                <li class="nav-item">
                                    <a class="nav-link d-inline-block p-0 fw-medium" href="{{ route('catalog.show', $category->getFullPath()) }}">
                                        Еще {{ $category->children_count - 5 }}...
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

@endsection
