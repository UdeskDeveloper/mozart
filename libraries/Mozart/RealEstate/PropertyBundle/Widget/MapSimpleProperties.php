<?php
namespace Mozart\RealEstate\PropertyBundle\Widget;

class MapSimpleProperties extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'MapSimpleProperties_Widget',
            __( 'Rhetina: Map Properties', 'mozart' ),
            array(
                'classname'   => 'properties',
                'description' => __( 'Map Properties', 'mozart' ),
            ) );
    }

    public function form($instance)
    {
        if ( isset( $instance['latitude'] ) ) {
            $latitude = $instance['latitude'];
        } else {
            $latitude = '34.019000';
        }

        if ( isset( $instance['longitude'] ) ) {
            $longitude = $instance['longitude'];
        } else {
            $longitude = '-118.455458';
        }

        if ( isset( $instance['zoom'] ) ) {
            $zoom = $instance['zoom'];
        } else {
            $zoom = '14';
        }

        if ( isset( $instance['height'] ) ) {
            $height = $instance['height'];
        } else {
            $height = '485px';
        }

        if ( isset( $instance['enable_geolocation'] ) ) {
            $enable_geolocation = $instance['enable_geolocation'];
        } else {
            $enable_geolocation = FALSE;
        }

        if ( isset( $instance['show_filter'] ) ) {
            $show_filter = $instance['show_filter'];
        } else {
            $show_filter = true;
        }

        if ( isset( $instance['horizontal_filter'] ) ) {
            $horizontal_filter = $instance['horizontal_filter'];
        } else {
            $horizontal_filter = FALSE;
        }

        if ( isset( $instance['map_filtering'] ) ) {
            $map_filtering = $instance['map_filtering'];
        } else {
            $map_filtering = FALSE;
        }

        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'latitude' ); ?>"><?php echo __( 'Latitude', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'latitude' ); ?>" name="<?php echo $this->get_field_name( 'latitude' ); ?>" type="text" value="<?php echo esc_attr( $latitude ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'longitude' ); ?>"><?php echo __( 'Longitude', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'longitude' ); ?>" name="<?php echo $this->get_field_name( 'longitude' ); ?>" type="text" value="<?php echo esc_attr( $longitude ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'zoom' ); ?>"><?php echo __( 'Zoom', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'zoom' ); ?>" name="<?php echo $this->get_field_name( 'zoom' ); ?>" type="text" value="<?php echo esc_attr( $zoom ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php echo __( 'Height', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'show_filter' ); ?>"><?php echo __( 'Show filter', 'mozart' ); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'show_filter' ); ?>" name="<?php echo $this->get_field_name( 'show_filter' ); ?>" value="1" <?php checked( $show_filter ); ?>>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'enable_geolocation' ); ?>"><?php echo __( 'Enable geolocation', 'mozart' ); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'enable_geolocation' ); ?>" name="<?php echo $this->get_field_name( 'enable_geolocation' ); ?>" value="1" <?php checked( $enable_geolocation ); ?>>
        </p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance                       = array();
        $instance['title']              = strip_tags( $new_instance['title'] );
        $instance['latitude']           = strip_tags( $new_instance['latitude'] );
        $instance['longitude']          = strip_tags( $new_instance['longitude'] );
        $instance['zoom']               = strip_tags( $new_instance['zoom'] );
        $instance['height']             = strip_tags( $new_instance['height'] );
        $instance['show_filter']        = strip_tags( $new_instance['show_filter'] );
        $instance['enable_geolocation'] = strip_tags( $new_instance['enable_geolocation'] );
        $instance['horizontal_filter']  = strip_tags( $new_instance['horizontal_filter'] );
        $instance['map_filtering']      = strip_tags( $new_instance['map_filtering'] );

        return $instance;
    }

    public function widget($args, $instance)
    {
        extract( $args );

        $price_range = array(
            'min' => parameter('property', 'filter', 'price_range_min', 0 ),
            'max' => parameter('property', 'filter', 'price_range_max', 1000000 ),
            'step' => parameter('property', 'filter', 'price_range_step', 10 )
        );

        $data['properties'] = \Mozart::service('realestate.property.model')
                ->getMostRecent();

        $data['properties'] = \Mozart::service('realestate.property.model')
                ->prepareForMapDisplay($data['properties']);

        twiggy( 'PropertyBundle:Property:map.html.twig', array(
            'latitude'           => $instance['latitude'],
            'longitude'          => $instance['longitude'],
            'zoom'               => $instance['zoom'],
            'height'             => $instance['height'],
            'properties'         => $data['properties'],
            'price_range'        => $price_range,
            'show_filter'        => $instance['show_filter'],
            'horizontal_filter'  => !empty($instance['horizontal_filter']) ? true : FALSE,
            'map_filtering'      => ! empty( $instance['map_filtering'] ) ? true : FALSE,
            'enable_geolocation' => ! empty( $instance['enable_geolocation'] ) ? true : FALSE,
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title
        ) );
    }
}
