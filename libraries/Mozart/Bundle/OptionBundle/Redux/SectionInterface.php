<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Redux;


interface SectionInterface  {
    public function getTitle();
    public function getIcon();
    public function getDescription();
    public function getFields();
} 