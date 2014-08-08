<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

interface WidgetManagerInterface
{
    public function getWidgets();

    public function registerWidget( WidgetInterface $widget );
}
