<!doctype html>
<html lang="en">
<head>
<title>Matt Glover - A Builder Of Things</title>
<meta charset="UTF-8">
<meta name="description" content="Matt Glover">
<meta name="keywords" content="personal, portfolio">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- CSRF Token -->
<meta name="_token" content="{!! csrf_token() !!}" />

<!-- Favicon -->   
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />

<!-- Stylesheets -->
<link rel="stylesheet" href="{{ asset('/theme_landing/css/bootstrap.css') }}"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<link rel="stylesheet" href="{{ asset('/theme_landing/css/reset.css') }}"/>
<link rel="stylesheet" href="{{ asset('/theme_landing/css/style.css') }}"/>
<link rel="stylesheet" href="{{ asset('/theme_landing/css/animate.css') }}"/>
<link rel="stylesheet" href="{{ asset('/theme_landing/css/owl.carousel.css') }}"/> 
<link rel="stylesheet" href="{{ asset('/theme_landing/css/magnific-popup.css') }}"/> 
    
<!-- Google Web fonts -->
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

<!-- Font icons -->
<link rel="stylesheet" href="{{ asset('/theme_theme_landing/icon-fonts/essential-regular-fonts/essential-icons.css') }}"/>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/themify-icons/0.1.2/css/themify-icons.css" rel="stylesheet">
<link rel="stylesheet" href="//cdn.materialdesignicons.com/2.4.85/css/materialdesignicons.min.css">

<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>
<body class="diag">
    
<style>
    .pointer{
        cursor: pointer !important;
    }
    .tilt {
        box-shadow: 0 20px 27px rgba(0, 0, 0, 0.05);
        transform-style: preserve-3d;
        margin: 50px auto;
    }
    .tilt-logo {
        width: 250px;
        height: 75px;
        background: #00B4DB;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to left, #0083B0, #00B4DB);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to left, #0083B0, #00B4DB); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

    }
    .tilt-logo-inner {
        transform: translateZ(50px) translateY(-50%) translateX(-50%);
        position: absolute;
        top: 50%;
        left: 50%;
        color: white;
        text-align: center;
        font-size: 0.9rem;
    }
</style>

<!-- LOADER -->
<!-- <div class="loader-wrapper">
    <div class="loader"></div>
</div>
     -->
    <nav>
        <div class="row" >
            <div class="container-fluid">
                <div class="logo">
                    <img src="{{ asset('images/problem_icon.png') }}" height="55"  alt="">
                </div>
                <div class="logo_text">
                    <span>Matt Glover</span>
                </div>
                <div class="responsive"><i class="ti-menu"></i></div>
                <ul class="nav-menu" style="margin-top: 13px">
                    <li><a href="#home" class="smoothScroll">Home</a></li>
                    <li><a href="#about" class="smoothScroll">About</a></li>
                    <li><a href="#portfolio" class="smoothScroll">Projects</a></li>
                    <li><a href="#blog" class="smoothScroll">Experience</a></li>
                    <li><a href="#contact" class="smoothScroll">Contact</a></li>
                    <li><a href="/login" class="smoothScroll">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!--HOME-->
    <section class="home" id="home"> 
            <div class="home-content">
                <div class="container">
                    <h1>I am <span class="element">Matt Glover</span></h1>
                    <div class="header-icons">
                        <i class="ti-light-bulb" aria-hidden="true" style="margin-left:0px;"></i>
                        <i class="ti-pulse" aria-hidden="true"></i>
                        <i class="ti-music-alt" aria-hidden="true"></i>
                        <i class="ti-infinite" aria-hidden="true"></i>
                        <a href="#about"><i class="fa fa-angle-down bounce"></i></a>
                    </div>
                </div>
            </div>
        <svg class="diagonal home-left" width="21%" height="170" viewBox="0 0 100 102" preserveAspectRatio="none">
            <path d="M0 100 L100 100 L0 10 Z"></path>
        </svg>
        <svg class="diagonal home-right" width="80%" height="170" viewBox="0 0 100 102" preserveAspectRatio="none">
            <path d="M0 100 L100 100 L100 10 Z"></path>
        </svg>
    </section>
    <!-- END HOME -->

    <!--ABOUT-->
    <section class="about dgray-bg pt-3 pt-md-2" id="about">
        <div class="about type-1">
            <div class="container">
      

                    <!-- about text -->
                    <div class="col-md-offset-2 col-md-push-4 col-md-6 col-sm-12 about-text wow fadeInUp"  data-wow-delay="0.6s">
                        <div class="section-title dleft bottom_30">
                            <h2>ABOUT ME</h2>
                        </div>
                        <p>I've always been fascinated with <b>invention</b>. Since I was young I had an underlying need to create and build things to serve me a purpose. I love engineering things to solve a problem or to automate a process in my life that I might find repetative, mundane, or could just be done better! No matter what the subject matter, the process of solving that problem and building the tools to get the job done will always be one of my favourite activities.
                        <br><br>
                            Technology, Engineering, and Music have been the focus of my life, you can find some of my work here.
                        </p>
                    </div>

                    <!-- about image -->
                    <div class="col-md-pull-7 p-5 p-md-0 col-md-4 col-sm-12 about-image wow fadeInUp" data-wow-delay="0.4s">
                        <div class="row">
                            <img id="profile-img" src="{{ asset('images/headshots/headshot1.JPG') }}" class="img-fluid mb-0 mb-md-5" alt="profile_photo">
                        </div>
                    </div>


                <!-- work areas -->
                <div class="work-areas mt-2 mt-md-5 bottom_170 wow fadeInUp" data-wow-delay="0.6s">
                    <!-- an area -->
                    <div class="row">
                        <div class="area col-md-4 col-sm-12 col-sm-12">
                            <div class="icon ">
                                <i class="ti-light-bulb"></i>
                            </div>
                            <div class="text">
                                <h6>Web Design</h6> 
                                <p>My bread and butter is web application design & development. I absolutely love to build web apps and deliver solutions to clients.</p>
                            </div>
                        </div>
                        <!-- an area -->
                        <div class="area col-md-4 col-sm-12 mt-4 mt-md-0">
                            <div class="icon">
                                <i class="ti-pulse"></i>
                            </div>
                            <div class="text">
                                <h6>Electronics</h6>
                                <p>My background is primarily in electronics, I live experimenting simple circuits. I have a few interesting projects you can find here.</p>
                            </div>
                        </div>
                        <!-- an area -->
                        <div class="area col-md-4 col-sm-12 mt-4 mt-md-0">
                            <div class="icon"> 
                                <i class="ti-music-alt"></i>
                            </div>
                            <div class="text">
                                <h6>Music</h6>
                                <p>Electronic music is a passion of mine, I have been a DJ for over 10 years and have produced several songs. Check them out!</p>
                            </div>
                        </div>
                    </div>
 
                </div>

            </div>
        <svg class="diagonal-gray" width="100%" height="170" viewBox="0 0 100 102" preserveAspectRatio="none">
            <path d="M0 0 L70 100 L100 0 Z"></path>
        </svg>
        </div>
    </section>
    
    <!--PORTFOLIO-->
    <section class="portfolio" id="portfolio">
        <div class="container">
            <div class="section-title dleft top_90 bottom_90">
                <h2>PROJECTS</h2>
                <div class="portfolio_filter">
                    <ul>
                        <li class="select-cat" data-filter="*">All</li>
                        <li data-filter=".web-design">Web Design</li>
                        <li data-filter=".aplication">Applications</li>
                        <li data-filter=".development">Development</li>
                        <li data-filter=".music">Music</li>
                    </ul>
                </div>
            </div>
            <!--Portfolio Items-->  
            <div class="row">
                <!-- Start Carousel -->
                <div class="owl-carousel work-areas mt-2 mt-md-5 bottom_170 wow fadeInUp" data-pagination="false" data-autoplay="3000" data-items-desktop="3" data-items-desktop-small="3" data-items-tablet="2" data-items-tablet-small="1"  data-wow-delay="0.6s">
                    <!-- an area -->
                    <div class="area col-md-12 item">
                        <div class="icon">
                            <i class="ti-light-bulb"></i>
                        </div>
                        <div class="text">
                            <h6>Web Design</h6>
                            <p>My bread and butter is web application design & development. I absolutely love to build web apps and deliver solutions to clients.</p>
                        </div>
                    </div>
                    <!-- an area -->
                    <div class="area col-md-12 item">
                        <div class="icon">
                            <i class="ti-pulse"></i>
                        </div>
                        <div class="text">
                            <h6>Electronics</h6>
                            <p>My background is primarily in electronics, I live experimenting simple circuits. I have a few interesting projects you can find here.</p>
                        </div>
                    </div>
                    <!-- an area -->
                    <div class="area col-md-12 item">
                        <div class="icon">
                            <i class="ti-music-alt"></i>
                        </div>
                        <div class="text">
                            <h6>Music</h6>
                            <p>Electronic music is a passion of mine, I have DJ for over 10 years and have produced several songs. Check them out!. </p>
                        </div>
                    </div>

 
                </div>

            </div>
        </div>
        <svg class="diagonal-white" width="100%" height="170" viewBox="0 0 100 102" preserveAspectRatio="none">
            <path d="M0 0 L30 100 L100 0 Z"></path>
        </svg>
    </section>
    
    <!-- BLOG -->
    <section class="blog" id="blog">
        <div class="container-fluid dgray-bg">
            <div class="container">

                    <div class="section-title dleft top_90 mb-4">
                        <h2>PROFESSIONAL EXPERIENCE</h2>
                    </div>

                    <div class="row">
                        <!-- experience text -->
                        <div class="col-md-push-5 col-md-7 col-sm-12 prof-text wow fadeInUp mt-0 mt-md-2"  data-wow-delay="0.6s">
                            <p>I have worked in several technology sectors over the course of my career. I started out working various jobs for family and friends, I went to college then eventually landed my first corporate gig. After I learned many tricks of the trade working for an international corporation, I eventually migrated to the start up world where I currently reside today. My current position is head quartered in Toronto Ontario.</p>
                        </div>

                        <!-- experience button -->
                        <div class="col-md-pull-7 col-md-5 col-sm-12 prof-image wow fadeInUp"  data-wow-delay="0.4s">
                            <div class="row ml-4 mt-0 mb-3">
                                <a href="/downloads/MATT_GLOVER_CV_2019.pdf" download> 
                                    <div class="tilt tilt-logo pointer" data-tilt data-tilt-glare="true" data-tilt-scale="1.1">
                                        <span class="tilt-logo-inner">
                                            Download my CV <i style="font-size: 20px;" class="ti-cloud-down bounce"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>

            </div>
            <svg class="diagonal-gray" width="100%" height="170" viewBox="0 0 100 102" preserveAspectRatio="none">
                <path d="M0 0 L70 100 L100 0 Z"></path>
            </svg>
        </div>
    </section>
    
    <!-- CONTACT -->
    <section class="contact" id="contact">
        <div class="container">
            
            <div class="section-title text-center text-md-left pb-0">
                <h2 class="bottom_30">GET IN TOUCH</h2>
                <p>Need help designing a product, or turning an idea into a reality? Give me a shout, I can help you get started!</p>
                <p><b>mattjglover@hotmail.com</b></p>
            </div>
            <div class="col-md-3 wow fadeInUp pl-2 pl-md-0" data-wow-delay="0.3s">
                <div class="social-icons text-center text-md-left"> 
                    <a class="fb" href="https://www.facebook.com/profile.php?id=500822307"><i class="ti-facebook" aria-hidden="true"></i></a>
                    <a class="ins" href="https://www.instagram.com/mj_glover/"><i class="ti-instagram" aria-hidden="true"></i></a> 
                    <a class="yt" href="https://www.youtube.com/channel/UCCnq48yvAm2sojCV55PDuPA"><i class="ti-youtube" aria-hidden="true"></i></a>
                    <a class="gh" href="https://github.com/loveglove"><i class="ti-github" aria-hidden="true"></i></a> 
                    <a class="pt" href="https://www.pinterest.ca/mattjglover0187/"><i class="ti-pinterest" aria-hidden="true"></i></a> 
                    
                </div>
            </div>
            <div class="col-md-7 col-md-offset-2 form bottom_90 wow fadeInUp" data-wow-delay="0.6s">
                <div class="page-title sub text-center text-md-right">
                    <h5>leave me a message</h5>
                </div>
                <form id="contact-form" class="contact-form top_60">
                    <div class="row">
                        <!--Name-->
                        <div class="col-md-6">
                            <input id="con_name" name="con_name" class="form-inp requie" type="text" placeholder="Name">
                        </div>
                        <!--Email-->
                        <div class="col-md-6">
                            <input id="con_email" name="con_email" class="form-inp requie" type="text" placeholder="Email">
                        </div>
                        <div class="col-md-12">
                            <!--Message-->
                            <textarea name="con_message" id="con_message" class="requie" placeholder="How can I help you?" rows="8"></textarea>
                            
                            <div class="g-recaptcha" data-sitekey="6LcSoJ8UAAAAAHu9DFvKD-_zZrupP4NmC_9h3nf9"></div>

                            <button id="con_submit" class="sitebtn mt-3" type="submit">Send Message <i class="fa fa-paper-plane pl-2"></i></button>
                        </div>
                    </div>
                </form>
                <div class="mt-3 mt-md-4 col-lg-12 col-md-12 mx-auto">
                    <div id="mailresponse" class="alert alert-primary" role="alert" style="display:none"></div>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <p>Copyright © 2019 Matt Glover</p>
        </div>
    </footer>
    

<!--             <div class="owl-carousel work-areas mt-2 mt-md-5 bottom_170 wow fadeInUp" data-pagination="false" data-autoplay="3000" data-items-desktop="3" data-items-desktop-small="3" data-items-tablet="2" data-items-tablet-small="1"  data-wow-delay="0.6s">
                    <div class="area col-md-12 item">
                        <div class="icon">
                            <i class="ti-light-bulb"></i>
                        </div>
                        <div class="text">
                            <h6>Web Design</h6>
                            <p>My bread and butter is web application design & development. I absolutely love to build web apps and deliver solutions to clients.</p>
                        </div>
                    </div>
                    <div class="area col-md-12 item">
                        <div class="icon">
                            <i class="ti-pulse"></i>
                        </div>
                        <div class="text">
                            <h6>Electronics</h6>
                            <p>My background is primarily in electronics, I live experimenting simple circuits. I have a few interesting projects you can find here.</p>
                        </div>
                    </div>
                    <div class="area col-md-12 item">
                        <div class="icon">
                            <i class="ti-music-alt"></i>
                        </div>
                        <div class="text">
                            <h6>Music</h6>
                            <p>Electronic music is a passion of mine, I have DJ for over 10 years and have produced several songs. Check them out!. </p>
                        </div>
                    </div>
                </div>


                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="isotope_items row">

                        <a href="single-blog.html" class="single_item link aplication col-lg-4 col-md-4 col-sm-6 col-xs-12 blog-content wow fadeInUp" data-wow-delay="0.4s">
                            <div class="blog-image">
                                <iframe width="100%" height="180" src="https://www.youtube.com/embed/heVuNAlaSww" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                            <h2 class="blog-title">My LED Wall - 6 x 8 feet</h2>
                            <p>Composed of four modular panels, The LEDs communicate via DMX protocol and are controlled by a touch screen. </p>
                        </a>
    
                        <a href="single-blog.html" class="single_item link development col-lg-4 col-md-4 col-sm-6 col-xs-12 blog-content wow fadeInUp" data-wow-delay="0.4s">
                            <div class="blog-image">
                                <img src="images/fc_banner.jpg" height="180">
                            </div>
                            <h2 class="blog-title">Fisher Classic - Golf Tournament App</h2>
                            <p>A handy web app built to organize large golf tournaments with friends. The app has several usefull features for large groups of golfers.</p>
                        </a>
                        <a href="single-blog.html" class="single_item link engineering col-lg-4 col-md-4 col-sm-6 col-xs-12 blog-content wow fadeInUp" data-wow-delay="0.4s">
                            <div class="blog-image">
                                <img src="images/ableton_banner.png" height="180">
                            </div>
                            <h2 class="blog-title">My Music Production</h2>
                            <p>Some of my electronic and house music production from back in the day. Some remixes and origial releases.  </p>
                        </a>
                    </div>
                </div> -->

                

<!-- Javascripts -->
<script src="{{ asset('/theme_landing/js/jquery-2.1.4.min.js') }}"></script><!-- jQuery library -->
<!-- <script src="{{ asset('/theme_landing/js/bootstrap.min.js') }}"></script>  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script src="{{ asset('/theme_landing/js/wow.min.js') }}"></script>
<script src="{{ asset('/theme_landing/js/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('/theme_landing/js/typed.js') }}"></script>
<script src="{{ asset('/theme_landing/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('/theme_landing/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('/theme_landing/js/main.js') }}"></script>
    
    
<script src="https://unpkg.com/tilt.js@1.1.21/dest/tilt.jquery.min.js"></script>


<script>

    /*
     * AJAX Setup
     */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        },
        type: "POST",
        dataType: "json"
    });

    $(document).ready(function(){


        $('#contact-form').submit(function(e){

            e.preventDefault();

            $('#mailresponse').removeClass("alert-success").removeClass("alert-danger");
            $('#mailresponse').addClass("alert-primary").html("Sending your message...").show();

            $.ajax({
                url: '/contact', 
                data: $(this).serialize(),
            })
            .done(function(data){
                if(data.success){
                    setTimeout(function(){
                        $('#mailresponse').addClass("alert-success").html("Message sent!").show();
                    },1300);
                }else{
                    $('#mailresponse').addClass("alert-danger").html(data.message).show();  
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                $('#mailresponse').addClass("alert-danger").html("Mail error :(").show(); 
            });

            // hide alert after some time
            setTimeout(function(){
                $('#mailresponse').fadeOut();
            },8000)
            // to prevent refreshing the whole page page
         
        });
    });


</script> 



</body>
</html>