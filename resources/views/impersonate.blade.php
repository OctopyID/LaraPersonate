<link rel="stylesheet" href="{{ asset('vendor/octopyid/impersonate/app.css?debug='.microtime()) }}">

<div class="impersonate-wrapper">
    <div class="impersonate-content">
        <label for="impersonate"></label>
        <select class="impersonate-select" id="impersonate">
            @if($impersonate->hasImpersonation())
                <option value="{{ $impersonate->getImpersonated()->getKey() }}" selected="selected">
                    {{ new \Octopy\Impersonate\Support\TextDisplay($impersonate->getImpersonated()) }}
                </option>
            @else
                <option value="{{ $impersonate->getCurrentUser()->getKey() }}" selected="selected">
                    {{ new \Octopy\Impersonate\Support\TextDisplay($impersonate->getCurrentUser()) }}
                </option>
            @endif
        </select>

        @if($impersonate->hasImpersonation())

            <div class="impersonate-info">
                <table>
                    <tr>
                        <td class="impersonate-bold">IMPERSONATOR</td>
                        <td class="impersonate-bold">:</td>
                        <td>{{ new \Octopy\Impersonate\Support\TextDisplay($impersonate->getImpersonator()) }}</td>
                    </tr>
                    <tr>
                        <td class="impersonate-bold">IMPERSONATED</td>
                        <td class="impersonate-bold">:</td>
                        <td>{{ new \Octopy\Impersonate\Support\TextDisplay($impersonate->getImpersonated()) }}</td>
                    </tr>
                </table>
            </div>

            <div class="impersonate-footer">
                <div class="impersonate-footer-logout">
                    <a href="#">✗ LEAVE</a>
                </div>

                <div class="impersonate-footer-version">
                    <a href="https://github.com/OctopyID/LaraPersonate" target="_blank">
                        {{ \Octopy\Impersonate\Impersonate::VERSION }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    <button class="impersonate-toggle">
        <img src="{{ asset('vendor/octopyid/impersonate/img/icon.svg') }}" alt="Impersonate">
    </button>
</div>

<script>
    const impersonate = {
        active: {{ $impersonate->hasImpersonation() ? 'true' : 'false' }},
        config: {
            token: '{{ csrf_token() }}',
            delay: '{{ config('impersonate.display.delay') }}',
            width: '{{ config('impersonate.display.width') }}'
        },
    }
</script>

<script src="{{ asset('vendor/octopyid/impersonate/app.js?time=' . time()) }}"></script>
