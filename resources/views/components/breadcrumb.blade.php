<nav class="container position-relative z-2 pt-lg-2 mt-3 mt-lg-4" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        @foreach ($items as $title => $url)
            @if ($loop->last || $url === null)
                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $url }}">{{ $title }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
