<?php
/**
 * User: David Riccitelli
 * Date: 20/09/12 09:28
 */


class WordLift_GeoMap
{

    public $defaultTypes = array(
        "Person",
        "Place",
        "Organization",
        "Event",
        "CreativeWork"
    );
    // public $urlTemplate = "wp-admin/admin-ajax.php?action=wordlift.georss&type={type}";
    // public $markerUrlTemplate = "wp-content/plugins/wordlift/images/marker_{type}.svg";

    public function get($attributes, $content = NULL)
    {

        // javascript code fragment.
        $fragment = "";

        $urlTemplate = site_url(
            "wp-admin/admin-ajax.php?action=wordlift.georss&type={type}"
        );
        $markerUrlTemplate = site_url(
            "wp-content/plugins/wordlift/images/marker_{type}.svg"
        );

        $geoCount = count( $this->defaultTypes );
        foreach ( $this->defaultTypes as &$type ) :
            $url = str_replace( "{type}", $type, $urlTemplate);
            $markerUrl = str_replace( "{type}", strtolower( $type ), $markerUrlTemplate );
            $fragment .= <<<EOF

$( that ).mapify( 'geoRSS', {
    url: '$url',
    title: '$type',
    className: {
        tag: 'category',
        attribute: 'term'
    },
    externalGraphic: {
        url: '$markerUrl',
        width: 9,
        height: 17,
        select: { width: 14, height: 26 }
    }
});
EOF;

        endforeach;


        // $geoMapURL = site_url( "wp-admin/admin-ajax.php?action=wordlift.georss" );
        // $markerImageURL= site_url( "wp-content/plugins/wordlift/images/noun_project_462.svg" );
        $popupContent =<<< EOF
<div class="{className}">{summary}</div>
EOF;

$content = <<<EOF

<div id="wordlift" class="container" style="width:100%; height: 400px;">
</div>

<script type="text/javascript">
    jQuery( function(\$) {
        var geoCount = $geoCount;

        $('#wordlift.container')
            .on('mapify.create', function (event) {
                var that = this;

                $.ajax( 'http://maps.stamen.com/js/tile.stamen.js?v1.2.1', {
                    dataType: "script",
                    success: function(data, textStatus, jqXHR) {
                        var map = $( that ).data('map');
                        var stamenLayer = new OpenLayers.Layer.Stamen( 'watercolor' );
                        map.addLayer( stamenLayer );
                        map.setBaseLayer( stamenLayer );

                        $fragment

                    }
                });

            })
            .on('mapify.georss', function (event, layer) {

                if ( 0 === --geoCount ) {

                    var layers = $(this).data( 'map' ).layers.slice( 2 );
                    $(this).mapify('popupControl', {
                        layers: layers,
                        size: {
                            width: 210,
                            height: 250
                        },
                        content: '$popupContent'
                    });

                    $( '.olControlAttribution' ).html( 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a>.' );
                }

            })
            .mapify({
                elementId: 'map',
                openLayersURL: 'http://dev.openlayers.org/releases/OpenLayers-2.11/OpenLayers.js',
                cache: true,
                zoom: 5,
                title: 'World Map',
                location: {latitude:41.91613, longitude:12.503052}
            });
    });
</script>

EOF;

        return $content;

    }

}

?>