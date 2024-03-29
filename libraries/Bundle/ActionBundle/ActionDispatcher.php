<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\ActionBundle;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActionDispatcher implements EventDispatcherInterface
{
    private $listeners = array();
    private $sorted = array();

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     * @param Event  $event     The event to pass to the event handlers/listeners.
     *                          If not supplied, an empty Event instance is created.
     *
     * @return Event
     *
     * @api
     */
    public function dispatch($eventName, Event $event = null)
    {
        global $wp_current_filter;

        if (null === $event) {
            $event = new Event();
        }
        $event->setDispatcher( $this );
        $event->setName( $eventName );

        if (!isset( $this->listeners[$eventName] )) {
            return $event;
        }

        $this->doDispatch( $this->getListeners( $eventName ), $eventName, $event );

        array_pop( $wp_current_filter );

        return $event;
    }

    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners.
     * @param string     $eventName The name of the event to dispatch.
     * @param Event      $event     The event object to pass to the event handlers/listeners.
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            call_user_func( $listener, $event, $eventName, $this );
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     *
     * @api
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        add_action( $eventName, $listener, $priority );
    }

    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events he is
     * interested in and added as a listener for these events.
     *
     * @param EventSubscriberInterface $subscriber The subscriber.
     *
     * @api
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        // TODO: Implement addSubscriber() method.
    }

    /**
     * Removes an event listener from the specified events.
     *
     * @param string|array $eventName The event(s) to remove a listener from
     * @param callable     $listener  The listener to remove
     */
    public function removeListener($eventName, $listener)
    {
    }

    /**
     * Removes an event subscriber.
     *
     * @param EventSubscriberInterface $subscriber The subscriber
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        // TODO: Implement removeSubscriber() method.
    }

    /**
     * Gets the listeners of a specific event or all listeners.
     *
     * @param string $eventName The name of the event
     *
     * @return array The event listeners for the specified event, or all event listeners by event name
     */
    public function getListeners($eventName = null)
    {
        global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;

        if (!isset( $wp_actions[$eventName] )) {
            $wp_actions[$eventName] = 1;
        } else {
            ++$wp_actions[$eventName];
        }

        // Do 'all' actions first
        if (isset( $wp_filter['all'] )) {
            $wp_current_filter[] = $eventName;
            $all_args = func_get_args();
            _wp_call_all_hook( $all_args );
        }

        if (!isset( $wp_filter[$eventName] )) {
            if (isset( $wp_filter['all'] )) {
                array_pop( $wp_current_filter );
            }

            return [ ];
        }

        if (!isset( $wp_filter['all'] )) {
            $wp_current_filter[] = $eventName;
        }

        // Sort
        if (!isset( $merged_filters[$eventName] )) {
            ksort( $wp_filter[$eventName] );
            $merged_filters[$eventName] = true;
        }

//        reset( $wp_filter[ $eventName ] );
        return $wp_filter[$eventName];
    }

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string $eventName The name of the event
     *
     * @return bool true if the specified event has any listeners, false otherwise
     */
    public function hasListeners($eventName = null)
    {
        // TODO: Implement hasListeners() method.
    }
}
