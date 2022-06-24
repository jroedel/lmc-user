<?php

namespace LmcUserTest\Entity;

use LmcUser\Entity\User as Entity;

class UserTest extends \PHPUnit_Framework_TestCase
{
    protected $user;

    public function setUp()
    {
        $user = new Entity;
        $this->user = $user;
    }

    /**
     * @covers LmcUser\Entity\User::setId
     * @covers LmcUser\Entity\User::getId
     */
    public function testSetGetId()
    {
        $this->user->setId(1);
        $this->assertEquals(1, $this->user->getId());
    }

    /**
     * @covers LmcUser\Entity\User::setUsername
     * @covers LmcUser\Entity\User::getUsername
     */
    public function testSetGetUsername()
    {
        $this->user->setUsername('zfcUser');
        $this->assertEquals('zfcUser', $this->user->getUsername());
    }

    /**
     * @covers LmcUser\Entity\User::setDisplayName
     * @covers LmcUser\Entity\User::getDisplayName
     */
    public function testSetGetDisplayName()
    {
        $this->user->setDisplayName('Lmc User');
        $this->assertEquals('Lmc User', $this->user->getDisplayName());
    }

    /**
     * @covers LmcUser\Entity\User::setEmail
     * @covers LmcUser\Entity\User::getEmail
     */
    public function testSetGetEmail()
    {
        $this->user->setEmail('zfcUser@zfcUser.com');
        $this->assertEquals('zfcUser@zfcUser.com', $this->user->getEmail());
    }

    /**
     * @covers LmcUser\Entity\User::setPassword
     * @covers LmcUser\Entity\User::getPassword
     */
    public function testSetGetPassword()
    {
        $this->user->setPassword('zfcUser');
        $this->assertEquals('zfcUser', $this->user->getPassword());
    }

    /**
     * @covers LmcUser\Entity\User::setState
     * @covers LmcUser\Entity\User::getState
     */
    public function testSetGetState()
    {
        $this->user->setState(1);
        $this->assertEquals(1, $this->user->getState());
    }
}
