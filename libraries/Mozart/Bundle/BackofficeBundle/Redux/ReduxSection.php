<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\BackofficeBundle\Redux;


/**
 * Class ReduxSection
 *
 * @package Mozart\Bundle\BackofficeBundle\Redux
 */
class ReduxSection implements SectionInterface, \ArrayAccess
{

    /**
     * @var array
     */
    private $section;


    /**
     * @return array
     */
    public function __construct()
    {
        $this->section = array(
            'icon'   => $this->getIcon(),
            'title'  => $this->getTitle(),
            'desc'   => $this->getDescription(),
            'fields' => $this->getFields()
        );
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return array();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists( $offset )
    {
        return isset( $this->section[$offset] );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet( $offset )
    {
        return isset( $this->section[$offset] ) ? $this->section[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        if (is_null( $offset )) {
            $this->section[] = $value;
        } else {
            $this->section[$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     */
    public function offsetUnset( $offset )
    {
        unset( $this->section[$offset] );
    }
} 