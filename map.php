<?php
/*
* Plugin Name: google map
* Description: use google map and save location to database
* Version: 1.0
* Author: Yang
*/

function showMap(){
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Google Map</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      #map {
        height: 800px;
      }
      #pos {
        width:500px;
        border: none;
        border-bottom: 1px solid grey;
      }
      #pos:focus{
        border-bottom:1px solid #3498db;
      }
      #btn{
        background: #3498db;
        border: none;
        outline: none;
        border-bottom: 4px solid #2980b9;
        padding: 10px 14px 6px 14px;
        color: #fff;
        font-weight: bold;
      }
    </style>
  </head>
  <body>
    <form name="loc" method="post" onsubmit="return validateForm()">
  		<p><label for="pos">Your Position: </label></p>
      <p><input type='text' id="pos" name="pos"></p>
  		<p><input id="btn" type="submit" value="Submit"></p>
  	</form>
    <div id="mes"></div>
    <div id="map"></div>
    <script>
    function validateForm() {
      var x = document.forms["loc"]["pos"].value;
      if (x == "") {
          alert("Location must be filled out");
          return false;
      }
    }
    </script>
    <script>
      var map, infoWindow, marker,geocoder;
      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: -34.397, lng: 150.644},
          zoom: 6
        });
        infoWindow = new google.maps.InfoWindow;
        geocoder = new google.maps.Geocoder;


        if (navigator.geolocation) {
          document.getElementById('mes').innerHTML = "Loading.."
          navigator.geolocation.getCurrentPosition(function(position) {
            var pos = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };

            infoWindow.setPosition(pos);
            infoWindow.setContent('Your Location.');
            infoWindow.open(map);
            map.setCenter(pos);
            map.setZoom(13);
            marker = new google.maps.Marker({
              position: pos,
              map: map,
              draggable: true,
              animation: google.maps.Animation.DROP,
            });

            geocodePosition(pos);

            google.maps.event.addListener(marker, 'dragend', function() {
                geocodePosition(marker.getPosition());
            });

          }, function() {
            handleLocationError(true, infoWindow, map.getCenter());
          });
        } else {
          handleLocationError(false, infoWindow, map.getCenter());
        }
      }

      function geocodePosition(pos) {
         geocoder = new google.maps.Geocoder();
         geocoder.geocode
          ({
              latLng: pos
          },
              function(results, status){
                  document.getElementById('mes').innerHTML = ""
                  if (status == google.maps.GeocoderStatus.OK){
                      document.getElementById('pos').value = results[0].formatted_address
                  }
                  else{
                      document.getElementById('pos').value = 'Cannot dertermine'
                  }
              }
          );
      }

      function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                              'Error: The Geolocation service failed.' :
                              'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCafrLqApOZm3KwKPaouXo2CgEioDqmES4&callback=initMap">
    </script>
  </body>
</html>
<?php




if ( isset( $_POST[ 'pos' ] ) ) {

  $pos = $_POST[ 'pos' ];

  $mysqli = new mysqli("localhost", "root", "", "kids");

  $result = $mysqli->query("INSERT INTO wp_locations VALUES(null,'{$pos}')");

  if ($result) {
    echo "<script>alert('Location saved!')</script>";
  }

  $_POST = array();
}

}

add_shortcode('map', 'showMap');



?>
