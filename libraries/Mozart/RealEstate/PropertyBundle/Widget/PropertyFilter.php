<?php
namespace Mozart\RealEstate\PropertyBundle\Widget;

class PropertyFilter extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'PropertyFilter_Widget',
            __( 'Rhetina:Property Filter', 'mozart' ),
            array(
                'classname'   => 'enquire',
                'description' => __( 'Property Filter', 'mozart' ),
            ) );
    }

    public function form($instance)
    {
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        } else {
            $title = __( 'Property Filter', 'mozart' );
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance          = array();
        $instance['title'] = strip_tags( $new_instance['title'] );

        return $instance;
    }

    public function widget($args, $instance)
    {
        global $post;

        extract( $args );

        $price_range = array(
            'min' => parameter('property', 'filter', 'price_range_min', 0 ),
            'max' => parameter('property', 'filter', 'price_range_max', 1000000 ),
            'step' => parameter('property', 'filter', 'price_range_step', 10 )
        );

        twiggy( 'PropertyBundle:Property:filter.html.twig', array(
            'id'            => $this->id,
            'title'         => apply_filters( 'widget_title', $instance['title'] ),
            'price_range'   => $price_range,
            'before_widget' => $before_widget,
            'after_widget'  => $after_widget,
            'before_title'  => $before_title,
            'after_title'   => $after_title
        ) );
    }
}
