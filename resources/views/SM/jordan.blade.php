<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>See Ya Bud!</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>

<meta name="viewport" content="width=device-width, user-scalable=1, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">

  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic|Josefin+Slab:400,700,700italic'>

  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet"> 

<style>
        /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
  body {
    background: white;
    font-family: 'Lato', sans-serif;
    font-size: 13px;
    -webkit-tap-highlight-color: transparent;
    color:#999;
  }

  body,
  html {
    height: 100vh;
    width:100vw;
  }

  h1,
  h2,
  h3,
  h4 {
    font-family: 'Lato', sans-serif;
  }

  h1 {
    font-style: italic;
    font-size: 22px;
  }

  h2 {
    font-size: 18px;
  }

  .noselect * {
    user-select: none;
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
    background: black;
  }
  #wipe h3 {
    color: white;
  }

  #second-wipe {
    height: 100%;
    overflow: hidden;
    position: absolute;
    top: 0;
    right: 0;
    transform: translateY(100%);
    width: 100%;
    background: #00a9ce;
    color:white;
  }

  #slide {
    height: 100%;
    position: absolute;
    top: -100%;
    width: 100%;
    background: #c7e1ff;
  }
  #slide h3 {
    position: absolute;
    width: 100%;
  }

  #slide-dos {
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    transform: translateX(-100%);
    width: 100%;
    background: black;
    color:maroon;
  }

  #unpin {
    height: 100%;
    position: absolute;
    top: 0;
    width: 100%;
    background: black;
    color:white;
  }

  .heart1{
    position: absolute;
  /*  top:150px;*/
    left:10px;
  }

  .heart2{
    position: absolute;
    bottom:100px;
    right:10px;
  }

  .heart1 img{
  width:150px;
  }
  .heart2 img{

    width:75px;
  }

  #wider {
    width: 100%;
    width: 100px;
    height: 50px;
    background-color: #56c78a;
    margin-top:130px;
/*    background-image: url("images/circle.png");
    background-repeat: repeat-x;
    background-position: center;
    background-size: contain;*/

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

#finger{

  background-image: url('images/finger.gif');
  background-position: center;
  background-size: contain;
  height:100%;
}

.cry{
  background-image: url('images/cry.gif');
  background-repeat: repeat;
  background-position: center;
  width:100%;

}

@media only screen 
  and (min-device-width: 375px) 
  and (max-device-width: 667px) 
  and (-webkit-min-device-pixel-ratio: 2) { 
    h3 {
      font-size: 40px !important;
    }

}



</style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>


</head>

<body>

  <div id="content-wrapper">

    <section id="pin" class="scroll-magic-section">
        
        <h3 id="hlpin">Fairwell Jordan</h3>

        <div id="wipe" class="cry">
            <div class="container cry">
                <h3>hate to see you go...</h3>
      <!--           <div class="flex-box">
                  <img src="{{ asset('images/cry.gif') }}" alt="gif" class='flex-item cry' /> 
                </div> -->
            </div>
        </div>

        <div id="second-wipe">
            <div class="container">
                <h3 style="color:white;">but good luck at your new job!        
                </h3>
            </div>
        </div>

    
      <div id="slide-dos">
            <div class="container">
                <h3>its been a slice..</h3>
            </div>
        </div>

        <div id="unpin">
            <div class="container" id="finger">
                <!-- <h3 style="color:white"></h3> -->
                <!-- <img src="{{ asset('images/finger.gif') }}" alt="gif" />  -->           
            </div>
        </div>

    </section>
</div>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/392/TweenMax.min.js'></script>
<script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/392/jquery.scrollmagic.min.js'></script>

  

    <script  src="{{ asset('SM/js/jordan_index.js') }}"></script>




</body>

</html>