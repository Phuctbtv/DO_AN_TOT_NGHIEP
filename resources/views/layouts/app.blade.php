<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ĐẠI PHÚC - Hệ thống hỗ trợ bão lũ minh bạch, hiệu quả">
    <title>@yield('title', 'ĐẠI PHÚC - Hỗ trợ bão lũ')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/daiphuc.css') }}">

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body>

    @yield('content')

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>

    {{-- Custom JS --}}
    <script src="{{ asset('js/daiphuc.js') }}"></script>

    @stack('scripts')
</body>
</html>
