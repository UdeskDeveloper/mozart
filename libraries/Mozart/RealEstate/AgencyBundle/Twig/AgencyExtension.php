<?php

namespace Mozart\RealEstate\AgencyBundle\Twig;

class AgencyExtension extends \Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('count_agency_properties', array($this, 'countAgencyProperties'))
        );
    }

    public function getName()
    {
        return 'realestate_agency';
    }

    public function countAgencyProperties($id)
    {
        return \Mozart::service('realestate.agency.model')->get_properties_count($id);
    }

}
