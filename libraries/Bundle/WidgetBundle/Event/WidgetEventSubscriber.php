<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\WidgetBundle\Event;

use Mozart\Bundle\NucleusBundle\MozartEvents;
use Mozart\Bundle\WidgetBundle\SidebarManager;
use Mozart\Bundle\WidgetBundle\WidgetManager;
use Mozart\Component\Widget\Logic\WidgetLogic;
use Mozart\Component\Widget\WidgetEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WidgetEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var WidgetLogic
     */
    private $widgetLogic;
    /**
     * @var SidebarManager
     */
    private $sidebarManager;
    /**
     * @var WidgetManager
     */
    private $widgetManager;

    /**
     * @param WidgetLogic $widgetLogic
     */
    public function __construct(WidgetLogic $widgetLogic, SidebarManager $sidebarManager, WidgetManager $widgetManager)
    {

        $this->widgetLogic = $widgetLogic;
        $this->sidebarManager = $sidebarManager;
        $this->widgetManager = $widgetManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        $events[MozartEvents::INIT][] = array( 'onMozartInit', 255 );
        $events[WidgetEvents::INIT][] = array( 'registerSidebars', -255 );
        $events[WidgetEvents::INIT][] = array( 'registerWidgets', -255 );

        return $events;
    }

    public function onMozartInit()
    {
        $this->widgetLogic->initialize();

	    add_action(
		    WidgetEvents::INIT,
		    function () {
			    \Mozart::dispatch( WidgetEvents::INIT );
		    },
		    0
	    );
    }

    public function registerSidebars()
    {
        if (false === $GLOBALS['wp_registered_sidebars']) {
            return false;
        }

        add_theme_support( 'widgets' );

        $sidebars = $this->sidebarManager->getSidebars();

        foreach ($sidebars as $sidebar) {

            $GLOBALS['wp_registered_sidebars'][$sidebar->getKey()] = $sidebar->getConfiguration();

            do_action( 'register_sidebar', $sidebar->getConfiguration() );
        }
    }

    public function registerWidgets()
    {

        if (false === isset( $GLOBALS['wp_widget_factory'] )) {
            return false;
        }

        $widgets = $this->widgetManager->getWidgets();

        $GLOBALS['wp_widget_factory']->widgets = array_merge( $GLOBALS['wp_widget_factory']->widgets, $widgets );
    }
}