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
     * @param array $params
     *
     * @return RestEntityInterface
     */
    public function getOneBy(array $params)
    {
        return $this->repository->findOneBy($params);
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
     * Edit a picture.
     *
     * @param RestEntityInterface $picture
     * @param array               $parameters
     *
     * @return RestEntityInterface
     */
    public function put(RestEntityInterface $obj, array $parameters)
    {
        //return $this->processForm($picture, $parameters, 'PUT');
    }

    /**
     * Partially update a picture.
     *
     * @param RestEntityInterface $picture
     * @param array               $parameters
     *
     * @return RestEntityInterface
     */
    public function patch(RestEntityInterface $obj, array $parameters)
    {
        //return $this->processForm($picture, $parameters, 'PATCH');
    }

    /**
     * @param array $parameters
     *
     * @return RestEntityInterface
     */
    public function post(array $parameters)
    {
        $entity = $this->createEntity();
        $entity->setUser($parameters['user']);
        $entity->setFriend($parameters['friend']);

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
