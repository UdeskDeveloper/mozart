<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;

use Mozart\Component\Support\Str;

/**
 * Class ConfigPage
 * @package Mozart\Component\Config\Page
 */
abstract class ConfigPage implements ConfigPageInterface
{
    const DOMAIN = 'Mozart';
    /**
     * @var
     */
    protected $key;

    protected $type = 'options_page';

    protected function getClassBaseName()
    {
        $className = get_class( $this );
        return substr( strrchr( $className, '\\' ), 1 );
    }

    /**
     * @return string
     */
    public function getKey()
    {
        if (null === $this->key) {

            $this->key = Str::slug( Str::snake( static::DOMAIN . ' ' . $this->getClassBaseName() . ' Settings Page'  ), '-' );
        }

        return $this->key;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Str::title( static::DOMAIN . ': ' . Str::snake( $this->getClassBaseName(), ' ' ) . ' Settings' );
    }

    public function getShortName()
    {
        $shortName = Str::title( Str::snake( $this->getClassBaseName(), ' ' ) );

        return $shortName;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getUserCapabilities()
    {
        return 'edit_posts';
    }

    /**
     * @return int|bool
     */
    public function getMenuPosition()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getIconUrl()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function toRedirect()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
