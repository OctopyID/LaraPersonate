<link rel="stylesheet" href="{{ asset('vendor/octopyid/impersonate/octopy.css') }}">

@php
    $width = config('impersonate.interface.width', 320);
    $width = is_numeric($width) ? $width . 'px' : $width;
@endphp

<div class="lp-root {{ $impersonate->check() ? 'lp-impersonated' : '' }}">
    <div class="lp-container" style="width: {{ $width }};">
        
        <div class="lp-select-container">
            <button class="lp-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="lp-select-value">
                    @if($impersonate->check())
                        {{ $impersonate->impersonated()->getImpersonateDisplayText() }}
                    @else
                        Select user to impersonate...
                    @endif
                </span>
                <span class="lp-select-arrow">▼</span>
            </button>

            <div class="lp-dropdown">
                <div class="lp-search-wrapper">
                    <input type="text" class="lp-search-input" placeholder="Search..." autocomplete="off">
                </div>
                <div class="lp-results" role="listbox"></div>
            </div>
        </div>

        @if($impersonate->check())
            <div class="lp-content">
                <div class="lp-info-row">
                    <span class="lp-info-label">IMPERSONATOR</span>
                    <span class="lp-info-separator">:</span>
                    <span class="lp-info-value">{{ $impersonate->impersonator()->getImpersonateDisplayText() }}</span>
                </div>
            </div>

            <div class="lp-footer">
                <div class="lp-logout">
                    <a href="#">✗ LEAVE</a>
                </div>
            </div>
        @endif
    </div>

    <button class="lp-toggle" aria-label="Toggle Impersonation">
        <img src="{{ asset('vendor/octopyid/impersonate/img/icon.svg') }}" alt="Impersonate">
    </button>
</div>

<script>
    window.impersonate = {
        config: {
            token: '{{ csrf_token() }}',
            route: '{{ rtrim(url('/'), '/') }}',
            delay: '{{ config('impersonate.interface.delay') }}',
        },
    }
</script>

<script src="{{ asset('vendor/octopyid/impersonate/octopy.js') }}"></script>
