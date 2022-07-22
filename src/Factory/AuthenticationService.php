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
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new \Laminas\Authentication\AuthenticationService(
            $container->get(Db::class),
            $container->get(AdapterChain::class)
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
