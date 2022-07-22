<?php

declare(strict_types=1);

namespace LmcUser\Mapper;

use Closure;
use Exception;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorInterface;
use LmcUser\Db\Adapter\MasterSlaveAdapterInterface;
use LmcUser\Entity\UserInterface as UserEntityInterface;
use LmcUser\EventManager\EventProvider;

use function is_object;

abstract class AbstractDbMapper extends EventProvider
{
    /** @var Adapter */
    protected $dbAdapter;

    /** @var Adapter */
    protected $dbSlaveAdapter;

    /** @var HydratorInterface */
    protected $hydrator;

    /** @var UserEntityInterface */
    protected $entityPrototype;

    /** @var HydratingResultSet */
    protected $resultSetPrototype;

    /** @var Select */
    protected $selectPrototype;

    /** @var Sql */
    private $sql;

    /** @var Sql */
    private $slaveSql;

    /** @var string */
    protected $tableName;

    /** @var boolean */
    private $isInitialized = false;

    /**
     * Performs some basic initialization setup and checks before running a query
     *
     * @throws Exception
     */
    protected function initialize(): void
    {
        if ($this->isInitialized) {
            return;
        }
        if (! $this->dbAdapter instanceof Adapter) {
            throw new Exception('No db adapter present');
        }
        if (! $this->hydrator instanceof HydratorInterface) {
            $this->hydrator = new ClassMethodsHydrator();
        }
        if (! is_object($this->entityPrototype)) {
            throw new Exception('No entity prototype set');
        }

        $this->isInitialized = true;
    }

    /**
     * @param string|null $table
     * @return Select
     */
    protected function getSelect($table = null)
    {
        $this->initialize();

        return $this->getSlaveSql()->select($table ?: $this->getTableName());
    }

    /**
     * @return HydratingResultSet
     * @throws Exception
     */
    protected function select(
        Select $select,
        ?UserEntityInterface $entityPrototype = null,
        ?HydratorInterface $hydrator = null
    ) {
        $this->initialize();
        $stmt      = $this->getSlaveSql()->prepareStatementForSqlObject($select);
        $resultSet = new HydratingResultSet(
            $hydrator ?: $this->getHydrator(),
            $entityPrototype ?: $this->getEntityPrototype()
        );
        $resultSet->initialize($stmt->execute());
        return $resultSet;
    }

    /**
     * @param string|TableIdentifier|null $tableName
     * @return ResultInterface
     */
    protected function insert(UserEntityInterface $entity, $tableName = null, ?HydratorInterface $hydrator = null)
    {
        $this->initialize();
        $tableName = $tableName ?: $this->tableName;
        $sql       = $this->getSql()->setTable($tableName);
        $insert    = $sql->insert();
        $rowData   = $this->entityToArray($entity, $hydrator);
        $insert->values($rowData);
        $statement = $sql->prepareStatementForSqlObject($insert);
        return $statement->execute();
    }

    /**
     * @param string|array|Closure $where
     * @param string|TableIdentifier|null $tableName
     * @return ResultInterface
     */
    protected function update(
        UserEntityInterface $entity,
        $where,
        $tableName = null,
        ?HydratorInterface $hydrator = null
    ) {
        $this->initialize();
        $tableName = $tableName ?: $this->tableName;
        $sql       = $this->getSql()->setTable($tableName);
        $update    = $sql->update();
        $rowData   = $this->entityToArray($entity, $hydrator);
        $update->set($rowData)
            ->where($where);
        $statement = $sql->prepareStatementForSqlObject($update);
        return $statement->execute();
    }

    /**
     * @param string|array|Closure $where
     * @param string|TableIdentifier|null $tableName
     * @return ResultInterface
     */
    protected function delete($where, $tableName = null)
    {
        $tableName = $tableName ?: $this->tableName;
        $sql       = $this->getSql()->setTable($tableName);
        $delete    = $sql->delete();
        $delete->where($where);
        $statement = $sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return UserEntityInterface
     */
    public function getEntityPrototype()
    {
        return $this->entityPrototype;
    }

    /**
     * @return AbstractDbMapper
     */
    public function setEntityPrototype(UserEntityInterface $entityPrototype): static
    {
        $this->entityPrototype    = $entityPrototype;
        $this->resultSetPrototype = null;
        return $this;
    }

    /**
     * @return Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @return AbstractDbMapper
     */
    public function setDbAdapter(Adapter $dbAdapter): static
    {
        $this->dbAdapter = $dbAdapter;
        if ($dbAdapter instanceof MasterSlaveAdapterInterface) {
            $this->setDbSlaveAdapter($dbAdapter->getSlaveAdapter());
        }
        return $this;
    }

    /**
     * @return Adapter
     */
    public function getDbSlaveAdapter()
    {
        return $this->dbSlaveAdapter ?: $this->dbAdapter;
    }

    /**
     * @return AbstractDbMapper
     */
    public function setDbSlaveAdapter(Adapter $dbSlaveAdapter): static
    {
        $this->dbSlaveAdapter = $dbSlaveAdapter;
        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (! $this->hydrator) {
            $this->hydrator = new ClassMethodsHydrator(false);
        }
        return $this->hydrator;
    }

    /**
     * @return AbstractDbMapper
     */
    public function setHydrator(HydratorInterface $hydrator): static
    {
        $this->hydrator           = $hydrator;
        $this->resultSetPrototype = null;
        return $this;
    }

    /**
     * @return Sql
     */
    protected function getSql()
    {
        if (! $this->sql instanceof Sql) {
            $this->sql = new Sql($this->getDbAdapter());
        }
        return $this->sql;
    }

    /**
     * @return AbstractDbMapper
     */
    protected function setSql(Sql $sql): static
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * @return Sql
     */
    protected function getSlaveSql()
    {
        if (! $this->slaveSql instanceof Sql) {
            $this->slaveSql = new Sql($this->getDbSlaveAdapter());
        }
        return $this->slaveSql;
    }

    /**
     * @return AbstractDbMapper
     */
    protected function setSlaveSql(Sql $sql): static
    {
        $this->slaveSql = $sql;
        return $this;
    }

    /**
     * Uses the hydrator to convert the entity to an array.
     *
     * Use this method to ensure that you're working with an array.
     *
     * @return array
     */
    protected function entityToArray(UserEntityInterface $entity, ?HydratorInterface $hydrator = null)
    {
        if (! $hydrator) {
            $hydrator = $this->getHydrator();
        }

        return $hydrator->extract($entity);
    }
}
