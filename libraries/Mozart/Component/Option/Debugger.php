<?php

namespace Mozart\Component\Option;

/**
 * Class Debugger
 * @package Mozart\Component\Option
 */
class Debugger
{
    /**
     * @var OptionBuilder
     */
    protected $builder;

    private $enabled = false;

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param OptionBuilder $builder
     */
    public function init(OptionBuilder $builder)
    {
        $this->enabled = true;
        $this->builder = $builder;
    }

    /**
     * @return string
     */
    public function render()
    {
        $html = '';
        $html .= '<div id="dev_mode_default_section_group' . '" class="redux-group-tab">';
        $html .= '<h3>' . __( 'Options Object', 'mozart-options' ) . '</h3>';
        $html .= '<div class="redux-section-desc">';
        $html .= '<div id="redux-object-browser"></div>';
        $html .= '</div>';

        $json = json_encode( $this->builder->getOptions(), true );

        $html .= '<div id="redux-object-json" class="hide">' . $json . '</div>';

        $html .= '<a href="#" id="consolePrintObject" class="button">' . __(
                'Show Object in Javascript Console Object',
                'mozart-options'
            ) . '</a>';

        $html .= '</div>';

        return $html;
    }

    /**
     *
     */
    public function add_submenu()
    {
        add_submenu_page(
            $this->builder->getParam('page_slug'),
            __( 'Options Object', 'mozart-options' ),
            __( 'Options Object', 'mozart-options' ),
            $this->builder->getParam('page_permissions'),
            $this->builder->getParam('page_slug') . '&tab=dev_mode_default',
            '__return_null'
        );
    }
}
