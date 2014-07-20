<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option\Section;

interface SectionInterface
{
    public function getTitle();
    public function getIcon();
    public function getDescription();
    public function getFields();
}
