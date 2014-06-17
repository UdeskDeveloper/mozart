<?php

namespace Mozart\Bundle\ShortcodeBundle\Shortcode;
use Mozart\Bundle\ShortcodeBundle\ShortcodeInterface;

/**
 * Class ContentBoxShortcode
 *
 * @package Mozart\Bundle\ShortcodeBundle\Shortcode
 */
class ContentBoxShortcode implements ShortcodeInterface
{
    /**
     * @param      $params
     * @param null $content
     *
     * @return string
     */
    public function process($params, $content = NULL)
    {
        $result = twiggy('ShortocodeBundle:Shortcodes:content_box.html.twig', array(
                    'content' => $content,
                    'icon' => !empty($params['icon']) ? $params['icon'] : FALSE,
                    'icon_pictopro_class' => !empty($params['icon_pictopro_class']) ? $params['icon_pictopro_class'] : FALSE,
                    'title' => !empty($params['title']) ? $params['title'] : FALSE,
                    'columns_for_content' => !empty($params['columns_for_content']) ? $params['columns_for_content'] : 3,
                ), false);

        return force_balance_tags($result);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_box';
    }
}
