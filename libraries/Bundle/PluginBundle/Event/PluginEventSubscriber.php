<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PluginBundle\Event;


use Mozart\Bundle\PluginBundle\PluginEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PluginEventSubscriber implements EventSubscriberInterface
{

    public function onBeforePluginActivation(PluginEvent $event)
    {

    }

    public function onAfterPluginActivation(PluginEvent $event)
    {

    }

    public function onBeforePluginDeactivation(PluginEvent $event)
    {

    }

    public function onAfterPluginDeactivation(PluginEvent $event)
    {

    }

    public function onPluginUninstall(PluginEvent $event)
    {

    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        $events[PluginEvents::BEFORE_ACTIVATION][] = array( 'onBeforePluginActivation' );
        $events[PluginEvents::AFTER_ACTIVATION][] = array( 'onAfterPluginActivation' );

        $events[PluginEvents::BEFORE_DEACTIVATION][] = array( 'onBeforePluginDeactivation' );
        $events[PluginEvents::AFTER_DEACTIVATION][] = array( 'onAfterPluginDeactivation' );

        $events[PluginEvents::UNINSTALL][] = array( 'onPluginUninstall' );

        return $events;
    }
}