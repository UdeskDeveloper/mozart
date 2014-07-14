<?php

namespace Mozart\Component\Option;

class Debugger
{
    protected $builder;

    public function init( OptionBuilderInterface $builder )
    {
        $this->builder = $builder;
    }

    public function render()
    {
        echo '<div id="dev_mode_default_section_group' . '" class="redux-group-tab">';
        echo '<h3>' . __( 'Options Object', 'mozart-options' ) . '</h3>';
        echo '<div class="redux-section-desc">';
        echo '<div id="redux-object-browser"></div>';
        echo '</div>';

        $json = json_encode( $this->builder->options, true );

        echo '<div id="redux-object-json" class="hide">' . $json . '</div>';

        echo '<a href="#" id="consolePrintObject" class="button">' . __(
                'Show Object in Javascript Console Object',
                'mozart-options'
            ) . '</a>';

        echo '</div>';
    }

    public function render_tab()
    {
        echo '<li id="dev_mode_default_section_group_li" class="redux-group-tab-link-li">';

        if (!empty( $this->builder->args['icon_type'] ) && $this->builder->args['icon_type'] == 'image') {
            $icon = ( !isset( $this->builder->args['dev_mode_icon'] ) ) ? '' : '<img src="' . $this->builder->args['dev_mode_icon'] . '" /> ';
        } else {
            $icon_class = ( !isset( $this->builder->args['dev_mode_icon_class'] ) ) ? '' : ' ' . $this->builder->args['dev_mode_icon_class'];
            $icon = ( !isset( $this->builder->args['dev_mode_icon'] ) ) ? '<i class="el-icon-info-sign' . $icon_class . '"></i>' : '<i class="icon-' . $this->builder->args['dev_mode_icon'] . $icon_class . '"></i> ';
        }

        echo '<a href="javascript:void(0);" id="dev_mode_default_section_group_li_a" class="redux-group-tab-link-a custom-tab" data-rel="dev_mode_default">' . $icon . ' <span class="group_title">' . __(
                'Options Object',
                'mozart-options'
            ) . '</span></a>';
        echo '</li>';
    }

    public function add_submenu()
    {
        add_submenu_page(
            $this->builder->args['page_slug'],
            __( 'Options Object', 'mozart-options' ),
            __( 'Options Object', 'mozart-options' ),
            $this->builder->args['page_permissions'],
            $this->builder->args['page_slug'] . '&tab=dev_mode_default',
            '__return_null'
        );
    }
}
