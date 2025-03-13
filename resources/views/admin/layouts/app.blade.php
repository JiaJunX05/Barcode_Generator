<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- JQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    @yield("css")
    <title>@yield("title") || Barcode Generator</title>
</head>
<body class="d-flex flex-column min-vh-100">
    <header>
        @include("admin.layouts.header")
    </header>

    <main class="flex-grow-1">
        @yield("content")
    </main>

    <footer>
        @include("admin.layouts.footer")
    </footer>

    @yield("scripts")
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
