<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Form;

use Mozart\Component\Option\OptionBuilder;

/**
 * Class Field
 * @package Mozart\Component\Form
 */
abstract class Field implements FieldInterface
{
    /**
     * @var OptionBuilder
     */
    protected $builder;
    /**
     * @var array
     */
    protected $field;
    /**
     * @var string
     */
    protected $value;

    /**
     * @param OptionBuilder $builder
     * @param array $field
     * @param string $value
     */
    public function __construct( OptionBuilder $builder, $field = array(), $value = '' )
    {
        $this->builder = $builder;
        $this->field = $field;
        $this->value = $value;

        $this->initialize();
    }

    /**
     *
     */
    protected function initialize(){

    }
}
