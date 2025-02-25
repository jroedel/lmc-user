<?php

declare(strict_types=1);

namespace LmcUser\Controller\Plugin;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Authentication\Adapter\AdapterChain as AuthAdapter;

class LmcUserAuthentication extends AbstractPlugin
{
    /** @var AuthAdapter */
    protected $authAdapter;

    /** @var AuthenticationService */
    protected $authService;

    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    /**
     * Proxy convenience method
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return $this->getAuthService()->hasIdentity();
    }

    /**
     * Proxy convenience method
     *
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }

    /**
     * Get authAdapter.
     *
     * @return LmcUserAuthentication
     */
    public function getAuthAdapter()
    {
        return $this->authAdapter;
    }

    /**
     * Set authAdapter.
     */
    public function setAuthAdapter(AuthAdapter $authAdapter): static
    {
        $this->authAdapter = $authAdapter;
        return $this;
    }

    /**
     * Get authService.
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * Set authService.
     */
    public function setAuthService(AuthenticationService $authService): static
    {
        $this->authService = $authService;
        return $this;
    }
}
