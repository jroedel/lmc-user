<?php

declare(strict_types=1);

namespace LmcUser\Form;

use Laminas\Form\Element\Captcha;
use LmcUser\Options\RegistrationOptionsInterface;

class Register extends Base
{
    protected $captchaElement;

    /** @var RegistrationOptionsInterface */
    protected $registrationOptions;

    /**
     * @param string|null $name
     */
    public function __construct($name, RegistrationOptionsInterface $options)
    {
        $this->setRegistrationOptions($options);

        parent::__construct($name);

        if ($this->getRegistrationOptions()->getUseRegistrationFormCaptcha()) {
            $this->add([
                'name'    => 'captcha',
                'type'    => Captcha::class,
                'options' => [
                    'label'   => 'Please type the following text',
                    'captcha' => $this->getRegistrationOptions()->getFormCaptchaOptions(),
                ],
            ]);
        }

        $this->remove('userId');
        if (! $this->getRegistrationOptions()->getEnableUsername()) {
            $this->remove('username');
        }
        if (! $this->getRegistrationOptions()->getEnableDisplayName()) {
            $this->remove('display_name');
        }
        if ($this->getRegistrationOptions()->getUseRegistrationFormCaptcha() && $this->captchaElement) {
            $this->add($this->captchaElement, ['name' => 'captcha']);
        }
        $this->get('submit')->setLabel('Register');
    }

    public function setCaptchaElement(Captcha $captchaElement)
    {
        $this->captchaElement = $captchaElement;
    }

    /**
     * Set Registration Options
     *
     * @return Register
     */
    public function setRegistrationOptions(RegistrationOptionsInterface $registrationOptions): static
    {
        $this->registrationOptions = $registrationOptions;
        return $this;
    }

    /**
     * Get Registration Options
     *
     * @return RegistrationOptionsInterface
     */
    public function getRegistrationOptions()
    {
        return $this->registrationOptions;
    }
}
