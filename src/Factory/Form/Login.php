<?php

declare(strict_types=1);

namespace LmcUser\Factory\Form;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Form;
use Psr\Container\ContainerInterface;

class Login implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, ?array $options = null)
    {
        $options = $serviceManager->get('lmcuser_module_options');
        $form    = new Form\Login(null, $options);

        $form->setInputFilter(new Form\LoginFilter($options));

        return $form;
    }
}
