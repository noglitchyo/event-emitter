<?php

namespace HappyEventEmitter;

/**
 * Interface EventEmitterInterface
 * @package HappyEventEmitter
 */
interface EventEmitterInterface
{
    /**
     * @param string $event
     * @param callable $listener
     * @return mixed
     */
    public function on(string $event, callable $listener);

    /**
     * @param string $event
     * @param callable $listener
     * @return mixed
     */
    public function removeListener(string $event, callable $listener);

    /**
     * @param string|null $event
     * @return mixed
     */
    public function removeAllListeners(string $event = null);

    /**
     * @param string $event
     * @param array ...$arguments
     * @return mixed
     */
    public function emit(string $event, ...$arguments);
}
