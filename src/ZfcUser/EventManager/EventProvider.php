<?php
namespace ZfcUser\EventManager;

use Traversable;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\EventManagerAwareTrait;

abstract class EventProvider implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;
}
