<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sistem Absensi Karyawan' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand">Absensi Karyawan</span>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light">Logout</button>
            </form>
        @endauth
    </div>
</nav>

<div class="container pb-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

<script>
    function fillGeo(formId) {
        const form = document.getElementById(formId);
        if (!navigator.geolocation || !form) {
            return;
        }

        navigator.geolocation.getCurrentPosition((position) => {
            form.querySelector('[name="latitude"]').value = position.coords.latitude;
            form.querySelector('[name="longitude"]').value = position.coords.longitude;
        });
    }
</script>
</body>
</html>
