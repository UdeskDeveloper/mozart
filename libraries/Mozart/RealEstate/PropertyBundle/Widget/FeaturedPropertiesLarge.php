<?php
namespace Mozart\RealEstate\PropertyBundle\Widget;

use Mozart\RealEstate\PropertyBundle\Helpers;

class FeaturedPropertiesLarge extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'FeaturedPropertiesLarge_Widget',
            __( 'Rhetina: Featured Properties Large', 'mozart' ),
            array(
                'classname'   => 'properties-large',
                'description' => __( 'Featured Properties Large', 'mozart' ),
            ) );
    }

    public function form($instance)
    {
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        } else {
            $title = __( 'Featured Properties', 'mozart' );
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
            <label for="<?php echo $this->get_field_id( 'shuffle' ); ?>"><?php echo __( 'Shuffle', 'mozart' ); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'shuffle' ); ?>" name="<?php echo $this->get_field_name( 'shuffle' ); ?>" value="1" <?php checked( $shuffle ); ?>>
        </p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance            = array();
        $instance['title']   = strip_tags( $new_instance['title'] );
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

        twiggy( 'PropertyBundle:Property:widget-large.html.twig', array(
            'title'         => apply_filters( 'widget_title', $instance['title'] ),
            'properties'    => Helpers::getInstance()->get_featured( 3, $do_shuffle ),
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title
        ) );
    }
}
