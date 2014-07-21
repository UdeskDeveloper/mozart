<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Form;

use Mozart\Component\Option\OptionBuilderInterface;

/**
 * Class Field
 * @package Mozart\Component\Form
 */
abstract class Field implements FieldInterface
{
    /**
     * @var OptionBuilderInterface
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
     * @param OptionBuilderInterface $builder
     * @param array $field
     * @param string $value
     */
    public function __construct( OptionBuilderInterface $builder, $field = array(), $value = '' )
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
