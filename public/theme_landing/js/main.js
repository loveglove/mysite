'use strict'; 
$(window).load( function() {	
    

        // LIGHTBOX VIDEO
    $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
		disableOn: 700,
		type: 'iframe',
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: false,

		fixedContentPos: false
	});
        

//PRELOADER
if(document.referrer.split('/')[2]!=location.hostname){
    //User came from other domain or from direct

    /** Loader */
    var loader = $(".loader");
    var wHeight = $(window).height();
    var wWidth = $(window).width();
    var o = 0;

    loader.css({
        top: wHeight / 2 - 2.5,
        left: wWidth / 2 - 200
    })

    do {
        loader.animate({
            width: o
        }, 10)
        o += 3;
    } while (o <= 400)
    if (o === 402) {

        $(".loader").fadeOut('slow');

        // loader.animate({
        //     left: 0,
        //     width: '100%'
        // })
        // loader.animate({
        //     top: '0',
        //     height: '100vh'
        // })
        setTimeout(function() {
            $(".loader-image").fadeOut();
        },1000);
    }

    setTimeout(function() {
        $(".loader-wrapper").fadeOut('slow');
        // (loader).fadeOut('slow');
    }, 2000);

}else{
    $(".loader-wrapper").hide();
}
    

if ($('.isotope_items').length) {

    // PORTFOLIO ISOTOPE
     var $container = $('.isotope_items');
     $container.isotope();

    $('.portfolio_filter ul li').on("click", function(){
        $(".portfolio_filter ul li").removeClass("select-cat");
        $(this).addClass("select-cat");				 
        var selector = $(this).attr('data-filter');
        $(".isotope_items").isotope({
            filter: selector,
            animationOptions: {
                duration: 750,
                easing: 'linear',
                queue: false,
            }
    });
        return false;
    });  
    
}
    
}); // window load end 



$(document).ready( function() {	
    
    
    // WOW JS
    new WOW({ mobile: true }).init();
    
    
      
    //SMOOTH SCROLL
    $(document).on("scroll", onScroll);
    $('a[href^="#"]').on('click', function (e) {
        e.preventDefault();
        $(document).off("scroll");
        
        $('a').each(function () {
            $(this).removeClass('active');
             if ($(window).width() < 768) {
                 $('.nav-menu').slideUp();
             }
        });
            
        $(this).addClass('active');
      
        var target = this.hash,
        //menu = target;
        target = $(target);
        $('html, body').stop().animate({
            'scrollTop': target.offset().top-85
        }, 500, 'swing', function () {
            window.location.hash = target.selector;
            $(document).on("scroll", onScroll);
        });
    });
    
        
        function onScroll(event){
          if ($('#home').length) {     
    var scrollPos = $(document).scrollTop();
    $('nav ul li a').each(function () {
        var currLink = $(this);
        var refElement = $(currLink.attr("href"));
        if (refElement.position().top-90 <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
            $('nav ul li a').removeClass("active");
            currLink.addClass("active");
        }
        else{
            currLink.removeClass("active");
        }
    });
   }              
}
    
    
    //NAVBAR SHOW - HIDE
    $(window).scroll(function() {				
    var scroll = $(window).scrollTop();
    var homeheight = $(".home").height() -86;			

    if (scroll > homeheight ) {												
        $("nav").slideDown(100);
        } else {
        $("nav").slideUp(100);
        }
     }); 
    
    	
 // RESPONSIVE MENU
$('.responsive').on('click', function (e) {
        $('.nav-menu').slideToggle();
    });
    
    
    // HOME PAGE HEIGHT
     function centerInit() {
        var hometext = $('.home')

        hometext.css({
            "height": $(window).height() + "px"
        });
    }
    centerInit();
    $(window).resize(centerInit);
    
    
    // HOME TYPED JS
      $(".element").typed({
        strings: ["...", "a Developer", "an Engineer", "a Problem Solver", "Matt Glover"],
        typeSpeed: 6,
        loop:false,
        backDelay: 2000
      });
 
    
    
    // MAGNIFIC POPUP FOR PORTFOLIO PAGE
    $('.link').magnificPopup({
        type:'image',
        gallery:{enabled:true},
        zoom:{enabled: true, duration: 300}
    });
    
       // OWL CAROUSEL GENERAL JS
    // var owlcar = $('.owl-carousel');
    // if (owlcar.length) {
    //     owlcar.each(function () {
    //         var $owl = $(this);
    //         var itemsData = $owl.data('items');
    //         var autoPlayData = $owl.data('autoplay');
    //         var paginationData = $owl.data('pagination');
    //         var navigationData = $owl.data('navigation');
    //         var stopOnHoverData = $owl.data('stop-on-hover');
    //         var itemsDesktopData = $owl.data('items-desktop');
    //         var itemsDesktopSmallData = $owl.data('items-desktop-small');
    //         var itemsTabletData = $owl.data('items-tablet');
    //         var itemsTabletSmallData = $owl.data('items-tablet-small');
    //         $owl.owlCarousel({
    //             items: itemsData
    //             , pagination: paginationData
    //             , navigation: navigationData
    //             , margin:10
    //             , center:true
    //             , stagePadding: 50
    //             , autoPlay: autoPlayData
    //             , stopOnHover: stopOnHoverData
    //             // , navigationText: ["<", ">"]
    //             , itemsCustom: [
    //                 [0, 1]
    //                 , [500, itemsTabletSmallData]
    //                 , [710, itemsTabletData]
    //                 , [992, itemsDesktopSmallData]
    //                 , [1199, itemsDesktopData]
    //             ]
    //         , });
    //     });
    // }
    
    
}); // document ready end 


