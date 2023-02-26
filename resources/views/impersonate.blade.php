<link rel="stylesheet" href="{{ asset('vendor/octopyid/impersonate/octopy.css') }}">

<div class="impersonate-wrapper">
    <div class="impersonate-content">
        <label for="impersonate"></label>
        <select class="impersonate-select" id="impersonate" style="width: 100% !important;">
            @if($impersonate->isInImpersonation())
                <option value="{{ $impersonate->getImpersonated()->getKey() }}" selected="selected">
                    {{ new \Octopy\Impersonate\Support\TextDisplay($impersonate->getImpersonated()) }}
                </option>
            @else
                <option value="{{ $impersonate->getCurrentUser()->getKey() }}" selected="selected">
                    {{ new \Octopy\Impersonate\Support\TextDisplay($impersonate->getCurrentUser()) }}
                </option>
            @endif
        </select>

        @if($impersonate->isInImpersonation())

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
                        {{ $impersonate->version() }}
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
        active: {{ $impersonate->isInImpersonation() ? 'true' : 'false' }},
        config: {
            token: '{{ csrf_token() }}',
            route: '{{ rtrim(config('app.url'), '/') }}',
            delay: '{{ config('impersonate.interface.delay') }}',
            width: '{{ config('impersonate.interface.width') }}',
        },
    }
</script>

<script src="{{ asset('vendor/octopyid/impersonate/octopy.js') }}"></script>
