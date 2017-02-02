<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\User;
use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\ServerBundle\Exception\InvalidFormException;

class UserRestHelper implements RestHelperInterface
{
    const UNFOLLOWS_USERS_LIMIT = 3;

    private $om;
    private $em;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, $entityClass)
    {
        //$this->om = $om;
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * @param mixed $id
     *
     * @return User
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param array $params
     *
     * @return User
     */
    public function getBy(array $params)
    {
        return $this->repository->findBy($params);
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
     * @param array $parameters
     *
     * @return User
     */
    public function post(array $parameters)
    {
        $picture = $this->createPicture();

        return $this->processForm($picture, $parameters, 'POST');
    }

    /**
     * @param RestEntityInterface $user
     * @param array               $parameters
     *
     * @return User
     */
    public function put(RestEntityInterface $user, array $parameters)
    {
        return $this->processForm($user, $parameters, 'PUT');
    }

    /**
     * @param RestEntityInterface $user
     * @param array               $parameters
     *
     * @return User
     */
    public function patch(RestEntityInterface $user, array $parameters)
    {
        return $this->processForm($user, $parameters, 'PATCH');
    }

    /**
     * Process the form
     *
     * @param RestEntityInterface $picture
     * @param array               $parameters
     * @param String              $method
     *
     * @return User
     *
     * @throws \Acme\ServerBundle\Exception\InvalidFormException
     */
    private function processForm(RestEntityInterface $user, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new PictureType(), $user, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $user = $form->getData();
            //$user->setDateUploadAndIsShowHost();

            $this->repository->save($user, true);

            return $user;
        }

        throw new InvalidFormException(
            'Invalid submitted data: ' . (string)$form->getErrors(true, false),
            $form
        );
    }

    private function createPicture()
    {
        return new $this->entityClass();
    }

    /**
     * @param RestEntityInterface $user
     *
     * @return bool
     */
    public function delete(RestEntityInterface $user)
    {
        return $this->repository->remove($user, true);
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getUnfollowsUsers($userId)
    {
        $query = $this->em->createQuery(
            'select u.id, u.login, u.avatar, GROUP_CONCAT(p.filename) pictures, count(u.id) as cnt_picture from AcmeServerBundle:User u
            inner join AcmeServerBundle:Picture p with p.userId = u.id
            where u.id not in (select f.friendId from AcmeServerBundle:Friend f where f.userId = :userId) and u.id != :userId
            group by u.id
            order by u.id'
        )->setMaxResults(self::UNFOLLOWS_USERS_LIMIT)->setParameter('userId', $userId);

        return $query->getResult();
    }
}