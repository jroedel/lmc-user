<?php

declare(strict_types=1);

namespace LmcUser\Service;

use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Form\Form;
use Laminas\Hydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\ServiceManager;
use LmcUser\Entity\UserInterface;
use LmcUser\EventManager\EventProvider;
use LmcUser\Mapper\UserInterface as UserMapperInterface;
use LmcUser\Options\UserServiceOptionsInterface;
use Psr\Container\ContainerInterface;

class User extends EventProvider
{
    /** @var UserMapperInterface */
    protected $userMapper;

    /** @var AuthenticationService */
    protected $authService;

    /** @var Form */
    protected $loginForm;

    /** @var Form */
    protected $registerForm;

    /** @var Form */
    protected $changePasswordForm;

    /** @var ServiceManager */
    protected $serviceManager;

    /** @var UserServiceOptionsInterface */
    protected $options;

    /** @var Hydrator\ClassMethodsHydrator */
    protected $formHydrator;

    /**
     * createFromForm
     *
     * @param array $data
     * @return false|UserInterface
     * @throws Exception\InvalidArgumentException
     */
    public function register(array $data)
    {
        $class = $this->getOptions()->getUserEntityClass();
        $user  = new $class();
        $form  = $this->getRegisterForm();
        $form->setHydrator($this->getFormHydrator());
        $form->bind($user);
        $form->setData($data);
        if (! $form->isValid()) {
            return false;
        }

        $user = $form->getData();
        /** @var UserInterface $user */

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());
        $user->setPassword($bcrypt->create($user->getPassword()));

        if ($this->getOptions()->getEnableUsername()) {
            $user->setUsername($data['username']);
        }
        if ($this->getOptions()->getEnableDisplayName()) {
            $user->setDisplayName($data['display_name']);
        }

        // If user state is enabled, set the default state value
        if ($this->getOptions()->getEnableUserState()) {
            $user->setState($this->getOptions()->getDefaultUserState());
        }
        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $user, 'form' => $form]);
        $this->getUserMapper()->insert($user);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $user, 'form' => $form]);
        return $user;
    }

    /**
     * change the current users password
     *
     * @param array $data
     * @return boolean
     */
    public function changePassword(array $data)
    {
        $currentUser = $this->getAuthService()->getIdentity();

        $oldPass = $data['credential'];
        $newPass = $data['newCredential'];

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (! $bcrypt->verify($oldPass, $currentUser->getPassword())) {
            return false;
        }

        $pass = $bcrypt->create($newPass);
        $currentUser->setPassword($pass);

        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $currentUser, 'data' => $data]);
        $this->getUserMapper()->update($currentUser);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $currentUser, 'data' => $data]);

        return true;
    }

    /**
     * @return bool
     */
    public function changeEmail(array $data)
    {
        $currentUser = $this->getAuthService()->getIdentity();

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (! $bcrypt->verify($data['credential'], $currentUser->getPassword())) {
            return false;
        }

        $currentUser->setEmail($data['newIdentity']);

        $this->getEventManager()->trigger(__FUNCTION__, $this, ['user' => $currentUser, 'data' => $data]);
        $this->getUserMapper()->update($currentUser);
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, ['user' => $currentUser, 'data' => $data]);

        return true;
    }

    /**
     * getUserMapper
     *
     * @return UserMapperInterface
     */
    public function getUserMapper()
    {
        if (null === $this->userMapper) {
            $this->userMapper = $this->getServiceManager()->get('lmcuser_user_mapper');
        }
        return $this->userMapper;
    }

    /**
     * setUserMapper
     *
     * @return User
     */
    public function setUserMapper(UserMapperInterface $userMapper): static
    {
        $this->userMapper = $userMapper;
        return $this;
    }

    /**
     * getAuthService
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceManager()->get('lmcuser_auth_service');
        }
        return $this->authService;
    }

    /**
     * setAuthenticationService
     *
     * @return User
     */
    public function setAuthService(AuthenticationService $authService): static
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * @return Form
     */
    public function getRegisterForm()
    {
        if (null === $this->registerForm) {
            $this->registerForm = $this->getServiceManager()->get('lmcuser_register_form');
        }
        return $this->registerForm;
    }

    /**
     * @return User
     */
    public function setRegisterForm(Form $registerForm): static
    {
        $this->registerForm = $registerForm;
        return $this;
    }

    /**
     * @return Form
     */
    public function getChangePasswordForm()
    {
        if (null === $this->changePasswordForm) {
            $this->changePasswordForm = $this->getServiceManager()->get('lmcuser_change_password_form');
        }
        return $this->changePasswordForm;
    }

    /**
     * @return User
     */
    public function setChangePasswordForm(Form $changePasswordForm): static
    {
        $this->changePasswordForm = $changePasswordForm;
        return $this;
    }

    /**
     * get service options
     *
     * @return UserServiceOptionsInterface
     */
    public function getOptions()
    {
        if (! $this->options instanceof UserServiceOptionsInterface) {
            $this->setOptions($this->getServiceManager()->get('lmcuser_module_options'));
        }
        return $this->options;
    }

    /**
     * set service options
     */
    public function setOptions(UserServiceOptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @return User
     */
    public function setServiceManager(ContainerInterface $serviceManager): static
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Return the Form Hydrator
     *
     * @return ClassMethodsHydrator
     */
    public function getFormHydrator()
    {
        if (! $this->formHydrator instanceof Hydrator\HydratorInterface) {
            $this->setFormHydrator($this->getServiceManager()->get('lmcuser_register_form_hydrator'));
        }

        return $this->formHydrator;
    }

    /**
     * Set the Form Hydrator to use
     *
     * @return User
     */
    public function setFormHydrator(Hydrator\HydratorInterface $formHydrator): static
    {
        $this->formHydrator = $formHydrator;
        return $this;
    }
}
