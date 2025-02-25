<?php

declare(strict_types=1);

namespace LmcUser\Authentication\Adapter;

use Laminas\Authentication\Storage;

abstract class AbstractAdapter implements ChainableAdapter
{
    /** @var Storage\StorageInterface */
    protected $storage;

    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Storage\Session(static::class));
        }

        return $this->storage;
    }

    /**
     * Sets the persistent storage handler
     *
     * @return AbstractAdapter Provides a fluent interface
     */
    public function setStorage(Storage\StorageInterface $storage): static
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Check if this adapter is satisfied or not
     *
     * @return bool
     */
    public function isSatisfied()
    {
        $storage = $this->getStorage()->read();
        return isset($storage['is_satisfied']) && true === $storage['is_satisfied'];
    }

    /**
     * Set if this adapter is satisfied or not
     *
     * @param bool $bool
     * @return AbstractAdapter
     */
    public function setSatisfied($bool = true): static
    {
        $storage                 = $this->getStorage()->read() ?: [];
        $storage['is_satisfied'] = $bool;
        $this->getStorage()->write($storage);
        return $this;
    }
}
