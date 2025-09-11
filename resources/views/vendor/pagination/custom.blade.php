@if ($paginator->hasPages())
    <nav class="rw-pagination" role="navigation" aria-label="Pagination Navigation">
        <ul class="rw-pagination__list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="rw-page rw-page--disabled" aria-disabled="true" aria-label="Previous">
                    <span>&laquo;</span>
                </li>
            @else
                <li class="rw-page">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="rw-page rw-page--ellipsis" aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="rw-page rw-page--active" aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li class="rw-page"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="rw-page">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
                </li>
            @else
                <li class="rw-page rw-page--disabled" aria-disabled="true" aria-label="Next">
                    <span>&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

@push('styles')
<style>
.rw-pagination { margin-top: 16px; }
.rw-pagination__list { list-style: none; display: flex; gap: 6px; align-items: center; justify-content: center; padding: 0; }
.rw-page a, .rw-page span { display:inline-flex; align-items:center; justify-content:center; min-width: 36px; height: 36px; padding: 0 10px; border-radius: 8px; border:1px solid #ccd5d6; color:#002f34; text-decoration:none; font-weight:600; font-size: 14px; }
.rw-page a:hover { background:#e9fdfa; border-color:#23e5db; }
.rw-page--active span { background:#23e5db; color:#002f34; border-color:#23e5db; }
.rw-page--disabled span { opacity: .5; border-style: dashed; }
.rw-page--ellipsis span { border: none; }
</style>
@endpush
