<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel App</title>
    @vite('resources/css/app.css')  <!-- Menyertakan CSS hasil kompilasi Vite -->
</head>
<body class="bg-gray-100">
    <div id="app">
        @yield('content')  <!-- Konten utama halaman -->
    </div>
</body>
</html>
