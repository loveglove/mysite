<!doctype html>
<html class="no-js" lang="en">
    <head>
        <!-- title -->
        <title>Matt Glover :: A Builder of Things</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
        <meta name="author" content="Matt Glover">


        <!-- animation -->
        <link rel="stylesheet" href="{{ asset('/theme/css/animate.css') }}" />
        <!-- bootstrap -->
        <link rel="stylesheet" href="{{ asset('/theme/css/bootstrap.min.css') }}" />
        <!-- et line icon --> 
        <link rel="stylesheet" href="{{ asset('/theme/css/et-line-icons.css') }}" />
        <!-- font-awesome icon -->
        <link rel="stylesheet" href="{{ asset('/theme/css/font-awesome.min.css') }}" />
        <!-- themify icon -->
        <link rel="stylesheet" href="{{ asset('/theme/css/themify-icons.css') }}">
    </head>
    <body>   


        @yield('content')


        <script type="text/javascript" src="{{ asset('/theme/js/jquery.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/modernizr.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/jquery.easing.1.3.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/wow.min.js') }}"></script>

        @yield('scripts')

    </body>
</html>