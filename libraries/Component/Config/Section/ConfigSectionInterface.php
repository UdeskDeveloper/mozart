<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Section;

interface ConfigSectionInterface
{
    public function getTitle();
    public function getAlias();
    public function getConfiguration();
    public function getIcon();
    public function getDescription();
    public function getFields();
}
