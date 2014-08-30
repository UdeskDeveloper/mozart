<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;

use Mozart\Component\Support\Str;

class FieldGroup implements FieldGroupInterface
{
    const DOMAIN = 'Mozart';

    protected $key;
    /**
     * @var ConfigPageInterface
     */
    protected $configPage;

    /**
     * @param ConfigPageInterface $configPage
     */
    public function __construct(ConfigPageInterface $configPage)
    {
        $this->configPage = $configPage;
    }

    protected function getClassBaseName()
    {
        $className = get_class( $this );
        $className = str_replace( 'FieldGroup', '', $className );

        return substr( strrchr( $className, '\\' ), 1 );
    }



    /**
     * @inheritdoc
     */
    public function getKey()
    {
        if (null === $this->key) {

            $this->key = Str::slug( Str::snake( static::DOMAIN . ' ' . $this->getClassBaseName() ), '-' );
        }

        return $this->key;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Str::title( Str::snake( $this->getClassBaseName(), ' ' ) );
    }

    /**
     * @inheritdoc
     */
    public function getFields()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function getConfigPage()
    {
        return $this->configPage;
    }

    /**
     * @param ConfigPageInterface $configPage
     */
    public function setConfigPage(ConfigPageInterface $configPage)
    {
        $this->configPage = $configPage;
    }

    /**
     * @return array
     */
    public function getLocation()
    {
        return array(
            array(
                array(
                    'param'    => $this->configPage->getType(),
                    'operator' => '==',
                    'value'    => $this->configPage->getKey(),
                ),
            ),
        );
    }

	public function getDisplayOptions()
	{
		return array(
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'left',
			'instruction_placement' => 'label',
			'hide_on_screen'        => ''
		);
	}
}
