<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle\Admin;

use Mozart\Bundle\PostBundle\PostType;
use Mozart\Bundle\PostBundle\PostTypeExtensionInterface;

/**
 * Class PostTypeExtension
 *
 * @package Mozart\Bundle\PostBundle\Admin
 */
class PostTypeExtension implements PostTypeExtensionInterface
{
    /**
     * @var PostType $postType
     */
    protected $postType;

    public function getKey()
    {
        return $this->postType->getKey();
    }

    public function load(PostType $postType)
    {
        $this->postType = $postType;

        add_filter( 'post_updated_messages', array( $this, 'getPostUpdatedMessages' ) );

        if ( true === $this->disableAutoSave() ) {
            add_action( 'admin_print_scripts', array( $this, 'dequeueAutoSaveScript' ) );
        }

        // TODO: see WC_Admin_Meta_Boxes and WC_Admin_Post_Types for more actions
        add_action( 'admin_init', array( $this, 'registerAdminInitActions' ) );

        // Status transitions
        add_action( 'delete_post', array( $this, 'onDeletePost' ) );
        add_action( 'wp_trash_post', array( $this, 'onTrashPost' ) );
        add_action( 'untrash_post', array( $this, 'onUntrashPost' ) );
    }

    public function registerAdminInitActions()
    {
        add_filter( "manage_edit-{$this->getKey()}_columns", array( $this, 'getScreenColumnHeaders' ) );
        add_action(
            "manage_{$this->getKey()}_posts_custom_column",
            array( $this, 'manageScreenColumnCells' ),
            10,
            2
        );

        // Insert into X media browser
        add_filter( 'media_view_strings', array( $this, 'changeMediaViewStrings' ) );

        // Post title fields
        add_filter( 'enter_title_here', array( $this, 'changeEnterTitleHereString' ), 1, 2 );
    }

    /**
     * Change title boxes in admin.
     *
     * @param string $text
     * @param object $post
     *
     * @return string
     */
    public function changeEnterTitleHereString($text, $post)
    {
        if ( $post->post_type == $this->getKey() ) {
            return __( $this->postType->getName() . ' name', 'mozart' );
        }

        return $text;
    }

    /**
     * @param mixed $id ID of post being deleted
     *
     * @return void
     */
    public function onDeletePost($id)
    {
        if ( !current_user_can( 'delete_posts' ) ) {
            return;
        }

        if ($id > 0) {
            // TODO: create an event class and call the onDeletePost from that class
        }
    }

    public function onTrashPost($id)
    {
        if ($id > 0) {
            // TODO: create an event class and call the onTrashPost from that class
        }
    }

    public function onUntrashPost($id)
    {
        if ($id > 0) {
            // TODO: create an event class and call the onUntrashPost from that class
        }
    }

    /**
     * Change label for insert buttons.
     *
     * @param array $strings
     *
     * @return array
     */
    public function changeMediaViewStrings($strings)
    {
        global $post_type;

        if ( $post_type == $this->getKey() ) {

            $strings['insertIntoPost']     = sprintf(
                __( 'Insert into %s', 'mozart' ),
                $this->postType->getName()
            );
            $strings['uploadedToThisPost'] = sprintf(
                __( 'Uploaded to this %s', 'mozart' ),
                $this->postType->getName()
            );
        }

        return $strings;
    }

    public function disableAutoSave()
    {
        return false;
    }

    /**
     * Disable the auto-save functionality.
     *
     * @access public
     * @return void
     */
    public function dequeueAutoSaveScript()
    {
        global $post;

        if ( $post && get_post_type( $post->ID ) === $this->getKey() ) {
            wp_dequeue_script( 'autosave' );
        }
    }

    /**
     * Change messages when a post type is updated.
     *
     * @param array $messages
     *
     * @return array
     */
    public function getPostUpdatedMessages($messages)
    {
        global $post, $post_ID;

        $messages[$this->postType->getKey()] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf(
                __(
                    "{$this->postType->getName()} updated. <a href='%s'>View {$this->postType->getName()}</a>",
                    'mozart'
                ),
                esc_url( get_permalink( $post_ID ) )
            ),
            2  => __( 'Custom field updated.', 'mozart' ),
            3  => __( 'Custom field deleted.', 'mozart' ),
            4  => __( $this->postType->getName() . ' updated.', 'mozart' ),
            5  => isset( $_GET['revision'] ) ? sprintf(
                    __( $this->postType->getName() . ' restored to revision from %s', 'mozart' ),
                    wp_post_revision_title( (int) $_GET['revision'], false )
                ) : false,
            6  => sprintf(
                __(
                    "{$this->postType->getName()} published. <a href='%s'>View {$this->postType->getName()}</a>",
                    'mozart'
                ),
                esc_url( get_permalink( $post_ID ) )
            ),
            7  => __( $this->postType->getName() . ' saved.', 'mozart' ),
            8  => sprintf(
                __(
                    $this->postType->getName(
                    ) . ' submitted. <a target="_blank" href="%s">Preview ' . $this->postType->getName() . '</a>',
                    'mozart'
                ),
                esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
            ),
            9  => sprintf(
                __(
                    $this->postType->getName(
                    ) . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $this->postType->getName(
                    ) . '</a>',
                    'mozart'
                ),
                date_i18n( __( 'M j, Y @ G:i', 'mozart' ), strtotime( $post->post_date ) ),
                esc_url( get_permalink( $post_ID ) )
            ),
            10 => sprintf(
                __(
                    $this->postType->getName(
                    ) . ' draft updated. <a target="_blank" href="%s">Preview ' . $this->postType->getName() . '</a>',
                    'mozart'
                ),
                esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
            ),
        );

        return $messages;

    }

    public function getScreenColumnHeaders()
    {
        return array();
    }

    public function manageScreenColumnCells()
    {

    }
}
