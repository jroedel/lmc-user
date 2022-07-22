<?php

declare(strict_types=1);

namespace LmcUser\Factory\Options;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Options;
use Psr\Container\ContainerInterface;

class ModuleOptions implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->get('Config');

        return new Options\ModuleOptions($config['lmcuser'] ?? []);
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
