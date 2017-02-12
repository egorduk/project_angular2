<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\EntityManager;

class FriendRestHelper implements RestHelperInterface
{
    private $em;
    private $entityClass;
    private $repository;

    public function __construct(EntityManager $em, $entityClass)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
    }

    /**
     * @param mixed $id
     *
     * @return RestEntityInterface
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $parameters
     *
     * @return RestEntityInterface
     */
    public function getOneBy(array $parameters)
    {
        return $this->repository->findOneBy($parameters);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * @param RestEntityInterface $obj
     * @param array               $parameters
     *
     * @return RestEntityInterface
     */
    public function put(RestEntityInterface $obj, array $parameters)
    {
        return $obj;
    }

    /**
     * @param RestEntityInterface $obj
     * @param array               $parameters
     *
     * @return RestEntityInterface
     */
    public function patch(RestEntityInterface $obj, array $parameters)
    {
        return $obj;
    }

    /**
     * @param array $parameters
     *
     * @return RestEntityInterface
     */
    public function post(array $parameters)
    {
        $entity = $this->createEntity();
        $entity->setUserFriend($parameters['user'], $parameters['friend']);

        return $this->repository->save($entity, true);
    }

    private function createEntity()
    {
        return new $this->entityClass();
    }

    /**
     * @param RestEntityInterface $obj
     *
     * @return bool
     */
    public function delete(RestEntityInterface $obj)
    {
        return $this->repository->remove($obj, true);
    }
}
