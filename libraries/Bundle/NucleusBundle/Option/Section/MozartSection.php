<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\NucleusBundle\Option\Section;

use Mozart\Component\Config\Section\ConfigSection;

/**
 * Class MozartSection
 *
 * @package Mozart\Bundle\ConfigBundle\Option\Section
 */
class MozartSection extends ConfigSection
{

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'el-icon-home';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'General';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Mozart Options';
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return array(

        );
    }
}