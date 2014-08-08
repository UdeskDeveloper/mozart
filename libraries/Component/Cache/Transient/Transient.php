<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Cache\Transient;

class Transient implements TransientInterface
{

    /**
     * Delete a transient.
     *
     * @since 2.8.0
     *
     * @param string $transient Transient name. Expected to not be SQL-escaped.
     *
     * @return bool true if successful, false otherwise
     */
    public function delete($transient)
    {

        /**
         * Fires immediately before a specific transient is deleted.
         *
         * The dynamic portion of the hook name, $transient, refers to the transient name.
         *
         * @since 3.0.0
         *
         * @param string $transient Transient name.
         */
        do_action( 'delete_transient_' . $transient, $transient );

        if (wp_using_ext_object_cache()) {
            $result = wp_cache_delete( $transient, 'transient' );
        } else {
            $option_timeout = '_transient_timeout_' . $transient;
            $option = '_transient_' . $transient;
            $result = delete_option( $option );
            if ($result) {
                delete_option( $option_timeout );
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
            do_action( 'deleted_transient', $transient );
        }

        return $result;
    }

    /**
     * Get the value of a transient.
     *
     * If the transient does not exist or does not have a value, then the return value
     * will be false.
     *
     * @since 2.8.0
     *
     * @param string $transient Transient name. Expected to not be SQL-escaped
     *
     * @return mixed Value of transient
     */
    public function get($transient)
    {

        /**
         * Filter the value of an existing transient.
         *
         * The dynamic portion of the hook name, $transient, refers to the transient name.
         *
         * Passing a truthy value to the filter will effectively short-circuit retrieval
         * of the transient, returning the passed value instead.
         *
         * @since 2.8.0
         *
         * @param mixed $pre_transient The default value to return if the transient does not exist.
         *                             Any value other than false will short-circuit the retrieval
         *                             of the transient, and return the returned value.
         */
        $pre = apply_filters( 'pre_transient_' . $transient, false );
        if (false !== $pre) {
            return $pre;
        }

        if (wp_using_ext_object_cache()) {
            $value = wp_cache_get( $transient, 'transient' );
        } else {
            $transient_option = '_transient_' . $transient;
            if (!defined( 'WP_INSTALLING' )) {
                // If option is not in alloptions, it is not autoloaded and thus has a timeout
                $alloptions = wp_load_alloptions();
                if (!isset( $alloptions[$transient_option] )) {
                    $transient_timeout = '_transient_timeout_' . $transient;
                    if (get_option( $transient_timeout ) < time()) {
                        delete_option( $transient_option );
                        delete_option( $transient_timeout );
                        $value = false;
                    }
                }
            }

            if (!isset( $value )) {
                $value = get_option( $transient_option );
            }
        }

        /**
         * Filter an existing transient's value.
         *
         * The dynamic portion of the hook name, $transient, refers to the transient name.
         *
         * @since 2.8.0
         *
         * @param mixed $value Value of transient.
         */

        return apply_filters( 'transient_' . $transient, $value );
    }

    /**
     * Set/update the value of a transient.
     *
     * You do not need to serialize values. If the value needs to be serialized, then
     * it will be serialized before it is set.
     *
     * @since 2.8.0
     *
     * @param string $transient  Transient name. Expected to not be SQL-escaped.
     * @param mixed  $value      Transient value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
     * @param int    $expiration Time until expiration in seconds, default 0
     *
     * @return bool False if value was not set and true if value was set.
     */
    public function set($transient, $value, $expiration = 0)
    {

        /**
         * Filter a specific transient before its value is set.
         *
         * The dynamic portion of the hook name, $transient, refers to the transient name.
         *
         * @since 3.0.0
         *
         * @param mixed $value New value of transient.
         */
        $value = apply_filters( 'pre_set_transient_' . $transient, $value );

        $expiration = (int) $expiration;

        if (wp_using_ext_object_cache()) {
            $result = wp_cache_set( $transient, $value, 'transient', $expiration );
        } else {
            $transient_timeout = '_transient_timeout_' . $transient;
            $transient = '_transient_' . $transient;
            if (false === get_option( $transient )) {
                $autoload = 'yes';
                if ($expiration) {
                    $autoload = 'no';
                    add_option( $transient_timeout, time() + $expiration, '', 'no' );
                }
                $result = add_option( $transient, $value, '', $autoload );
            } else {
                // If expiration is requested, but the transient has no timeout option,
                // delete, then re-create transient rather than update.
                $update = true;
                if ($expiration) {
                    if (false === get_option( $transient_timeout )) {
                        delete_option( $transient );
                        add_option( $transient_timeout, time() + $expiration, '', 'no' );
                        $result = add_option( $transient, $value, '', 'no' );
                        $update = false;
                    } else {
                        update_option( $transient_timeout, time() + $expiration );
                    }
                }
                if ($update) {
                    $result = update_option( $transient, $value );
                }
            }
        }

        if ($result) {

            /**
             * Fires after the value for a specific transient has been set.
             *
             * The dynamic portion of the hook name, $transient, refers to the transient name.
             *
             * @since 3.0.0
             *
             * @param mixed $value Transient value.
             * @param int $expiration Time until expiration in seconds. Default 0.
             */
            do_action( 'set_transient_' . $transient, $value, $expiration );

            /**
             * Fires after the value for a transient has been set.
             *
             * @since 3.0.0
             *
             * @param string $transient The name of the transient.
             * @param mixed $value Transient value.
             * @param int $expiration Time until expiration in seconds. Default 0.
             */
            do_action( 'setted_transient', $transient, $value, $expiration );
        }

        return $result;
    }

}
