<?php

declare(strict_types=1);

namespace LmcUser\Factory\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\View;
use Psr\Container\ContainerInterface;

class LmcUserDisplayName implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $viewHelper = new View\Helper\LmcUserDisplayName();
        $viewHelper->setAuthService($container->get('lmcuser_auth_service'));

        return $viewHelper;
    }
}
