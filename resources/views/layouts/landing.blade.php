<!doctype html>
<html class="no-js" lang="en">
    <head>
        <!-- title -->
        <title>Matt Glover :: A Builder of Things</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
        <meta name="author" content="ThemeZaa">
        <!-- description -->
        <meta name="description" content="POFO is a highly creative, modern, visually stunning and Bootstrap responsive multipurpose agency and portfolio HTML5 template with 25 ready home page demos.">
        <!-- keywords -->
        <meta name="keywords" content="creative, modern, clean, bootstrap responsive, html5, css3, portfolio, blog, agency, templates, multipurpose, one page, corporate, start-up, studio, branding, designer, freelancer, carousel, parallax, photography, personal, masonry, grid, coming soon, faq">



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
        <!-- swiper carousel -->
        <link rel="stylesheet" href="{{ asset('/theme/css/swiper.min.css') }}">
        <!-- justified gallery  -->
        <link rel="stylesheet" href="{{ asset('/theme/css/justified-gallery.min.css') }}">
        <!-- magnific popup -->
        <link rel="stylesheet" href="{{ asset('/theme/css/magnific-popup.css') }}" />
        <!-- revolution slider -->
        <link rel="stylesheet" type="text/css" href="{{ asset('/theme/revolution/css/settings.css') }}" media="screen" />
        <link rel="stylesheet" type="text/css" href="{{ asset('/theme/revolution/css/layers.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/theme/revolution/css/navigation.css') }}">
        <!-- bootsnav -->
        <link rel="stylesheet" href="{{ asset('/theme/css/bootsnav.css') }}">
        <!-- style -->
        <link rel="stylesheet" href="{{ asset('/theme/css/style.css') }}" />
        <!-- responsive css -->
        <link rel="stylesheet" href="{{ asset('/theme/css/responsive.css') }}" />

         <link rel="stylesheet" href="{{ asset('/css/custom.css') }}" />
        <!--[if IE]>
            <script src="js/html5shiv.js"></script>
        <![endif]-->
    </head>
    <body>   

  
        @yield('content')



        <!-- end countdown section -->
        <!-- start scroll to top -->
        <a class="scroll-top-arrow" href="javascript:void(0);"><i class="ti-arrow-up"></i></a>
        <!-- end scroll to top  -->
        <!-- javascript libraries -->
        <script type="text/javascript" src="{{ asset('/theme/js/jquery.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/modernizr.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/jquery.easing.1.3.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/skrollr.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/smooth-scroll.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/jquery.appear.js') }}"></script>
        <!-- menu navigation -->
        <script type="text/javascript" src="{{ asset('/theme/js/bootsnav.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/theme/js/jquery.nav.js') }}"></script>
        <!-- animation -->
        <script type="text/javascript" src="{{ asset('/theme/js/wow.min.js') }}"></script>
        <!-- page scroll -->
   
        <!-- revolution slider extensions (load below extensions JS files only on local file systems to make the slider work! The following part can be removed on server for on demand loading) -->
        <!--<script type="text/javascript" src="revolution/js/extensions/revolution.extension.actions.min.js"></script>
        <script type="text/javascript" src="revolution/js/extensions/revolution.extension.carousel.min.js"></script>
        <script type="text/javascript" src="revolution/js/extensions/revolution.extension.kenburn.min.js"></script>
        <script type="text/javascript" src="revolution/js/extensions/revolution.extension.layeranimation.min.js"></script>
        <script type="text/javascript" src="revolution/js/extensions/revolution.extension.migration.min.js"></script>
        <script type="text/javascript" src="revolution/js/extensions/revolution.extension.navigation.min.js"></script>
        <script type="text/javascript" src="revolution/js/extensions/revolution.extension.parallax.min.js"></script>
        <script type="text/javascript" src="revolution/js/extensions/revolution.extension.slideanims.min.js"></script>
        <script type="text/javascript" src="revolution/js/extensions/revolution.extension.video.min.js"></script>-->
        <!-- setting -->
        <!-- <script type="text/javascript" src="{{ asset('/theme/js/main.js') }}"></script> -->

        @yield('scripts')

    </body>
</html>