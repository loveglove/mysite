<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Matt Glover :: Applications</title>

    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('/theme_app/assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('/theme_app/horizontal/css/style.css') }}" rel="stylesheet">
    <!-- You can change the theme colors from here -->
    <link href="{{ asset('/theme_app/horizontal/css/colors/blue.css') }}" id="theme" rel="stylesheet">


</head>
<body class="fix-header card-no-border logo-center">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" style="background-color: #fff;">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">
                        <!-- Logo icon -->
                        <b>
                            <!-- Light Logo icon -->
                            <img src="{{ asset('images/bulb_logo.png') }}" height="48" alt="homepage" class="light-logo" />
                        </b>
                        <!--End Logo icon -->
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto mt-md-0">
                        <!-- This is  -->
<!--                         <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li> -->
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
      <!--                   <li class="nav-item hidden-sm-down search-box">
                            <a class="nav-link hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-search"></i></a>
                            <form class="app-search">
                                <input type="text" class="form-control" placeholder="Search & enter"> <a class="srh-btn"><i class="ti-close"></i></a>
                            </form>
                        </li> -->

                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ti ti-user"></i></a>
                            <div class="dropdown-menu dropdown-menu-right scale-up">
                                <ul class="dropdown-user">
                                    <li>
                                        <div class="dw-user-box">
                                            <div class="u-img"><img src="{{ Auth::user()->avatar }}" alt="user"></div>
                                            <div class="u-text">
                                                <h4>{{ Auth::user()->name }}</h4>
                                                <p class="text-muted">{{ Auth::user()->email }}</p><a href="profile.html" class="btn btn-rounded btn-danger btn-sm">View Profile</a></div>
                                        </div>
                                    </li>
 <!--                                    <li role="separator" class="divider"></li>
                                    <li><a href="#"><i class="ti-user"></i> My Profile</a></li>
                                    <li><a href="#"><i class="ti-wallet"></i> My Balance</a></li>
                                    <li><a href="#"><i class="ti-email"></i> Inbox</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="#"><i class="ti-settings"></i> Account Setting</a></li> -->
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="fa fa-power-off"></i> Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>

                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">

                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                @yield('content')


                <!-- ============================================================== -->
                <!-- End Page Content -->
                <!-- ============================================================== -->

            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
<!--             <footer class="footer">
                © 2017 Material Pro Admin by wrappixel.com
            </footer> -->
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('/theme_app/assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('/theme_app/assets/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('/theme_app/assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('/theme_app/horizontal/js/jquery.slimscroll.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('/theme_app/horizontal/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('/theme_app/horizontal/js/sidebarmenu.js') }}"></script>
    <!--stickey kit -->
    <script src="{{ asset('/theme_app/assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('/theme_app/assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('/theme_app/horizontal/js/custom.min.js') }}"></script>
    <!-- ============================================================== -->
    <!-- Style switcher -->
    <!-- ============================================================== -->
<!--     <script src="{{ asset('/theme_app/assets/plugins/styleswitcher/jQuery.style.switcher.js') }}"></script> -->

	@yield('scripts')

</body>

</html>
