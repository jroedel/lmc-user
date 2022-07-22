<?php

declare(strict_types=1);

namespace LmcUser\Factory\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\View;
use Psr\Container\ContainerInterface;

class LmcUserIdentity implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $viewHelper = new View\Helper\LmcUserIdentity();
        $viewHelper->setAuthService($container->get('lmcuser_auth_service'));

        return $viewHelper;
    }
}
