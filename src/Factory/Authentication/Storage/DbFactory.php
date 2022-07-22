<?php

declare(strict_types=1);

namespace LmcUser\Factory\Authentication\Storage;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Authentication\Storage\Db;
use Psr\Container\ContainerInterface;

class DbFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $db = new Db();
        $db->setServiceManager($container);

        return $db;
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
