<?php
namespace Mozart\Bundle\NucleusBundle\Widget;

class CallToAction extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'CallToAction_Widget',
            __( 'Rhetina: Call to Action', 'mozart' ),
            array(
                'classname'   => 'call-to-action',
                'description' => __( 'Call to Action', 'mozart' ),
            ) );
    }

    public function form($instance)
    {
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        } else {
            $title = __( 'Call to action', 'mozart' );
        }

        if ( isset( $instance['link'] ) ) {
            $link = $instance['link'];
        }

        if ( isset( $instance['text'] ) ) {
            $text = $instance['text'];
        }

        if ( isset( $instance['class'] ) ) {
            $class = $instance['class'];
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php echo __( 'Text', 'mozart' ); ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_attr( $text ); ?></textarea>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php echo __( 'Link', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php echo __( 'Class', 'mozart' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'class' ); ?>" id="<?php echo $this->get_field_id( 'class' ); ?>" class="widefat">
                <option value="address" <?php if (esc_attr( $class ) == 'address'): ?>selected<?php endif; ?>><?php echo __( 'Address', 'mozart' ); ?></option>
                <option value="gps" <?php if (esc_attr( $class ) == 'gps'): ?>selected<?php endif; ?>><?php echo __( 'GPS', 'mozart' ); ?></option>
                <option value="key" <?php if (esc_attr( $class ) == 'key'): ?>selected<?php endif; ?>><?php echo __( 'Key', 'mozart' ); ?></option>
            </select>
        </p>

    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance          = array();
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['text']  = strip_tags( $new_instance['text'] );
        $instance['link']  = strip_tags( $new_instance['link'] );
        $instance['class'] = strip_tags( $new_instance['class'] );

        return $instance;
    }

    public function widget($args, $instance)
    {
        extract( $args );

        twiggy( 'NucleusBundle:Nucleus:widgets/calltoaction.html.twig', array(
            'title'         => apply_filters( 'widget_title', $instance['title'] ),
            'text'          => $instance['text'],
            'link'          => $instance['link'],
            'class'         => $instance['class'],
            'before_widget' => $before_widget,
            'after_widget'  => $after_widget,
            'before_title'  => $before_title,
            'after_title'   => $after_title
        ) );
    }
}
