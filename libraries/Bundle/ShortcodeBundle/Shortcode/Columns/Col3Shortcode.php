<?php

namespace Mozart\Bundle\ShortcodeBundle\Shortcode\Columns;

use Mozart\Bundle\ShortcodeBundle\ShortcodeInterface;

/**
 * Class ContentBoxShortcode
 *
 * @package Mozart\Bundle\ShortcodeBundle\Shortcode
 */
class Col3Shortcode implements ShortcodeInterface
{
    /**
     * @param      $params
     * @param null $content
     *
     * @return string
     */
    public function process($params, $content = null)
    {
        $result = '<div class="col-xs-3">' . do_shortcode( $content ) . '</div>';

        return force_balance_tags( $result );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'span3';
    }
}
