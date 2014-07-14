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
     * Mozart domain
     */
    const DOMAIN = 'mozart';
    /**
     * The currently active container object.
     *
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * Sets a new global container.
     *
     * @param ContainerInterface $container A new container instance to replace the current.
     *
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
     * @param string $id The ID of the service to retrieve.
     * @param int $invalidBehavior The behavior when the service does not exist
     *
     * @return mixed The specified service.
     */
    public static function service( $id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE )
    {
        return static::$container->get( $id, $invalidBehavior );
    }

    /**
     * Indicates if a service is defined in the container.
     *
     * @param string $id The ID of the service to check.
     *
     * @return bool true if the service is defined, false otherwise
     */
    public static function hasService( $id )
    {
        return static::$container && static::$container->has( $id );
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     *
     */
    public static function parameter( $name )
    {
        return self::$container->getParameter( $name );
    }

    /**
     * @param array $bundles
     * @return array|mixed|void
     */
    public static function registerAdditionalBundles( array $bundles )
    {
        $bundles = apply_filters( 'register_mozart_bundle', $bundles );

        return $bundles;
    }

    /**
     * @param       $view
     * @param array $context
     *
     * @return mixed
     */
    public static function render( $view, array $context = array() )
    {
        $view = apply_filters( 'mozart_view', $view, $context );
        $context = apply_filters( 'mozart_context', $context );

        return static::service( 'templating' )->render( $view, $context );
    }

    /**
     * @param       $view
     * @param array $context
     */
    public static function renderView( $view, array $context = array() )
    {
        echo static::render( $view, $context );
    }
}
