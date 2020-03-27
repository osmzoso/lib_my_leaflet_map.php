<?php
/*
README.md

# lib_my_leaflet_map.php

Easy access to the [Leaflet.js](https://leafletjs.com/) library with PHP.  


## Installation

The following files are required for a simple example:  
(tested with Leaflet.js version 1.3.4)  

- **lib_my_leaflet_map.php**  
- **example.php** (see below)  

The following leaflet files are required in a subdirectory **./libs/leaflet/**:  

- **leaflet.css**  
- **leaflet.js**  
- **marker1.png ... marker8.png**  
- **marker-shadow.png**  

The following leaflet files are required in a subdirectory **./libs/leaflet/images/**:  

- **layers.png**  
- **layers-2x.png**  


## example.php

```php

<html>
<h1>Example for lib_my_leaflet_map.php</h1>

<?php
include './lib_my_leaflet_map.php';

// new map object
$map = new my_leaflet_map;
// add Marker
$map->add_marker( 52.5185551, 13.3757533, 'Marker1', 'Berlin Reichstag' );
// add polyline
$lat_lon[0] = 52.5191325;  // lat
$lat_lon[1] = 13.3688328;  // lon
$lat_lon[2] = 52.5138487;
$lat_lon[3] = 13.3727595;
$lat_lon[4] = 52.5196180;
$lat_lon[5] = 13.3826700;
$lat_lon[6] = 52.5203694;
$lat_lon[7] = 13.3781623;
$map->add_polyline( $lat_lon );
// add polygon
$lat_lon = array();
$lat_lon[0] = 52.5113891;
$lat_lon[1] = 13.3672651;
$lat_lon[2] = 52.5130339;
$lat_lon[3] = 13.3734430;
$lat_lon[4] = 52.5110236;
$lat_lon[5] = 13.3760600;
$map->set_weight( 20 );
$map->add_polygon( $lat_lon, 'This is a polygon' );
// add circle 1
$map->set_color( '#bb1122' );
$map->set_opacity( 0.6 );
$map->set_fillcolor( 'none' );
$map->add_circle( 52.5143171, 13.3634942, 300, '' );
// add circle 2
$map->set_all( '#992255', 1.0, 4, '4 7', '#aa0000', 0.3 );
$map->add_circle( 52.5183113, 13.3657672, 200, 'This is a dotted circle' );
// add rectangle
$map->set_all( '#005588', 1.0, 3, 'none', '#005588', 0.3 );
$map->add_rectangle( 52.5152569, 13.3788740, 52.5104557, 13.3856956, 'This is a rectangle' );
// add the red dotted boundingbox (with the current coordinates of the object)
$map->set_all( '#ff0000', 1.0, 2, '5 5', 'none', 1.0 );
$map->add_rectangle( $map->bbox_min_lat, $map->bbox_min_lon, $map->bbox_max_lat, $map->bbox_max_lon, '' );
// finish -> print javascript
$map->print_javascript( 'xxx' );

?>

<div id="xxx" style="height: 75vh; width: 100%;"></div>
</html>

```

*/

class my_leaflet_map
{
   // javascript code created
   private $js_code;

   // boundingbox of the map
   public $bbox_min_lat = 90;
   public $bbox_min_lon = 180;
   public $bbox_max_lat = -90;
   public $bbox_max_lon = -180;

   // attributes (color, opacity, weight, dasharray)
   public $color = '#0000ff';
   public $opacity = 0.5;
   public $weight = 10;
   public $dasharray = 'none';      // '5 5' or 'none'
   public $fillcolor = '#00ff00';   // hexcolor or 'none'
   public $fillopacity = 0.3;

   // set attributes
   function set_color( $v )       { $this->color       = $v; }
   function set_opacity( $v )     { $this->opacity     = $v; }
   function set_weight( $v )      { $this->weight      = $v; }
   function set_dasharray( $v )   { $this->dasharray   = $v; }
   function set_fillcolor( $v )   { $this->fillcolor   = $v; }
   function set_fillopacity( $v ) { $this->fillopacity = $v; }
   function set_all( $v1, $v2, $v3, $v4, $v5, $v6 ) {
      $this->set_color( $v1 );
      $this->set_opacity( $v2 );
      $this->set_weight( $v3 );
      $this->set_dasharray( $v4 );
      $this->set_fillcolor( $v5 );
      $this->set_fillopacity( $v6 );
   }

   function add_marker( $lat, $lon, $marker, $popuptext ) {
      $this->js_code .= 'L.marker(['.$lat.','.$lon.'], {icon: '.$marker.'})';
      if( $popuptext != '' ) { $this->js_code .= ".bindPopup('".$popuptext."')"; }
      $this->js_code .= '.addTo(l_standort)';
      $this->js_code .= ";\n";
      
      $this->adjust_boundingbox( $lat, $lon );
   }

   function add_polyline( array $lat_lon ) {
      $lat_lon_str = $this->lat_lon2str( $lat_lon );
      $this->js_code .= 'L.polyline( [ '.$lat_lon_str.' ], ';
      $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', stroke:true }";
      $this->js_code .= ' )';
      $this->js_code .= '.addTo(l_verbindung)';
      $this->js_code .= ";\n";
   }

   function add_polygon( array $lat_lon, $popuptext ) {
      $lat_lon_str = $this->lat_lon2str( $lat_lon );
      $this->js_code .= 'L.polygon( [ '.$lat_lon_str.' ], ';
      $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', fillColor:'".$this->fillcolor."', fillOpacity:".$this->fillopacity." }";
      $this->js_code .= ' )';
      if( $popuptext != '' ) { $this->js_code .= ".bindPopup('".$popuptext."')"; }
      $this->js_code .= '.addTo(l_grenzen)';
      $this->js_code .= ";\n";
   }

   function add_circle( $lat, $lon, $radius, $popuptext ) {
      $this->js_code .= 'L.circle(['.$lat.','.$lon.'], '.$radius.', ';
      $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', fillColor:'".$this->fillcolor."', fillOpacity:".$this->fillopacity." }";
      $this->js_code .= ' )';
      if( $popuptext != '' ) { $this->js_code .= ".bindPopup('".$popuptext."')"; }
      $this->js_code .= '.addTo(l_grenzen)';
      $this->js_code .= ";\n";
      
      $this->adjust_boundingbox( $lat, $lon );
   }

   function add_circlemarker( $lat, $lon ) {
      $this->js_code .= 'L.circleMarker(['.$lat.','.$lon.'], ';
      $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', fillColor:'".$this->fillcolor."', fillOpacity:".$this->fillopacity." }";
      $this->js_code .= ' )';
      $this->js_code .= '.addTo(l_grenzen)';
      $this->js_code .= ";\n";
      
      $this->adjust_boundingbox( $lat, $lon );
   }

   function add_rectangle( $lat1, $lon1, $lat2, $lon2, $popuptext ) {
      $this->js_code .= 'L.rectangle( [ ['.$lat1.','.$lon1.'], ['.$lat2.','.$lon2.'] ], ';
      $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', fillColor:'".$this->fillcolor."', fillOpacity:".$this->fillopacity." }";
      $this->js_code .= ' )';
      if( $popuptext != '' ) { $this->js_code .= ".bindPopup('".$popuptext."')"; }
      $this->js_code .= '.addTo(l_grenzen)';
      $this->js_code .= ";\n";
      
      $this->adjust_boundingbox( $lat1, $lon1 );
      $this->adjust_boundingbox( $lat2, $lon2 );
   }

   function adjust_boundingbox( $lat, $lon ) {
      if( $this->bbox_min_lat > $lat ) { $this->bbox_min_lat = $lat; }
      if( $this->bbox_min_lon > $lon ) { $this->bbox_min_lon = $lon; }
      if( $this->bbox_max_lat < $lat ) { $this->bbox_max_lat = $lat; }
      if( $this->bbox_max_lon < $lon ) { $this->bbox_max_lon = $lon; }
   }

   function lat_lon2str( array $lat_lon ) {
      $lat_lon_str = '';
      for( $i=0; $i < count( $lat_lon ); $i=$i+2 ) {
         $lat = $lat_lon[$i];
         $lon = $lat_lon[$i+1];
         if( $i > 0 ) { $lat_lon_str .= ','; }
         $lat_lon_str .= '['.$lat.','.$lon.']';

         $this->adjust_boundingbox( $lat, $lon );
      }
   return $lat_lon_str;
   }

   function print_javascript( $id ) {
      echo "\n\n<!-- ******** Javascript for Leaflet.js ******** -->\n";

      // link to the original libraries
      //echo '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>',"\n";
      //echo '<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js" integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA==" crossorigin=""></script>',"\n";

      // link to the local libraries (faster)
      echo '<link rel="stylesheet" href="./libs/leaflet/leaflet.css" />',"\n";
      echo '<script src="./libs/leaflet/leaflet.js" ></script>',"\n";

      echo "<script>\n";

      echo "// start init function automatically when loading the web page\n";
      echo "window.onload = function init() {\n\n";

      echo "// boundingbox of the map to be displayed\n";
      echo 'var boundingbox = [ ';
      echo '['.$this->bbox_min_lat.','.$this->bbox_min_lon.'],';
      echo '['.$this->bbox_max_lat.','.$this->bbox_max_lon.']';
      echo " ];\n\n";

      echo "// initialize map with specified bounding box\n";
      echo "var mymap = L.map('$id').fitBounds( boundingbox, {padding: [0,0], maxZoom: 19} );\n\n";

      echo "// tile server for tile layer (OpenStreetMap's standard tile layer)\n";
      echo "var tile_server = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';\n";
      echo "L.tileLayer( tile_server, {\n";
      echo "   maxZoom: 19,\n";
      echo "   attribution: 'Map data &copy; <a href=\"https://www.openstreetmap.org/\">OpenStreetMap</a> contributors'\n";
      echo "}).addTo(mymap);\n\n";

      echo "// do not show link to 'leafletjs.com'\n";
      echo "mymap.attributionControl.setPrefix('');\n\n";

      echo "// show scale\n";
      echo "L.control.scale( { position: 'bottomleft', maxWidth: 200, metric:true, imperial:false } ).addTo(mymap);\n\n";

      echo "// use your own markers\n";
      echo "var MyIcon = L.Icon.extend({\n";
      echo " options: {\n";
      echo "  iconSize:   [ 25, 41 ],\n";
      echo "  iconAnchor: [ 12, 41 ],\n";
      echo "  popupAnchor: [ 0, -35 ],\n";
      echo "  shadowUrl: './libs/leaflet/marker-shadow.png',\n";
      echo "  shadowSize: [ 41, 41],\n";
      echo "  shadowAnchor: [ 12, 40 ]\n";
      echo " }\n";
      echo "});\n";
      echo "var Marker1 = new MyIcon({iconUrl: './libs/leaflet/marker1.png'});\n";
      echo "var Marker2 = new MyIcon({iconUrl: './libs/leaflet/marker2.png'});\n";
      echo "var Marker3 = new MyIcon({iconUrl: './libs/leaflet/marker3.png'});\n";
      echo "var Marker4 = new MyIcon({iconUrl: './libs/leaflet/marker4.png'});\n";
      echo "var Marker5 = new MyIcon({iconUrl: './libs/leaflet/marker5.png'});\n";
      echo "var Marker6 = new MyIcon({iconUrl: './libs/leaflet/marker6.png'});\n";
      echo "var Marker7 = new MyIcon({iconUrl: './libs/leaflet/marker7.png'});\n";
      echo "var Marker8 = new MyIcon({iconUrl: './libs/leaflet/marker8.png'});\n\n";

      echo "// definition of the layers\n";
      echo "var l_standort   = L.layerGroup();\n";
      echo "var l_grenzen    = L.layerGroup();\n";
      echo "var l_verbindung = L.layerGroup();\n";
      echo "mymap.addLayer(l_standort);   // Alle Layer beim Start anzeigen\n";
      echo "mymap.addLayer(l_grenzen);    //\n";
      echo "mymap.addLayer(l_verbindung); //\n";
      echo "var l_base = {};\n";
      echo "var l_overlay = {\n";
      echo "  \"Standorte\": l_standort,\n";
      echo "  \"Grenzen\": l_grenzen,\n";
      echo "  \"Verbindungen\": l_verbindung\n";
      echo "};\n";
      echo "L.control.layers( l_base, l_overlay ).addTo(mymap);\n\n";

      echo "// show popup with coordinates at mouse click\n";
      echo "var popup = L.popup();\n";
      echo "function onMapClick(e) {\n";
      echo "   // copy coordinates in new object\n";
      echo "   var geo = e.latlng;\n";
      echo "   var lat = geo.lat;\n";
      echo "   var lon = geo.lng;\n";
      echo "   // round to 7 decimal places\n";
      echo "   lat = lat.toFixed(7);\n";
      echo "   lon = lon.toFixed(7);\n";
      echo "   // output coordinates and zoomlevel on console\n";
      echo "   console.log( 'mouse coordinates:' );\n";
      echo "   console.log( 'lat: ' + lat + ' lon: ' + lon );\n";
      echo "   console.log( 'zoomlevel:', mymap.getZoom() );\n";
      echo "   //console.log( 'Leaflet version ', L.version );\n";
      echo "   //\n";
      echo "   var popuptext = 'Breite: '+lat+'<br>Länge: '+lon+'<br>';\n\n";
       echo "   var url1 = '<a href=\"karte_ipac_umkreis.php?laenge='+lon+'&amp;breite='+lat+'&amp;umkreis=500'+'\">Umkreissuche 500m</a><br>';\n";
       echo "   var url2 = '<a href=\"karte_ipac_umkreis.php?laenge='+lon+'&amp;breite='+lat+'&amp;umkreis=1000'+'\">Umkreissuche 1 km</a><br>';\n";
       echo "   var url3 = '<a href=\"karte_ipac_umkreis.php?laenge='+lon+'&amp;breite='+lat+'&amp;umkreis=5000'+'\">Umkreissuche 5 km</a><br>';\n";
       echo "   popuptext = popuptext+'<br>'+url1+url2+url3\n\n";
      echo "   popup\n";
      echo "    .setLatLng(e.latlng)\n";
      echo "    .setContent( popuptext )\n";
      echo "    .openOn(mymap);\n";
      echo "}\n";
      echo "mymap.on('click', onMapClick);\n\n";

      echo "// show marker, circles etc.\n";
      echo $this->js_code;

      echo "\n} // end init()\n\n";

      echo "</script>\n\n";
   }
}

?>
