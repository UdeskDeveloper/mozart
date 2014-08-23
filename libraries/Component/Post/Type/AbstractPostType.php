<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Post\Type;

use Mozart\Component\Support\Str;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PostType
 *
 * @package Mozart\Bundle\PostBundle
 */
class AbstractPostType implements PostTypeInterface
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
        if (in_array( $this->getKey(), self::$reservedKeys )) {
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
    public function getName()
    {
        $alias = $this->getKey();
        $alias = str_replace( '_', ' ', $alias );

        return translate( ucwords( $alias ) );
    }

    /**
     * @return string
     */
    public function getPluralName()
    {
        return translate( Str::plural($this->getName()) );
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
     * Sets the query_var key for this post type. Defaults to $post_type key
     * If false, a post type cannot be loaded at ?{query_var}={post_slug}
     * If specified as a string, the query ?{query_var_string}={post_slug} will be valid.
     *
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
        if (false === $this->isPublic()) {
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
        if ($this->hasArchive()) {
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

        if ($this->isHierarchical()) {
            $supports[] = 'page-attributes';
        }

        return $supports;
    }

    /**
     * Whether to use the internal default meta capability handling
     *
     * @return bool
     */
    protected function mapMetaCap()
    {
        return null;
    }

    /**
     * @see get_post_type_capabilities()
     *
     * @return array
     */
    protected function getCapabilities()
    {
        $capability_type = $this->getCapabilityType();

        // Singular base for meta capabilities, plural base for primitive capabilities.
        $singular_base = $capability_type[0];
        $plural_base   = $capability_type[1];

        return array(
            // Meta capabilities
            'edit_post'              => 'edit_' . $singular_base,
            'read_post'              => 'read_' . $singular_base,
            'delete_post'            => 'delete_' . $singular_base,
            // Primitive capabilities used outside of map_meta_cap():
            'edit_posts'             => 'edit_' . $plural_base,
            'edit_others_posts'      => 'edit_others_' . $plural_base,
            'publish_posts'          => 'publish_' . $plural_base,
            'read_private_posts'     => 'read_private_' . $plural_base,
            // Capabilities for mapping
            'delete_posts'           => 'delete_' . $plural_base,
            'delete_private_posts'   => 'delete_private_' . $plural_base,
            'delete_published_posts' => 'delete_published_' . $plural_base,
            'delete_others_posts'    => 'delete_others_' . $plural_base,
            'edit_private_posts'     => 'edit_private_' . $plural_base,
            'edit_published_posts'   => 'edit_published_' . $plural_base,
        );
    }

    /**
     * @return array
     */
    protected function getCapabilityType()
    {
        return array( 'post', 'posts' );
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
        return false;
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
        return !$this->isPublic();
    }

    /**
     * Whether the post type is hierarchical (e.g. page)
     * Hierarchical causes memory issues - WP loads all records!
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

    /**
     * Change label for insert buttons.
     *
     * @access   public
     *
     * @param array $strings
     *
     * @return array
     */
    public function changeMediaViewStrings($strings)
    {
        $strings['insertIntoPost']     = sprintf( __( 'Insert into %s', 'woocommerce' ), $this->getName() );
        $strings['uploadedToThisPost'] = sprintf(
            __( 'Uploaded to this %s', 'woocommerce' ),
            $this->getName()
        );

        return $strings;
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // TODO: Implement setDefaultOptions() method.
    }
}
