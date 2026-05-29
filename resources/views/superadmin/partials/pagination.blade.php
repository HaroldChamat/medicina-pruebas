@if($paginator->hasPages())
<div class="sa-pagination">
    {{-- Anterior --}}
    @if($paginator->onFirstPage())
        <span class="sa-page-btn disabled"><i class="bi bi-chevron-left"></i></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="sa-page-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
    @endif

    {{-- Números --}}
    @php
        $start = max(1, $paginator->currentPage() - 2);
        $end   = min($paginator->lastPage(), $paginator->currentPage() + 2);
    @endphp

    @if($start > 1)
        <a href="{{ $paginator->url(1) }}" class="sa-page-btn">1</a>
        @if($start > 2)
            <span class="sa-page-btn disabled">…</span>
        @endif
    @endif

    @for($p = $start; $p <= $end; $p++)
        <a href="{{ $paginator->url($p) }}"
           class="sa-page-btn {{ $paginator->currentPage() === $p ? 'active' : '' }}">
            {{ $p }}
        </a>
    @endfor

    @if($end < $paginator->lastPage())
        @if($end < $paginator->lastPage() - 1)
            <span class="sa-page-btn disabled">…</span>
        @endif
        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="sa-page-btn">
            {{ $paginator->lastPage() }}
        </a>
    @endif

    {{-- Siguiente --}}
    @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="sa-page-btn">
            <i class="bi bi-chevron-right"></i>
        </a>
    @else
        <span class="sa-page-btn disabled"><i class="bi bi-chevron-right"></i></span>
    @endif
</div>
@endif