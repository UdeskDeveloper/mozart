<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

use Mozart\Component\Support\Str;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Widget
 *
 * @package Mozart\Bundle\WidgetBundle
 */
class Widget extends \WP_Widget implements WidgetInterface, ContainerAwareInterface
{
    const NAME_SPACE = 'mozart';
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

    /**
     * @return string
     */
    public function getAlias()
    {
        $className = get_class( $this );
        $classBaseName = substr( strrchr( $className, '\\' ), 1 );

        return Str::snake( $classBaseName ) . '_' . static::NAME_SPACE;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $alias = $this->getAlias();
        $alias = str_replace( '_' . static::NAME_SPACE, ' | ' . static::NAME_SPACE, $alias );

        return ucwords( str_replace( array( '-', '_' ), ' ', $alias ) );
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'classname' => $this->getAlias(),
            'description' => str_replace( ' | ' . static::NAME_SPACE, '', $this->getName() )
        );
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        parent::widget( $args, $instance );
    }

    /**
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    /**
     * @param array $instance
     *
     * @return string
     */
    public function form($instance)
    {
        return '';
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
