<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle\Redux\Section;

use Mozart\Bundle\OptionBundle\Redux\ReduxSection;

/**
 * Class MozartSection
 *
 * @package Mozart\Bundle\OptionBundle\Redux\Section
 */
class MozartSection extends ReduxSection
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
