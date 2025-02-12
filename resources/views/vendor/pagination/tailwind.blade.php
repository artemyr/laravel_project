@if ($paginator->hasPages())
    <nav>
        <ul class="flex flex-wrap items-center justify-center gap-3">
            {{-- Previous Page Link --}}
            @if (!$paginator->onFirstPage())
                <li>
                    <a class="block p-3 pointer-events-none text-pink text-sm font-black leading-none">{{ __('pagination.prev') }}</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span class="text-body/50 text-sm font-black leading-none">...</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="block p-3 pointer-events-none text-pink text-sm font-black leading-none">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="block p-3 text-white hover:text-pink text-sm font-black leading-none">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="block p-3 text-white hover:text-pink text-sm font-black leading-none">{{ __('pagination.next') }}</a>
                </li>
            @endif
        </ul>
    </nav>
@endif
