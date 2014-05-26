<?php

namespace Mozart\Bundle\WooCommerceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MozartWooCommerceBundle extends Bundle
{

    private $template_path;

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        $this->template_path = __DIR__ . '/Templates/';

        if (\Mozart::isWpRunning() === false) {
            return;
        }
        add_filter('WC_TEMPLATE_PATH', array($this, 'newTemplatePath'), 1, 1);
        add_filter('template_include', array($this, 'templateLoader'), 99);
        add_filter('comments_template', array($this, 'commentsTemplateLoader'), 99);

        add_filter('woocommerce_locate_template', array($this, 'relocateTemplate'), 99, 3);
    }

    public function relocateTemplate($template, $template_name, $template_path)
    {
        if (file_exists(trailingslashit($template_path) . $template_name)) {
            return trailingslashit($template_path) . $template_name;
        }
        return $template;
    }

    public function newTemplatePath($path)
    {
        return str_replace($this->container->getParameter('kernel.root_dir'), '', $this->template_path);
    }

    public function templateLoader($template)
    {
        $find = array('woocommerce.php');
        $file = '';

        if (is_single() && get_post_type() == 'product') {

            $file = 'single-product.php';
            $find[] = $file;
            $find[] = WC()->template_path() . $file;
        } elseif (is_tax('product_cat') || is_tax('product_tag')) {

            $term = get_queried_object();

            $file = 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] =  WC()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = $file;
            $find[] =  WC()->template_path() . $file;
        } elseif (is_post_type_archive('product') || is_page(wc_get_page_id('shop'))) {

            $file = 'archive-product.php';
            $find[] = $file;
            $find[] =  WC()->template_path() . $file;
        }

        if ($file) {
            $template = locate_template($find);
            $status_options = get_option('woocommerce_status_options', array());
            if (!$template || (!empty($status_options['template_debug_mode']) && current_user_can('manage_options') )) {
                if (file_exists($this->template_path . $file)) {
                    $template = $this->template_path . $file;
                } else {
                    $template = WC()->plugin_path() . '/templates/' . $file;
                }
            }
        }

        return $template;
    }

    /**
     * comments_template_loader function.
     *
     * @param  mixed  $template
     * @return string
     */
    public function commentsTemplateLoader($template)
    {
        if (get_post_type() !== 'product')
            return $template;

        if (file_exists(STYLESHEETPATH . '/' .  WC()->template_path() . 'single-product-reviews.php'))
            return STYLESHEETPATH . '/' .  WC()->template_path() . 'single-product-reviews.php';
        elseif (file_exists(TEMPLATEPATH . '/' .  WC()->template_path() . 'single-product-reviews.php'))
            return TEMPLATEPATH . '/' .  WC()->template_path() . 'single-product-reviews.php';
        elseif (file_exists(STYLESHEETPATH . '/' . 'single-product-reviews.php'))
            return STYLESHEETPATH . '/' . 'single-product-reviews.php';
        elseif (file_exists(TEMPLATEPATH . '/' . 'single-product-reviews.php'))
            return TEMPLATEPATH . '/' . 'single-product-reviews.php';
        else
            return WC()->plugin_path() . '/templates/single-product-reviews.php';
    }

}
