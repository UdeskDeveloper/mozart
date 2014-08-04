<?php

/**
 * @file
 * Contains Mozart.
 */

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Static Service Container wrapper.
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
    public static function setContainer(ContainerInterface $container = null)
    {
        static::$container = $container;
    }

    /**
     * Returns the currently active global container.
     *
     * @return ContainerInterface
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
     * @param string $id              The ID of the service to retrieve.
     * @param int    $invalidBehavior The behavior when the service does not exist
     *
     * @return mixed The specified service.
     */
    public static function service($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
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
    public static function hasService($id)
    {
        return static::$container && static::$container->has( $id );
    }

    /**
     * Retrieves the currently active request object.
     *
     * Note: The use of this wrapper in particular is especially discouraged. Most
     * code should not need to access the request directly.  Doing so means it
     * will only function when handling an HTTP request, and will require special
     * modification or wrapping when run from a command line tool, from certain
     * queue processors, or from automated tests.
     *
     * If code must access the request, it is considerably better to register
     * an object with the Service Container and give it a setRequest() method
     * that is configured to run when the service is created.  That way, the
     * correct request object can always be provided by the container and the
     * service can still be unit tested.
     *
     * If this method must be used, never save the request object that is
     * returned.  Doing so may lead to inconsistencies as the request object is
     * volatile and may change at various times, such as during a subrequest.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     *                                                   The currently active request object.
     */
    public static function request()
    {
        return static::$container->get('request');
    }

    /**
     * Indicates if there is a currently active request object.
     *
     * @return bool
     *              TRUE if there is a currently active request object, FALSE otherwise.
     */
    public static function hasRequest()
    {
        return static::$container && static::$container->has('request') && static::$container->initialized('request') && static::$container->isScopeActive('request');
    }

    /**
     * Retrieves a configuration object.
     *
     * This is the main entry point to the configuration API. Calling
     * @code \Drupal::config('book.admin') @endcode will return a configuration
     * object in which the book module can store its administrative settings.
     *
     * @param string $name
     *                     The name of the configuration object to retrieve. The name corresponds to
     *                     a configuration file. For @code \Drupal::config('book.admin') @endcode, the config
     *                     object returned will contain the contents of book.admin configuration file.
     *
     * @return \Drupal\Core\Config\Config
     *                                    A configuration object.
     */
    public static function config($name)
    {
        return static::$container->get('config.factory')->get($name);
    }

    /**
     * Retrieves the configuration factory.
     *
     * This is mostly used to change the override settings on the configuration
     * factory. For example, changing the language, or turning all overrides on
     * or off.
     *
     * @return \Drupal\Core\Config\ConfigFactoryInterface
     *                                                    The configuration factory service.
     */
    public static function configFactory()
    {
        return static::$container->get('config.factory');
    }

    /**
     * Returns a channel logger object.
     *
     * @param string $channel
     *                        The name of the channel. Can be any string, but the general practice is
     *                        to use the name of the subsystem calling this.
     *
     * @return \Drupal\Core\Logger\LoggerChannelInterface
     *                                                    The logger for this channel.
     */
    public static function logger($channel)
    {
        return static::$container->get('logger.factory')->get($channel);
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
    public static function parameter($name)
    {
        return self::$container->getParameter( $name );
    }

    /**
     * @param  array            $bundles
     * @return array|mixed|void
     */
    public static function registerAdditionalBundles(array $bundles)
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
