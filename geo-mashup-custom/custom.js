GeoMashup.addAction( 'objectIcon', function( properties, object ) {

    // Use a special icon in case the custom 'complete' var is set to 1
    if ( object.link_to_image_icon != null && typeof object.link_to_image_icon !== 'undefined' ) {

        object.icon.image = object.link_to_image_icon;
        object.icon.iconSize = [ 32, 37 ];

    } 
    
});

GeoMashup.addAction( 'termIcon', function( icon, taxonomy, term_id ){
    
    icon.image = icons[taxonomy][term_id];

});
/*

 */
 // An Example Google V3 customization

GeoMashup.addAction( 'loadedMap', function( properties, mxn ) {

    // Load some KML only into global maps - for instance pictures of squirrels
    //alert('sfdfsf');d
    var google_map = mxn.getMap();
    //alert('s');
    if (properties.map_content == 'global') {
        //alert('sdfdsf');
        //WE NEED TO BASICALLY CALL THE ADD AND REMOVE
        
        // Make the Google v3 call to add a Flickr KML layer

        var kml = new google.maps.KmlLayer( 'http://www.iceagetrail.org/wp-content/uploads/2015/12/iat-connector.kml', {
            map: google_map
        } );



        /*var kml = new google.maps.KmlLayer( kml_layer.kml_layer_1, {
            map: google_map
        } );*/

       /* var kml2 = new google.maps.KmlLayer( kml_layer.kml_layer_2, {
            map: google_map
        } );*/


        /*var kml3 = new google.maps.KmlLayer( kml_layer.kml_layer_3, {
            map: google_map
        } );*/

    }

} );

/*
** Customizes the look of map
 */
GeoMashup.addAction( 'loadedMap', function( properties, map ) {
    //alert('loaded');
    var google_map = map.getMap();

    var custom_styles = [{
        featureType: "all",
        elementType: "all",
        stylers: [
            { saturation: -20 }
        ]
    },{

        featureType: "administrative.locality",
        elementType: "all",
        stylers: [
            { visibility: "on" }
        ]
    },{
        featureType: "road.highway",
        elementType: "all",
        stylers: [
            { visibility: "on" },
            { hue: "#E39939" },
            { saturation: 70 },
            { lightness: 60 }
        ]
    },{

        featureType: "transit.station",
        elementType: "all",
        stylers: [
            { visibility: "on" },
            { color: "#f30000" },
        ]
    },{
        featureType:    "poi.park",
        elementType:    "geometry.fill",
        stylers: [ 
            { visibility:"on" },
            { color: "#9EDC88" },
            { saturation:0 },
            { lightness:0 },
        ]
    },{
        featureType:    "water",
        elementType:    "geometry.fill",
        stylers: [ 
            { visibility:"on" },
            { color: "#5794bf" },
            { saturation:-20 },
        ]
    },{
        featureType: "poi",
        elementType: "labels",
        stylers: [
            { visibility: "on" }
        ]
    },{
        featureType: "administrative.neighborhood",
        elementType: "all",
        stylers: [
            { visibility: "on" }
        ]
    },{
        featureType: "all",
        elementType: "labels.text",
        stylers: [
            { color: "#555555" },
            { weight: "0.1" },
        ]
    },{       
        featureType: "administrative.neighborhood",
        elementType: "labels.text",
        stylers: [
            { color: "#733e1d"},
            { weight: "0.1" },
        { visibility: "on" }
        ]
    },{       
        featureType: "transit.station",
        elementType: "labels.text",
        stylers: [
            { visibility: "on" },
            { color: "#f30000" },
            { weight:"0.05" },
        ]
    },{
        featureType: "transit.station.bus",
        elementType: "geometry",
        stylers: [
            { visibility: "on" },
            { color: "#f30000" },
            { weight:"0.05" },
        ]
    },{
        featureType: "transit.station.bus",
        elementType: "geometry",
        stylers: [
            { visibility: "on" },
            { color: "#f30000" },
            { weight:"0.05" },
        ]
    },{
        featureType: "road.arterial",
        elementType: "all",
        stylers: [
            { saturation: 70 },
            { hue: "#E39939" },
            { lightness: 30 }
        ]
    }];

    var map_type = new google.maps.StyledMapType( custom_styles, { name: 'trail-map' } );

    google_map.mapTypes.set( 'custom_styles', map_type );
    google_map.setMapTypeId( 'custom_styles', map_type );

    

}); 
