<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;

class AbstractFieldGroupManager
{
    protected $fieldGroups;

    public function __construct()
    {

        $this->fieldGroups = array();
    }

    public function registerFieldGroup(FieldGroupInterface $fieldGroup)
    {
    }

    public function registerFieldGroups()
    {
    }

    public function getFieldGroup($key)
    {
        return $this->fieldGroups[$key];
    }

    /**
     * @return mixed
     */
    public function getFieldGroups()
    {
        return $this->fieldGroups;
    }
}