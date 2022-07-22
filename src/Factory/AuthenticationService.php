<?php

declare(strict_types=1);

namespace LmcUser\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Authentication\Adapter\AdapterChain;
use LmcUser\Authentication\Storage\Db;
use Psr\Container\ContainerInterface;

class AuthenticationService implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        return new \Laminas\Authentication\AuthenticationService(
            $serviceLocator->get(Db::class),
            $serviceLocator->get(AdapterChain::class)
        );
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
