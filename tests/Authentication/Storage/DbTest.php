<?php

namespace LmcUserTest\Authentication\Storage;

use LmcUser\Authentication\Storage\Db;

class DbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The object to be tested.
     *
     * @var Db
     */
    protected $db;

    /**
     * Mock of Storage.
     *
     * @var storage
     */
    protected $storage;

    /**
     * Mock of Mapper.
     *
     * @var mapper
     */
    protected $mapper;

    public function setUp()
    {
        $db = new Db;
        $this->db = $db;

        $this->storage = $this->getMock('Laminas\Authentication\Storage\Session');
        $this->mapper = $this->getMock('LmcUser\Mapper\User');
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::isEmpty
     */
    public function testIsEmpty()
    {
        $this->storage->expects($this->once())
                      ->method('isEmpty')
                      ->will($this->returnValue(true));

        $this->db->setStorage($this->storage);

        $this->assertTrue($this->db->isEmpty());
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::read
     */
    public function testReadWithResolvedEntitySet()
    {
        $reflectionClass = new \ReflectionClass('LmcUser\Authentication\Storage\Db');
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->db, 'zfcUser');

        $this->assertSame('zfcUser', $this->db->read());
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityIntUserFound()
    {
        $this->storage->expects($this->once())
                      ->method('read')
                      ->will($this->returnValue(1));

        $this->db->setStorage($this->storage);

        $user = $this->getMock('LmcUser\Entity\User');
        $user->setUsername('zfcUser');

        $this->mapper->expects($this->once())
                     ->method('findById')
                     ->with(1)
                     ->will($this->returnValue($user));

        $this->db->setMapper($this->mapper);

        $result = $this->db->read();

        $this->assertSame($user, $result);
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityIntUserNotFound()
    {
        $this->storage->expects($this->once())
                      ->method('read')
                      ->will($this->returnValue(1));

        $this->db->setStorage($this->storage);

        $this->mapper->expects($this->once())
                     ->method('findById')
                     ->with(1)
                     ->will($this->returnValue(false));

        $this->db->setMapper($this->mapper);

        $result = $this->db->read();

        $this->assertNull($result);
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityObject()
    {
        $user = $this->getMock('LmcUser\Entity\User');
        $user->setUsername('zfcUser');

        $this->storage->expects($this->once())
                      ->method('read')
                      ->will($this->returnValue($user));

        $this->db->setStorage($this->storage);

        $result = $this->db->read();

        $this->assertSame($user, $result);
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::write
     */
    public function testWrite()
    {
        $reflectionClass = new \ReflectionClass('LmcUser\Authentication\Storage\Db');
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);

        $this->storage->expects($this->once())
                      ->method('write')
                      ->with('zfcUser');

        $this->db->setStorage($this->storage);

        $this->db->write('zfcUser');

        $this->assertNull($reflectionProperty->getValue($this->db));
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::clear
     */
    public function testClear()
    {
        $reflectionClass = new \ReflectionClass('LmcUser\Authentication\Storage\Db');
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);

        $this->storage->expects($this->once())
            ->method('clear');

        $this->db->setStorage($this->storage);

        $this->db->clear();

        $this->assertNull($reflectionProperty->getValue($this->db));
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::getMapper
     */
    public function testGetMapperWithNoMapperSet()
    {
        $sm = $this->getMock('Laminas\ServiceManager\ServiceManager');
        $sm->expects($this->once())
           ->method('get')
           ->with('lmcuser_user_mapper')
           ->will($this->returnValue($this->mapper));

        $this->db->setServiceManager($sm);

        $this->assertInstanceOf('LmcUser\Mapper\UserInterface', $this->db->getMapper());
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::setMapper
     * @covers LmcUser\Authentication\Storage\Db::getMapper
     */
    public function testSetGetMapper()
    {
        $mapper = new \LmcUser\Mapper\User;
        $mapper->setTableName('zfcUser');

        $this->db->setMapper($mapper);

        $this->assertInstanceOf('LmcUser\Mapper\User', $this->db->getMapper());
        $this->assertSame('zfcUser', $this->db->getMapper()->getTableName());
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::setServiceManager
     * @covers LmcUser\Authentication\Storage\Db::getServiceManager
     */
    public function testSetGetServicemanager()
    {
        $sm = $this->getMock('Laminas\ServiceManager\ServiceManager');

        $this->db->setServiceManager($sm);

        $this->assertInstanceOf('Laminas\ServiceManager\ServiceLocatorInterface', $this->db->getServiceManager());
        $this->assertSame($sm, $this->db->getServiceManager());
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::getStorage
     * @covers LmcUser\Authentication\Storage\Db::setStorage
     */
    public function testGetStorageWithoutStorageSet()
    {
        $this->assertInstanceOf('Laminas\Authentication\Storage\Session', $this->db->getStorage());
    }

    /**
     * @covers LmcUser\Authentication\Storage\Db::getStorage
     * @covers LmcUser\Authentication\Storage\Db::setStorage
     */
    public function testSetGetStorage()
    {
        $storage = new \Laminas\Authentication\Storage\Session('LmcUserStorage');
        $this->db->setStorage($storage);

        $this->assertInstanceOf('Laminas\Authentication\Storage\Session', $this->db->getStorage());
    }
}
