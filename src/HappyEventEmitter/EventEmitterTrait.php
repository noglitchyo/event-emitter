<?php

namespace HappyEventEmitter;

use \Ds\Vector;
use HappyEventEmitter\Exception\ListenerNotExistException;
use HappyEventEmitter\Exception\ListenerAlreadyExistException;

/**
 * Trait EventEmitterTrait
 *
 * Just a cool EventEmitter using a Vector implementation
 *
 * @see http://php.net/manual/en/class.ds-vector.php
 * @package HappyEventEmitter
 */
trait EventEmitterTrait
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param string $eventName
     * @param callable $listener
     */
    public function on(string $eventName, callable $listener)
    {
        if (!$this->hasListener($eventName)) {
            $this->listeners[$eventName] = new Vector();
        }

        if ($this->listeners[$eventName]->contains($listener)) {
            throw new ListenerAlreadyExistException(sprintf('Duplicate listener for event %s is not allowed', $eventName));
        }

        $this->listeners[$eventName]->push($listener);
    }

    /**
     * @param string $eventName
     * @param callable $listener
     */
    public function removeListener(string $eventName, callable $listener)
    {
        if (!$this->hasListener($eventName)) {
            return;
        }

        if (($index = $this->listeners[$eventName]->find($listener)) !== false) {
            try {
                $this->listeners[$eventName]->remove($index);
            } catch (\OutOfRangeException $e) {
            } finally {
                if ($this->listeners[$eventName]->isEmpty()) {
                    unset($this->listeners[$eventName]);
                }
            }
        }
    }

    /**
     * @param string|null $eventName
     */
    public function removeAllListeners(string $eventName = null)
    {
        if ($eventName !== null) {
            if (!$this->hasListener($eventName)) {
                return;
            }
            unset($this->listeners[$eventName]);
        } else {
            $this->listeners = [];
        }
    }

    /**
     * Synchronously calls each of the listeners registered for the event named eventName,
     * in the order they were registered, passing the supplied arguments to each.
     * @param string $event
     * @param array ...$arguments
     */
    public function emit(string $eventName, ...$arguments)
    {
        if (!$this->hasListener($eventName)) {
            return;
        }

        foreach ($this->listeners[$eventName] as $listener) {
            $listener(...$arguments);
        }
    }

    /**
     * @param string $eventName
     * @return mixed
     */
    public function getListeners(string $eventName): array
    {
        if (!$this->hasListener($eventName)) {
            throw new \InvalidArgumentException(sprintf('No listener defined for event %s', $eventName));
        }

        return $this->listeners[$eventName]->toArray();
    }

    /**
     * @param $eventName
     * @return bool
     */
    protected function hasListener($eventName): bool
    {
        return array_key_exists($eventName, $this->listeners);
    }
}
