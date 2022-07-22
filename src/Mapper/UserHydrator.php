<?php

declare(strict_types=1);

namespace LmcUser\Mapper;

use Laminas\Hydrator\ClassMethodsHydrator;
use LmcUser\Entity\UserInterface as UserEntityInterface;

class UserHydrator extends ClassMethodsHydrator
{
    /**
     * Extract values from an object
     *
     * @param UserEntityInterface $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object): array
    {
        if (! $object instanceof UserEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of LmcUser\Entity\UserInterface');
        }

        $data = parent::extract($object);
        if ($data['id'] !== null) {
            $data = $this->mapField('id', 'user_id', $data);
        } else {
            unset($data['id']);
        }

        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  UserEntityInterface $object
     * @return UserInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (! $object instanceof UserEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of LmcUser\Entity\UserInterface');
        }

        $data = $this->mapField('user_id', 'id', $data);

        return parent::hydrate($data, $object);
    }

    protected function mapField(string $keyFrom, string $keyTo, array $array): array
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);

        return $array;
    }
}
