<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

    <title>Compass</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- FAVICON -->
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="favicon/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="favicon/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="favicon/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="favicon/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="favicon/apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="favicon/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="favicon/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="favicon/apple-touch-icon-152x152.png" />
    <link rel="icon" type="image/png" href="favicon/favicon-196x196.png" sizes="196x196" />
    <link rel="icon" type="image/png" href="favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/png" href="favicon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="favicon/favicon-16x16.png" sizes="16x16" />
    <link rel="icon" type="image/png" href="favicon/favicon-128.png" sizes="128x128" />
    <meta name="application-name" content="&nbsp;"/>
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="mstile-144x144.png" />
    <meta name="msapplication-square70x70logo" content="mstile-70x70.png" />
    <meta name="msapplication-square150x150logo" content="mstile-150x150.png" />
    <meta name="msapplication-wide310x150logo" content="mstile-310x150.png" />
    <meta name="msapplication-square310x310logo" content="mstile-310x310.png" />

    <style>
      p {
        font-family: verdana;
        font-size: 400px;
        color: #000;
      }
    </style>


  </head>

  <body>
    <br><br>
    <p id="heading" style="text-align:center"></p>
  </body>

  <script>

    // Get event data
    function deviceOrientationListener(event) {
      var alpha    = event.alpha; //z axis rotation [0,360)
      var beta     = event.beta; //x axis rotation [-180, 180]
      var gamma    = event.gamma; //y axis rotation [-90, 90]
      //Check if absolute values have been sent
      if (typeof event.webkitCompassHeading !== "undefined") {
        alpha = event.webkitCompassHeading; //iOS non-standard
        var heading = alpha
        document.getElementById("heading").innerHTML = heading.toFixed([0]);
        alert("Compass heading undefined");
      }
      else {
        alert("Your device is reporting relative alpha values, so this compass won't point north :(");
        var heading = 360 - alpha; //heading [0, 360)
        document.getElementById("heading").innerHTML = heading.toFixed([0]);
      }
      
      // Change backgroud colour based on heading
      // Green for North and South, black otherwise
      if (heading > 359 || heading < 1) { //Allow +- 1 degree
        document.body.style.backgroundColor = "green";
        document.getElementById("heading").innerHTML = "N"; // North
        alert("you are facing NORTH");
      }
      else if (heading > 179 && heading < 181){ //Allow +- 1 degree
        document.body.style.backgroundColor = "green";
        document.getElementById("heading").innerHTML = "S"; // South
        alert("you are facing SOUTH");
      } 
      else { // Otherwise, use near black
        alert("should be black");
        document.body.style.backgroundColor = "#161616";
      }
    }
    
    // Check if device can provide absolute orientation data
    if (window.DeviceOrientationAbsoluteEvent) {
      window.addEventListener("DeviceOrientationAbsoluteEvent", deviceOrientationListener);
      alert("absolute event");
    } // If not, check if the device sends any orientation data
    else if(window.DeviceOrientationEvent){
      window.addEventListener("deviceorientation", deviceOrientationListener);
      alert("orientation event");
    } // Send an alert if the device isn't compatible
    else {
      alert("Sorry, try again on a compatible mobile device!");
    }
    </script>


</html>

