<?php
//
// Easy access with PHP to the Leaflet.js library
//
// Leaflet Version 1.3.4 - https://leafletjs.com/reference-1.3.4.html
//

class MyLeafletMap
{
    public $bbox_min_lon; // boundingbox of the map
    public $bbox_min_lat;
    public $bbox_max_lon;
    public $bbox_max_lat;
    public $color;        // line and fill attributes
    public $opacity;
    public $weight;
    public $dasharray;
    public $fillcolor;
    public $fillopacity;
    private $js_code;     // created javascript code

    //
    // Constructor (Set default values)
    //
    public function __construct() {
        $this->bbox_min_lon =  180;
        $this->bbox_min_lat =   90;
        $this->bbox_max_lon = -180;
        $this->bbox_max_lat =  -90;
        $this->color        = '#0000ff';
        $this->opacity      = 0.5;
        $this->weight       = 4;
        $this->dasharray    = 'none';      // '5 5' or 'none'
        $this->fillcolor    = '#ff7800';   // hexcolor or 'none'
        $this->fillopacity  = 0.5;
        $this->js_code      = '';
    }

    //
    // Private Methods
    //

    private function adjust_boundingbox( $lon, $lat ) {
        if( $this->bbox_min_lon > $lon ) { $this->bbox_min_lon = $lon; }
        if( $this->bbox_min_lat > $lat ) { $this->bbox_min_lat = $lat; }
        if( $this->bbox_max_lon < $lon ) { $this->bbox_max_lon = $lon; }
        if( $this->bbox_max_lat < $lat ) { $this->bbox_max_lat = $lat; }
    }

    private function lonlat2str( array $lonlat ) {
        $lat_lon_str = '';
        for( $i=0; $i < count( $lonlat ); $i=$i+2 ) {
            if( $i > 0 ) { $lat_lon_str .= ','; }
            $lat_lon_str .= '['.$lonlat[$i+1].','.$lonlat[$i].']';
            $this->adjust_boundingbox( $lonlat[$i], $lonlat[$i+1] );
        }
    return $lat_lon_str;
    }

    //
    // Public Methods
    //

    // set single attribute
    public function set_color( $color ) {
        $this->color = $color;
    }

    public function set_opacity( $opacity ) {
        $this->opacity = $opacity;
    }

    public function set_weight( $weight ) {
        $this->weight = $weight;
    }

    public function set_dasharray( $dasharray ) {
        $this->dasharray = $dasharray;
    }

    public function set_fillcolor( $fillcolor ) {
        $this->fillcolor = $fillcolor;
    }

    public function set_fillopacity( $fillopacity ) {
        $this->fillopacity = $fillopacity;
    }

    // set all attributes at once
    public function set_properties( $color, $opacity, $weight, $dasharray, $fillcolor, $fillopacity ) {
        $this->set_color( $color );
        $this->set_opacity( $opacity );
        $this->set_weight( $weight );
        $this->set_dasharray( $dasharray );
        $this->set_fillcolor( $fillcolor );
        $this->set_fillopacity( $fillopacity );
    }

    // add Leaflet objects
    public function add_marker( $lon, $lat, $marker, $popuptext, $openpopup ) {
        $this->js_code .= 'L.marker(['.$lat.','.$lon.'], {icon: '.$marker.'})';
        if( $popuptext != '' ) { $this->js_code .= ".bindPopup('".$popuptext."')"; }
        $this->js_code .= '.addTo(l_standort)';
        if( $openpopup ) { $this->js_code .= '.openPopup()'; }
        $this->js_code .= ";\n";
        $this->adjust_boundingbox( $lon, $lat );
    }

    public function add_polyline( array $lonlat ) {
        $lat_lon_str = $this->lonlat2str( $lonlat );
        $this->js_code .= 'L.polyline( [ '.$lat_lon_str.' ], ';
        $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', stroke:true }";
        $this->js_code .= ' )';
        $this->js_code .= '.addTo(l_verbindung)';
        $this->js_code .= ";\n";
    }

    public function add_line( $lon1, $lat1, $lon2, $lat2 ) {
        $this->add_polyline( array( $lon1, $lat1, $lon2, $lat2  ) );    // wrapper for a simple line
    }

    public function add_polygon( array $lonlat, $popuptext ) {
        $lat_lon_str = $this->lonlat2str( $lonlat );
        $this->js_code .= 'L.polygon( [ '.$lat_lon_str.' ], ';
        $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', fillColor:'".$this->fillcolor."', fillOpacity:".$this->fillopacity." }";
        $this->js_code .= ' )';
        if( $popuptext != '' ) { $this->js_code .= ".bindPopup('".$popuptext."')"; }
        $this->js_code .= '.addTo(l_grenzen)';
        $this->js_code .= ";\n";
    }

    public function add_circle( $lon, $lat, $radius, $popuptext ) {
        $this->js_code .= 'L.circle(['.$lat.','.$lon.'], '.$radius.', ';
        $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', fillColor:'".$this->fillcolor."', fillOpacity:".$this->fillopacity." }";
        $this->js_code .= ' )';
        if( $popuptext != '' ) { $this->js_code .= ".bindPopup('".$popuptext."')"; }
        $this->js_code .= '.addTo(l_grenzen)';
        $this->js_code .= ";\n";
        $this->adjust_boundingbox( $lon, $lat );
    }

    public function add_circlemarker( $lon, $lat ) {
        $this->js_code .= 'L.circleMarker(['.$lat.','.$lon.'], ';
        $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', fillColor:'".$this->fillcolor."', fillOpacity:".$this->fillopacity." }";
        $this->js_code .= ' )';
        $this->js_code .= '.addTo(l_grenzen)';
        $this->js_code .= ";\n";
        $this->adjust_boundingbox( $lon, $lat );
    }

    public function add_rectangle( $lon1, $lat1, $lon2, $lat2, $popuptext ) {
        $this->js_code .= 'L.rectangle( [ ['.$lat1.','.$lon1.'], ['.$lat2.','.$lon2.'] ], ';
        $this->js_code .= "{ color:'".$this->color."', opacity:".$this->opacity.", weight:".$this->weight.", dashArray:'".$this->dasharray."', fillColor:'".$this->fillcolor."', fillOpacity:".$this->fillopacity." }";
        $this->js_code .= ' )';
        if( $popuptext != '' ) { $this->js_code .= ".bindPopup('".$popuptext."')"; }
        $this->js_code .= '.addTo(l_grenzen)';
        $this->js_code .= ";\n";
        $this->adjust_boundingbox( $lon1, $lat1 );
        $this->adjust_boundingbox( $lon2, $lat2 );
    }

    // print the generated javascript code
    public function print_javascript( $id ) {
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
        echo "   var popuptext = '<pre>Länge  : '+lon+'&deg;<br>Breite : '+lat+'&deg;<br>';\n\n";
            // here you can enter additional text to the popup show coordinates
            echo "   var url1 = '<a href=\"karte_ipac_umkreis.php?laenge='+lon+'&amp;breite='+lat+'&amp;umkreis=500'+'\">Umkreissuche 500m</a><br>';\n";
            echo "   var url2 = '<a href=\"karte_ipac_umkreis.php?laenge='+lon+'&amp;breite='+lat+'&amp;umkreis=1000'+'\">Umkreissuche 1 km</a><br>';\n";
            echo "   var url3 = '<a href=\"karte_ipac_umkreis.php?laenge='+lon+'&amp;breite='+lat+'&amp;umkreis=5000'+'\">Umkreissuche 5 km</a><br>';\n";
            echo "   popuptext = popuptext+'<br>'+url1+url2+url3;\n";
            // end
        echo "   popuptext = popuptext+'</pre>';\n\n";
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
