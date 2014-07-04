<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle;

/**
 * Interface WidgetInterface
 * @package Mozart\Bundle\WidgetBundle
 */
interface WidgetInterface
{
    /**
     * @return mixed
     */
    public function getAlias();

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getOptions();
}
