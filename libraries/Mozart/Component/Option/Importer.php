<?php
namespace Mozart\Component\Option;

/**
 * Class Importer
 * @package Mozart\Component\Option
 */
class Importer
{
    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @var array
     */
    public $field_args = array();

    /**
     * @var
     */
    private $builder;

    /**
     * @param OptionBuilderInterface $builder
     */
    public function init(OptionBuilderInterface $builder)
    {
        $this->builder = $builder;

        $this->checkEnabled() ;

        add_action( "wp_ajax_redux_link_options", array( $this, "link_options" ) );
        add_action( "wp_ajax_nopriv_redux_link_options", array( $this, "link_options" ) );

        add_action( "wp_ajax_redux_download_options", array( $this, "download_options" ) );
        add_action( "wp_ajax_nopriv_redux_download_options", array( $this, "download_options" ) );
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @return void
     */
    public function render()
    {
        $secret = md5( md5( AUTH_KEY . SECURE_AUTH_KEY ) . '-' . $this->builder->args['opt_name'] );

        $c = '';
        $bDoClose = false;

        if (false == $this->isEnabled()) {
            $c = 'redux-group-tab hide';
        } elseif (true == $this->isEnabled() && false == $this->field_args['full_width']) {
            echo '</td></tr></table><table class="form-table no-border redux-group-table redux-raw-table" style="margin-top: -20px;"><tbody><tr><td>';
            $bDoClose = true;
        }

        echo '<div id="import_export_default_section_group' . '" class="' . $c . '">';

        if (false == $this->isEnabled()) {
            echo '<h3>' . __( 'Import / Export Options', 'mozart-options' ) . '</h3>';
        }

        echo '<h4>' . __( 'Import Options', 'mozart-options' ) . '</h4>';
        echo '<p><a href="javascript:void(0);" id="redux-import-code-button" class="button-secondary">' . __(
                'Import from file',
                'mozart-options'
            ) . '</a> <a href="javascript:void(0);" id="redux-import-link-button" class="button-secondary">' . __(
                'Import from URL',
                'mozart-options'
            ) . '</a></p>';

        echo '<div id="redux-import-code-wrapper">';
        echo '<p class="description" id="import-code-description">' . apply_filters(
                'redux-import-file-description',
                __(
                    'Input your backup file below and hit Import to restore your sites options from a backup.',
                    'mozart-options'
                )
            ) . '</p>';
        echo '<textarea id="import-code-value" name="' . $this->builder->args['opt_name'] . '[import_code]" class="large-text noUpdate" rows="8"></textarea>';
        echo '</div>';

        echo '<div id="redux-import-link-wrapper">';
        echo '<p class="description" id="import-link-description">' . apply_filters(
                'redux-import-link-description',
                __(
                    'Input the URL to another sites options set and hit Import to load the options from that site.',
                    'mozart-options'
                )
            ) . '</p>';
        echo '<input type="text" id="import-link-value" name="' . $this->builder->args['opt_name'] . '[import_link]" class="large-text noUpdate" value="" />';
        echo '</div>';

        echo '<p id="redux-import-action"><input type="submit" id="redux-import" name="' . $this->builder->args['opt_name'] . '[import]" class="button-primary" value="' . __(
                'Import',
                'mozart-options'
            ) . '">&nbsp;&nbsp;<span>' . apply_filters(
                'redux-import-warning',
                __(
                    'WARNING! This will overwrite all existing option values, please proceed with caution!',
                    'mozart-options'
                )
            ) . '</span></p>';

        echo '<div class="hr"/><div class="inner"><span>&nbsp;</span></div></div>';
        echo '<h4>' . __( 'Export Options', 'mozart-options' ) . '</h4>';
        echo '<div class="redux-section-desc">';

        echo '<p class="description">' . apply_filters(
                'redux-backup-description',
                __(
                    'Here you can copy/download your current option settings. Keep this safe as you can use it as a backup should anything go wrong, or you can use it to restore your settings on this site (or any other site).',
                    'mozart-options'
                )
            ) . '</p>';
        echo '</div>';

        $link = admin_url( 'admin-ajax.php?action=redux_download_options&secret=' . $secret );
        echo '<p><a href="javascript:void(0);" id="redux-export-code-copy" class="button-secondary">' . __(
                'Copy',
                'mozart-options'
            ) . '</a> <a href="' . $link . '" id="redux-export-code-dl" class="button-primary">' . __(
                'Download',
                'mozart-options'
            ) . '</a> <a href="javascript:void(0);" id="redux-export-link" class="button-secondary">' . __(
                'Copy Link',
                'mozart-options'
            ) . '</a></p>';

        $backup_options = $this->builder->options;
        $backup_options['redux-backup'] = '1';
        echo "<p>";
        echo '<textarea class="large-text noUpdate" id="redux-export-code" rows="8">';

        echo json_encode( ( $backup_options ) );

        echo '</textarea>';

        $link = admin_url( 'admin-ajax.php?action=redux_link_options&secret=' . $secret );

        echo '<input type="text" class="large-text noUpdate" id="redux-export-link-value" value="' . $link . '" />';
        echo "</p>";
        echo '</div>';

        if (true == $bDoClose) {
            echo '</td></tr></table><table class="form-table no-border" style="margin-top: 0;"><tbody><tr style="border-bottom: 0;"><th></th><td>';
        }
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     *
     */
    public function checkEnabled()
    {
        $this->setEnabled( $this->builder->isFieldInUse( $this->builder->getSections(), 'import_export' ) );
    }

    /**
     *
     */
    public function render_tab()
    {
        echo '<li id="import_export_default_section_group_li" class="redux-group-tab-link-li">';

        if (!empty( $this->builder->args['icon_type'] ) && $this->builder->args['icon_type'] == 'image') {
            $icon = ( !isset( $this->builder->args['import_icon'] ) ) ? '' : '<img src="' . $this->builder->args['import_icon'] . '" /> ';
        } else {
            $icon_class = ( !isset( $this->builder->args['import_icon_class'] ) ) ? '' : ' ' . $this->builder->args['import_icon_class'];
            $icon = ( !isset( $this->builder->args['import_icon'] ) ) ? '<i class="el-icon-refresh' . $icon_class . '"></i>' : '<i class="icon-' . $this->builder->args['import_icon'] . $icon_class . '"></i> ';
        }

        echo '<a href="javascript:void(0);" id="import_export_default_section_group_li_a" class="redux-group-tab-link-a" data-rel="import_export_default">' . $icon . ' <span class="group_title">' . __(
                'Import / Export',
                'mozart-options'
            ) . '</span></a>';
        echo '</li>';

        echo '<li class="divide">&nbsp;</li>';
    }

    /**
     *
     */
    public function add_submenu()
    {
        add_submenu_page(
            $this->builder->args['page_slug'],
            __( 'Import / Export', 'mozart-options' ),
            __( 'Import / Export', 'mozart-options' ),
            $this->builder->args['page_permissions'],
            $this->builder->args['page_slug'] . '&tab=import_export_default',
            '__return_null'
        );
    }

    /**
     *
     */
    public function link_options()
    {
        if (!isset( $_GET['secret'] ) || $_GET['secret'] != md5(
                md5( AUTH_KEY . SECURE_AUTH_KEY ) . '-' . $this->builder->args['opt_name']
            )
        ) {
            wp_die( 'Invalid Secret for options use' );
        }

        $var = $this->builder->options;
        $var['redux-backup'] = '1';
        if (isset( $var['REDUX_imported'] )) {
            unset( $var['REDUX_imported'] );
        }

        die( json_encode( $var ));
    }

    /**
     *
     */
    public function download_options()
    {
        if (!isset( $_GET['secret'] ) || $_GET['secret'] != md5(
                md5( AUTH_KEY . SECURE_AUTH_KEY ) . '-' . $this->builder->args['opt_name']
            )
        ) {
            wp_die( 'Invalid Secret for options use' );
            exit;
        }

        $this->builder->get_options();
        $backup_options = $this->builder->options;
        $backup_options['redux-backup'] = '1';
        if (isset( $var['REDUX_imported'] )) {
            unset( $var['REDUX_imported'] );
        }

        $content = json_encode( $backup_options );

        if (isset( $_GET['action'] ) && $_GET['action'] == 'redux_download_options') {
            header( 'Content-Description: File Transfer' );
            header( 'Content-type: application/txt' );
            header(
                'Content-Disposition: attachment; filename="redux_options_' . $this->builder->args['opt_name'] . '_backup_' . date(
                    'd-m-Y'
                ) . '.json"'
            );
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate' );
            header( 'Pragma: public' );

        } else {
            header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
            header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
            header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' );
        }
        die( $content);
    }
}
