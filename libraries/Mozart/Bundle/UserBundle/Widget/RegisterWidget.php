<?php
namespace Mozart\Bundle\UserBundle\Widget;

/**
 * Class RegisterWidget
 *
 * @package Mozart\Bundle\UserBundle\Widget
 */
class RegisterWidget extends \WP_Widget
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct(
            'Register_Widget',
            __( 'Rhetina: Register', 'mozart' ),
            array(
                'classname'   => 'register',
                'description' => __( 'Register', 'mozart' ),
            )
        );
    }

    /**
     * @param array $instance
     */
    public function form( $instance )
    {
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        } else {
            $title = __( 'Register', 'mozart' );
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'mozart' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>"/>
        </p>
    <?php
    }

    /**
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array
     */
    public function update( $new_instance, $old_instance )
    {
        $instance          = array();
        $instance['title'] = strip_tags( $new_instance['title'] );

        return $instance;
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance )
    {
        echo \Mozart::service( 'templating' )->render(
            'accounts/register.twig',
            array(
                'title'         => apply_filters( 'widget_title', $instance['title'] ),
                'before_widget' => $args['before_widget'],
                'after_widget'  => $args['after_widget'],
                'before_title'  => $args['before_title'],
                'after_title'   => $args['after_title']
            )
        );
    }

}
