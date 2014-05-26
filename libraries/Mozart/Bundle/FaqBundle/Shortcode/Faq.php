<?php

namespace Mozart\Bundle\FaqBundle\Shortcode;

class Faq
{
    public function shortcode($params, $content = NULL)
    {
        $attributes = array(
            'post_type' => 'faq',
            'posts_per_page' => '-1',
        );

        if (!empty($params['category'])) {
            $attributes['tax_query'] = array(
                array(
                    'taxonomy' => 'faq_categories',
                    'field' => 'id',
                    'terms' => $params['category'],
                    'operator' => 'IN',
                ),
            );
        }
        $questions = new \WP_Query($attributes);

        $result = twiggy('FaqBundle:Faq:shortcodes/faq.html.twig', array(
                    'questions' => $questions->posts,
                    'category' => !empty($params['category']) ? $params['category'] : NULL,
        ), false);

        return force_balance_tags($result);
    }

}
