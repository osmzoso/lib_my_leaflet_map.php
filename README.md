# lib_my_leaflet_map.php

Easy access with PHP to the [Leaflet.js](https://leafletjs.com/) library.  


## Installation

The following files are required for the simple example.php (see below):  
(tested with Leaflet.js version 1.3.4)  

    +---example.php
    +---lib_my_leaflet_map.php
    +---libs
        +---leaflet
            +---leaflet.css
            +---leaflet.js
            +---marker-icon.png
            +---marker-shadow.png
            +---marker1.png
            +---marker2.png
            +---marker3.png
            +---marker4.png
            +---marker5.png
            +---marker6.png
            +---marker7.png
            +---marker8.png
            +---images
                +---layers.png
                +---layers-2x.png


## Public Methods

    set_color( $color )
    set_opacity( $opacity )
    set_weight( $weight )
    set_dasharray( $dasharray )
    set_fillcolor( $fillcolor )
    set_fillopacity( $fillopacity )
    set_properties( $color, $opacity, $weight, $dasharray, $fillcolor, $fillopacity )

    add_marker( $lon, $lat, $marker, $popuptext, $openpopup )
    add_polyline( array $lonlat )
    add_line( $lon1, $lat1, $lon2, $lat2 )
    add_polygon( array $lonlat, $popuptext )
    add_circle( $lon, $lat, $radius, $popuptext )
    add_circlemarker( $lon, $lat )
    add_rectangle( $lon1, $lat1, $lon2, $lat2, $popuptext )

    print_javascript( $id )


## Example

```php

<html>
<h1>Demo lib_my_leaflet_map.php</h1>

<?php
include './lib_my_leaflet_map.php';

// new map object
$map = new MyLeafletMap;
// add Marker
$map->add_marker( 13.3757533, 52.5185551, 'Marker1', 'Berlin Reichstag', true );
// add polyline
//
// all coordinates are stored in a simple array
// [ lon1, lat1, lon2, lat2, lon3, lat3, ... ]
//
$lonlat[0] = 13.3688328;  // lon
$lonlat[1] = 52.5191325;  // lat
$lonlat[2] = 13.3727595;
$lonlat[3] = 52.5138487;
$lonlat[4] = 13.3826700;
$lonlat[5] = 52.5196180;
$lonlat[6] = 13.3781623;
$lonlat[7] = 52.5203694;
$map->add_polyline( $lonlat );
// add polygon
$lonlat = array();
$lonlat[0] = 13.3672651;
$lonlat[1] = 52.5113891;
$lonlat[2] = 13.3734430;
$lonlat[3] = 52.5130339;
$lonlat[4] = 13.3760600;
$lonlat[5] = 52.5110236;
$map->set_weight( 20 );
$map->add_polygon( $lonlat, 'This is a polygon' );
// add circle 1
$map->set_color( '#bb1122' );
$map->set_opacity( 0.6 );
$map->set_fillcolor( 'none' );
$map->add_circle( 13.3634942, 52.5143171, 300, '' );
// add circle 2, set all properties (color, opacity, weight, dasharray, fillcolor, fillopacity)
$map->set_properties( '#992255', 1.0, 4, '4 7', '#aa0000', 0.3 );
$map->add_circle( 13.3657672, 52.5183113, 200, 'This is a dotted circle' );
// add rectangle
$map->set_properties( '#005588', 1.0, 3, 'none', '#005588', 0.3 );
$map->add_rectangle( 13.3788740, 52.5152569, 13.3856956, 52.5104557, 'This is a rectangle' );
// add the red dotted boundingbox (with the current coordinates of the object)
$map->set_properties( '#ff0000', 1.0, 2, '5 5', 'none', 1.0 );
$map->add_rectangle( $map->bbox_min_lon, $map->bbox_min_lat, $map->bbox_max_lon, $map->bbox_max_lat, '' );
// finish -> print javascript
$map->print_javascript( 'xxx' );

?>

<div id="xxx" style="height: 75vh; width: 100%;"></div>
</html>


```
