<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;


interface FieldGroupInterface
{

    public function getKey();

    public function getName();

    public function getFields();

    /**
     * @return ConfigPageInterface
     */
    public function getConfigPage();
}