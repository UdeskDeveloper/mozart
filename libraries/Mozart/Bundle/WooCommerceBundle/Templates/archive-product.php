<?php

global $wp_query;

twiggy('MozartWooCommerceBundle:Product:archive.html.twig', array(
    'wp_query' => $wp_query,
    'posts' => $wp_query->posts,
    'posts_per_page' => -1,
    'show_title' => apply_filters('woocommerce_show_page_title', true),
    'no_products' => !woocommerce_product_subcategories(
            array(
                'before' => woocommerce_product_loop_start(false),
                'after' => woocommerce_product_loop_end(false)
            )
    )
));
