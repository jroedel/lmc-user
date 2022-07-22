<?php

declare(strict_types=1);

namespace LmcUser\Db\Adapter;

use Laminas\Db\Adapter\Adapter;

interface MasterSlaveAdapterInterface
{
    /**
     * @return Adapter
     */
    public function getSlaveAdapter();
}
