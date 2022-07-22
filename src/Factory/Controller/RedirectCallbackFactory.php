<?php

declare(strict_types=1);

namespace LmcUser\Factory\Controller;

use Laminas\Mvc\Application;
use Laminas\Router\RouteInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Controller\RedirectCallback;
use LmcUser\Options\ModuleOptions;
use Psr\Container\ContainerInterface;

class RedirectCallbackFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var RouteInterface $router */
        $router = $container->get('Router');

        /** @var Application $application */
        $application = $container->get('Application');

        /** @var ModuleOptions $options */
        $options = $container->get('lmcuser_module_options');

        return new RedirectCallback($application, $router, $options);
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
