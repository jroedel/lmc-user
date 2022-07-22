<?php

namespace LmcUserTest\Authentication\Adapter;

use LmcUserTest\Authentication\Adapter\TestAsset\AbstractAdapterExtension;

class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The object to be tested.
     *
     * @var AbstractAdapterExtension
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new AbstractAdapterExtension();
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AbstractAdapter::getStorage
     */
    public function testGetStorageWithoutStorageSet()
    {
        $this->assertInstanceOf('Laminas\Authentication\Storage\Session', $this->adapter->getStorage());
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AbstractAdapter::getStorage
     * @covers \LmcUser\Authentication\Adapter\AbstractAdapter::setStorage
     */
    public function testSetGetStorage()
    {
        $storage = new \Laminas\Authentication\Storage\Session('LmcUser');
        $storage->write('zfcUser');
        $this->adapter->setStorage($storage);

        $this->assertInstanceOf('Laminas\Authentication\Storage\Session', $this->adapter->getStorage());
        $this->assertSame('zfcUser', $this->adapter->getStorage()->read());
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AbstractAdapter::isSatisfied
     */
    public function testIsSatisfied()
    {
        $this->assertFalse($this->adapter->isSatisfied());
    }

    public function testSetSatisfied()
    {
        $result = $this->adapter->setSatisfied();
        $this->assertInstanceOf('LmcUser\Authentication\Adapter\AbstractAdapter', $result);
        $this->assertTrue($this->adapter->isSatisfied());

        $result = $this->adapter->setSatisfied(false);
        $this->assertInstanceOf('LmcUser\Authentication\Adapter\AbstractAdapter', $result);
        $this->assertFalse($this->adapter->isSatisfied());
    }
}
