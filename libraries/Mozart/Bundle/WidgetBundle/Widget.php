<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

use Symfony\Component\DependencyInjection\Container;
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

    /**
     *
     */
    public function __construct()
    {
        parent::__construct(
            $this->getAlias(),
            $this->getName(),
            $this->getOptions()
        );
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        $className = get_class($this);
        $classBaseName = substr(strrchr($className, '\\'), 1);

        return Container::underscore($classBaseName) . '_mozart';
    }

    /**
     * @return string
     */
    public function getName()
    {
        $alias = $this->getAlias();
        $alias = str_replace('_mozart', ' | mozart', $alias);

        return ucfirst($alias);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'classname' => $this->getAlias(),
            'description' => str_replace(' | mozart', '', $this->getName())
        );
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        parent::widget($args, $instance);
    }

    /**
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        return parent::update($new_instance, $old_instance);
    }

    /**
     * @param array $instance
     * @return string
     */
    public function form($instance)
    {
        return parent::form($instance);
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
