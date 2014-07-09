<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Twig\Extension;


use Mozart\Bundle\OptionBundle\Model\OptionManagerInterface;

class OptionTemplateExtension extends \Twig_Extension
{
	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'mozart_option';
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
	 * @param OptionManagerInterface $optionManager
	 * @param string[]               $sectionOrder The order in which sections will be rendered.
	 */
	public function __construct( OptionManagerInterface $optionManager, array $sectionOrder = array() )
	{
		$this->optionManager = $optionManager;
		$this->sectionOrder  = $sectionOrder;
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
			new \Twig_SimpleFunction( 'setting', array( $this, 'findOneOptionByName' ) ),
			new \Twig_SimpleFunction( 'sett', array( $this, 'findOneOptionByName' ) ),
			new \Twig_SimpleFunction( 'option', array( $this, 'findOneOptionByName' ) ),
			new \Twig_SimpleFunction( 'opt', array( $this, 'findOneOptionByName' ) ),
		);
	}

	/**
	 * @param string[] $sections
	 *
	 * @return string[]
	 */
	public function sortSections( array $sections )
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

	/**
	 * @param string $name Name of the option
	 *
	 * @return bool|\Mozart\Bundle\OptionBundle\Model\Option|string
	 */
	public function findOneOptionByName( $name )
	{
		return $this->optionManager->findOneOptionByName( $name )->getValue();
	}
}