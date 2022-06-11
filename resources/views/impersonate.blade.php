<link rel="stylesheet" href="{{ asset('vendor/octopyid/impersonate/app.css') }}">

<div class="impersonate-wrapper">
    <div class="impersonate-content">
        <select class="impersonate-select">
            <option>Foo Bar</option>
            <option>Bar Baz</option>
            <option>Baz Qux</option>
        </select>

        <div class="impersonate-info">
            <table>
                <tr>
                    <td class="impersonate-bold">IMPERSONATOR</td>
                    <td class="impersonate-bold">:</td>
                    <td>Foo Bar</td>
                </tr>
                <tr>
                    <td class="impersonate-bold">IMPERSONATED</td>
                    <td class="impersonate-bold">:</td>
                    <td>Bar Baz</td>
                </tr>
            </table>
        </div>

        <div class="impersonate-footer">
            <div class="impersonate-footer-logout">
                <a href="#">✗ LEAVE</a>
            </div>

            <div class="impersonate-footer-version">
                <a href="https://github.com/OctopyID/LaraPersonate"> v2.1.0 </a>
            </div>
        </div>
    </div>

    <button class="impersonate-toggle">
        <img src="{{ asset('vendor/octopyid/impersonate/img/icon.svg') }}" alt="Impersonate">
    </button>
</div>

<script src="{{ asset('vendor/octopyid/impersonate/app.js') }}"></script>
