<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') - Toko Rukun Jaya</title>
    <!-- Screen Zoom Preference Script -->
    <script>
        if (localStorage.getItem('screen-zoom') === 'enlarged') {
            document.documentElement.classList.add('zoom-enlarged');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html {
            font-size: 0.9375rem;
        }
        html.zoom-enlarged {
            font-size: 1.125rem;
        }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6 font-sans">
    <div class="text-center max-w-md">
        @yield('content')
    </div>
</body>
</html>
