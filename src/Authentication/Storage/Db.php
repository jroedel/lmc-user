<?php

declare(strict_types=1);

namespace LmcUser\Authentication\Storage;

use Laminas\Authentication\Exception\InvalidArgumentException;
use Laminas\Authentication\Storage;
use Laminas\Authentication\Storage\StorageInterface;
use Laminas\ServiceManager\ServiceManager;
use LmcUser\Mapper\UserInterface as UserMapper;
use Psr\Container\ContainerInterface;

use function is_int;
use function is_scalar;

class Db implements Storage\StorageInterface
{
    /** @var StorageInterface */
    protected $storage;

    /** @var UserMapper */
    protected $mapper;

    /** @var mixed */
    protected $resolvedIdentity;

    /** @var ServiceManager */
    protected $serviceManager;

    /**
     * Returns true if and only if storage is empty
     *
     * @throws InvalidArgumentException If it is impossible to determine whether
     * storage is empty or not
     * @return boolean
     */
    public function isEmpty()
    {
        if ($this->getStorage()->isEmpty()) {
            return true;
        }
        $identity = $this->getStorage()->read();
        if ($identity === null) {
            $this->clear();
            return true;
        }

        return false;
    }

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws InvalidArgumentException If reading contents from storage is impossible
     * @return mixed
     */
    public function read()
    {
        if (null !== $this->resolvedIdentity) {
            return $this->resolvedIdentity;
        }

        $identity = $this->getStorage()->read();

        if (is_int($identity) || is_scalar($identity)) {
            $identity = $this->getMapper()->findById($identity);
        }

        if ($identity) {
            $this->resolvedIdentity = $identity;
        } else {
            $this->resolvedIdentity = null;
        }

        return $this->resolvedIdentity;
    }

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws InvalidArgumentException If writing $contents to storage is impossible
     * @return void
     */
    public function write($contents)
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->write($contents);
    }

    /**
     * Clears contents from storage
     *
     * @throws InvalidArgumentException If clearing contents from storage is impossible
     * @return void
     */
    public function clear()
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->clear();
    }

    /**
     * getStorage
     *
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Storage\Session());
        }
        return $this->storage;
    }

    /**
     * setStorage
     *
     * @access public
     * @return Db
     */
    public function setStorage(Storage\StorageInterface $storage): static
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * getMapper
     *
     * @return UserMapper
     */
    public function getMapper()
    {
        if (null === $this->mapper) {
            $this->mapper = $this->getServiceManager()->get('lmcuser_user_mapper');
        }
        return $this->mapper;
    }

    /**
     * setMapper
     *
     * @return Db
     */
    public function setMapper(UserMapper $mapper): static
    {
        $this->mapper = $mapper;
        return $this;
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
     * @param ContainerInterface $locator
     * @return void
     */
    public function setServiceManager(ContainerInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}
