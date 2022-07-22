<?php

declare(strict_types=1);

namespace LmcUser\Form;

use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\Form\Form;

class ProvidesEventsForm extends Form
{
    use EventManagerAwareTrait;
}
