<?php

declare(strict_types=1);

namespace LmcUser;

use Laminas\Db\Adapter\Adapter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use LmcUser\Authentication\Adapter\AdapterChain;
use LmcUser\Authentication\Adapter\AdapterChainServiceFactory;
use LmcUser\Factory\Authentication\Adapter\DbFactory;
use LmcUser\Factory\AuthenticationService;
use LmcUser\Factory\Controller\Plugin\LmcUserAuthentication;
use LmcUser\Factory\Controller\RedirectCallbackFactory;
use LmcUser\Factory\Controller\UserControllerFactory;
use LmcUser\Factory\Form\ChangeEmail;
use LmcUser\Factory\Form\ChangePassword;
use LmcUser\Factory\Form\Login;
use LmcUser\Factory\Form\Register;
use LmcUser\Factory\Mapper\User;
use LmcUser\Factory\Options\ModuleOptions;
use LmcUser\Factory\Service\UserFactory;
use LmcUser\Factory\UserHydrator;
use LmcUser\Factory\View\Helper\LmcUserDisplayName;
use LmcUser\Factory\View\Helper\LmcUserIdentity;
use LmcUser\Factory\View\Helper\LmcUserLoginWidget;

class Module implements
    ControllerProviderInterface,
    ControllerPluginProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function getConfig($env = null)
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'lmcUserAuthentication' => LmcUserAuthentication::class,
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                'lmcuser' => UserControllerFactory::class,
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'lmcUserDisplayName' => LmcUserDisplayName::class,
                'lmcUserIdentity'    => LmcUserIdentity::class,
                'lmcUserLoginWidget' => LmcUserLoginWidget::class,
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'aliases'    => [
                'lmcuser_laminas_db_adapter'   => Adapter::class,
                'lmcuser_user_mapper'          => Mapper\User::class,
                'lmcuser_login_form'           => Form\Login::class,
                'lmcuser_register_form'        => Form\Register::class,
                'lmcuser_change_password_form' => Form\ChangePassword::class,
                'lmcuser_change_email_form'    => Form\ChangeEmail::class,
                'lmcuser_user_service'         => Service\User::class,
            ],
            'invokables' => [
                'lmcuser_register_form_hydrator' => ClassMethodsHydrator::class,
            ],
            'factories'  => [
                'lmcuser_redirect_callback' => RedirectCallbackFactory::class,
                'lmcuser_module_options'    => ModuleOptions::class,
                AdapterChain::class         => AdapterChainServiceFactory::class,

                // We alias this one because it's LmcUser's instance of
                // Laminas\Authentication\AuthenticationService. We don't want to
                // hog the FQCN service alias for a Zend\* class.
                'lmcuser_auth_service'           => AuthenticationService::class,
                'lmcuser_user_hydrator'          => UserHydrator::class,
                Mapper\User::class               => User::class,
                Form\Login::class                => Login::class,
                Form\Register::class             => Register::class,
                Form\ChangePassword::class       => ChangePassword::class,
                Form\ChangeEmail::class          => ChangeEmail::class,
                Authentication\Adapter\Db::class => DbFactory::class,
                Authentication\Storage\Db::class => Factory\Authentication\Storage\DbFactory::class,
                Service\User::class              => UserFactory::class,
            ],
        ];
    }
}
