<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\PictureLike;
use Acme\ServerBundle\Exception\InvalidFormException;
use Acme\ServerBundle\Form\LikeType;
use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;

class LikeRestHelper implements RestHelperInterface
{
    private $em;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, $entityClass)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * @param int $id
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
     * @return RestEntityInterface[]
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
        //return $this->processForm($picture, $parameters, 'PUT');
    }

    /**
     * @param RestEntityInterface $obj
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

        return $this->processForm($entity, $parameters, 'POST');
    }

    /**
     * Process the form.
     *
     * @param PictureLike $obj
     * @param array       $parameters
     * @param string      $method
     *
     * @return RestEntityInterface
     *
     * @throws InvalidFormException
     */
    private function processForm(PictureLike $obj, array $parameters, $method = 'PUT')
    {
        $form = $this->formFactory->create(new LikeType(), $obj, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $like = $form->getData();
            $like->setUser($parameters['user']);

            $this->repository->save($like, true);

            return $like;
        }

        throw new InvalidFormException(
            'Invalid submitted data: '.(string) $form->getErrors(true, false),
            $form
        );
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
