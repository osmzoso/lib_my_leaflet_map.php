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
