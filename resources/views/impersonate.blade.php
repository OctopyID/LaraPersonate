<link rel="stylesheet" href="{{ asset('vendor/octopyid/impersonate/octopy.css') }}">

<div class="oim-root {{ $impersonate->check() ? 'oim-impersonated' : '' }}">
    <div class="oim-container">
        @if(!$impersonate->check())
            <div class="oim-search-wrapper">
                <input type="text" class="oim-search-input" placeholder="Search user to impersonate..." autocomplete="off">
                <div class="oim-results"></div>
            </div>
        @else
            <div class="oim-content">
                <table>
                    <tbody>
                        <tr>
                            <td>IMPERSONATOR</td>
                            <td>:</td>
                            <td>{{ $impersonate->impersonator()->getImpersonateDisplayText() }}</td>
                        </tr>
                        <tr>
                            <td>IMPERSONATED</td>
                            <td>:</td>
                            <td>{{ $impersonate->impersonated()->getImpersonateDisplayText() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="oim-footer">
                <div class="oim-logout">
                    <a href="#">✗ LEAVE</a>
                </div>

                <div class="oim-version">
                    <a href="https://github.com/OctopyID/LaraPersonate" target="_blank">
                        Octopy ID
                    </a>
                </div>
            </div>
        @endif
    </div>

    <button class="oim-toggle" aria-label="Toggle Impersonation">
        <img src="{{ asset('vendor/octopyid/impersonate/img/icon.svg') }}" alt="Impersonate">
    </button>
</div>

<script>
    window.impersonate = {
        config: {
            token: '{{ csrf_token() }}',
            route: '{{ rtrim(url('/'), '/') }}',
            delay: '{{ config('impersonate.interface.delay') }}',
            width: '{{ config('impersonate.interface.width') }}',
        },
    }
</script>

<script src="{{ asset('vendor/octopyid/impersonate/octopy.js') }}"></script>
