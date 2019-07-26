<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />

   <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css">

  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"> 
   <link href="https://maxcdn.bootstrapcdn.com/bootswatch/4.0.0/yeti/bootstrap.min.css" rel="stylesheet">

{{ app('debugbar')->disable() }}

   <title>NXM TRIP</title>
      <style>
         html, body{
            width:100% !important;
            height:100% !important;;
         }
         .tb1{
            border:1px solid red !important;
         }
         .tb2{
            border:1px solid blue !important;
         }
         #map {
            height: 100%;
         }
         .car-icon{
            width:100px;
            padding:8px;
            margin-top: 10px;
            margin-right:10px;
         }
         .car-make{
            font-size:24px;
            font-weight: 700;
         }
         .car-model{
            font-size:18px;
         }
         .car-data{
            margin-top: 15px;
            font-size:16px;
         }
         .badge{
            color:white !important;
            margin-left: 3px;
         }
         .btn-fixed{
            width:50px;
            height:50px;
         }
         #search-bar{
            position: absolute; 
            top: 10px; 
            left: 45%; 
            z-index: 999;
         }
         .car-line{
            padding:0px;
            margin:5px 0px;
         }
         #close-btn{
            position:absolute;
            right:10px;
            top:5px;
            color:silver;
            cursor:pointer;
         }
         .aligner {
           display: flex;
           align-items: center;
           justify-content: center;
         }
         .aligner-item {
           max-width: 50%;
         }

         .aligner-item--top {
           align-self: flex-start;
         }

         .aligner-item--bottom {
           align-self: flex-end;
         }

      </style>

</head>
<body>
<!-- ============================================================== -->
<div class="container-fluid" style="height:100%;">
   <div class="row" style="height:100%;">
      <div id="map-side" class="col-lg-12" style="padding:0px;">
         <div id="map"></div>
         <div id="search-bar">

              <a href="map.html" class="btn btn-info" >Return to Vehicles</a>
         </div>
      </div>
   
   </div>
</div>



<script src="{{ asset('nxcar/vehicles.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js'></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>


   <script>


      function initMap() {

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 7,
          center: {lat: 41.85, lng: -87.65}
        });
        directionsDisplay.setMap(map);

       
          calculateAndDisplayRoute(directionsService, directionsDisplay);
        
      }

      function calculateAndDisplayRoute(directionsService, directionsDisplay) {


        var idx = getUrlVars()["idx"];


        console.log(vehicles[idx].origin);
        console.log(vehicles[idx].destination);
        
        directionsService.route({
          origin: {lat:vehicles[idx].trip_lat, lng:vehicles[idx].trip_lng},
          destination: {lat:vehicles[idx].lat, lng:vehicles[idx].lng},
          travelMode: 'DRIVING'
        }, function(response, status) {
          console.log(response);
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            console.log('Directions request failed due to ' + status);
          }
        });
      }


      function getUrlVars()
      {
          var vars = [], hash;
          var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
          for(var i = 0; i < hashes.length; i++)
          {
              hash = hashes[i].split('=');
              vars.push(hash[0]);
              vars[hash[0]] = hash[1];
          }
          return vars;
      }

   </script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJCY5lOQMDwNqdHl4KSUHpuBNjCAj3ta0&callback=initMap"></script>
  </body>
</html>