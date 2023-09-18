<link rel="stylesheet" href="{{ asset('vendor/octopyid/impersonate/impersonate.css') }}">

<div class="oim-root oim-active">
    <div class="oim-container oim-hidden">
        <div class="oim-header oim-hidden">
            <table>
                <tbody>
                    <tr>
                        <td>Name</td>
                        <td>:</td>
                        <td>Supian M</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <label for="select"></label>
        <select id="select">
            <option value="1">Supian M</option>
            <option value="2">Yulia S</option>
            <option value="3">Arash</option>
        </select>

        <div class="oim-footer oim-hidden">
            <div class="impersonate-footer-logout">
                <a href="#">✗ LEAVE</a>
            </div>

            <div class="impersonate-footer-version">
                <a href="https://github.com/OctopyID/LaraPersonate" target="_blank">
                    Octopy ID
                </a>
            </div>
        </div>
    </div>

    <button class="oim-toggle oim-rounded">
        <img src="{{ asset('vendor/octopyid/impersonate/img/icon.svg') }}" alt="Impersonate">
    </button>
</div>

<script src="{{ asset('vendor/octopyid/impersonate/impersonate.js') }}"></script>
