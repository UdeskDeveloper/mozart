<?php
namespace Mozart\Bundle\NucleusBundle\Twig;

/**
 *
 * Twig environemntal proxy
 */

class TwigProxy
{
    /**
     *
     * Registration
     *
     * @static
     * @access public
     * @return void
     */
    public static function register()
    {
    }

    /**
     *
     * Proxy calls
     *
     * @access public
     * @param  string $function
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($function, $arguments)
    {
        if (!function_exists($function)) {
            trigger_error('call to unexisting function ' . $function, E_USER_ERROR);

            return NULL;
        }

        return call_user_func_array($function, $arguments);
    }

}
