<?php

declare(strict_types=1);

namespace LmcUser\Factory\Controller\Plugin;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Authentication\Adapter\AdapterChain;
use LmcUser\Controller;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class LmcUserAuthentication implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Controller\Plugin\LmcUserAuthentication
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $authService = $container->get('lmcuser_auth_service');
        $authAdapter = $container->get(AdapterChain::class);

        $controllerPlugin = new Controller\Plugin\LmcUserAuthentication();
        $controllerPlugin->setAuthService($authService);
        $controllerPlugin->setAuthAdapter($authAdapter);

        return $controllerPlugin;
    }
}
