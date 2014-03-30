<?php

class WordLift_Geo_Widget extends WP_Widget
{

    /**
     * Sets up the widgets name etc
     */
    public function __construct()
    {
        // Initialize the Widget.
        parent::__construct(
            'wl_geo_widget', // Base ID
            __('Geo Widget', 'wordlift'), // Name
            array('description' => __('Geo Widget description', 'wordlift'),) // Args
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {

        // Don't show the widget if it's not a single post.
        if (!is_single()) {
            return;
        }

        // Get a reference to the post.
        global $post;

        // Don't show the widget if it's not an entity.
        if ('entity' !== $post->post_type) {
            $entity_ids = wl_get_related_entities($post->ID);
        } else {
            $entity_ids = array($post->ID);
        }

        // If there are no entity IDs, we don't show the map.
        if (0 === count($entity_ids)) {
            return;
        }

        // Prepare for min/max lat/long in case we need to define a view boundary for the client JavaScript.
        $min_latitude  = PHP_INT_MAX;
        $min_longitude = PHP_INT_MAX;
        $max_latitude  = ~PHP_INT_MAX;
        $max_longitude = ~PHP_INT_MAX;

        // Prepare an empty array of POIs.
        $pois          = array();

        // Add a POI for each entity that has coordinates.
        foreach ($entity_ids as $entity_id) {

            // Get the coordinates.
            $coordinates = wl_get_coordinates($entity_id);

            // Don't show the widget if the coordinates aren't set.
            if (!is_array($coordinates) || !is_numeric($coordinates['latitude']) || !is_numeric($coordinates['longitude'])) {
                continue;
            }

            $entity = get_post($entity_id);

            // Ignore entities that are not published.
            if ('publish' !== $entity->post_status) {
                continue;
            }

            // Get the title of the entity.
            $title   = htmlentities( $entity->post_title );
            $link    = htmlentities( get_permalink( $entity->ID ) );
            $content = json_encode( "<a href=\"$link\">$title</a>" );

            array_push( $pois, array(
                'latitude'     => $coordinates['latitude'],
                'longitude'    => $coordinates['longitude'],
                'popupContent' => $content
            ) );

            // TODO: calculate the type to choose a marker of the appropriate color.

            // Set a reference to the coordinates.
            $latitude =  & $coordinates['latitude'];
            if ( $latitude < $min_latitude ) {
                $min_latitude = $latitude;
            }
            if ( $latitude > $max_latitude ) {
                $max_latitude = $latitude;
            }
            $longitude =  & $coordinates['longitude'];
            if ( $longitude < $min_longitude ) {
                $min_longitude = $longitude;
            }
            if ( $longitude > $max_longitude ) {
                $max_longitude = $longitude;
            }
        }

        // If no pois are gathered, don't print the widget.
        if ( 0 === count($pois) ) {
            return;
        }
        // From here on, start printing the Widget output.

        // Enqueue the required scripts/stylesheets.
        wp_enqueue_style('wordlift_css', 'http://localhost:8000/app/css/wordlift.min.css');;
        wp_enqueue_style('leaflet_css', '//cdn.leafletjs.com/leaflet-0.7.2/leaflet.css');
        wp_enqueue_script('leaflet_js', '//cdn.leafletjs.com/leaflet-0.7.2/leaflet.js');

        // Print out the header.
        echo <<<EOF
        <script type="text/javascript">
            jQuery( function() {

                // Initialize the features array.
                var features = [], bounds = [];

EOF;

        // Print out each POI.
        foreach ($pois as $poi) {
            $popupContent = & $poi['popupContent'];
            $latitude     = & $poi['latitude'];
            $longitude    = & $poi['longitude'];

            // Print each feature.
            echo <<<EOF
                features.push({
                    "type": "Feature",
                    "properties": {
                        "popupContent": $popupContent
                    },
                    "geometry": {
                        "type": "Point",
                        "coordinates": [$longitude, $latitude]
                    }
                });

EOF;

        }

        // The element id for the map.
        $element_id = uniqid('map-');

        // Print the remainder of the JavaScript including the initialization stuff.
        echo <<<EOF

                    // create a map in the "map" div, set the view to a given place and zoom
                    var map = L.map('$element_id');

                    // Set the bounds of the map or the center, according on how many features we have on the map.
                    if (1 === features.length) {
                        map.setView([$latitude, $longitude], 13);
                    } else {
                        map.fitBounds([
                            [$min_latitude, $min_longitude],
                            [$max_latitude, $max_longitude]
                        ]);
                    }

                    // add an OpenStreetMap tile layer
                    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.geoJson(features, {
                        pointToLayer: function (feature, latlng) {
                            return L.marker(latlng, {});
                        },
                        onEachFeature: function onEachFeature(feature, layer) {
                            // does this feature have a property named popupContent?
                            if (feature.properties && feature.properties.popupContent) {
                                layer.bindPopup(feature.properties.popupContent);
                            }
                        }
                    }).addTo(map);

                } );
            </script>
EOF;

        // Get the widget's title.
        $title = apply_filters('widget_title', $instance['title']);

        // Print the HTML output.
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo <<<EOF
        <div class="wl-container" >
            <div class="wl-dummy" ></div >
            <div class="wl-element" id = "$element_id" ></div >
        </div >
EOF;

        echo $args['after_widget'];
    }

    /**
     * Ouputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public
    function form($instance)
    {

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'text_domain');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
    <?php

    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public
    function update($new_instance, $old_instance)
    {

        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }
}

function wl_register_geo_widget()
{

    register_widget('WordLift_Geo_Widget');
}

add_action('widgets_init', 'wl_register_geo_widget');