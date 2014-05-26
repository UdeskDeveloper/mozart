<?php

namespace Mozart\RealEstate\DeveloperBundle\Widget;

use Mozart\RealEstate\DeveloperBundle\Helpers;

class AssignedDeveloper extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
                'AssignedDevelopers_Widget', __('Rhetina: Assigned Developers', 'mozart'), array(
            'classname' => 'developers',
            'description' => __('Assigned Developers', 'mozart'),
        ));
    }

    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Assigned Developers', 'mozart');
        }

        if (isset($instance['count'])) {
            $count = $instance['count'];
        } else {
            $count = 3;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'mozart'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php echo __('Count', 'mozart'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);

        return $instance;
    }

    public function widget($args, $instance)
    {
        global $post;

        extract($args);

        if (!is_singular('property')) {
            return;
        }

        twiggy('developers/widget.twig', array(
            'title' => apply_filters('widget_title', $instance['title']),
            'count' => $instance['count'],
            'developers' => Helpers::getInstance()->get_assigned($post),
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title
        ));
    }

}
