<?php

declare(strict_types=1);

namespace LmcUser\Factory\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Service\User;
use Psr\Container\ContainerInterface;

class UserFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $service = new User();
        $service->setServiceManager($container);

        return $service;
    }

    /**
     * Create service
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, null);
    }
}
