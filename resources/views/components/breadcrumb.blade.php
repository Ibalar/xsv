<nav class="container position-relative z-2 pt-lg-2 my-3 my-lg-4" aria-label="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
    <ol class="breadcrumb mb-0">
        @foreach ($items as $index => $item)
            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}"
                itemprop="itemListElement"
                itemscope
                itemtype="https://schema.org/ListItem">

                @if (!$loop->last && !empty($item['url']))
                    <a href="{{ $item['url'] }}" itemprop="item">
                        <span itemprop="name">{{ $item['title'] }}</span>
                    </a>
                @else
                    <span itemprop="name">{{ $item['title'] }}</span>
                @endif

                <meta itemprop="position" content="{{ $index + 1 }}">
            </li>
        @endforeach
    </ol>
</nav>
