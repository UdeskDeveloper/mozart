<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\TaxonomyBundle;


/**
 * Class Taxonomy
 *
 * @package Mozart\Bundle\TaxonomyBundle
 */
class Taxonomy implements TaxonomyInterface
{

    /**
     *
     * The name of the taxonomy.
     * Name should be in slug form
     * (must not contain capital letters or spaces)
     * and not more than 32 characters long (database structure restriction).
     *
     * @return string
     */
    public function getName()
    {
        return '';
    }

    public function getObjectTypes()
    {
        return array();
    }

    public function getLabels()
    {
        $name = ucfirst( $this->getName() );

        return array(
            'name'              => _x( $name . 's', 'taxonomy general name' ),
            'singular_name'     => _x( $name, 'taxonomy singular name' ),
            'search_items'      => __( "Search {$name}s" ),
            'all_items'         => __( "All {$name}s" ),
            'parent_item'       => __( "Parent {$name}" ),
            'parent_item_colon' => __( "Parent {$name}:" ),
            'edit_item'         => __( "Edit {$name}" ),
            'update_item'       => __( "Update {$name}" ),
            'add_new_item'      => __( "Add New {$name}" ),
            'new_item_name'     => __( "New {$name} Name" ),
            'menu_name'         => __( $name ),
        );
    }

    /**
     * If the taxonomy should be publicly queryable.
     */
    public function isPublic()
    {
        return true;
    }

    /**
     * Whether to generate a default UI for managing this taxonomy.
     */
    public function showUI()
    {
        return $this->isPublic();
    }

    /**
     * true makes this taxonomy available for selection in navigation menus.
     */
    public function showInNavMenus()
    {
        return $this->isPublic();
    }

    /**
     * Whether to allow the Tag Cloud widget to use this taxonomy.
     */
    public function showTagCloud()
    {
        return $this->showUI();
    }

    /**
     * Provide a callback function name for the meta box display
     * Defaults to the categories meta box (post_categories_meta_box() in meta-boxes.php) for hierarchical taxonomies
     * and the tags meta box (post_tags_meta_box()) for non-hierarchical taxonomies.
     * No meta box is shown if set to false.
     */
    public function getMetaboxCallback()
    {
        return null;
    }

    /**
     *Whether to allow automatic creation of taxonomy columns on associated post-types table.
     */
    public function showAdminColumn()
    {
        return false;
    }

    /**
     * Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.
     */
    public function isHierarchical()
    {
        return false;
    }

    /**
     *A function name that will be called when the count
     * of an associated $object_type, such as post, is updated. Works much like a hook.
     *
     * While the default is '', when actually performing the count update in wp_update_term_count_now(),
     * if the taxonomy is only attached to post types (as opposed to other WordPress objects, like user),
     * the built-in _update_post_term_count() function will be used to count only published
     * posts associated with that term, otherwise _update_generic_term_count()
     * will be used instead, that does no such checking.
     *
     * This is significant in the case of attachments.
     * Because an attachment is a type of post, the default _update_post_term_count() will be used.
     * However, this may be undesirable, because this will only count attachments that are actually
     * attached to another post (like when you insert an image into a post).
     * This means that attachments that you simply upload to WordPress using the Media Library,
     * but do not actually attach to another post will not be counted. If your intention behind
     * associating a taxonomy with attachments was to leverage the Media Library
     * as a sort of Document Management solution, you are probably more interested in the counts
     * of unattached Media items, than in those attached to posts.
     * In this case, you should force the use of _update_generic_term_count()
     * by setting '_update_generic_term_count' as the value for update_count_callback.
     */
    public function getUpdateCountCallback()
    {
        return '';
    }

    /**
     * False to disable the query_var, set as string to use custom
     * query_var instead of default which is $taxonomy, the taxonomy's "name".
     *
     * The query_var is used for direct queries through WP_Query like
     * new WP_Query(array('people'=>$person_name)) and URL queries like /?people=$person_name.
     * Setting query_var to false will disable these methods, but you can still fetch posts
     * with an explicit WP_Query taxonomy query like WP_Query(array('taxonomy'=>'people', 'term'=>$person_name)).
     */
    public function getQueryVariable()
    {
        return $this->getName();
    }

    /**
     * Whether this taxonomy is a native or "built-in" taxonomy.
     * Note: this Codex entry is for documentation -
     * core developers recommend you don't use this when registering your own taxonomy
     */
    public function isBuiltin()
    {
        return false;
    }

    /**
     * Whether this taxonomy should remember the order in which terms are added to objects.
     */
    public function toSort()
    {
        return false;
    }

    /**
     * An array of the capabilities for this taxonomy.
     */
    public function getCapabilities()
    {
        return array(
            'manage_terms' => 'manage_categories',
            'edit_terms' => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts'
        );
    }

    /**
     * Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks".
     * Pass an $args array to override default URL settings for permalinks as outlined below.
     *
     * You may need to flush the rewrite rules after changing this.
     * You can do it manually by going to the Permalink Settings page and re-saving the rules --
     * you don't need to change them -- or by calling $wp_rewrite->flush_rules().
     * You should only flush the rules once after the taxonomy has been created, not every time the plugin/theme loads.
     */
    public function getRewriteOptions()
    {
        return array(
            'slug' => $this->getName(),
            'with_front' => true,
            'hierarchical' => false,
            'ep_mask' => EP_NONE
        );
    }
}