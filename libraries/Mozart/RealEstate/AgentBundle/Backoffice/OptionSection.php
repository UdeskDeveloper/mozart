<?php

namespace Mozart\RealEstate\AgentBundle\Backoffice;

class OptionSection
{
    public function get()
    {
        return array(
            'title' => __('Agents Settings', 'mozart'),
            'desc' => __('Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at: <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>', 'mozart'),
            'icon' => 'el-icon-home',
            // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
            'fields' => array(
                array(
                    'id' => 'agents_per_page',
                    'type' => 'slider',
                    'title' => __('Agents per page', 'mozart'),
                    'desc' => __('JQuery UI slider description. Min: 1, max: 500, step: 3, default value: 45', 'mozart'),
                    "default" => "9",
                    "min" => "1",
                    "step" => "1",
                    "max" => "500",
                )
            )
        );
    }

}
