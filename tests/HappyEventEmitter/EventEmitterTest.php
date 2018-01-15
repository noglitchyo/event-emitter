<?php

namespace HappyEventEmitter\Tests;

use HappyEventEmitter\EventEmitter;
use HappyEventEmitter\Exception\ListenerAlreadyExistException;
use PHPUnit\Framework\TestCase;

class EventEmitterTest extends TestCase
{
    /**
     * @var EventEmitter
     */
    private $emitter;

    public function setUp()
    {
        $this->emitter = new EventEmitter();
    }

    public function testAddListenerWithRandomEventName()
    {
        $eventName = 'myRandomEventName';
        $func = function () {};
        $this->emitter->on($eventName, $func);
        $listeners = $this->emitter->getListeners($eventName);
        $this->assertContains($func, $listeners);
    }

    public function testAddListenerAlreadyExistIsNotAllowed()
    {
        $eventName = 'myRandomEventName';
        $func = function ($test) {
            return $test;
        };

        $this->emitter->on($eventName, $func);

        try {
            $this->emitter->on($eventName, $func);
        } catch (\Exception $exception) {
            $this->assertTrue($exception instanceof ListenerAlreadyExistException);
        }
    }

    public function testEmitWithoutArgs()
    {
        $eventName = 'myRandomEventName';
        $listenerCalled = false;
        $this->emitter->on($eventName, function () use (&$listenerCalled) {
            $listenerCalled = true;
        });
        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit($eventName);
        $this->assertSame(true, $listenerCalled);
    }

    public function testEmitWithArgs()
    {
        $listenerCalled = false;
        $this->emitter->on('happyEvent', function ($arg1, $arg2) use (&$listenerCalled) {
            $listenerCalled = $arg1 === $arg2;
        });
        $this->assertSame(false, $listenerCalled);
        $this->emitter->emit('happyEvent', 1, 1);
        $this->assertSame(true, $listenerCalled);
    }

    public function testEmitUnregisteredEvent()
    {
        $this->emitter->emit('myRandomEventName');
        $this->assertTrue(true);
    }

    public function testGetListeners()
    {
        $this->assertTrue(true);
    }

    public function testRemoveListener()
    {
        $valueEqualZero = 0;
        $valueEqualOne = 0;
        $eventName = 'myRandomEventName';
        $func = function () use (&$valueEqualZero) {
            $valueEqualZero++;
        };
        $func2 = function () use (&$valueEqualOne) {
            $valueEqualOne++;
        };
        $this->emitter->on($eventName, $func);
        $this->emitter->on($eventName, $func2);
        $this->emitter->removeListener($eventName, $func);
        $this->emitter->emit($eventName);

        $this->assertSame(0, $valueEqualZero);
        $this->assertSame(1, $valueEqualOne);
    }

    public function testRemoveListenerWithSameCallback()
    {
        $isTrue = false;
        $eventName = 'event';
        $func = function () use (&$isTrue) {
            $isTrue = true;
        };
        $func2 = function () use (&$isTrue) {
            $isTrue = false;
        };
        $this->emitter->on($eventName, $func);
        $this->emitter->on($eventName, $func2);

        $this->emitter->removeListener($eventName, $func2);

        $this->emitter->emit($eventName);

        $this->assertTrue($isTrue);
    }

    public function testRemoveAllListeners()
    {
        $isDisabled = true;
        $isEqualToOne = 1;

        $this->emitter->on('randomEvent1', function () use (&$isDisabled) {
            $isDisabled = false;
        });

        $this->emitter->on('randomEvent2', function ($multiple) use (&$isEqualToOne) {
            $isEqualToOne = $isEqualToOne * $multiple;
        });

        $this->emitter->removeAllListeners();

        $this->emitter->emit('randomEvent1');
        $this->assertTrue($isDisabled, 'Failed to remove all listeners without args');

        $this->emitter->emit('randomEvent2', 2);
        $this->assertTrue($isEqualToOne === 1);
    }
}
