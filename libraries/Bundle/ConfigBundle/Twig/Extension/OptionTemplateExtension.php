<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ConfigBundle\Twig\Extension;

use Mozart\Bundle\ConfigBundle\Model\OptionManagerInterface;
use Mozart\Component\Config\ConfigFactory;

class OptionTemplateExtension extends \Twig_Extension
{
    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'mozart_config';
    }

    /**
     * @var string[]
     */
    protected $sectionOrder;

    /**
     * @var OptionManagerInterface $optionManager
     */
    protected $optionManager;

    /**
     * @param ConfigFactory          $configFactory
     * @param OptionManagerInterface $optionManager
     * @param string[]               $sectionOrder  The order in which sections will be rendered.
     */
    public function __construct(
        ConfigFactory $configFactory,
        OptionManagerInterface $optionManager,
        array $sectionOrder = array()
    ) {
        $this->optionManager = $optionManager;
        $this->sectionOrder = $sectionOrder;
        $this->configFactory = $configFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter( 'section_sort', array( $this, 'sortSections' ) ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
//            new \Twig_SimpleFunction( 'setting', array( $this, 'findOneOptionByName' ) ),
//            new \Twig_SimpleFunction( 'sett', array( $this, 'findOneOptionByName' ) ),
            new \Twig_SimpleFunction( 'option', array( $this, 'getConfig' ) ),
            new \Twig_SimpleFunction( 'opt', array( $this, 'getConfig' ) ),
            new \Twig_SimpleFunction( 'config', array( $this, 'getConfig' ) )
        );
    }

    /**
     * @param string[] $sections
     *
     * @return string[]
     */
    public function sortSections(array $sections)
    {
        $finalSectionOrder = array();

        // add null section first (if it exists)
        $nullIndex = array_search( null, $sections );
        if ($nullIndex !== false) {
            $finalSectionOrder[] = $sections[$nullIndex];
            unset( $sections[$nullIndex] );
        }

        // add sections in given order
        foreach (array_intersect( $this->sectionOrder, $sections ) as $section) {
            $finalSectionOrder[] = $section;
        }

        // add remaining sections
        foreach (array_diff( $sections, $this->sectionOrder ) as $section) {
            $finalSectionOrder[] = $section;
        }

        return $finalSectionOrder;
    }

    public function getConfig($name)
    {
        return $this->configFactory->getOption($name);
    }

    /**
     * @param string $name Name of the option
     *
     * @return bool|\Mozart\Bundle\ConfigBundle\Model\Option|string
     */
    public function findOneOptionByName($name)
    {
        return $this->optionManager->findOneOptionByName( $name )->getValue();
    }
}
