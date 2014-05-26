<?php
namespace Mozart\RealEstate\PropertyBundle\Widget;

class ReducedProperties extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'ReducedProperties_Widget',
            __( 'Rhetina: Reduced Properties', 'mozart' ),
            array(
                'classname'   => 'properties',
                'description' => __( 'Reduced Properties', 'mozart' ),
            ) );
    }

    public function form($instance)
    {
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        } else {
            $title = __( 'Reduced Properties', 'mozart' );
        }

        if ( isset( $instance['count'] ) ) {
            $count = $instance['count'];
        } else {
            $count = 3;
        }

        if ( isset( $instance['shuffle'] ) ) {
            $shuffle = $instance['shuffle'];
        } else {
            $shuffle = FALSE;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php echo __( 'Count', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'shuffle' ); ?>"><?php echo __( 'Shuffle', 'mozart' ); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'shuffle' ); ?>" name="<?php echo $this->get_field_name( 'shuffle' ); ?>" value="1" <?php checked( $shuffle ); ?>>
        </p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance            = array();
        $instance['title']   = strip_tags( $new_instance['title'] );
        $instance['count']   = strip_tags( $new_instance['count'] );
        $instance['shuffle'] = strip_tags( $new_instance['shuffle'] );

        return $instance;
    }

    public function widget($args, $instance)
    {
        extract( $args );

        $do_shuffle = FALSE;
        if ( ! empty( $instance['shuffle'] ) && $instance['shuffle'] ) {
            $do_shuffle = true;
        }

        twiggy( 'PropertyBundle:Property:widget.html.twig', array(
            'title'         => apply_filters( 'widget_title', $instance['title'] ),
            'count'         => $instance['count'],
            'properties'    => \Mozart::service('realestate.property.model')->get_reduced( $instance['count'], $do_shuffle ),
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title
        ) );
    }
}
