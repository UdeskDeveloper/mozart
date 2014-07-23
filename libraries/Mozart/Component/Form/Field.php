<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Form;

use Mozart\Component\Config\ConfigFactory;

/**
 * Class Field
 * @package Mozart\Component\Form
 */
abstract class Field implements FieldInterface
{
    /**
     * @var ConfigFactory
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
     * @param ConfigFactory $builder
     * @param array $field
     * @param string $value
     */
    public function __construct( ConfigFactory $builder, $field = array(), $value = '' )
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
