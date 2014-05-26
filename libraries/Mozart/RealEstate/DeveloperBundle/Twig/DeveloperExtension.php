<?php

namespace Mozart\RealEstate\DeveloperBundle\Twig;

class DeveloperExtension extends \Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('count_developer_properties', array($this, 'countDeveloperProperties'))
        );
    }

    public function getName()
    {
        return 'realestate_developer';
    }

    public function countDeveloperProperties($id)
    {
        return \Mozart::service('realestate.developer.model')->get_properties_count($id);
    }

}
