<!doctype html>
<html class="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SMBR</title>

    <!-- Bootswatch theme -->
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.1.1/darkly/bootstrap.min.css"> --}}
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}"> 
    {{-- <link rel="stylesheet" type="text/css" href="{{ mix('css/darkly/bulmaswatch/bulmaswatch.min.css') }}">--}} 
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css"> --}} 
    {{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet"> --}}


    <style>
        body {
            padding-top: 0px;
        }

        html {
            overflow-y: auto !important;
        }
    </style>
</head>

<body>
    @yield('content')
    @include('partials.scripts')
</body>

</html>