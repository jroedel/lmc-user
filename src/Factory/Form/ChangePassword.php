<?php

declare(strict_types=1);

namespace LmcUser\Factory\Form;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Form;
use Psr\Container\ContainerInterface;

class ChangePassword implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, ?array $options = null)
    {
        $options = $serviceManager->get('lmcuser_module_options');
        $form    = new Form\ChangePassword(null, $options);

        $form->setInputFilter(new Form\ChangePasswordFilter($options));

        return $form;
    }
}
