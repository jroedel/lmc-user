<?php

declare(strict_types=1);

namespace LmcUser\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator\ValidatorInterface;
use LmcUser\Options\AuthenticationOptionsInterface;

class ChangeEmailFilter extends InputFilter
{
    public function __construct(AuthenticationOptionsInterface $options, protected ValidatorInterface $emailValidator)
    {
        $this->emailValidator = $emailValidator;

        $identityParams = [
            'name'       => 'identity',
            'required'   => true,
            'validators' => [],
        ];

        $identityFields = $options->getAuthIdentityFields();
        if ($identityFields === ['email']) {
            $validators                     = ['name' => 'EmailAddress'];
            $identityParams['validators'][] = $validators;
        }

        $this->add($identityParams);

        $this->add([
            'name'       => 'newIdentity',
            'required'   => true,
            'validators' => [
                [
                    'name' => 'EmailAddress',
                ],
                $this->emailValidator,
            ],
        ]);

        $this->add([
            'name'       => 'newIdentityVerify',
            'required'   => true,
            'validators' => [
                [
                    'name'    => 'identical',
                    'options' => [
                        'token' => 'newIdentity',
                    ],
                ],
            ],
        ]);
    }

    public function getEmailValidator(): ValidatorInterface
    {
        return $this->emailValidator;
    }

    public function setEmailValidator(ValidatorInterface $emailValidator): static
    {
        $this->emailValidator = $emailValidator;
        return $this;
    }
}
