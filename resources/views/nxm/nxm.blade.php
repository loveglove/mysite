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

   <title>NXM MAP</title>
      <style>
         html, body{
            width:100% !important;
            height:100% !important;
            font-family: 'Montserrat', sans-serif !important;
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
            font-size:18px;
            /*font-weight: 700;*/
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
            left: 35%; 
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
           max-width: 75%;
         }

         .aligner-item--top {
           align-self: flex-start;
         }

         .aligner-item--bottom {
           align-self: flex-end;
         }
         #car-fuel{
          color:white !important;
         }
         .card-title{
          margin-top: 10px;
          font-size: 26px;
         }
         .card-subtitle{
          margin-top: 10px;
          font-size: 20px;
         }
         #car-miles{
          font-size: 20px;
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

             <div class="input-group">
               <span class="input-group-btn">
                 <button class="btn btn-info" onclick="nextCar(false)" type="button"><i class="fa fa-chevron-left"></i></button>
               </span>
               <input type="text" class="form-control" placeholder="Search for...">
               <span class="input-group-btn">
                 <button class="btn btn-secondary" onclick="recenter()" type="button"><i class="fa fa-refresh"></i></button>
               </span>
               <span class="input-group-btn">
                 <button class="btn btn-info" onclick="nextCar(true)" type="button"><i class="fa fa-chevron-right"></i></button>
               </span>
             </div>
         </div>
      </div>
      <div id="side-bar" class="col-lg-4" style="padding:0px !important; display: none;">
       <div id="side-card" class="card"  style="margin:0px !important; height:100%;">
          <div class="card-body aligner">
            <div id="close-btn"><a onclick="closeSidebar()" ><i class="fa fa-times"></i></a></div>
            <center class="m-t-30 aligner-item"> <img id="car-image-side" src="nxcar/images/focus.png" class="" width="80%" />
                  <h4 id="car-make" class="card-title m-t-20">Ford Focus, 2012</h4>
                  <h6 id="car-vin" class="card-subtitle"><b>VIN:</b> 18276490328</h6>
                  <div class="row text-center justify-content-md-center">
                     <div class="col-12 m-t-10">
                        <i class="fa fa-tachometer" style="font-size: 20px; color: silver;"></i>&nbsp&nbsp<font id="car-miles"> 254,300 miles</font>
                     </div>
                  </div>
              </center>
          </div>
          <div>
              <hr> </div>
         <div class="card-body"> 
            <small class="text-muted">Last Position: </small>
            <h6 id="car-last">Monday, March 16th 2018 at 2:45pm</h6> 

            <small class="text-muted p-t-30 db">VSC #: </small>
            <h6 id="car-warranty">WN2-3MY7823NM</h6> 
            <small class="text-muted p-t-30 db">Contract Start: </small>
            <h6 id="car-warranty">March 30th, 2016</h6> 
            <small class="text-muted p-t-30 db">Contract End: </small>
            <h6 id="car-warranty">March 30th, 2022</h6> 
            <small class="text-muted p-t-30 db">SKU: </small>
            <h6 id="car-warranty">6/100 1234</h6> 

            <small class="text-muted p-t-30 db">Fuel:</small>
            <div class="progress">
              <div id="car-fuel" class="progress-bar bg-danger" role="progressbar" style="width: 75%;">75%</div>
            </div>
            <br>
            <small class="text-muted p-t-30 db">Diagnostic Alerts:</small>
            <div class="row text-center justify-content-md-center">
               <div class="col-4"><button id="car-engine" class="btn btn-secondary btn-fixed">8</button><br>
               <small class="text-muted p-t-30 db">Engine</small></div>
               <div class="col-4"><button id="car-body" class="btn btn-secondary btn-fixed">24</button><br>
               <small class="text-muted p-t-30 db">Body</small></div>
               <div class="col-4"><button id="car-emergency" class="btn btn-secondary btn-fixed">3</button><br>
               <small class="text-muted p-t-30 db">Chasis</small></div>
            </div>
            <br/>
             <small class="text-muted p-t-30 db">Trips:</small>
              <br/>
              <a id="car-trips" href="trip.html?idx=0" class="btn btn-circle btn-block btn btn-primary"><i class="fa fa-map"></i> View</a>
      
          </div>
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

      


      var icon_path = 'https://mattglover.ca/nxcar/';
      var markers = [];
      var infoWindows = [];
      var map;
      var bounds;
      var cur_idx = -1;

      function initMap() {

         map = new google.maps.Map(document.getElementById('map'), {
            zoom: 3,
            center: {lat: -28.024, lng: 140.887},
            mapTypeId: google.maps.MapTypeId.ROADMAP
         });
        
         bounds = new google.maps.LatLngBounds();

         $.each(vehicles, function(index, car){

            var position = new google.maps.LatLng(car.lat, car.lng);

            var marker = new google.maps.Marker({
               animation: google.maps.Animation.DROP,
               position: position,
               draggable: false,
               map: map,
               // label: car.make
            });


            var contentString = '<div class="container-fluid">'+
                                    '<div class="row">'+
                                       '<div class="col-xs-4">'+
                                          '<img class="car-icon" src="' + icon_path + car.icon + '" alt="car" />'+
                                       '</div>'+
                                       '<div class="col-xs-8">'+
                                          '<span class="car-make">' + car.make + '</span>&nbsp'+
                                          '<span class="car-model">'+ car.model + ', ' + car.year + '</span><br><hr class="car-line">'+
                                          '<span class="car-data"><b>VIN</b>: '+ car.vin + '</span><br>'+
                                          '<span class="car-data"><a href="#" onclick="openSidebar('+index+');" class="trip-link">See Details</a></span><br>'+
                                       '</div>'+
                                    '</div>'+
                                 '</div>';

            var infowindow = new google.maps.InfoWindow({
               content: contentString
            });

            marker.addListener('click', function() {
               closeAllInfoWindows();
               infowindow.open(map, marker);
            });

            bounds.extend(position);
            markers.push(marker);
            infoWindows.push(infowindow); 

         });

         map.fitBounds(bounds);

         var mcOptions = {
            //imagePath: 'https://googlemaps.github.io/js-marker-clusterer/images/m',
          styles:[{

          url: "https://googlemaps.github.io/js-marker-clusterer/images/m1.png",
                width: 53,
                height:53,
                // fontFamily:"comic sans ms",
                textSize:14,
                textColor:"white",
                //color: #00FF00,
          }]

        };

         var markerCluster = new MarkerClusterer(map, markers, mcOptions);
      }

      function closeAllInfoWindows() {
         for (var i=0;i<infoWindows.length;i++) {
            infoWindows[i].close();
         }
      }

      function recenter(){
         map.fitBounds(bounds);
      }


      function openSidebar(idx){
         $("#map-side").removeClass('col-lg-12').addClass('col-lg-8');
         $("#side-card").addClass('animated fadeInDown'); 
         $("#side-bar").show();
         loadCarData(idx);
      }

      function closeSidebar(){
            $("#map-side").removeClass('col-lg-8').addClass('col-lg-12');
            $("#side-bar").hide();
      }

      function loadCarData(idx){
         cur_idx = idx;
         var car = vehicles[idx];
         map.setCenter(new google.maps.LatLng(car.lat, car.lng));
         map.setZoom(18);
         // infoWindows[idx].open(map, markers[idx]);
         $("#car-image-side").attr('src', icon_path + car.icon);
         $("#car-make").html(car.make + ' ' + car.model + ', ' + car.year);
         $("#car-vin").html('<b>VIN:</b> ' + car.vin);
         $("#car-miles").html(car.miles + ' miles');
         $("#car-last").html(car.last);
         $("#car-warranty").html(car.warranty);
         $("#car-fuel").html(car.fuel).css('width', car.fuel);
         $("#car-engine").html(car.engine);
         $("#car-body").html(car.body);
         $("#car-emergency").html(car.emergency);
         $("#car-trips").attr("href", "trip.html?idx=" + idx);

      }

      function nextCar(dir){
         if(dir){
            var new_idx = cur_idx + 1;
            if(new_idx > vehicles.length - 1){
               new_idx = 0;
            }
            loadCarData(new_idx);
         }else{
            var new_idx = cur_idx - 1;
            if(new_idx < 0){
               new_idx = vehicles.length -1;
            }
            loadCarData(new_idx);       
         }
      }

   </script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJCY5lOQMDwNqdHl4KSUHpuBNjCAj3ta0&callback=initMap"></script>
  </body>
</html>