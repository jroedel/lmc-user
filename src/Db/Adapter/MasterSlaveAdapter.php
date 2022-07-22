<?php

declare(strict_types=1);

namespace LmcBase\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Platform;
use Laminas\Db\ResultSet;
use LmcUser\Db\Adapter\MasterSlaveAdapterInterface;

class MasterSlaveAdapter extends Adapter implements MasterSlaveAdapterInterface
{
    /**
     * slave adapter
     *
     * @var Adapter
     */
    protected $slaveAdapter;
    /**
     * @param DriverInterface|array $driver
     * @param ResultSet\ResultSet $queryResultPrototype
     */
    public function __construct(
        Adapter $slaveAdapter,
        $driver,
        ?Platform\PlatformInterface $platform = null,
        ?ResultSet\ResultSetInterface $queryResultPrototype = null
    ) {
        $this->slaveAdapter = $slaveAdapter;
        parent::__construct($driver, $platform, $queryResultPrototype);
    }

    /**
     * get slave adapter
     *
     * @return Adapter
     */
    public function getSlaveAdapter()
    {
        return $this->slaveAdapter;
    }
}
