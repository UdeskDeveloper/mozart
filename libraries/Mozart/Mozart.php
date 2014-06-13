<?php

/**
 * @file
 * Contains Mozart.
 */

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Static Service Container wrapper.
 *
 */
class Mozart
{
    /**
     * The currently active container object.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static $container;

    /**
     * Sets a new global container.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *   A new container instance to replace the current. NULL may be passed by
     *   testing frameworks to ensure that the global state of a previous
     *   environment does not leak into a test.
     */
    public static function setContainer( ContainerInterface $container = null )
    {
        static::$container = $container;
    }

    /**
     * Returns the currently active global container.
     *
     * @deprecated This method is only useful for the testing environment. It
     * should not be used otherwise.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * Retrieves a service from the container.
     *
     * Use this method if the desired service is not one of those with a dedicated
     * accessor method below. If it is listed below, those methods are preferred
     * as they can return useful type hints.
     *
     * @param string $id
     *   The ID of the service to retrieve.
     *
     * @return mixed
     *   The specified service.
     */
    public static function service( $id )
    {
        return static::$container->get( $id );
    }

    /**
     * Indicates if a service is defined in the container.
     *
     * @param string $id
     *   The ID of the service to check.
     *
     * @return bool
     *   TRUE if the specified service exists, FALSE otherwise.
     */
    public static function hasService( $id )
    {
        return static::$container && static::$container->has( $id );
    }


    /**
     * Gets Wordpress state.
     *
     * @return bool
     */
    public static function isWpRunning()
    {
        return defined( 'ABSPATH' );
    }

    /**
     *
     */
    public static function shutdown()
    {
        self::service( 'kernel' )->shutdown();
    }
}
