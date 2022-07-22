<?php

declare(strict_types=1);

namespace LmcUser\Factory\Mapper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Mapper;
use LmcUser\Options\ModuleOptions;
use Psr\Container\ContainerInterface;

class User implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        /** @var ModuleOptions $options */
        $options   = $serviceLocator->get('lmcuser_module_options');
        $dbAdapter = $serviceLocator->get('lmcuser_laminas_db_adapter');

        $entityClass = $options->getUserEntityClass();
        $tableName   = $options->getTableName();

        $mapper = new Mapper\User();
        $mapper->setDbAdapter($dbAdapter);
        $mapper->setTableName($tableName);
        $mapper->setEntityPrototype(new $entityClass());
        $mapper->setHydrator(new Mapper\UserHydrator());

        return $mapper;
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
