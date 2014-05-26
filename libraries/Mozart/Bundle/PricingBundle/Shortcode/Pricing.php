<?php

namespace Mozart\Bundle\PricingBundle\Shortcode;
use Mozart\Bundle\NucleusBundle\Shortcode\ShortcodeInterface;

class Pricing implements ShortcodeInterface
{
    public function shortcode($params, $content = NULL)
    {
        if (!empty($params['post'])) {
            $post = get_post($params['post']);
        }
        $result = twiggy('PricingBundle:Pricing:shortcodes/pricing.html.twig', array(
                    'post' => $post,
                    'promoted' => !empty($params['promoted']) ? $params['promoted'] : FALSE,
                    'title' => !empty($params['title']) ? $params['title'] : NULL,
                    'subtitle' => !empty($params['subtitle']) ? $params['subtitle'] : NULL,
                    'price' => !empty($params['price']) ? $params['price'] : NULL,
                    'link' => !empty($params['link']) ? $params['link'] : FALSE,
                    'button_text' => !empty($params['button_text']) ? $params['button_text'] : FALSE,
                ), false);

        return force_balance_tags($result);
    }
}
