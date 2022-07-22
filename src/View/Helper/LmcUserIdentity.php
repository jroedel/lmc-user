<?php

declare(strict_types=1);

namespace LmcUser\View\Helper;

use Laminas\Authentication\AuthenticationService;
use Laminas\View\Helper\AbstractHelper;
use LmcUser\Entity\UserInterface;

class LmcUserIdentity extends AbstractHelper
{
    /** @var AuthenticationService */
    protected $authService;

    /**
     * __invoke
     *
     * @access public
     * @return UserInterface
     */
    public function __invoke()
    {
        if ($this->getAuthService()->hasIdentity()) {
            return $this->getAuthService()->getIdentity();
        } else {
            return false;
        }
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
     *
     * @return LmcUserIdentity
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }
}
