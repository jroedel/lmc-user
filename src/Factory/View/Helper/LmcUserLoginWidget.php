<?php

declare(strict_types=1);

namespace LmcUser\Factory\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\View;
use Psr\Container\ContainerInterface;

class LmcUserLoginWidget implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $viewHelper = new View\Helper\LmcUserLoginWidget();
        $viewHelper->setViewTemplate($container->get('lmcuser_module_options')->getUserLoginWidgetViewTemplate());
        $viewHelper->setLoginForm($container->get('lmcuser_login_form'));

        return $viewHelper;
    }
}
