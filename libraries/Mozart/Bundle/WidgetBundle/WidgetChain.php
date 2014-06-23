<?php

namespace Mozart\Bundle\WidgetBundle;

/**
 * Class WidgetChain
 *
 * @package Mozart\Bundle\WidgetBundle
 */
class WidgetChain
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
     * @param WidgetInterface $widget
     */
    public function registerWidget(WidgetInterface $widget)
    {
        $this->widgets[$widget->getName()] = $widget;
    }

}