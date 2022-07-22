<?php

declare(strict_types=1);

namespace LmcUser\Validator;

use Exception;
use InvalidArgumentException;
use Laminas\Validator\AbstractValidator;
use LmcUser\Mapper\UserInterface;

use function array_key_exists;

abstract class AbstractRecord extends AbstractValidator
{
    /**
     * Error constants
     */
    public const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    public const ERROR_RECORD_FOUND    = 'recordFound';

    /** @var array Message templates */
    protected $messageTemplates = [
        self::ERROR_NO_RECORD_FOUND => "No record matching the input was found",
        self::ERROR_RECORD_FOUND    => "A record matching the input was found",
    ];

    /** @var UserInterface */
    protected $mapper;

    /** @var string */
    protected $key;

    /**
     * Required options are:
     *  - key     Field to use, 'email' or 'username'
     */
    public function __construct(array $options)
    {
        if (! array_key_exists('key', $options)) {
            throw new InvalidArgumentException('No key provided');
        }

        $this->setKey($options['key']);

        parent::__construct($options);
    }

    /**
     * getMapper
     *
     * @return UserInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * setMapper
     *
     * @return AbstractRecord
     */
    public function setMapper(UserInterface $mapper): static
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Get key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key.
     *
     * @param string $key
     */
    public function setKey($key): static
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Grab the user from the mapper
     *
     * @param string $value
     * @return mixed
     */
    protected function query($value)
    {
        $result = false;

        switch ($this->getKey()) {
            case 'email':
                $result = $this->getMapper()->findByEmail($value);
                break;

            case 'username':
                $result = $this->getMapper()->findByUsername($value);
                break;

            default:
                throw new Exception('Invalid key used in LmcUser validator');
        }

        return $result;
    }
}
