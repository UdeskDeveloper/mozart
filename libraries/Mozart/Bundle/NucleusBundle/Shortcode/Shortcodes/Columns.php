<?php

namespace Mozart\Bundle\NucleusBundle\Shortcode\Shortcodes;

class Columns
{
    // Row
    public function row($params, $content = null)
    {
        $result = '<div class="row">' . do_shortcode($content) . '</div>';

        return force_balance_tags($result);
    }

// Col 3/12
    public function row_span3($params, $content = null)
    {
        $result = '<div class="col-xs-3">' . do_shortcode($content) . '</div>';

        return force_balance_tags($result);
    }

// Col 4/12
    public function row_span4($params, $content = null)
    {
        $result = '<div class="col-xs-4">' . do_shortcode($content) . '</div>';

        return force_balance_tags($result);
    }

// Col 6/12
    public function row_span6($params, $content = null)
    {
        $result = '<div class="col-xs-6">' . do_shortcode($content) . '</div>';

        return force_balance_tags($result);
    }

// Col 8/12
    public function row_span8($params, $content = null)
    {
        $result = '<div class="col-xs-8">' . do_shortcode($content) . '</div>';

        return force_balance_tags($result);
    }

// Col 9/12
    public function row_span9($params, $content = null)
    {
        $result = '<div class="col-xs-9">' . do_shortcode($content) . '</div>';

        return force_balance_tags($result);
    }

}
