<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option;

interface OptionBuilderInterface
{
    public function boot( $params = array() );

    public function getSections();

    public function getOptions();

    public function getParam( $param );
}
