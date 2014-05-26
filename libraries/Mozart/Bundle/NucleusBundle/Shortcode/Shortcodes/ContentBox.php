<?php

namespace Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes;

class ContentBox
{
    public function shortcode($params, $content = NULL)
    {
        $result = twiggy('NucleusBundle:Shortcodes:content_box.html.twig', array(
                    'content' => $content,
                    'icon' => !empty($params['icon']) ? $params['icon'] : FALSE,
                    'icon_pictopro_class' => !empty($params['icon_pictopro_class']) ? $params['icon_pictopro_class'] : FALSE,
                    'title' => !empty($params['title']) ? $params['title'] : FALSE,
                    'columns_for_content' => !empty($params['columns_for_content']) ? $params['columns_for_content'] : 3,
                ), false);

        return force_balance_tags($result);
    }

}
