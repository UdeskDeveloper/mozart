<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option;


interface OptionBuilderInterface
{
    public function boot( $sections = array(), $args = array(), $extra_tabs = array() );
} 