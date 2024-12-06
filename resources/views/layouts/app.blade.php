<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyLaundry</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div id="app">
        <div class="w-full min-h-screen flex flex-col pt-6 sm:pt-0 bg-gray-100 font-outfit">
            @yield('content')
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
