<?php
namespace Mozart\RealEstate\PropertyBundle\Widget;

use Mozart\RealEstate\PropertyBundle\Helpers;

class SliderProperties extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'SliderProperties_Widget',
            __( 'Rhetina: Property Slider', 'mozart' ),
            array(
                'classname'   => 'property-slider',
                'description' => __( 'Property slider', 'mozart' ),
            ) );
    }

    public function form($instance)
    {
        if ( isset( $instance['properties'] ) ) {
            $properties = $instance['properties'];
        }

        if ( isset( $instance['show_filter'] ) ) {
            $show_filter = $instance['show_filter'];
        } else {
            $show_filter = true;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'properties' ); ?>"><?php echo __( 'Properties - property IDs (eg. 11,25,36)', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'properties' ); ?>" name="<?php echo $this->get_field_name( 'properties' ); ?>" type="text" value="<?php echo esc_attr( $properties ); ?>" />
        </p>

    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance                = array();
        $instance['properties']  = strip_tags( $new_instance['properties'] );
        $instance['show_filter'] = strip_tags( $new_instance['show_filter'] );

        return $instance;
    }

    public function widget($args, $instance)
    {
        global $post;
        extract( $args );

        $posts = array();
        $parts = explode( ',', $instance['properties'] );

        foreach ($parts as $part) {
            $posts[] = trim( $part );
        }

        $args = array(
            'post__in'       => $posts,
            'post_type'      => 'property',
            'posts_per_page' => - 1,
        );

        $price_range = array(
            'min' => parameter('property', 'filter', 'price_range_min', 0 ),
            'max' => parameter('property', 'filter', 'price_range_max', 1000000 ),
            'step' => parameter('property', 'filter', 'price_range_step', 10 )
        );

        twiggy( 'PropertyBundle:Property:slider-large.html.twig', array(
            'id'            => $this->id,
            'properties'    => Helpers::getInstance()->prepare( new WP_Query( $args ) ),
            'price_range'   => $price_range,
            'before_widget' => $before_widget,
            'after_widget'  => $after_widget,
            'before_title'  => $before_title,
            'after_title'   => $after_title
        ) );
    }
}
