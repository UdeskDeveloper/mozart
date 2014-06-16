<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\BackofficeBundle\Redux\Section;

use Mozart\Bundle\BackofficeBundle\Redux\ReduxSection;
use ReduxFramework;

/**
 * Class MozartSection
 *
 * @package Mozart\Bundle\BackofficeBundle\Redux\Section
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
        return 'Mozart';
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

            array(
                'id'       => 'opt-checkbox',
                'type'     => 'checkbox',
                'title'    => __( 'Checkbox Option', 'mozart' ),
                'subtitle' => __( 'No validation can be done on this field type', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                'default'  => '1' // 1 = on | 0 = off
            ),
            array(
                'id'       => 'opt-multi-check',
                'type'     => 'checkbox',
                'title'    => __( 'Multi Checkbox Option', 'mozart' ),
                'subtitle' => __( 'No validation can be done on this field type', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                //Must provide key => value pairs for multi checkbox options
                'options'  => array(
                    '1' => 'Opt 1',
                    '2' => 'Opt 2',
                    '3' => 'Opt 3'
                ),
                //See how std has changed? you also don't need to specify opts that are 0.
                'default'  => array(
                    '1' => '1',
                    '2' => '0',
                    '3' => '0'
                )
            ),
            array(
                'id'       => 'opt-checkbox-data',
                'type'     => 'checkbox',
                'title'    => __( 'Multi Checkbox Option (with menu data)', 'mozart' ),
                'subtitle' => __( 'No validation can be done on this field type', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                'data'     => 'menu'
            ),
            array(
                'id'       => 'opt-checkbox-sidebar',
                'type'     => 'checkbox',
                'title'    => __( 'Multi Checkbox Option (with sidebar data)', 'mozart' ),
                'subtitle' => __( 'No validation can be done on this field type', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                'data'     => 'sidebars'
            ),
            array(
                'id'       => 'opt-radio',
                'type'     => 'radio',
                'title'    => __( 'Radio Option', 'mozart' ),
                'subtitle' => __( 'No validation can be done on this field type', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                //Must provide key => value pairs for radio options
                'options'  => array(
                    '1' => 'Opt 1',
                    '2' => 'Opt 2',
                    '3' => 'Opt 3'
                ),
                'default'  => '2'
            ),
            array(
                'id'       => 'opt-radio-data',
                'type'     => 'radio',
                'title'    => __( 'Multi Checkbox Option (with menu data)', 'mozart' ),
                'subtitle' => __( 'No validation can be done on this field type', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                'data'     => 'menu'
            ),
            array(
                'id'       => 'opt-image-select',
                'type'     => 'image_select',
                'title'    => __( 'Images Option', 'mozart' ),
                'subtitle' => __( 'No validation can be done on this field type', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                //Must provide key => value(array:title|img) pairs for radio options
                'options'  => array(
                    '1' => array( 'title' => 'Opt 1', 'img' => 'images/align-none.png' ),
                    '2' => array( 'title' => 'Opt 2', 'img' => 'images/align-left.png' ),
                    '3' => array( 'title' => 'Opt 3', 'img' => 'images/align-center.png' ),
                    '4' => array( 'title' => 'Opt 4', 'img' => 'images/align-right.png' )
                ),
                'default'  => '2'
            ),
            array(
                'id'       => 'opt-image-select-layout',
                'type'     => 'image_select',
                'title'    => __( 'Images Option for Layout', 'mozart' ),
                'subtitle' => __( 'No validation can be done on this field type', 'mozart' ),
                'desc'     => __(
                    'This uses some of the built in images, you can use them for layout options.',
                    'mozart'
                ),
                //Must provide key => value(array:title|img) pairs for radio options
                'options'  => array(
                    '1' => array( 'alt' => '1 Column', 'img' => ReduxFramework::$_url . 'assets/img/1col.png' ),
                    '2' => array( 'alt' => '2 Column Left', 'img' => ReduxFramework::$_url . 'assets/img/2cl.png' ),
                    '3' => array(
                        'alt' => '2 Column Right',
                        'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
                    ),
                    '4' => array(
                        'alt' => '3 Column Middle',
                        'img' => ReduxFramework::$_url . 'assets/img/3cm.png'
                    ),
                    '5' => array( 'alt' => '3 Column Left', 'img' => ReduxFramework::$_url . 'assets/img/3cl.png' ),
                    '6' => array( 'alt' => '3 Column Right', 'img' => ReduxFramework::$_url . 'assets/img/3cr.png' )
                ),
                'default'  => '2'
            ),
            array(
                'id'       => 'opt-sortable',
                'type'     => 'sortable',
                'title'    => __( 'Sortable Text Option', 'mozart' ),
                'subtitle' => __( 'Define and reorder these however you want.', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                'options'  => array(
                    'si1' => 'Item 1',
                    'si2' => 'Item 2',
                    'si3' => 'Item 3',
                )
            ),
            array(
                'id'       => 'opt-check-sortable',
                'type'     => 'sortable',
                'mode'     => 'checkbox', // checkbox or text
                'title'    => __( 'Sortable Text Option', 'mozart' ),
                'subtitle' => __( 'Define and reorder these however you want.', 'mozart' ),
                'desc'     => __(
                    'This is the description field, again good for additional info.',
                    'mozart'
                ),
                'options'  => array(
                    'si1' => false,
                    'si2' => true,
                    'si3' => false,
                )
            )

        );
    }
}