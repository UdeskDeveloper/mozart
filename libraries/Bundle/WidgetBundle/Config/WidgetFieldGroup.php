<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle\Config;

use Mozart\Bundle\WidgetBundle\WidgetInterface;
use Mozart\Component\Config\Page\FieldGroup;

class WidgetFieldGroup extends FieldGroup
{
    /**
     * @var WidgetInterface
     */
    protected $widget;

    public function __construct(WidgetInterface $widget)
    {
        $this->widget = $widget;
    }

    public function getLocation()
    {
        return array(
            array(
                array(
                    'param'    => 'widget',
                    'operator' => '==',
                    'value'    => $this->widget->getAlias(),
                ),
            ),
        );
    }

    /**
     * @return WidgetInterface
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * @param WidgetInterface $widget
     */
    public function setWidget(WidgetInterface $widget)
    {
        $this->widget = $widget;
    }

}
