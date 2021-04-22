<link rel="stylesheet" type="text/css" href="{{ asset('vendor/octopyid/impersonate/app.css') }}">

@if(config('impersonate.position', 'right') === Octopy\LaraPersonate\Impersonate::POSITION_LEFT)
    <style>
        .impersonate {
            right : 0;
            left  : 30px;
        }

        .impersonate-interface {
            right : 0;
            left  : 50px;
        }
    </style>
@endif

<div class="impersonate">
    <div class="impersonate-toggle {{ $impersonate->impersonated() ? 'impersonate-toggle-has-signed' : '' }} ">
        <img src="{{ asset('vendor/octopyid/impersonate/icon.svg') }}" alt="Impersonate" title="User Impersonate">
    </div>

    <div class="impersonate-interface impersonate-hidden impersonate-interface-has-signed">
        @if($impersonate->impersonated())
            <div class="impersonate-info-line">
                <table>
                    <tr>
                        <td class="impersonate-bold">Impersonated</td>
                        <td class="impersonate-bold">:</td>
                        <td>{{ $impersonate->getDisplayNameNextUser() }}</td>
                    </tr>
                    <tr>
                        <td class="impersonate-bold">Impersonating</td>
                        <td class="impersonate-bold">:</td>
                        <td>{{ $impersonate->getDisplayNamePrevUser() }}</td>
                    </tr>
                </table>
            </div>
        @endif

        <label for="impersonate-select"></label>
        <select class="impersonate-select" id="impersonate-select">
            @if($impersonate->impersonated())
                <option value="{{ $impersonate->getNextUserId() }}" selected="selected">
                    {{ $impersonate->getDisplayNameNextUser() }}
                </option>
            @else
                <option value="{{ $impersonate->getPrevUser() }}" selected="selected">
                    {{ $impersonate->getDisplayNamePrevUser() }}
                </option>
            @endif
        </select>

        @if($impersonate->impersonated())
            <div class="impersonate-footer">
                <div class="impersonate-logout">
                    <a href="#">&#10007; Leave</a>
                </div>

                <div class="impersonate-version">
                    version :
                    <a href="https://github.com/OctopyID/LaraPersonate">
                        {{ \Octopy\LaraPersonate\Impersonate::VERSION }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script type="text/javascript">
    window.csrf = '{{ csrf_token() }}';
    window.user = '{{ $impersonate->getPrevUserId() }}';
</script>
<script type="text/javascript" src="{{ asset('vendor/octopyid/impersonate/app.js') }}"></script>
