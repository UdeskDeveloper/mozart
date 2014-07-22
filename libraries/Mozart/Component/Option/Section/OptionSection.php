<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Option\Section;

use Mozart\Component\Support\Str;

/**
 * Class OptionSection
 * @package Mozart\Component\Option\Section
 */
class OptionSection implements SectionInterface
{
    public function __construct()
    {
    }

    public function getAlias() {
        $className     = get_class( $this );
        $classBaseName = substr( strrchr( $className, '\\' ), 1 );

        return Str::snake( $classBaseName );
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        $conf = array(
            'id' => $this->getAlias(),
            'icon' => $this->getIcon(),
            'title' => $this->getTitle(),
            'desc' => $this->getDescription(),
            'subsection' => false,
            'type' => null
        );
        if ($this->getParent() !== '') {
            $conf['subsection'] = true;
        }
        $conf['fields'] = (array) $this->getFields();

        return $conf;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return '';
    }

    /**
     * The icon to be displayed next to the section title.
     * This could be a preset Elusive Icon or a URL to an icon of your own.
     *
     * @return string
     */
    public function getIcon()
    {
        return '';
    }

    /**
     * The title of the section that will appear on the option tab.
     *
     * @return string
     */
    public function getTitle()
    {
        $alias = $this->getAlias();

        return translate( Str::studly($alias) );
    }

    /**
     * Text to appear under the section title. HTML is permitted.
     *
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     *
     * @return array
     */
    public function getFields()
    {
        return array();
    }

}
