<html>
<h1>Demo MyLeafletMap</h1>

<?php
include './lib_my_leaflet_map.php';

// new map object
$map = new MyLeafletMap;
// add Marker
$map->add_marker( 13.3757533, 52.5185551, 'Marker1', 'Berlin Reichstag', true );
// add circle 1 (with default colors)
$map->add_circle( 13.3634942, 52.5143171, 300, 'This is a circle with default colors' );
// add circle 2 (set every property manually)
$map->set_color( '#bb1122' );
$map->set_opacity( 0.6 );
$map->set_fillcolor( '#ffffff' );
$map->set_fillopacity( 0.6 );
$map->add_circle( 13.384, 52.517, 200, 'This is a white circle' );
// add circle 3 (set all 6 properties at once)
$map->set_properties( '#992255', 1.0, 4, '4 7', '#ff0000', 0.3 );
$map->add_circle( 13.3657672, 52.5183113, 200, 'This is a dotted circle' );
// add line
$map->set_properties( '#ff0000', 0.6, 6, '', '#00ffff', 0.7 );
$map->add_line( 13.369, 52.513, 13.376, 52.514 );
// add polyline
// (all coordinates in a simple array [ lon1,lat1, lon2,lat2, lon3,lat3, ... ])
$lonlat = array(13.368,52.519,  13.372,52.514,  13.382,52.519,  13.378,52.522);
$map->add_polyline( $lonlat );
// add polygon
$lonlat = array(13.367,52.511,  13.373,52.513,  13.376,52.511);
$map->set_weight( 20 );
$map->add_polygon( $lonlat, 'This is a polygon' );
// add rectangle
$map->set_properties( '#005588', 1.0, 3, 'none', '#005588', 0.3 );
$map->add_rectangle( 13.3788740, 52.5152569, 13.3856956, 52.5104557, 'This is a rectangle' );
// add the red dotted boundingbox
$map->set_properties( '#ff0000', 1.0, 2, '5 5', 'none', 1.0 );
$map->add_rectangle( $map->bbox_min_lon, $map->bbox_min_lat, $map->bbox_max_lon, $map->bbox_max_lat, '' );
// finish -> print the javascript code
$map->print_javascript( 'map_berlin' );
// print the coordinates of the boundingbox
echo "Boundingbox: ".$map->bbox_min_lon.', '.$map->bbox_min_lat.' - '.$map->bbox_max_lon.', '.$map->bbox_max_lat,"\n";
?>

<div id="map_berlin" style="height: 75vh; width: 100%;"></div>
</html>
