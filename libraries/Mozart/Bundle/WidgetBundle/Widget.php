<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Widget
 * @package Mozart\Bundle\WidgetBundle
 */
class Widget extends \WP_Widget implements WidgetInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct()
    {
        parent::__construct(
            $this->getAlias(),
            $this->getName(),
            $this->getOptions()
        );
    }

    public function getAlias() {
        return 'mozart_' . strtolower(get_class($this));
    }

    public function getName() {
        return get_class($this);
    }

    public function getOptions() {
        return array(
            'classname' => $this->getAlias(),
            'description' => $this->getName()
        );
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        parent::widget($args, $instance); // TODO: Change the autogenerated stub
    }

    /**
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        return parent::update($new_instance, $old_instance); // TODO: Change the autogenerated stub
    }

    /**
     * @param array $instance
     * @return string
     */
    public function form($instance)
    {
        return parent::form($instance); // TODO: Change the autogenerated stub
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}