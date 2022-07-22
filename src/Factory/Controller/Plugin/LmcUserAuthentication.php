<?php

declare(strict_types=1);

namespace LmcUser\Factory\Controller\Plugin;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Authentication\Adapter\AdapterChain;
use LmcUser\Controller;
use Psr\Container\ContainerInterface;

class LmcUserAuthentication implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $authService = $serviceLocator->get('lmcuser_auth_service');
        $authAdapter = $serviceLocator->get(AdapterChain::class);

        $controllerPlugin = new Controller\Plugin\LmcUserAuthentication();
        $controllerPlugin->setAuthService($authService);
        $controllerPlugin->setAuthAdapter($authAdapter);

        return $controllerPlugin;
    }

    /**
     * Create service
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $serviceLocator = $serviceManager->getServiceLocator();

        return $this->__invoke($serviceLocator, null);
    }
}
