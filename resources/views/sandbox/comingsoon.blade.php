
@extends('layouts.simple')

@section('content')

<link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet"> 
<link href="https://fonts.googleapis.com/css?family=Permanent+Marker" rel="stylesheet"> 

 <link rel="stylesheet" href="{{ asset('/css/custom.css') }}" />
<style>

	
/* ============================================================================================== 
Nick Mkrtchyan
https://www.facebook.com/sonick.pk
http://www.Mkrtchyan.zz.mu
================================================================================================= */
* {
    margin: 0;
    padding: 0;
}

html, body { height: 100%; width:100%; }
body {
    margin: 0;
    background: black;
}

header {
    background-color:rgba(33, 33, 33, 0.9);
    color:#ffffff;
    display:block;
/*    font: 14px/1.3 Arial,sans-serif;*/
    height:50px;
    position:relative;
    z-index:5;
}
h2{
    margin-top: 30px;
    text-align: center;
}
header h2{
    font-size: 22px;
    margin: 0 auto;
    padding: 10px 0;
    width: 80%;
    text-align: center;
}
header a, a:visited {
    text-decoration:none;
    color:#fcfcfc;
}

@keyframes move-twink-back {
    from {background-position:0 0;}
    to {background-position:-10000px 5000px;}
}
@-webkit-keyframes move-twink-back {
    from {background-position:0 0;}
    to {background-position:-10000px 5000px;}
}
@-moz-keyframes move-twink-back {
    from {background-position:0 0;}
    to {background-position:-10000px 5000px;}
}
@-ms-keyframes move-twink-back {
    from {background-position:0 0;}
    to {background-position:-10000px 5000px;}
}

@keyframes move-clouds-back {
    from {background-position:0 0;}
    to {background-position:10000px 0;}
}
@-webkit-keyframes move-clouds-back {
    from {background-position:0 0;}
    to {background-position:10000px 0;}
}
@-moz-keyframes move-clouds-back {
    from {background-position:0 0;}
    to {background-position:10000px 0;}
}
@-ms-keyframes move-clouds-back {
    from {background-position: 0;}
    to {background-position:10000px 0;}
}

.stars, .twinkling, .clouds {
  position:absolute;
  top:0;
  left:0;
  right:0;
  bottom:0;
  width:100%;
  height:100%;
  display:block;
}


.stars {
/*	background:transparent url('images/stars-sky-night.jpeg') no-repeat bottom center;*/
  background:#000 url(images/stars.png) repeat top center;
  z-index:0;
}

.twinkling{
/*	height:600px;*/
  background:transparent url(images/twinkling.png) repeat top center;
  z-index:1;

  -moz-animation:move-twink-back 200s linear infinite;
  -ms-animation:move-twink-back 200s linear infinite;
  -o-animation:move-twink-back 200s linear infinite;
  -webkit-animation:move-twink-back 200s linear infinite;
  animation:move-twink-back 200s linear infinite;
}

.clouds{
	background:transparent url(images/cloud.png) repeat-x top center;
   /* background:transparent url(http://www.script-tutorials.com/demos/360/images/clouds3.png) repeat-x top center;*/
    z-index:3;

  -moz-animation:move-clouds-back 200s linear infinite;
  -ms-animation:move-clouds-back 200s linear infinite;
  -o-animation:move-clouds-back 200s linear infinite;
  -webkit-animation:move-clouds-back 200s linear infinite;
  animation:move-clouds-back 200s linear infinite;
}


.moving-zone {
    perspective: 1000px;
    width:100%;
    height:100%;
    box-sizing: border-box;
    border-radius: 20px 0 20px 0;
}
.popup {
    width:auto;
    padding: 10px;
    box-sizing: border-box;
    border-radius: 20px 0 20px 0;
    cursor: pointer;
    transform-style: preserve-3d;
}


.flex-box {
  display: flex;
  align-items: center;
  justify-content: center;
  height:100%;
  width:100%;
}
.flex-item {
  position: relative;
}


.title{
	position: relative;
	height:100vh;
}

.house{
	color:white;
	background:transparent url('images/stars-sky-night.jpeg') no-repeat bottom center;
	height:1200px;
	width:100%;
}



.custom-button{
   color:coral;
   background-color: transparent;
   border:1px solid coral;
   transition: all 1s;
}
.custom-button:hover{
   background-color: transparent;
   color: white;
   border:1px solid white;
   color: rgba(255, 255, 255, 1);
   box-shadow: 0 8px 15px rgba(145, 92, 182, .4);
}

.button-cont{
  text-align: center;
  margin-top: 350px;
  z-index: 99999;
  color:white;
  font-size: 16px;
}


</style>

{{ app('debugbar')->disable() }}

<section class="title">

	<div class="stars"></div>
	<div class="twinkling">

		<div class="flex-box">
		  	<div class="flex-item">
				<div class="moving-zone">
				   <div class="popup">
                  <div class="wow zoomInDown">
     				 		<span class="titleName ">Matt Glover</span>
                  </div>
     					<br/>
                  <div class="wow zoomInDown" data-wow-delay="0.2s">
     						<span class="titleSub">A Builder of Things</span>
                  </div>  


				   </div>
				</div>

			</div>
		</div>

	</div>
	<div class="clouds">
            <div class="flex-box">
         <div class="flex-item">
                <div class="wow fadeIn button-cont" data-wow-delay="2s" data-wow-duration="6s">
                   <!--   <button type="button" class="btn btn-lg custom-button">Continue..</button> -->
                  coming soon...
            </div>
         </div>
      </div>


   </div>

</section>

<!-- <section class="house"> -->
<!-- 	<div class="about-me wow fadeInLeftBig" data-wow-offset="500" >
		
	</div> -->
<!-- 
</section> -->


@endsection

@section('scripts')

<script>

	var moveForce = 50; // max popup movement in pixels
	var rotateForce = 25; // max popup rotation in deg

   new WOW().init();

	$(document).mousemove(function(e) {
	    var docX = $(document).width();
	    var docY = $(document).height();
	    
	    var moveX = (e.pageX - docX/2) / (docX/2) * -moveForce;
	    var moveY = (e.pageY - docY/2) / (docY/2) * -moveForce;
	    
	    var rotateY = (e.pageX / docX * rotateForce*2) - rotateForce;
	    var rotateX = -((e.pageY / docY * rotateForce*2) - rotateForce);
	    
	    $('.popup')
	        .css('left', moveX+'px')
	        .css('top', moveY+'px')
	        .css('transform', 'rotateX('+rotateX+'deg) rotateY('+rotateY+'deg)');
	});







</script>


@endsection