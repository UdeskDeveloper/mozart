<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option\Section;

interface OptionSectionInterface
{
    public function getTitle();
    public function getAlias();
    public function getConfiguration();
    public function getIcon();
    public function getDescription();
    public function getFields();
}
