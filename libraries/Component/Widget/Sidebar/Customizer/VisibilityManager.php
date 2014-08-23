<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Widget\Sidebar\Customizer;


use Mozart\Component\Widget\Sidebar\SidebarCustomizer;

/**
 * Adds visibility options to all widgets: Hide or show widgets only when
 * specific conditions are met.
 *
 * Class VisibilityManager
 * @package Mozart\Component\Widget\Sidebar\Customizer
 */
class VisibilityManager {
    /**
     * Constructor is private -> singleton.
     *
     * @since  2.0
     */
    private function __construct() {
        if ( is_admin() ) {
            // in_widget_form: Add our button inside each widget.
            add_action(
                'in_widget_form',
                array( $this, 'admin_widget_button' ),
                10, 3
            );

            // When the widget is saved (via Ajax) we save our options.
            add_filter(
                'widget_update_callback',
                array( $this, 'admin_widget_update' ),
                10, 3
            );

            \TheLib::add_ui( CSB_JS_URL . 'cs-visibility.min.js', 'widgets.php' );
            \TheLib::add_ui( CSB_CSS_URL . 'cs-visibility.css', 'widgets.php' );
        } else {
            // Filters the list of widget-areas and their widgets
            add_action(
                'sidebars_widgets',
                array( $this, 'sidebars_widgets' )
            );
        }
    }

    /**
     * Extracts and sanitizes the CSB visibility data from the widget instance.
     *
     * @since  2.0
     * @param  array $instance The widget instance data.
     * @return array Sanitized CSB visibility data.
     */
    protected function get_widget_data( $instance ) {
        static $Condition_keys = null;
        $data = array();

        if ( null === $Condition_keys ) {
            $tax_list = get_taxonomies( array( 'public' => true ), 'objects' );
            $type_list = CustomSidebars::get_post_types( 'objects' );
            $Condition_keys = array(
                'date' => '',
                'roles' => array(),
                'pagetypes' => array(),
                'posttypes' => array(),
                'membership' => array(),
                'prosite' => array(),
            );
            foreach ( $type_list as $type_item ) {
                $Condition_keys[ 'pt-' . $type_item->name ] = array();
            }
            foreach ( $tax_list as $tax_item ) {
                $Condition_keys[ 'tax-' . $tax_item->name ] = array();
            }
        }

        if ( isset( $instance['csb_visibility'] ) ) {
            $data = $instance['csb_visibility'];
        }

        $valid_action = array( 'show', 'hide' );
        if ( ! in_array( @$data['action'], $valid_action ) ) {
            $data['action'] = reset( $valid_action );
        }

        $conditions = @$data['conditions'];
        if ( ! is_array( $conditions ) ) {
            $conditions = array();
        }
        $data['conditions'] = array();

        $data['always'] = true;
        foreach ( $Condition_keys as $key => $def_value ) {
            $val = $def_value;
            if ( isset( $conditions[ $key ] ) && ! empty( $conditions[ $key ] ) ) {
                $data['always'] = false;
                $val = $conditions[ $key ];
            }
            $data['conditions'][ $key ] = $val;
        }

        return $data;
    }

    /**
     * Action handler for 'in_widget_form'
     *
     * @since  2.0
     */
    public function admin_widget_button( $widget, $return, $instance ) {
        $is_visible = ('1' == @$_POST['csb_visible'] ? 1 : 0);
        $tax_list = get_taxonomies( array( 'public' => true ), 'objects' );
        $type_list = CustomSidebars::get_post_types( 'objects' );
        $role_list = array_reverse( get_editable_roles() );
        $membership_levels = $this->get_membership_levels();
        $pagetype_list = array(
            'frontpage' => 'Frontpage',
            'single' => 'Single page',
            'posts' => 'Posts page',
            'archive' => 'Archives',
            'search' => 'Search results',
            'e404' => 'Not found (404)',
            'preview' => 'Preview',
            'day' => 'Archive: Day',
            'month' => 'Archive: Month',
            'year' => 'Archive: Year',
        );

        // Remove taxonomies without values.
        foreach ( $tax_list as $index => $tax_item ) {
            $tags = get_terms( $tax_item->name, array( 'hide_empty' => false ) );
            if ( empty( $tags ) ) {
                unset( $tax_list[ $index ] );
            }
        }

        $data = $this->get_widget_data( $instance );
        $action_show = ($data['action'] == 'show');
        $cond = $data['conditions'];

        ?>
        <div class="csb-visibility csb-visibility-<?php echo esc_attr( $widget->id ); ?>">
            <?php
            /*
             * This input is only used to determine if the "visibility" button
             * should be displayed in the widget form.
             */
            ?>
            <input type="hidden" name="csb-visibility-button" value="0" />
            <?php if ( ! isset( $_POST[ 'csb-visibility-button' ] ) ) : ?>
                <a href="#" class="button csb-visibility-button"><span class="dashicons dashicons-visibility"></span> <?php _e( 'Visibility', CSB_LANG ); ?></a>
            <?php else : ?>
                <script>jQuery(function() { jQuery('.csb-visibility-<?php echo esc_js( $widget->id ); ?>').closest('.widget').trigger('csb:update'); }); </script>
            <?php endif; ?>

            <div class="csb-visibility-inner" <?php if ( ! $is_visible ) : ?>style="display:none"<?php endif; ?>>
                <input type="hidden" name="csb_visible" class="csb-visible-flag" value="<?php echo esc_attr( $is_visible ); ?>" />

                <div class="csb-option-row csb-action">
                    <label for="<?php echo esc_attr( $widget->id ); ?>-action" class="lbl-show-if toggle-action" <?php if ( ! $action_show ) : ?>style="display:none"<?php endif; ?>><?php _e( '<b>Show</b> widget if:', CSB_LANG ); ?></label>
                    <label for="<?php echo esc_attr( $widget->id ); ?>-action" class="lbl-hide-if toggle-action" <?php if ( $action_show ) : ?>style="display:none"<?php endif; ?>><?php _e( '<b>Hide</b> widget if:', CSB_LANG ); ?></label>
                    <input type="hidden" id="<?php echo esc_attr( $widget->id ); ?>-action" name="csb_visibility[action]" value="<?php echo esc_attr( $data['action'] ); ?>" />
                    <i class="dashicons dashicons-plus choose-filters show-on-hover action"></i>
                    <ul class="dropdown" style="display:none">
                        <li class="csb-group">Filters</li>
                        <li class="add-filter" data-for=".csb-date" style="display:none">Date</li>
                        <li class="add-filter" data-for=".csb-roles" <?php if ( ! empty( $cond['roles'] ) ) : ?>style="display:none"<?php endif; ?>>Roles</li>
                        <?php if ( false != $membership_levels ) : ?>
                            <li class="add-filter" data-for=".csb-membership">Membership</li>
                        <?php endif; ?>
                        <li class="add-filter" data-for=".csb-prosite" style="display:none">ProSite</li>
                        <li class="add-filter" data-for=".csb-pagetypes" <?php if ( ! empty( $cond['pagetypes'] ) ) : ?>style="display:none"<?php endif; ?>>Special pages</li>
                        <li class="add-filter" data-for=".csb-posttypes" <?php if ( ! empty( $cond['posttypes'] ) ) : ?>style="display:none"<?php endif; ?>>For posttype</li>
                        <li class="csb-group">Taxonomy</li>
                        <?php foreach ( $tax_list as $tax_item ) :
                            $row_id = 'tax-' . $tax_item->name;
                            ?>
                            <li class="add-filter" data-for=".csb-<?php echo esc_attr( $row_id ); ?>" <?php if ( ! empty( $cond[ $row_id ] ) ) : ?>style="display:none"<?php endif; ?>>
                                <?php echo esc_html( $tax_item->labels->name ); ?>
                            </li>
                        <?php
                        endforeach; ?>
                    </ul>
                </div>

                <?php $block_name = 'csb_visibility[conditions]'; ?>

                <div class="csb-option-row csb-always" <?php if ( ! $data['always'] ) : ?>style="display:none"<?php endif; ?>>
                    <label><?php _e( 'Always', CSB_LANG ); ?></label>
                </div>

                <?php /* DATE */ ?>
                <div class="csb-option-row csb-date" <?php if ( empty( $cond['date'] ) ) : ?>style="display:none"<?php endif; ?>>
                    <label for="<?php echo esc_attr( $widget->id ); ?>-date"><span class="csb-and" style="display:none"><?php _e( 'AND', CSB_LANG ); ?></span> <?php _e( 'On these dates', CSB_LANG ); ?></label>
                    <i class="dashicons dashicons-trash clear-filter show-on-hover action"></i>
                    <input type="text" id="<?php echo esc_attr( $widget->id ); ?>-date" name="<?php echo esc_attr( $block_name ); ?>[date]" value="<?php echo esc_attr( @$cond['date'] ); ?>" />
                </div>

                <?php /* ROLES */ ?>
                <div class="csb-option-row csb-roles" <?php if ( empty( $cond['roles'] ) ) : ?>style="display:none"<?php endif; ?>>
                    <label for="<?php echo esc_attr( $widget->id ); ?>-roles"><span class="csb-and" style="display:none"><?php _e( 'AND', CSB_LANG ); ?></span> <?php _e( 'User has role', CSB_LANG ); ?></label>
                    <i class="dashicons dashicons-trash clear-filter show-on-hover action"></i>
                    <select id="<?php echo esc_attr( $widget->id ); ?>-roles" name="<?php echo esc_attr( $block_name ); ?>[roles][]" multiple="multiple">
                        <?php foreach ( $role_list as $role => $details ) : ?>
                            <?php $name = translate_user_role( $details['name'] ); ?>
                            <?php $is_selected = in_array( $role, $cond['roles'] ); ?>
                            <option <?php selected( $is_selected, true ); ?> value="<?php echo esc_attr( $role ); ?>">
                                <?php echo esc_html( $name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php /* MEMBERSHIP */ ?>
                <?php if ( is_array( $membership_levels ) ) : ?>
                    <div class="csb-option-row csb-membership" <?php if ( empty( $cond['membership'] ) ) : ?>style="display:none"<?php endif; ?>>
                        <label for="<?php echo esc_attr( $widget->id ); ?>-membership"><span class="csb-and" style="display:none"><?php _e( 'AND', CSB_LANG ); ?></span> <?php _e( 'User has Membership Level', CSB_LANG ); ?></label>
                        <i class="dashicons dashicons-trash clear-filter show-on-hover action"></i>
                        <select id="<?php echo esc_attr( $widget->id ); ?>-membership" name="<?php echo esc_attr( $block_name ); ?>[membership][]" multiple="multiple">
                            <?php foreach ( $membership_levels as $level ) : ?>
                                <?php $is_selected = in_array( $level['id'], $cond['membership'] ); ?>
                                <option <?php selected( $is_selected ); ?> value="<?php echo esc_attr( $level['id'] ); ?>">
                                    <?php echo esc_html( $level['level_title'] ); ?>
                                    <?php if ( ! $level['level_active'] ) { _e( '(inactive)', CSB_LANG ); } ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php /* PRO-SITE */ ?>
                <div class="csb-option-row csb-prosite" <?php if ( empty( $cond['prosite'] ) ) : ?>style="display:none"<?php endif; ?>>
                    <label for="<?php echo esc_attr( $widget->id ); ?>-prosite"><span class="csb-and" style="display:none"><?php _e( 'AND', CSB_LANG ); ?></span> <?php _e( 'Pro Sites Level', CSB_LANG ); ?></label>
                    <i class="dashicons dashicons-trash clear-filter show-on-hover action"></i>
                    <select id="<?php echo esc_attr( $widget->id ); ?>-prosite" name="<?php echo esc_attr( $block_name ); ?>[prosite][]" multiple="multiple">
                        <?php foreach ( array() as $level ) : ?>
                            <?php $is_selected = in_array( $level['id'], $cond['prosite'] ); ?>
                            <option <?php selected( $is_selected ); ?> value="<?php echo esc_attr( $level['id'] ); ?>">
                                <?php echo esc_html( 'title' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php /* PAGE TYPES */ ?>
                <div class="csb-option-row csb-pagetypes" <?php if ( empty( $cond['pagetypes'] ) ) : ?>style="display:none"<?php endif; ?>>
                    <label for="<?php echo esc_attr( $widget->id ); ?>-pagetypes"><span class="csb-and" style="display:none"><?php _e( 'AND', CSB_LANG ); ?></span> <?php _e( 'On these special pages', CSB_LANG ); ?></label>
                    <i class="dashicons dashicons-trash clear-filter show-on-hover action"></i>
                    <select id="<?php echo esc_attr( $widget->id ); ?>-pagetypes" name="<?php echo esc_attr( $block_name ); ?>[pagetypes][]" multiple="multiple">
                        <?php foreach ( $pagetype_list as $type => $name ) : ?>
                            <?php $is_selected = in_array( $type, $cond['pagetypes'] ); ?>
                            <option <?php selected( $is_selected ); ?> value="<?php echo esc_attr( $type ); ?>">
                                <?php echo esc_html( $name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php /* POSTTYPES */ ?>
                <div class="csb-option-row csb-posttypes" <?php if ( empty( $cond['posttypes'] ) ) : ?>style="display:none"<?php endif; ?>>
                    <label for="<?php echo esc_attr( $widget->id ); ?>-posttypes"><span class="csb-and" style="display:none"><?php _e( 'AND', CSB_LANG ); ?></span> <?php _e( 'On any page of these types', CSB_LANG ); ?></label>
                    <i class="dashicons dashicons-trash clear-filter show-on-hover action"></i>
                    <select class="posttype" id="<?php echo esc_attr( $widget->id ); ?>-posttypes" name="<?php echo esc_attr( $block_name ); ?>[posttypes][]" multiple="multiple">
                        <?php foreach ( $type_list as $type_item ) : ?>
                            <?php $is_selected = in_array( $type_item->name, $cond['posttypes'] ); ?>
                            <option <?php selected( $is_selected ); ?> value="<?php echo esc_attr( $type_item->name ); ?>">
                                <?php echo esc_html( $type_item->labels->name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php /* SPECIFIC POSTS */ ?>
                    <?php foreach ( $type_list as $type_item ) :
                        $row_id = 'pt-' . $type_item->name;
                        $lbl_all = sprintf( __( 'Only for specific %s', CSB_LANG ), $type_item->labels->name );
                        $lbl_single = sprintf( __( 'Only these %s:', CSB_LANG ), $type_item->labels->name );
                        $is_selected = in_array( $type_item->name, $cond['posttypes'] );
                        $posts = get_posts(
                            array(
                                'post_type' => $type_item->name,
                                'order_by' => 'title',
                                'order' => 'ASC',
                                'numberposts' => '0',
                            )
                        );
                        ?>
                        <div class="csb-detail-row csb-<?php echo esc_attr( $row_id ); ?>" <?php if ( ! $is_selected ) : ?>style="display:none"<?php endif; ?>>
                            <label for="<?php echo esc_attr( $widget->id ); ?>-<?php echo esc_attr( $row_id ); ?>">
                                <input type="checkbox" id="<?php echo esc_attr( $widget->id ); ?>-<?php echo esc_attr( $row_id ); ?>" <?php checked( ! empty( $cond[ $row_id ] ) ); ?> data-lbl-all="<?php echo esc_attr( $lbl_all ); ?>" data-lbl-single="<?php echo esc_attr( $lbl_single ); ?>" />
                                <span class="lbl"><?php echo esc_html( empty( $cond[ $row_id ] ) ? $lbl_all : $lbl_single ); ?></span>
                            </label>
                            <div class="detail" <?php if ( empty( $cond[ $row_id ] ) ) : ?>style="display:none"<?php endif; ?>>
                                <select name="<?php echo esc_attr( $block_name ); ?>[<?php echo esc_attr( $row_id ); ?>][]" multiple="multiple">
                                    <?php foreach ( $posts as $post ) : ?>
                                        <?php $is_selected = in_array( $post->ID, $cond[ $row_id ] ); ?>
                                        <option <?php selected( $is_selected ); ?> value="<?php echo esc_attr( $post->ID ); ?>">
                                            <?php echo esc_html( $post->post_title ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php /* SPECIFIC TAXONOMY */ ?>
                <?php
                foreach ( $tax_list as $tax_item ) {
                    $row_id = 'tax-' . $tax_item->name;
                    $tags = get_terms( $tax_item->name, array( 'hide_empty' => false ) );
                    ?>
                    <div class="csb-option-row csb-<?php echo esc_attr( $row_id ); ?>" <?php if ( empty( $cond[ $row_id ] ) ) : ?>style="display:none"<?php endif; ?>>
                        <label for="<?php echo esc_attr( $widget->id ); ?>-<?php echo esc_attr( $row_id ); ?>"><span class="csb-and" style="display:none"><?php _e( 'AND', CSB_LANG ); ?></span> <?php echo esc_html( $tax_item->labels->name ); ?></label>
                        <i class="dashicons dashicons-trash clear-filter show-on-hover action"></i>
                        <select id="<?php echo esc_attr( $widget->id ); ?>-<?php echo esc_attr( $row_id ); ?>" name="<?php echo esc_attr( $block_name ); ?>[<?php echo esc_attr( $row_id ); ?>][]" multiple="multiple">
                            <?php foreach ( $tags as $tag ) : ?>
                                <?php $is_selected = in_array( $tag->term_id, $cond[ $row_id ] ); ?>
                                <option <?php selected( $is_selected ); ?>value="<?php echo esc_attr( $tag->term_id ); ?>">
                                    <?php echo esc_html( $tag->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php
                }
                ?>

            </div>
        </div>
    <?php
    }

    /**
     * Integration with the WPMU Dev Membership plugin:
     * If the plugin is installed and active this function returns a list of
     * all membership levels.
     *
     * If the plugin is not active the return value is boolean false.
     *
     * @since  2.0
     * @return bool|array
     */
    public function get_membership_levels() {
        $Result = null;

        if (
            null === $Result &&
            function_exists( 'M_get_membership_active' ) &&
            'no' != M_get_membership_active() &&
            defined( 'MEMBERSHIP_TABLE_LEVELS' )
        ) {
            global $wpdb;
            $Result = $wpdb->get_results(
                sprintf(
                    'SELECT
						id, level_title, level_active
					FROM %s
					ORDER BY id',
                    MEMBERSHIP_TABLE_LEVELS
                ), ARRAY_A
            );
        } else {
            $Result = false;
        }

        return $Result;
    }

    /**
     * When user saves the widget we check for the
     *
     * @since  2.0
     * @param  array $new_instance New settings for this instance as input by the user.
     * @param  array $old_instance Old settings for this instance.
     * @return array Modified settings.
     */
    public function admin_widget_update( $instance, $new_instance, $old_instance ) {
        $data = $this->get_widget_data( $_POST );

        $instance['csb_visibility'] = $data;

        return $instance;
    }

    // == Front-end functions

    /**
     * Filter the list of widgets for a sidebar so that active sidebars work as expected.
     *
     * @since  2.0
     * @param  array $widget_areas An array of widget areas and their widgets.
     * @return array The modified $widget_area array.
     */
    public function sidebars_widgets( $widget_areas ) {
        static $Settings = array();

        foreach ( $widget_areas as $widget_area => $widgets ) {
            if ( empty( $widgets ) ) {
                continue;
            }

            if ( 'wp_inactive_widgets' == $widget_area ) {
                continue;
            }

            foreach ( $widgets as $position => $widget_id ) {
                // Find the conditions for this widget.
                if ( preg_match( '/^(.+?)-(\d+)$/', $widget_id, $matches ) ) {
                    $id_base = $matches[1];
                    $widget_number = intval( $matches[2] );
                } else {
                    $id_base = $widget_id;
                    $widget_number = null;
                }

                if ( ! isset( $Settings[ $id_base ] ) ) {
                    $Settings[ $id_base ] = get_option( 'widget_' . $id_base );
                }

                // New multi widget (WP_Widget)
                if ( ! is_null( $widget_number ) ) {
                    if ( isset( $Settings[ $id_base ][ $widget_number ] ) && false === $this->maybe_display_widget( $Settings[ $id_base ][ $widget_number ] ) ) {
                        unset( $widget_areas[ $widget_area ][ $position ] );
                    }
                }

                // Old single widget
                else if ( ! empty( $Settings[ $id_base ] ) && false === $this->maybe_display_widget( $Settings[ $id_base ] ) ) {
                    unset( $widget_areas[ $widget_area ][ $position ] );
                }
            }
        }

        return $widget_areas;
    }

    public function maybe_display_widget( $instance ) {
        global $post, $wp_query;
        static $Type_list = null;
        static $Tax_list = null;

        $show_widget = true;
        $condition_true = true;
        $action = 'show';

        if ( empty( $instance['csb_visibility'] ) || empty( $instance['csb_visibility']['conditions'] ) ) {
            return $show_widget;
        }

        $cond = $instance['csb_visibility']['conditions'];
        $action = 'hide' != $instance['csb_visibility']['action'] ? 'show' : 'hide';

        if ( $instance['csb_visibility']['always'] ) {
            return ( 'hide' == $action ? false : true );
        }

        if ( null === $Type_list ) {
            $Tax_list = get_taxonomies( array( 'public' => true ), 'objects' );
            $Type_list = get_post_types( array( 'public' => true ), 'objects' );
        }

        // Filter for DATE-RANGE.
        if ( $condition_true && ! empty( $cond['date'] ) ) {
            // not implemented yet...
        }

        // Filter for USER ROLES.
        if ( $condition_true && ! empty( $cond['roles'] ) && is_array( $cond['roles'] ) ) {
            if ( ! is_user_logged_in() ) {
                $condition_true = false;
            } else {
                global $current_user;
                $has_role = false;
                foreach ( $current_user->roles as $user_role ) {
                    if ( in_array( $user_role, $cond['roles'] ) ) {
                        $has_role = true;
                        break;
                    }
                }
                if ( ! $has_role ) {
                    $condition_true = false;
                }
            }
        }

        // Filter for MEMBERSHIP Level.
        if ( $condition_true && ! empty( $cond['membership'] ) ) {
            if ( ! is_user_logged_in() ) {
                $condition_true = false;
            } else {
                if ( class_exists( 'Membership_Factory' ) ) {
                    $factory = new Membership_Factory();
                    $user = $factory->get_member( get_current_user_id() );
                    $has_level = false;
                    foreach ( $cond['membership'] as $level ) {
                        if ( $user->on_level( $level ) ) {
                            $has_level = true;
                            break;
                        }
                    }
                    if ( ! $has_level ) {
                        $condition_true = false;
                    }
                }
            }
        }

        // Filter for PRO-SITE Level.
        if ( $condition_true && ! empty( $cond['prosite'] ) ) {
            // not implemented yet...
        }

        // Filter for SPECIAL PAGES.
        if ( $condition_true && ! empty( $cond['pagetypes'] ) && is_array( $cond['pagetypes'] ) ) {
            $is_type = false;
            foreach ( $cond['pagetypes'] as $type ) {
                if ( $is_type ) {
                    break;
                }

                switch ( $type ) {
                    case 'e404':
                        $is_type = $is_type || is_404();
                        break;
                    case 'single':
                        $is_type = $is_type || is_singular();
                        break;
                    case 'search':
                        $is_type = $is_type || is_search();
                        break;
                    case 'archive':
                        $is_type = $is_type || is_archive();
                        break;
                    case 'posts':
                        $is_type = $is_type || $wp_query->is_posts_page;
                        break;
                    case 'preview':
                        $is_type = $is_type || is_preview();
                        break;
                    case 'day':
                        $is_type = $is_type || is_day();
                        break;
                    case 'month':
                        $is_type = $is_type || is_month();
                        break;
                    case 'year':
                        $is_type = $is_type || is_year();
                        break;
                    case 'frontpage':
                        if ( current_theme_supports( 'infinite-scroll' ) )
                            $is_type = $is_type || is_front_page();
                        else {
                            $is_type = $is_type ||  ( is_front_page() && ! is_paged() );
                        }
                        break;
                }
            }
            if ( ! $is_type ) {
                $condition_true = false;
            }
        }

        // Filter for POST-TYPE.
        if ( $condition_true && ! empty( $cond['posttypes'] ) ) {
            $posttype = get_post_type();
            if ( ! in_array( $posttype, $cond['posttypes'] ) ) {
                $condition_true = false;
            } else {
                // Filter for SPECIFIC POSTS.
                if ( ! empty( $cond[ 'pt-' . $posttype ] ) ) {
                    if ( ! in_array( get_the_ID(), $cond[ 'pt-' . $posttype ] ) ) {
                        $condition_true = false;
                    }
                }
            }
        }

        if ( $condition_true ) {
            // TAXONOMY condition.
            $tax_query = @$wp_query->tax_query->queries;
            if ( is_array( $tax_query ) ) {
                $tax_type = @$tax_query[0]['taxonomy'];
                $tax_terms = @$tax_query[0]['terms'];
            } else {
                $tax_type = false;
                $tax_terms = false;
            }

            foreach ( $Tax_list as $tax_item ) {
                if ( ! $condition_true ) {
                    break;
                }

                $tax_key = 'tax-' . $tax_item->name;
                if ( isset( $cond[ $tax_key ] ) && ! empty( $cond[ $tax_key ] ) ) {
                    $has_term = false;

                    if ( $tax_type && $tax_type == $tax_item->name ) {
                        // Check if we did filter for the specific taxonomy.
                        foreach ( $tax_terms as $slug ) {
                            $term_data = get_term_by( 'slug', $slug, $tax_type );
                            if ( in_array( $term_data->term_id, $cond[ $tax_key ] ) ) {
                                $has_term = true;
                            }
                        }
                    } else {
                        // Check if current post has the specific taxonomy.
                        foreach ( $cond[ $tax_key ] as $term ) {
                            if ( has_term( $term, $tax_item->name ) ) {
                                $has_term = true;
                                break;
                            }
                        }
                    }
                    if ( ! $has_term ) {
                        $condition_true = false;
                    }
                }
            }
        }

        if ( ( 'show' == $action && ! $condition_true ) || ( 'hide' == $action && $condition_true ) ) {
            $show_widget = false;
        }

        return $show_widget;
    }
} 