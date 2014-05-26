<?php

namespace Mozart\RealEstate\PropertyBundle\Widget;

class CarouselProperties extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
                'CarouselProperties_Widget', __('Rhetina: Carousel Properties', 'mozart'), array(
            'classname' => 'property-carousel',
            'description' => __('Carousel Properties', 'mozart'),
                )
        );
    }

    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Carousel Properties', 'mozart');
        }

        if (isset($instance['count'])) {
            $count = $instance['count'];
        } else {
            $count = 10;
        }

        if (isset($instance['type'])) {
            $type = $instance['type'];
        } else {
            $type = 'new';
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'mozart'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php echo __('Count', 'mozart'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>"
                   name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($count); ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('type'); ?>"><?php echo __('Type', 'mozart'); ?></label>

            <select class="widefat" id="<?php echo $this->get_field_id('type'); ?>"
                    name="<?php echo $this->get_field_name('type'); ?>" type="select" value="<?php echo esc_attr($type); ?>">

                <option <?php
        if ($type == 'new') {
            echo "selected=selected";
        }
        ?> value="new"><?php
                echo __(
                        'New', 'mozart'
                );
                ?></option>
                <option <?php
                if ($type == 'featured') {
                    echo "selected=selected";
                }
                ?> value="featured"><?php
                        echo __(
                                'Featured', 'mozart'
                        );
                        ?></option>

                <option <?php
                        if ($type == 'random') {
                            echo "selected=selected";
                        }
                        ?> value="random"><?php
            echo __(
                    'Random', 'mozart'
            );
                        ?></option>

                <option <?php
            if ($type == 'reduced') {
                echo "selected=selected";
            }
                        ?> value="reduced"><?php
        echo __(
                'Reduced', 'mozart'
        );
        ?></option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);
        $instance['type'] = strip_tags($new_instance['type']);

        return $instance;
    }

    public function widget($args, $instance)
    {
        extract($args);
        $instance['count'] = !empty($instance['count']) ? $instance['count'] : 3;
        $instance['type'] = !empty($instance['type']) ? $instance['type'] : 'new';
        switch ($instance['type']) {
            case 'featured':
                $properties = \Mozart::service('realestate.property.model')->get_featured($instance['featured']);
                break;
            case 'reduced':
                $properties = \Mozart::service('realestate.property.model')->get_reduced($instance['count']);
                break;
            case 'new':
                $properties = \Mozart::service('realestate.property.model')->get_most_recent($instance['count']);
                break;
            case 'random':
                $properties = \Mozart::service('realestate.property.model')->get_most_recent($instance['count'], true);
                break;
            default:
                $properties = \Mozart::service('realestate.property.model')->get_most_recent($instance['count']);
                break;
        }

        twiggy(
                'PropertyBundle:Property:carousel.html.twig', array(
            'title' => apply_filters('widget_title', $instance['title']),
            'properties' => $properties,
            'before_widget' => $before_widget,
            'after_widget' => $after_widget,
            'before_title' => $before_title,
            'after_title' => $after_title
                )
        );
    }

}
