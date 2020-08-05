@php $id = config('sudo.fields.id'); $name = config('sudo.fields.name') @endphp

<link rel="stylesheet" href="/vendor/octopyid/sudo/sudo.min.css">
<link rel="stylesheet" href="/vendor/octopyid/sudo/tail.min.css">

<div class="octopyid-sudo">
    <div class="octopyid-sudo-btn {{ $hasSudoed ? 'octopyid-sudo-btn-has-sudoed' : '' }}"
         style="position: relative;"
         id="octopyid-sudo-toggle">
        <img src="/vendor/octopyid/sudo/sudo.svg" alt="Impersonate">
    </div>

    <div class="octopyid-sudo-interface octopyid-sudo-interface-has-sudoed hidden"
         id="octopyid-sudo-interface">
        @if ($hasSudoed)
            <div class="octopyid-sudo-info-line">
                <table>
                    <tr>
                        <td class="octopyid-sudo-font-bold">Sign In As</td>
                        <td class="octopyid-sudo-font-bold">:</td>
                        <td>{{ $currentUser->{$name} }}</td>
                    </tr>
                    <tr>
                        <td class="octopyid-sudo-font-bold">My Account</td>
                        <td class="octopyid-sudo-font-bold">:</td>
                        <td>{{ $originalUser->{$name} }}</td>
                    </tr>
                </table>
            </div>
        @endif
        <form method="POST" action="{{ route('sudo.signin') }}">
            {!! csrf_field() !!}
            <select id="octopy-sudo-select" name="userId" onchange="this.form.submit()" style="width: 100%;">
                @foreach ($users as $user)
                    <option
                        value="{{ $user->{$id} }}" {{ $user->{$id} === $currentUser->{$id} ? 'selected' : '' }}>{{ $user->{$name} }}</option>
                @endforeach
            </select>
            <input type="hidden" name="originalUserId" value="{{ $originalUser->{$id} ?? null }}">
        </form>
    </div>
</div>

<script type="text/javascript" src="/vendor/octopyid/sudo/tail.min.js"></script>
<script type="text/javascript">
    const button = document.getElementById('octopyid-sudo-toggle');
    const element = document.getElementById('octopyid-sudo-interface');

    button.addEventListener('click', () => element.classList.toggle('hidden'));

    document.addEventListener('DOMContentLoaded', () => {
        tail.select(document.getElementById('octopy-sudo-select'), {
            search: true,
            width: '100%'
        });
    });
</script>

