<?php

declare(strict_types=1);

namespace LmcUser\Authentication\Adapter;

use Laminas\Authentication\Storage\StorageInterface;

interface ChainableAdapter
{
    /**
     * @return bool
     */
    public function authenticate(AdapterChainEvent $e);

    /**
     * @return StorageInterface
     */
    public function getStorage();
}
