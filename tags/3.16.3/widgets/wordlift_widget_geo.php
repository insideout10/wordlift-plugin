<?php
/**
 * This file contains the Geo Widget class.
 */

/**
 * Class WordLift_Geo_Widget
 */
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
    public function widget( $args, $instance )
    {
        // Get the widget's title.
        $title = apply_filters('widget_title', $instance['title']);

        // Print the HTML output.
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // Print the geomap shortcode
        // ( global = true - because it is not post-specific)
        echo do_shortcode('[wl_geomap global=true]');

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
            $title = __('New title', 'wordlift');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:'); ?></label>
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
