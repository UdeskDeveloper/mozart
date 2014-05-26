<?php

namespace Mozart\RealEstate\PropertyBundle\Shortcode;

use Mozart\Bundle\NucleusBundle\Shortcode\ShortcodeInterface;

class SubmittedPropertiesList implements ShortcodeInterface
{
    public function shortcode($params)
    {
        \Mozart::service('realestate.property.controller')->listUserSubmittedPropertiesAction();
    }
}
