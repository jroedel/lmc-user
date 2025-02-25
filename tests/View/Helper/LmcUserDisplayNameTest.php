<?php

namespace LmcUserTest\View\Helper;

use LmcUser\View\Helper\LmcUserDisplayName as ViewHelper;

class LmcUserDisplayNameTest extends \PHPUnit_Framework_TestCase
{
    protected $helper;

    protected $authService;

    protected $user;

    public function setUp()
    {
        $helper = new ViewHelper;
        $this->helper = $helper;

        $authService = $this->getMock('Laminas\Authentication\AuthenticationService');
        $this->authService = $authService;

        $user = $this->getMock('LmcUser\Entity\User');
        $this->user = $user;

        $helper->setAuthService($authService);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserAndNotLoggedIn()
    {
        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(false));

        $result = $this->helper->__invoke(null);

        $this->assertFalse($result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     * @expectedException LmcUser\Exception\DomainException
     */
    public function testInvokeWithoutUserButLoggedInWithWrongUserObject()
    {
        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(true));
        $this->authService->expects($this->once())
                          ->method('getIdentity')
                          ->will($this->returnValue(new \StdClass));

        $this->helper->__invoke(null);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithDisplayName()
    {
        $this->user->expects($this->once())
                   ->method('getDisplayName')
                   ->will($this->returnValue('lmcUser'));

        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(true));
        $this->authService->expects($this->once())
                          ->method('getIdentity')
                          ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('lmcUser', $result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithoutDisplayNameButWithUsername()
    {
        $this->user->expects($this->once())
                   ->method('getDisplayName')
                   ->will($this->returnValue(null));
        $this->user->expects($this->once())
                   ->method('getUsername')
                   ->will($this->returnValue('lmcUser'));

        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(true));
        $this->authService->expects($this->once())
                          ->method('getIdentity')
                          ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('lmcUser', $result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithoutDisplayNameAndWithOutUsernameButWithEmail()
    {
        $this->user->expects($this->once())
                   ->method('getDisplayName')
                   ->will($this->returnValue(null));
        $this->user->expects($this->once())
                   ->method('getUsername')
                   ->will($this->returnValue(null));
        $this->user->expects($this->once())
                   ->method('getEmail')
                   ->will($this->returnValue('lmcUser@lmcUser.com'));

        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(true));
        $this->authService->expects($this->once())
                          ->method('getIdentity')
                          ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('lmcUser', $result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::setAuthService
     * @covers LmcUser\View\Helper\LmcUserDisplayName::getAuthService
     */
    public function testSetGetAuthService()
    {
        // We set the authservice in setUp, so we dont have to set it again
        $this->assertSame($this->authService, $this->helper->getAuthService());
    }
}
