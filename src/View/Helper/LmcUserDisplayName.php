<?php

declare(strict_types=1);

namespace LmcUser\View\Helper;

use Laminas\Authentication\AuthenticationService;
use Laminas\View\Helper\AbstractHelper;
use LmcUser\Entity\UserInterface as User;
use LmcUser\Exception\DomainException;

use function strpos;
use function substr;

class LmcUserDisplayName extends AbstractHelper
{
    /** @var AuthenticationService */
    protected $authService;

    /**
     * __invoke
     *
     * @access public
     * @throws DomainException
     * @return String
     */
    public function __invoke(?User $user = null)
    {
        if (null === $user) {
            if ($this->getAuthService()->hasIdentity()) {
                $user = $this->getAuthService()->getIdentity();
                if (! $user instanceof User) {
                    throw new DomainException(
                        '$user is not an instance of User',
                        500
                    );
                }
            } else {
                return false;
            }
        }

        $displayName = $user->getDisplayName();
        if (null === $displayName) {
            $displayName = $user->getUsername();
        }
        // User will always have an email, so we do not have to throw error
        if (null === $displayName) {
            $displayName = $user->getEmail();
            $displayName = substr($displayName, 0, strpos($displayName, '@'));
        }

        return $displayName;
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
     * @return LmcUserDisplayName
     */
    public function setAuthService(AuthenticationService $authService): static
    {
        $this->authService = $authService;
        return $this;
    }
}
