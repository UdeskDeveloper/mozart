<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ThemeBundle\Loader;

class TemplateLoader
{
    public function __construct()
    {

    }

    public function load()
    {

        if ('HEAD' === $_SERVER['REQUEST_METHOD'] && apply_filters( 'exit_on_http_head', true )) {
            return;
        }
        // Process feeds and trackbacks even if not using themes.
        if (is_robots() ||
            is_feed() ||
            is_trackback()
        ) {
            return;
        }

        if (is_404() && $template = get_query_template('404')) :
        elseif (is_search() && $template = get_search_template()) :
        elseif (is_front_page() && $template = get_front_page_template()) :
        elseif (is_home() && $template = get_home_template()) :
        elseif (is_post_type_archive() && $template = get_post_type_archive_template()) :
        elseif (is_tax() && $template = get_taxonomy_template()) :
        elseif (is_attachment() && $template = get_attachment_template()) :
            remove_filter( 'the_content', 'prepend_attachment' );
        elseif (is_single() && $template = get_single_template()) :
        elseif (is_page() && $template = get_page_template()) :
        elseif (is_category() && $template = get_category_template()) :
        elseif (is_tag() && $template = get_tag_template()) :
        elseif (is_author() && $template = get_author_template()) :
        elseif (is_date() && $template = get_date_template()) :
        elseif (is_archive() && $template = get_archive_template()) :
        elseif (is_comments_popup() && $template = get_comments_popup_template()) :
        elseif (is_paged() && $template = get_paged_template()) :
        else :
            $template = get_index_template();
        endif;

        if ($template = apply_filters( 'template_include', $template )) {
            include( $template );
        }
        exit;
    }
}
