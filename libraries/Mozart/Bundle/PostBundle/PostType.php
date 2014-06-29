<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle;

use Symfony\Component\DependencyInjection\Container;


/**
 * Class PostType
 *
 * @package Mozart\Bundle\PostBundle
 */
class PostType implements PostTypeInterface
{
    private static $reservedKeys = array(
        'post',
        'page',
        'attachment',
        'revision',
        'nav_menu_item',
        'action',
        'order',
        'theme'
    );

    /**
     *
     */
    public function getConfiguration()
    {
        if ( in_array( $this->getKey(), self::$reservedKeys ) ) {
            // TODO: throw an error
        }

        return array(
            'labels'               => $this->getLabels(),
            'description'          => $this->getDescription(),
            'public'               => $this->isPublic(),
            'hierarchical'         => $this->isHierarchical(),
            'exclude_from_search'  => $this->excludeFromSearch(),
            'publicly_queryable'   => $this->isPubliclyQuerable(),
            'show_ui'              => $this->showUI(),
            'show_in_menu'         => $this->showInMenu(),
            'show_in_nav_menus'    => $this->showInNavMenus(),
            'show_in_admin_bar'    => $this->showInAdminBar(),
            'menu_position'        => $this->getMenuPosition(),
            'menu_icon'            => $this->getMenuIcon(),
            'capability_type'      => $this->getCapabilityType(),
            'capabilities'         => $this->getCapabilities(),
            'map_meta_cap'         => $this->mapMetaCap(),
            'supports'             => $this->addPostTypeSupport(),
            'register_meta_box_cb' => $this->metaboxCallback(),
            'taxonomies'           => $this->getTaxonomies(),
            'has_archive'          => $this->hasArchive(),
            'rewrite'              => $this->getRewriteRules(),
            'query_var'            => $this->getQueryVarKey(),
            'can_export'           => $this->isExportable(),
            'delete_with_user'     => $this->deleteWithUser()
        );
    }

    /**
     * @return string
     */
    public function getKey()
    {
        $className     = get_class( $this );
        $classBaseName = substr( strrchr( $className, '\\' ), 1 );

        return Container::underscore( $classBaseName );
    }

    /**
     * @return string
     */
    protected function getName()
    {
        $alias = $this->getKey();
        $alias = str_replace( '_', ' ', $alias );

        return __( ucfirst( $alias ) );
    }

    /**
     * @return string
     */
    protected function getPluralName()
    {
        return __( $this->getName() . 's' );
    }

    /**
     * @return null
     */
    protected function deleteWithUser()
    {
        return null;
    }

    /**
     * @return bool
     */
    protected function isExportable()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getQueryVarKey()
    {
        return $this->getKey();
    }

    /**
     * Triggers the handling of rewrites for this post type. Defaults to true, using $post_type as slug.
     *     To prevent rewrite, set to false.
     *     To specify rewrite rules, an array can be passed with any of these keys
     *         'slug' => string Customize the permastruct slug. Defaults to $post_type key
     *         'with_front' => bool Should the permastruct be prepended with WP_Rewrite::$front. Defaults to true.
     *         'feeds' => bool Should a feed permastruct be built for this post type. Inherits default from has_archive.
     *         'pages' => bool Should the permastruct provide for pagination. Defaults to true.
     *         'ep_mask' => const Assign an endpoint mask.
     *             * If not specified and permalink_epmask is set, inherits from permalink_epmask.
     *             * If not specified and permalink_epmask is not set, defaults to EP_PERMALINK
     *
     * @return bool
     */
    protected function getRewriteRules()
    {
        if ( false === $this->isPublic() ) {
            return false;
        }

        return array(
            'slug'       => $this->getSlug(),
            'with_front' => true,
            'feeds'      => $this->hasArchive(),
            'pages'      => true,
            'ep_mask'    => EP_PERMALINK
        );
    }

    protected function getSlug()
    {
        // let's name a convention:
        // if this post type has archive, let's make its permalink more suggestive
        if ( $this->hasArchive() ) {
            return sanitize_title( $this->getPluralName() );
        }

        return __( $this->getKey() );
    }

    /**
     * @return bool
     */
    protected function hasArchive()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function getTaxonomies()
    {
        return array();
    }

    /**
     * @return null
     */
    protected function metaboxCallback()
    {
        return null;
    }

    /**
     * @return array
     */
    protected function addPostTypeSupport()
    {
        $supports = array(
            'editor',
            'title',
            'thumbnail'
        );

        if ( $this->isHierarchical() ) {
            $supports[] = 'page-attributes';
        }

        return $supports;
    }

    /**
     * @return bool
     */
    protected function mapMetaCap()
    {
        return null;
    }

    /**
     * @return array
     */
    protected function getCapabilities()
    {
        return array();
    }

    /**
     * @return string
     */
    protected function getCapabilityType()
    {
        return 'post';
    }

    /**
     * @return null
     */
    protected function getMenuIcon()
    {
        return null;
    }

    /**
     * The position in the menu order the post type should appear. show_in_menu must be true.
     * Default: null - defaults to below Comments
     * 5 - below Posts
     * 10 - below Media
     * 15 - below Links
     * 20 - below Pages
     * 25 - below comments
     * 60 - below first separator
     * 65 - below Plugins
     * 70 - below Users
     * 75 - below Tools
     * 80 - below Settings
     * 100 - below second separator
     *
     * @return null
     */
    protected function getMenuPosition()
    {
        return null;
    }

    /**
     * @return bool
     */
    protected function showInAdminBar()
    {
        return $this->showInMenu();
    }

    /**
     * @return bool
     */
    protected function showInNavMenus()
    {
        return $this->isPublic();
    }

    /**
     * Where to show the post type in the admin menu.
     * If true, the post type is shown in its own top level menu.
     * If false, no menu is shown
     * If a string of an existing top level menu (eg. 'tools.php' or 'edit.php?post_type=page'), the post type will
     * be placed as a sub menu of that.
     * showUI() must return true.
     *
     * @return bool
     */
    protected function showInMenu()
    {
        return $this->showUI();
    }

    /**
     * Whether to generate a default UI for managing this post type in the admin.
     *
     * @return bool
     */
    protected function showUI()
    {
        return $this->isPublic();
    }

    /**
     * Whether queries can be performed on the front end for the post type as part of parse_request().
     *     ?post_type={post_type_key}
     *     ?{post_type_key}={single_post_slug}
     *     ?{post_type_query_var}={single_post_slug}
     *
     * @return bool
     */
    protected function isPubliclyQuerable()
    {
        return $this->isPublic();
    }

    /**
     *  Whether to exclude posts with this post type from front end search results.
     *
     * @return bool
     */
    protected function excludeFromSearch()
    {
        return $this->isPublic();
    }

    /**
     * Whether the post type is hierarchical (e.g. page). Defaults to false.
     *
     * @return bool
     */
    protected function isHierarchical()
    {
        return false;
    }

    /**
     * Whether a post type is intended for use publicly either via the admin interface or by front-end users.
     *
     * While the default settings of exclude_from_search, publicly_queryable, show_ui, and show_in_nav_menus are
     *      inherited from public, each does not rely on this relationship and controls a very specific intention.
     *
     * @return bool
     */
    protected function isPublic()
    {
        return true;
    }

    /**
     * A short descriptive summary of what the post type is. Defaults to blank.
     *
     * @return string
     */
    protected function getDescription()
    {
        return '';
    }

    /**
     * An array of labels for this post type.
     * If not set, post labels are inherited for non-hierarchical types and page labels for hierarchical ones.
     * You can see accepted values in {@link get_post_type_labels()}.
     *
     * @return array
     */
    protected function getLabels()
    {
        return array(
            'name'               => _x( $this->getPluralName(), 'post type general name' ),
            'singular_name'      => _x( $this->getName(), 'post type general name' ),
            'add_new'            => _x( 'Add New', $this->getKey() ),
            'add_new_item'       => __( 'Add New ' . $this->getName() ),
            'edit_item'          => __( 'Edit ' . $this->getName() ),
            'new_item'           => __( 'New ' . $this->getName() ),
            'view_item'          => __( 'View ' . $this->getName() ),
            'search_items'       => __( 'Search ' . $this->getPluralName() ),
            'not_found'          => __( 'No ' . strtolower( $this->getPluralName() ) . ' found.' ),
            'not_found_in_trash' => __( 'No ' . strtolower( $this->getPluralName() ) . ' found in Trash.' ),
            'parent_item_colon'  => __( 'Parent Page:' ),
            'all_items'          => __( 'All ' . $this->getPluralName() )
        );
    }
}