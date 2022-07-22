<?php

declare(strict_types=1);

namespace LmcUser\Factory\Form;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Form;
use LmcUser\Validator;
use Psr\Container\ContainerInterface;

class ChangeEmail implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $options = $container->get('lmcuser_module_options');
        $form    = new Form\ChangeEmail(null, $options);

        $form->setInputFilter(new Form\ChangeEmailFilter(
            $options,
            new Validator\NoRecordExists([
                'mapper' => $container->get('lmcuser_user_mapper'),
                'key'    => 'email',
            ])
        ));

        return $form;
    }
}
