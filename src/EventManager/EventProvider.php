<?php

declare(strict_types=1);

namespace LmcUser\EventManager;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Traversable;

use function array_merge;
use function array_unique;
use function is_array;
use function is_object;
use function is_string;

abstract class EventProvider implements EventManagerAwareInterface
{
    /** @var EventManagerInterface */
    protected $events;
    /**
     * Set the event manager instance used by this context
     *
     * @return mixed
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $identifiers = [self::class, static::class];
        if (isset($this->eventIdentifier)) {
            if (
                (is_string($this->eventIdentifier))
                || (is_array($this->eventIdentifier))
                || $this->eventIdentifier instanceof Traversable
            ) {
                $identifiers = array_unique(array_merge($identifiers, (array) $this->eventIdentifier));
            } elseif (is_object($this->eventIdentifier)) {
                $identifiers[] = $this->eventIdentifier;
            }
            // silently ignore invalid eventIdentifier types
        }
        $events->setIdentifiers($identifiers);
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (! $this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
