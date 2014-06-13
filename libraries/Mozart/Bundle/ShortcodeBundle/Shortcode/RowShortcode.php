<?php

namespace Mozart\Bundle\ShortcodeBundle\Shortcode;
use Mozart\Bundle\ShortcodeBundle\ShortcodeInterface;

/**
 * Class ContentBoxShortcode
 *
 * @package Mozart\Bundle\ShortcodeBundle\Shortcode
 */
class RowShortcode implements ShortcodeInterface
{
    /**
     * @param      $params
     * @param null $content
     *
     * @return string
     */
    public function process($params, $content = null)
    {
        $result = '<div class="row">' . do_shortcode($content) . '</div>';

        return force_balance_tags($result);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'row';
    }
}
