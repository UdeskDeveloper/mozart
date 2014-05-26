<?php

namespace Mozart\RealEstate\PropertyBundle\Backoffice;

class OptionSection
{
    public function get()
    {
        return array(
            'title' => __('Property Settings', 'mozart'),
            'desc' => __('Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at: <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>', 'mozart'),
            'icon' => 'el-icon-home',
            // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
            'fields' => array(
                array(
                    'id' => 'section-property-general-start',
                    'type' => 'section',
                    'title' => __('General Options', 'mozart'),
                    'subtitle' => __('With the "section" field you can create indent option sections.', 'mozart'),
                    'indent' => false // Indent all options below until the next 'section' option is set.
                ),
                array(
                    'id' => 'contract-type-label',
                    'type' => 'switch',
                    'title' => __('Contract Type Label', 'mozart'),
                    'desc' => __('If checked all properties in grid version will have disabled label with information about the contract type.', 'mozart'),
                    "default" => 1,
                    'on' => 'Enabled',
                    'off' => 'Disabled',
                ),
                array(
                    'id' => 'frontend_needs_submission',
                    'type' => 'switch',
                    'title' => __('Approving frontend submission', 'mozart'),
                    'desc' => __('If checked, all fronted submissions need to be approved/published by administrator', 'mozart'),
                    "default" => 0
                ),
                array(
                    'id' => 'rating-for-property',
                    'type' => 'switch',
                    'title' => __('Ratings for properties', 'mozart'),
                    'desc' => __('If checked ratings will be enabled for properties.', 'mozart'),
                    "default" => 1,
                    'on' => 'Enabled',
                    'off' => 'Disabled',
                ),
                array(
                    'id' => 'show_only_checked_amenities',
                    'type' => 'switch',
                    'title' => __('Show only checked amenities', 'mozart'),
                    'desc' => __('If checked only checked amenities will display, otherwise all amenities will display.', 'mozart'),
                    "default" => 0,
                    'on' => 'Yes',
                    'off' => 'No',
                ),
                array(
                    'id' => 'section-property-general-end',
                    'type' => 'section',
                    'indent' => false // Indent all options below until the next 'section' option is set.
                ),
            )
        );
    }

}
