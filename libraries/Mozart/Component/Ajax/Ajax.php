<?php
namespace Mozart\Component\Ajax;

class Ajax
{
    /**
     * Handle the Ajax response. Run the appropriate
     * action hooks used by WordPress in order to perform
     * POST ajax request securely.
     * Developers have the option to run ajax for the
     * Front-end, Back-end either users are logged in or not
     * or both.
     *
     * @param string   $action  Your ajax 'action' name
     * @param callable $closure The function to run when ajax action is called
     * @param string   $logged  Accepted values are 'no', 'yes', 'both'
     *
     * @throws AjaxException
     */
    public static function run($action, callable $closure, $logged = 'both')
    {
        if (false === defined( 'DOING_AJAX' ) || false === DOING_AJAX) {
            return false;
        }

        if (!is_string( $action ) || !is_callable( $closure )) {
            throw new AjaxException( "Invalid parameters for the Ajax::run method." );
        }

        // Front-end ajax for non-logged users
        // Set $logged to FALSE
        if ($logged === 'no') {
            add_action( 'wp_ajax_nopriv_' . $action, $closure );
        }

        // Front-end and back-end for logged users
        if ($logged === 'yes') {
            add_action( 'wp_ajax_' . $action, $closure );
        }

        // Front-end and back-end for both logged in or out users
        if ($logged === 'both') {
            add_action( 'wp_ajax_nopriv_' . $action, $closure );
            add_action( 'wp_ajax_' . $action, $closure );
        }

    }

    /**
     * Install the Ajax global variable in the <head> tag.
     *
     * @return void
     * @ignore
     */
    public static function installScript($namespace, $datas = array())
    {
        if (false === isset( $datas['ajax_url'] )) {
            $datas['ajax_url'] = get_admin_url( null, 'admin-ajax.php' );
        }
        $output = "\n\r<script>\n\r";
        $output .= "/* <![CDATA[ */\n\r";
        $output .= "var $namespace = $namespace || {}; ";

        if (!empty( $datas )) {
            foreach ($datas as $key => $value) {
                $output .= $namespace . '.' . $key . " = " . json_encode( $value ) . ";";
            }
        }
        $output .= "\n\r";
        $output .= "/* ]]> */\n\r";
        $output .= "</script>\n\r";

        // Output the datas.
        echo( $output );
    }
}
