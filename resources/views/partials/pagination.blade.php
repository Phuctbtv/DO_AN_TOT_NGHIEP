@if ($paginator->hasPages())
@php
  $window   = \Illuminate\Pagination\UrlWindow::make($paginator);
  $elements = array_filter([
      $window['first'],
      is_array($window['slider']) ? '...' : null,
      $window['slider'],
      is_array($window['last'])   ? '...' : null,
      $window['last'],
  ]);
@endphp
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-top:1.25rem">

  <div style="font-size:.8rem;color:#64748b">
    Hiển thị <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
    trong <strong>{{ $paginator->total() }}</strong> kết quả
  </div>

  <div style="display:flex;gap:.25rem;flex-wrap:wrap;align-items:center">

    @if ($paginator->onFirstPage())
      <span style="padding:.38rem .75rem;border:1px solid #e2e8f0;border-radius:7px;color:#cbd5e1;font-size:.82rem;cursor:not-allowed">‹</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}"
         style="padding:.38rem .75rem;border:1px solid #e2e8f0;border-radius:7px;color:#475569;font-size:.82rem;text-decoration:none"
         onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background=''">‹</a>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <span style="padding:.38rem .5rem;font-size:.82rem;color:#94a3b8">…</span>
      @elseif (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span style="padding:.38rem .8rem;border-radius:7px;background:#0d9488;color:#fff;font-size:.82rem;font-weight:700;display:inline-block;min-width:32px;text-align:center">{{ $page }}</span>
          @else
            <a href="{{ $url }}"
               style="padding:.38rem .8rem;border:1px solid #e2e8f0;border-radius:7px;color:#475569;font-size:.82rem;text-decoration:none;display:inline-block;min-width:32px;text-align:center"
               onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background=''">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}"
         style="padding:.38rem .75rem;border:1px solid #e2e8f0;border-radius:7px;color:#475569;font-size:.82rem;text-decoration:none"
         onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background=''">›</a>
    @else
      <span style="padding:.38rem .75rem;border:1px solid #e2e8f0;border-radius:7px;color:#cbd5e1;font-size:.82rem;cursor:not-allowed">›</span>
    @endif

  </div>
</div>
@endif
