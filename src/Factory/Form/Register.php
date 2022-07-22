<?php

declare(strict_types=1);

namespace LmcUser\Factory\Form;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Form;
use LmcUser\Validator;
use Psr\Container\ContainerInterface;

class Register implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $options = $container->get('lmcuser_module_options');
        $form    = new Form\Register(null, $options);

        //$form->setCaptchaElement($sm->get('lmcuser_captcha_element'));
        $form->setHydrator($container->get('lmcuser_register_form_hydrator'));
        $form->setInputFilter(new Form\RegisterFilter(
            new Validator\NoRecordExists([
                'mapper' => $container->get('lmcuser_user_mapper'),
                'key'    => 'email',
            ]),
            new Validator\NoRecordExists([
                'mapper' => $container->get('lmcuser_user_mapper'),
                'key'    => 'username',
            ]),
            $options
        ));

        return $form;
    }
}
