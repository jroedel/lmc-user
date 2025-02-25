<?php

declare(strict_types=1);

namespace LmcUser\Options;

use Laminas\Stdlib\AbstractOptions;
use LmcUser\Authentication\Adapter\Db;
use LmcUser\Entity\User;

class ModuleOptions extends AbstractOptions implements
    UserControllerOptionsInterface,
    UserServiceOptionsInterface
{
    /**
     * Turn off strict options mode
     * @var bool $__strictMode__
     */
    protected $__strictMode__ = false;

    /** @var bool */
    protected $useRedirectParameterIfPresent = true;

    /** @var string */
    protected $loginRedirectRoute = 'lmcuser';

    /** @var string */
    protected $logoutRedirectRoute = 'lmcuser/login';

    /** @var int */
    protected $loginFormTimeout = 300;

    /** @var int */
    protected $userFormTimeout = 300;

    /** @var bool */
    protected $loginAfterRegistration = true;

    /** @var int */
    protected $enableUserState = false;

    /** @var int */
    protected $defaultUserState = 1;

    /** @var array */
    protected $allowedLoginStates = [null, 1];

    /** @var array */
    protected $authAdapters = [100 => Db::class];

    /** @var array */
    protected $authIdentityFields = ['email'];

    /** @var string */
    protected $userEntityClass = User::class;

    /** @var string */
    protected $userLoginWidgetViewTemplate = 'lmc-user/user/login.phtml';

    /** @var bool */
    protected $enableRegistration = true;

    /** @var bool */
    protected $enableUsername = false;

    /** @var bool */
    protected $enableDisplayName = false;

    /** @var bool */
    protected $useRegistrationFormCaptcha = false;

    /** @var int */
    protected $passwordCost = 14;

    /** @var string */

    protected $tableName = 'user';

    /** @var array */
    protected $formCaptchaOptions = [
        'class'   => 'figlet',
        'options' => [
            'wordLen'    => 5,
            'expiration' => 300,
            'timeout'    => 300,
        ],
    ];

    /**
     * set login redirect route
     *
     * @param string $loginRedirectRoute
     * @return ModuleOptions
     */
    public function setLoginRedirectRoute($loginRedirectRoute): static
    {
        $this->loginRedirectRoute = $loginRedirectRoute;
        return $this;
    }

    /**
     * get login redirect route
     *
     * @return string
     */
    public function getLoginRedirectRoute()
    {
        return $this->loginRedirectRoute;
    }

    /**
     * set logout redirect route
     *
     * @param string $logoutRedirectRoute
     * @return ModuleOptions
     */
    public function setLogoutRedirectRoute($logoutRedirectRoute): static
    {
        $this->logoutRedirectRoute = $logoutRedirectRoute;
        return $this;
    }

    /**
     * get logout redirect route
     *
     * @return string
     */
    public function getLogoutRedirectRoute()
    {
        return $this->logoutRedirectRoute;
    }

    /**
     * set use redirect param if present
     *
     * @param bool $useRedirectParameterIfPresent
     * @return ModuleOptions
     */
    public function setUseRedirectParameterIfPresent($useRedirectParameterIfPresent): static
    {
        $this->useRedirectParameterIfPresent = $useRedirectParameterIfPresent;
        return $this;
    }

    /**
     * get use redirect param if present
     *
     * @return bool
     */
    public function getUseRedirectParameterIfPresent()
    {
        return $this->useRedirectParameterIfPresent;
    }

    /**
     * set the view template for the user login widget
     *
     * @param string $userLoginWidgetViewTemplate
     * @return ModuleOptions
     */
    public function setUserLoginWidgetViewTemplate($userLoginWidgetViewTemplate): static
    {
        $this->userLoginWidgetViewTemplate = $userLoginWidgetViewTemplate;
        return $this;
    }

    /**
     * get the view template for the user login widget
     *
     * @return string
     */
    public function getUserLoginWidgetViewTemplate()
    {
        return $this->userLoginWidgetViewTemplate;
    }

    /**
     * set enable user registration
     *
     * @param bool $enableRegistration
     * @return ModuleOptions
     */
    public function setEnableRegistration($enableRegistration): static
    {
        $this->enableRegistration = $enableRegistration;
        return $this;
    }

    /**
     * get enable user registration
     *
     * @return bool
     */
    public function getEnableRegistration()
    {
        return $this->enableRegistration;
    }

    /**
     * set login form timeout
     *
     * @param int $loginFormTimeout
     * @return ModuleOptions
     */
    public function setLoginFormTimeout($loginFormTimeout): static
    {
        $this->loginFormTimeout = $loginFormTimeout;
        return $this;
    }

    /**
     * get login form timeout in seconds
     *
     * @return int
     */
    public function getLoginFormTimeout()
    {
        return $this->loginFormTimeout;
    }

    /**
     * set user form timeout in seconds
     *
     * @param int $userFormTimeout
     * @return ModuleOptions
     */
    public function setUserFormTimeout($userFormTimeout): static
    {
        $this->userFormTimeout = $userFormTimeout;
        return $this;
    }

    /**
     * get user form timeout in seconds
     *
     * @return int
     */
    public function getUserFormTimeout()
    {
        return $this->userFormTimeout;
    }

    /**
     * set login after registration
     *
     * @param bool $loginAfterRegistration
     * @return ModuleOptions
     */
    public function setLoginAfterRegistration($loginAfterRegistration): static
    {
        $this->loginAfterRegistration = $loginAfterRegistration;
        return $this;
    }

    /**
     * get login after registration
     *
     * @return bool
     */
    public function getLoginAfterRegistration()
    {
        return $this->loginAfterRegistration;
    }

    /**
     * get user state usage for registration/login process
     *
     * @return int
     */
    public function getEnableUserState()
    {
        return $this->enableUserState;
    }

    /**
     * set user state usage for registration/login process
     *
     * @param boolean $flag
     * @return ModuleOptions
     */
    public function setEnableUserState($flag): static
    {
        $this->enableUserState = $flag;
        return $this;
    }

    /**
     * get default user state on registration
     *
     * @return int
     */
    public function getDefaultUserState()
    {
        return $this->defaultUserState;
    }

    /**
     * set default user state on registration
     *
     * @param int $state
     * @return ModuleOptions
     */
    public function setDefaultUserState($state): static
    {
        $this->defaultUserState = $state;
        return $this;
    }

    /**
     * get list of states to allow user login
     *
     * @return array
     */
    public function getAllowedLoginStates()
    {
        return $this->allowedLoginStates;
    }

    /**
     * set list of states to allow user login
     *
     * @param array $states
     * @return ModuleOptions
     */
    public function setAllowedLoginStates(array $states): static
    {
        $this->allowedLoginStates = $states;
        return $this;
    }

    /**
     * set auth adapters
     *
     * @param array $authAdapters
     * @return ModuleOptions
     */
    public function setAuthAdapters($authAdapters): static
    {
        $this->authAdapters = $authAdapters;
        return $this;
    }

    /**
     * get auth adapters
     *
     * @return array
     */
    public function getAuthAdapters()
    {
        return $this->authAdapters;
    }

    /**
     * set auth identity fields
     *
     * @param array $authIdentityFields
     * @return ModuleOptions
     */
    public function setAuthIdentityFields($authIdentityFields)
    {
        $this->authIdentityFields = $authIdentityFields;
        return $this;
    }

    /**
     * get auth identity fields
     *
     * @return array
     */
    public function getAuthIdentityFields()
    {
        return $this->authIdentityFields;
    }

    /**
     * set enable username
     *
     * @param bool $flag
     * @return ModuleOptions
     */
    public function setEnableUsername($flag)
    {
        $this->enableUsername = (bool) $flag;
        return $this;
    }

    /**
     * get enable username
     *
     * @return bool
     */
    public function getEnableUsername()
    {
        return $this->enableUsername;
    }

    /**
     * set enable display name
     *
     * @param bool $flag
     * @return ModuleOptions
     */
    public function setEnableDisplayName($flag)
    {
        $this->enableDisplayName = (bool) $flag;
        return $this;
    }

    /**
     * get enable display name
     *
     * @return bool
     */
    public function getEnableDisplayName()
    {
        return $this->enableDisplayName;
    }

    /**
     * set use a captcha in registration form
     *
     * @param bool $useRegistrationFormCaptcha
     * @return ModuleOptions
     */
    public function setUseRegistrationFormCaptcha($useRegistrationFormCaptcha)
    {
        $this->useRegistrationFormCaptcha = $useRegistrationFormCaptcha;
        return $this;
    }

    /**
     * get use a captcha in registration form
     *
     * @return bool
     */
    public function getUseRegistrationFormCaptcha()
    {
        return $this->useRegistrationFormCaptcha;
    }

    /**
     * set user entity class name
     *
     * @param string $userEntityClass
     * @return ModuleOptions
     */
    public function setUserEntityClass($userEntityClass)
    {
        $this->userEntityClass = $userEntityClass;
        return $this;
    }

    /**
     * get user entity class name
     *
     * @return string
     */
    public function getUserEntityClass()
    {
        return $this->userEntityClass;
    }

    /**
     * set password cost
     *
     * @param int $passwordCost
     * @return ModuleOptions
     */
    public function setPasswordCost($passwordCost)
    {
        $this->passwordCost = $passwordCost;
        return $this;
    }

    /**
     * get password cost
     *
     * @return int
     */
    public function getPasswordCost()
    {
        return $this->passwordCost;
    }

    /**
     * set user table name
     *
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * get user table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * set form CAPTCHA options
     *
     * @param array $formCaptchaOptions
     * @return ModuleOptions
     */
    public function setFormCaptchaOptions($formCaptchaOptions)
    {
        $this->formCaptchaOptions = $formCaptchaOptions;
        return $this;
    }

    /**
     * get form CAPTCHA options
     *
     * @return array
     */
    public function getFormCaptchaOptions()
    {
        return $this->formCaptchaOptions;
    }
}
