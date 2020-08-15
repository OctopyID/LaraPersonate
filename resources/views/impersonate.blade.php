@php extract(config('impersonate.fields')) @endphp
<link rel="stylesheet" href="{{ asset('vendor/supianidz/impersonate/impersonate.css') }}">

<div class="_impersonate">
    <div class="_impersonate-btn {{ $hasSigned ? '_impersonate-btn-has-signed' : '' }} _impersonate-toggle">
        <img src="{{ asset('vendor/supianidz/impersonate/impersonate.svg') }}" alt="Impersonate" title="User Impersonate">
    </div>

    <div class="_impersonate-interface _impersonate-hidden _impersonate-interface-has-signed">
        @if($hasSigned)
            <div class="_impersonate-info-line">
                <table>
                    <tr>
                        <td class="_impersonate-bold">Original Name</td>
                        <td class="_impersonate-bold">:</td>
                        <td>{{ $originalUser->$name }}</td>
                    </tr>
                    <tr>
                        <td class="_impersonate-bold">Impersonate As</td>
                        <td class="_impersonate-bold">:</td>
                        <td>{{ $currentUser->$name }}</td>
                    </tr>
                </table>
            </div>
        @endif
        <form method="POST" action="{{ route('impersonate.signin') }}">
            @csrf
            <input type="hidden" name="originalId" value="{{ $originalUser->$id }}">
            <select class="_impersonate-select" name="userId" onchange="this.form.submit();"></select>
        </form>
        @if($hasSigned)
            <div class="_impersonate-signout">
                <a href="{{ route('impersonate.signout') }}">&#10007; Sign Out</a>
            </div>
        @endif
    </div>
</div>

<script type="text/javascript">
    const impersonate_current_user_id = {{ $currentUser->$id }};
    const impersonate_user_list_url = '{{ route('impersonate.users') }}';
</script>
<script type="text/javascript" src="{{ asset('vendor/supianidz/impersonate/impersonate.js') }}"></script>
