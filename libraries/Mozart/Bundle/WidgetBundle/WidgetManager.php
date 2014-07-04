<?php

namespace Mozart\Bundle\WidgetBundle;

/**
 * Class WidgetChain
 *
 * @package Mozart\Bundle\WidgetBundle
 */
class WidgetManager implements WidgetManagerInterface
{
    /**
     * @var WidgetInterface[]
     */
    protected $widgets = array();

    /**
     *
     */
    public function __construct()
    {
        $this->widgets = array();
    }

    /**
     * @return \Mozart\Bundle\WidgetBundle\WidgetInterface[]
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * @param string $widget
     */
    public function registerWidget(WidgetInterface $widget)
    {
        $this->widgets[get_class( $widget )] = $widget;
    }

}
