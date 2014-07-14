<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Builder;


class OptionBuilder extends \ReduxFramework implements OptionBuilderInterface
{

    public function __construct()
    {

    }

    public function boot( $sections = array(), $args = array(), $extra_tabs = array() )
    {
        parent::__construct( $sections, $args, $extra_tabs );
    }
} 