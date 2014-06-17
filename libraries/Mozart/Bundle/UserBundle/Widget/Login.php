<?php

namespace Mozart\Bundle\UserBundle\Widget;

class Login extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
                'Login_Widget', __('Rhetina: Login', 'mozart'), array(
            'classname' => 'login',
            'description' => __('Login', 'mozart'),
        ));
    }

    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Login', 'mozart');
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'mozart'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    public function widget($args, $instance)
    {
        extract($args);

        twiggy('accounts/login.twig', array(
            'title' => apply_filters('widget_title', $instance['title']),
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title
        ));
    }

}
