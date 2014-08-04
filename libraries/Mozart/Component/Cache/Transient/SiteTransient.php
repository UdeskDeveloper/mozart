<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Cache\Transient;

class SiteTransient implements TransientInterface
{

    /**
     * Set/update the value of a site transient.
     *
     * You do not need to serialize values, if the value needs to be serialize, then
     * it will be serialized before it is set.
     *
     * @since 2.9.0
     *
     * @see   set_transient()
     *
     * @param string $transient  Transient name. Expected to not be SQL-escaped.
     * @param mixed  $value      Transient value. Expected to not be SQL-escaped.
     * @param int    $expiration Time until expiration in seconds, default 0
     *
     * @return bool False if value was not set and true if value was set.
     */
    public function set($transient, $value, $expiration = 0)
    {

        /**
         * Filter the value of a specific site transient before it is set.
         *
         * The dynamic portion of the hook name, $transient, refers to the transient name.
         *
         * @since 3.0.0
         *
         * @param mixed $value Value of site transient.
         */
        $value = apply_filters( 'pre_set_site_transient_' . $transient, $value );

        $expiration = (int) $expiration;

        if (wp_using_ext_object_cache()) {
            $result = wp_cache_set( $transient, $value, 'site-transient', $expiration );
        } else {
            $transient_timeout = '_site_transient_timeout_' . $transient;
            $option = '_site_transient_' . $transient;
            if (false === get_site_option( $option )) {
                if ($expiration) {
                    add_site_option( $transient_timeout, time() + $expiration );
                }
                $result = add_site_option( $option, $value );
            } else {
                if ($expiration) {
                    update_site_option( $transient_timeout, time() + $expiration );
                }
                $result = update_site_option( $option, $value );
            }
        }
        if ($result) {

            /**
             * Fires after the value for a specific site transient has been set.
             *
             * The dynamic portion of the hook name, $transient, refers to the transient name.
             *
             * @since 3.0.0
             *
             * @param mixed $value Site transient value.
             * @param int $expiration Time until expiration in seconds. Default 0.
             */
            do_action( 'set_site_transient_' . $transient, $value, $expiration );

            /**
             * Fires after the value for a site transient has been set.
             *
             * @since 3.0.0
             *
             * @param string $transient The name of the site transient.
             * @param mixed $value Site transient value.
             * @param int $expiration Time until expiration in seconds. Default 0.
             */
            do_action( 'setted_site_transient', $transient, $value, $expiration );
        }

        return $result;
    }

    /**
     * Get the value of a site transient.
     *
     * If the transient does not exist or does not have a value, then the return value
     * will be false.
     *
     * @since 2.9.0
     *
     * @see   get_transient()
     *
     * @param string $transient Transient name. Expected to not be SQL-escaped.
     *
     * @return mixed Value of transient
     */
    public function get($transient)
    {

        /**
         * Filter the value of an existing site transient.
         *
         * The dynamic portion of the hook name, $transient, refers to the transient name.
         *
         * Passing a truthy value to the filter will effectively short-circuit retrieval,
         * returning the passed value instead.
         *
         * @since 2.9.0
         *
         * @param mixed $pre_site_transient The default value to return if the site transient does not exist.
         *                                  Any value other than false will short-circuit the retrieval
         *                                  of the transient, and return the returned value.
         */
        $pre = apply_filters( 'pre_site_transient_' . $transient, false );

        if (false !== $pre) {
            return $pre;
        }

        if (wp_using_ext_object_cache()) {
            $value = wp_cache_get( $transient, 'site-transient' );
        } else {
            // Core transients that do not have a timeout. Listed here so querying timeouts can be avoided.
            $no_timeout = array( 'update_core', 'update_plugins', 'update_themes' );
            $transient_option = '_site_transient_' . $transient;
            if (!in_array( $transient, $no_timeout )) {
                $transient_timeout = '_site_transient_timeout_' . $transient;
                $timeout = get_site_option( $transient_timeout );
                if (false !== $timeout && $timeout < time()) {
                    delete_site_option( $transient_option );
                    delete_site_option( $transient_timeout );
                    $value = false;
                }
            }

            if (!isset( $value )) {
                $value = get_site_option( $transient_option );
            }
        }

        /**
         * Filter the value of an existing site transient.
         *
         * The dynamic portion of the hook name, $transient, refers to the transient name.
         *
         * @since 2.9.0
         *
         * @param mixed $value Value of site transient.
         */

        return apply_filters( 'site_transient_' . $transient, $value );
    }

    /**
     * Delete a site transient.
     *
     * @since 2.9.0
     *
     * @param string $transient Transient name. Expected to not be SQL-escaped.
     *
     * @return bool True if successful, false otherwise
     */
    public function delete($transient)
    {

        /**
         * Fires immediately before a specific site transient is deleted.
         *
         * The dynamic portion of the hook name, $transient, refers to the transient name.
         *
         * @since 3.0.0
         *
         * @param string $transient Transient name.
         */
        do_action( 'delete_site_transient_' . $transient, $transient );

        if (wp_using_ext_object_cache()) {
            $result = wp_cache_delete( $transient, 'site-transient' );
        } else {
            $option_timeout = '_site_transient_timeout_' . $transient;
            $option = '_site_transient_' . $transient;
            $result = delete_site_option( $option );
            if ($result) {
                delete_site_option( $option_timeout );
            }
        }
        if ($result) {

            /**
             * Fires after a transient is deleted.
             *
             * @since 3.0.0
             *
             * @param string $transient Deleted transient name.
             */
            do_action( 'deleted_site_transient', $transient );
        }

        return $result;
    }
}
