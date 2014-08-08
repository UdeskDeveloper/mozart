<?php

namespace Mozart\Bundle\NucleusBundle\Twig\Extension;

use Mozart\Bundle\NucleusBundle\Twig\TwigProxy;

class NucleusExtension extends \Twig_Extension
{

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        return array(
            'wp' => new TwigProxy(),
            'q' => $_GET,
            'p' => $_POST
        );
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('wp_footer', array($this, 'wp_footer')),
            new \Twig_SimpleFunction('wp_head', array($this, 'wp_head')),
            new \Twig_SimpleFunction('comment_form', array($this, 'comment_form')),
            new \Twig_SimpleFunction('body_class', array($this, 'body_class')),
            new \Twig_SimpleFunction('wp_list_comments', array($this, 'wp_list_comments')),
            new \Twig_SimpleFunction('post_class', array($this, 'post_class')),
            new \Twig_SimpleFunction('dynamic_sidebar', array($this, 'dynamic_sidebar')),
            new \Twig_SimpleFunction('comments_template', array($this, 'comments_template')),
            new \Twig_SimpleFunction('paginate_comments_links', array($this, 'paginate_comments_links')),
            new \Twig_SimpleFunction('next_comments_link', array($this, 'next_comments_link')),
            new \Twig_SimpleFunction('previous_comments_link', array($this, 'previous_comments_link')),
            new \Twig_SimpleFunction('posts_nav_link', array($this, 'posts_nav_link')),
            new \Twig_SimpleFunction('paginate_links', array($this, 'paginate_links')),
            new \Twig_SimpleFunction('next_posts_link', array($this, 'next_posts_link')),
            new \Twig_SimpleFunction('previous_posts_link', array($this, 'previous_posts_link')),
            new \Twig_SimpleFunction('kk_star_ratings', array($this, 'kk_star_ratings')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('translate', array($this, 'translate')),
        );
    }

    public function translate($text, $domain = 'mozart')
    {
        return \__($text, $domain);
    }

    /**
     * Get an array of all available template directories for plugins
     * $dir can be the child theme root dir or parent theme root dir
     * @return array
     */
    public function prepare_components_template_dirs($dir)
    {
        $templates = array();

        $modules = \Rhetina::service('kernel')->getContainer()->getParameter('container.modules');

        foreach ($modules as $moduleName => $moduleFile) {
            $templates[] = dirname($moduleFile) . '/Resources/views';
        }

        return $templates;
    }

    public function wp_footer()
    {
        wp_footer();
    }

    public function wp_head()
    {
        wp_head();
    }

    public function comment_form()
    {
        return comment_form();
    }

    public function body_class($attrs = NULL)
    {
        body_class($attrs);
    }

    public function wp_list_comments($attrs = NULL)
    {
        wp_list_comments();
    }

    public function post_class($attrs = NULL)
    {
        post_class($attrs);
    }

    public function dynamic_sidebar($attrs = NULL)
    {
        dynamic_sidebar($attrs);
    }

    public function comments_template($attrs = NULL)
    {
        comments_template();
    }

    public function paginate_comments_links($attrs = NULL)
    {
        paginate_comments_links();
    }

    public function next_comments_link($attrs = NULL)
    {
        next_comments_link();
    }

    public function previous_comments_link($attrs = NULL)
    {
        paginate_comments_links();
    }

    public function posts_nav_link($attrs = NULL)
    {
        posts_nav_link($attrs);
    }

    public function paginate_links($attrs = NULL)
    {
        paginate_links($attrs);
    }

    public function next_posts_link($attrs = NULL)
    {
        next_posts_link($attrs);
    }

    public function previous_posts_link($attrs = NULL)
    {
        previous_posts_link($attrs);
    }

    public function kk_star_ratings($attrs = NULL)
    {
        if (function_exists('kk_star_ratings')) {
            return kk_star_ratings($attrs);
        }
    }

    public function getName()
    {
        return 'mozart_nucleus';
    }

}
