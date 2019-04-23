<!DOCTYPE html>
<html lang="en" >

<head>
   <meta charset="UTF-8">
   <title>Matt Glover</title>
   <meta name="viewport" content="width=device-width, user-scalable=1, initial-scale=1">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
   <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic|Josefin+Slab:400,700,700italic'>

<style>
        /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
body {
   background: transparent;
   font-family: "Source Sans Pro", Arial, sans-serif;
   font-size: 13px;
   -webkit-tap-highlight-color: transparent;
   -webkit-font-smoothing: antialiased;
}

body,
html {
   height: 100vh;
   width: 100vw;
}

h1,
h2,
h3,
h4 {
   font-family: "Josefin Slab", serif;
   font-weight: bold;
}
h1 {
   font-style: italic;
   font-weight: bold;
   font-size: 22px;
}
h2 { font-size: 18px; }

.noselect * {
   user-select: none;
}
.ScrollSceneIndicators {
   z-index: 9999999;
}

.container {
   height: 100%;
   margin: 0 auto;
   max-width: 900px;
   position: relative;
   width: 100%;
}

#content-wrapper {
   height: 100%;
   min-height: 500px;
   width: 100%;
}

.scroll-magic-section {
   height: 100%;
}

#pin {
   overflow: hidden;
   width: 100%;
}

#pin h3 {
   margin: -40px 0 0 0;
   position: relative;
   top: 50%;
   font-size: 80px;
   line-height: 80px;
   text-align: center;
}

#wipe {
   height: 100%;
   overflow: hidden;
   position: absolute;
   top: 0;
   right: 0;
   transform: translateY(100%);
   width: 100%;
   background: linear-gradient(tomato, crimson);
}
#wipe h3 {
   color: white;
}

/* PROJECTS SECTION */

#list {
 /*  padding: 200px 0 1500px 0;*/
   overflow: hidden;

}

#projects {
   position: absolute;
   top: 0;
   right: 0;
   transform: translateY(100%);
   width: 100%;
   min-height: 300vh;
   background: #ebebeb;
   -webkit-transition: all 400ms ease;
   -moz-transition: all 400ms ease;
   transition: all 400ms ease;
}


/* Create huge scrolling area (imitates height of a page) */

.wrap {
   text-align: center;
   background: #fff;
   padding: 10px 0;
   -moz-box-shadow: 0 0 6px rgba(153, 153, 153, 0.25);
   -webkit-box-shadow: 0 0 6px rgba(153, 153, 153, 0.25);
   box-shadow: 0 0 6px rgba(153, 153, 153, 0.25);
   position: relative;
   margin-top: 200px;
   margin-bottom: 200px;
   overflow: hidden;
}

.wrap h2{
   font-size: 42px;
   padding:0px;
}

/* Default Animation Styles */
.animation {
   background: #000;
   width: 300px;
   height: 300px;
   border-radius: 100%;
   margin: 50px auto;
   position: relative;
}
.animation i {
   color: #fff;
   position: absolute;
   left: 50%;
   top: 50%;
   font-size: 100px;
   -webkit-transform: translate(-50%, -50%);
   -moz-transform: translate(-50%, -50%);
   -ms-transform: translate(-50%, -50%);
   -o-transform: translate(-50%, -50%);
   transform: translate(-50%, -50%);
}


/* SCENE COLORS */

#projects.scene-1-active {
   background: rgb(180, 30, 50) !important;
}

#projects.scene-2-active {
   background: rgb(235, 50, 0) !important;
}

#projects.scene-3-active {
   background: rgb(255, 150, 46) !important;
}

#projects.scene-4-active {
   background: rgb(10, 200, 60) !important;
}
/*.wrap h2{
   display:none;
}
.title-active{
   display:inline-block;
}*/

.title-name{
   position: absolute;
   top: 50%;
   width:100%;
   z-index: 2;
}

.title-name h3{
      font-size: 150px !important;
}

</style>


</head>

<body>
{{ app('debugbar')->disable() }}
 
  <div id="content-wrapper">

      <section id="pin" class="scroll-magic-section">
         <div class="title-name"><h3>Matt Glover</h3></div>
         <div id="hlpin" style="position: relative;"> 
            <canvas id="surface" style="z-index: 0;"/>
            
         </div>

         


         <div id="wipe" style="z-index: 999;">
            <div class="container">
                <h3>About me</h3>
            </div>
         </div>

      </section>

      <section id="list">
         <div id="projects">

            <div class="container">
               <h1> Here is some of the things i do...</h1>

               <div class="row">
                  <div class="col-md-6 col-md-offset-1">
                     <div class="wrap" id="scene-1">
                        <div id="scene-1-title"><h2>Experience</h2></div>
                        <div class="animation" id="animation-1"><i class="fa fa-briefcase "></i></div>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6 col-md-offset-2">
                     <div class="wrap" id="scene-2">
                        <div id="scene-2-title"><h2>Projects</h2></div>
                        <div class="animation" id="animation-2"><i class="fa fa-code "></i></div>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6 col-md-offset-3">
                     <div class="wrap" id="scene-3">
                        <div id="scene-3-title"><h2>Music</h2></div>
                        <div class="animation" id="animation-3"><i class="fa fa-music "></i></div>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6 col-md-offset-4">
                     <div class="wrap" id="scene-4">
                        <div id="scene-4-title"><h2>Thoughts</h2></div>
                        <div class="animation" id="animation-4"><i class="fa fa-lightbulb-o "></i></div>
                     </div>
                  </div>
               </div>
            </div>

         </div>
      </section>

   </div>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/1.14.2/TweenMax.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/1.3.0/jquery.scrollmagic.js'></script>



<script  src="{{ asset('js/scrollmain.js') }}"></script>

<script>
   
var NUMBER_OF_LAYERS = 6;
var MAX_SQ_SIZE = 10;
var MIN_SQ_SIZE = 40;
var squares = 60;
var winW, winH;
var lastMouse, lastOrientation;
var animationId;

function init() {
    lastOrientation = {};
    window.addEventListener('resize', doLayout, false);
    document.body.addEventListener('mousemove', onMouseMove, false);
    if ('webkitRequestFullScreen' in document.body) {
        document.addEventListener('webkitfullscreenchange', onFullscreenChange, false);
    } else {
        id('fullscreenButton').style.display = 'none';
    }
    if ('ondeviceorientation' in window) {
        window.addEventListener('deviceorientation', deviceOrientationTest, false);
    }
    lastMouse = null;
    doLayout(document);
}

// Does the gyroscope or accelerometer actually work?
function deviceOrientationTest(event) {
    window.removeEventListener('deviceorientation', deviceOrientationTest);
    if (event.beta != null && event.gamma != null) {
        window.addEventListener('deviceorientation', onDeviceOrientationChange, false);
        animationId = setInterval(onRenderUpdate, 10); 
    }
}

function doLayout(event) {
    winW = window.innerWidth;
    winH = window.innerHeight;
    var surface = id('surface');
    surface.width = winW;
    surface.height = winH;
    squares = new Array(); 
    var alphaStep = (.9 / NUMBER_OF_LAYERS);
    for (var i = 0; i < NUMBER_OF_LAYERS; ++i) {
        squares.push(new Array());
        var alpha = (i * alphaStep) + .1;
        for (var j = 0; j <= ((NUMBER_OF_LAYERS - 1) - i); ++j) {
            var size = getRandomWholeNumber(winW/MIN_SQ_SIZE, winW/MAX_SQ_SIZE);
            var sq = {size:size,
                      x:getRandomWholeNumber(0, winW-size),
                      y:getRandomWholeNumber(0, winH-size),
                      color:'rgba('+getRandomWholeNumber(0, 255)+', '+getRandomWholeNumber(0, 255)+', '+getRandomWholeNumber(0, 255)+', '+ alpha +')'};
            squares[i][j] = sq;
        }
    }
    renderSquares();
}

function renderSquares() {
    var surface = id('surface');
    var context = surface.getContext('2d');
    context.clearRect(0, 0, surface.width, surface.height);

    // Cool, but too expensive
    
    context.shadowOffsetX = 2;
    context.shadowOffsetY = 2;
    context.shadowBlur = 5;
    context.shadowColor = '#555';
    

    var iMax = squares.length;
    for (var i = 0; i < iMax; ++i) {
        var jMax = squares[i].length;
        for (var j = 0; j < jMax; ++j) {
            var sq = squares[i][j];
            context.fillStyle = sq.color;
            context.fillRect(sq.x, sq.y, sq.size, sq.size);
        }
    }
}

function onMouseMove(event) {
    var xDelta, yDelta;
    if (navigator.webkitPointer && navigator.webkitPointer.isLocked) {
        xDelta = event.webkitMovementX;
        yDelta = event.webkitMovementY;
    } else {
        if (lastMouse == null) lastMouse = {x:event.clientX, y:event.clientY};
        xDelta = event.clientX - lastMouse.x;
        yDelta = event.clientY - lastMouse.y;
    }
    moveSquares(xDelta, yDelta);
    lastMouse.x = event.clientX;
    lastMouse.y = event.clientY;
}

function moveSquares(xDelta, yDelta) {
    var iMax = squares.length;
    for (var i = 0; i < iMax; ++i) {
        var jMax = squares[i].length;
        for (var j = 0; j < jMax; ++j) {
            var sq = squares[i][j];
            sq.x += (xDelta / (iMax - (i)));
            sq.y += (yDelta / (iMax - (i)));
        }
    }
    renderSquares();
}

function onDeviceOrientationChange(event) {
    lastOrientation.gamma = event.gamma;
    lastOrientation.beta = event.beta;
}

function onRenderUpdate(event) {
    var xDelta, yDelta;
    switch (window.orientation) {
        case 0:
            xDelta = lastOrientation.gamma;
            yDelta = lastOrientation.beta;
            break;
        case 180:
            xDelta = lastOrientation.gamma * -1;
            yDelta = lastOrientation.beta * -1;
            break;
        case 90:
            xDelta = lastOrientation.beta;
            yDelta = lastOrientation.gamma * -1;
            break;
        case -90:
            xDelta = lastOrientation.beta * -1;
            yDelta = lastOrientation.gamma;
            break;
        default:
            xDelta = lastOrientation.gamma;
            yDelta = lastOrientation.beta;
    }
    moveSquares(xDelta, yDelta);
}

function onFullscreen(event) {
    document.body.webkitRequestFullScreen();
}

function onFullscreenChange(event) {
    if (document.webkitIsFullScreen) {
        id('fullscreenButton').style.display = 'none';
        if (navigator.webkitPointer) navigator.webkitPointer.lock(document.body);
    } else {
        if (navigator.webkitPointer) navigator.webkitPointer.unlock();
        id('fullscreenButton').style.display = 'block';
    }
}

function id(name) { return document.getElementById(name); };
function getRandomWholeNumber(min, max) { return Math.round(((Math.random() * (max - min)) + min)); };
function getRandomHex() { return (Math.round(Math.random() * 0xFFFFFF)).toString(16); };

window.onload = init;

doLayout();

</script>


</body>

</html>
