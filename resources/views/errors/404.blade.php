<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex,follow">

    <title>404 — {{ config('app.name', 'Contacts') }}</title>

    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md text-center">
            <p class="text-sm font-semibold text-indigo-600">404</p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Page not found</h1>
            <p class="mt-2 text-sm text-gray-600">Looks like you took a wrong turn somewhere.</p>
            <a href="{{ route('welcome') }}" class="mt-6 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Back to contacts
            </a>
        </div>
    </div>
</body>
</html>
