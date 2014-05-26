<?php
namespace Mozart\RealEstate\PropertyBundle\Widget;

class EnquireProperties extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'EnquireProperties_Widget',
            __( 'Rhetina: Enquire Property', 'mozart' ),
            array(
                'classname'   => 'enquire',
                'description' => __( 'Enquire', 'mozart' ),
            ) );
    }

    public function form($instance)
    {
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        } else {
            $title = __( 'Enquire Now', 'mozart' );
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

        twiggy( 'PropertyBundle:Property:widgets/enquire.html.twig', array(
            'title'         => apply_filters( 'widget_title', $instance['title'] ),
            'post_id'       => $post->ID,
            'before_widget' => $before_widget,
            'after_widget'  => $after_widget,
            'before_title'  => $before_title,
            'after_title'   => $after_title
        ) );
    }
}
