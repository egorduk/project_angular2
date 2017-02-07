<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\User;
use Acme\ServerBundle\Form\LoginType;
use Acme\ServerBundle\Form\ProfileType;
use Acme\ServerBundle\Form\RegistrationType;
use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\ServerBundle\Exception\InvalidFormException;

class UserRestHelper implements RestHelperInterface
{
    const UNFOLLOWS_USERS_LIMIT = 3;

    private $em;
    private $entityClass;
    private $repository;
    private $formFactory;
    private $authHelper;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, AuthHelper $authHelper, $entityClass)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->authHelper = $authHelper;
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
        return $this->processRegistrationForm($this->createEntity(), $parameters, 'POST');
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
     * @return RestEntityInterface
     */
    public function patch(RestEntityInterface $user, array $parameters = null)
    {
        if (is_null($parameters)) {
            return $this->repository->save($user, true);
        } else {
            return $this->processProfileForm($user, $parameters, 'PATCH');
        }
    }

    /**
     * Process profile form.
     *
     * @param RestEntityInterface $user
     * @param array               $parameters
     * @param string              $method
     *
     * @return User
     *
     * @throws InvalidFormException
     */
    private function processProfileForm(RestEntityInterface $user, array $parameters = null, $method)
    {
        $form = $this->formFactory->create(new ProfileType(), $user, ['method' => $method]);
        $form->submit($parameters);

        if ($form->isValid()) {
            $user = $form->getData();
            $regInfo = $form->get('regInfo')->getData();

            $user->setEmailLoginPassword($regInfo->getEmail(), $regInfo->getLogin(), md5($regInfo->getPassword()));

            $this->repository->save($user, true);

            return $user;
        }

        throw new InvalidFormException(
            'Invalid submitted data: '.(string) $form->getErrors(true, false),
            $form
        );
    }

    /**
     * Process registration form.
     *
     * @param RestEntityInterface $user
     * @param array               $parameters
     * @param string              $method
     *
     * @return User
     *
     * @throws InvalidFormException
     */
    private function processRegistrationForm(RestEntityInterface $user, array $parameters = null, $method = 'PUT')
    {
        $form = $this->formFactory->create(new RegistrationType(), $user, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $user = $form->getData();
            $user->setPassword(md5($user->getPassword()));

            $this->repository->save($user, true);

            return $user;
        }

        throw new InvalidFormException(
            'Invalid submitted data: '.(string) $form->getErrors(true, false),
            $form
        );
    }

    /**
     * Process the form.
     *
     * @param RestEntityInterface $user
     * @param array               $parameters
     * @param string              $method
     *
     * @return User
     *
     * @throws InvalidFormException
     */
    private function processForm(RestEntityInterface $user, array $parameters = null, $method = 'PUT')
    {
        $form = $this->formFactory->create(new RegistrationType(), $user, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $user = $form->getData();
            $user->setPassword(md5($user->getPassword()));

            $this->repository->save($user, true);

            return $user;
        }

        throw new InvalidFormException(
            'Invalid submitted data: '.(string) $form->getErrors(true, false),
            $form
        );
    }

    /**
     * Process login form.
     *
     * @param array $parameters
     *
     * @return User
     *
     * @throws InvalidFormException
     */
    public function processLoginForm(array $parameters)
    {
        $form = $this->formFactory->create(new LoginType(), $this->createEntity());
        $form->submit($parameters);

        if ($form->isValid()) {
            $userData = $form->getData();

            $user = $this->repository->findOneBy(
                [
                    'email' => $userData->getEmail(),
                    'password' => md5($userData->getPassword()),
                ]
            );

            return $user;
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
     * @param RestEntityInterface $user
     *
     * @return bool
     */
    public function delete(RestEntityInterface $user)
    {
        return $this->repository->remove($user, true);
    }

    /**
     * @param User $user
     *
     * @return User[]
     */
    public function getUnfollowsUsers($user)
    {
        $qb = $this->em->createQueryBuilder();
        $qb1 = $this->em->createQueryBuilder();

        $sub = $qb1->select('f.id')
            ->from('AcmeServerBundle:Friend', 'f')
            ->where('f.user = :user')
            ->setParameter('user', $user);

        return $qb
            ->select('u.id, u.login, u.avatar, GROUP_CONCAT(p.filename) pictures, COUNT(u.id) cntPictures')
            ->from('AcmeServerBundle:User', 'u')
            ->innerJoin('AcmeServerBundle:Picture', 'p', 'WITH', 'p.user = u')
            ->where('f.user = :user')
            ->where(
                $qb->expr()->notIn('u', $sub->getDQL())
            )
            ->andWhere('u != :user')
            ->groupBy('u.id')
            ->orderBy('u.id')
            ->setMaxResults(self::UNFOLLOWS_USERS_LIMIT)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
