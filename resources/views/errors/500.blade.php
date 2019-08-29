<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex,follow">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        404 - {{ config('app.name', 'Contacts') }}
    </title>

    <!-- Styles -->
    <link href="{{ asset('css/vendor.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="gray-bg">

<div class="container">
    <h1>500</h1>
    <p>
        <strong>Uh oh!</strong>
        Looks like something went wrong on our end.<br>
        <br>
        <a href="{{ route('welcome') }}">Back to contacts</a>
    </p>
</div>

</body>
</html>
