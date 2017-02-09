<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\PictureComment;
use Acme\ServerBundle\Exception\InvalidFormException;
use Acme\ServerBundle\Form\CommentType;
use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;

class CommentRestHelper implements RestHelperInterface
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
     * @param RestEntityInterface $picture
     *
     * @return RestEntityInterface[]
     */
    public function getByPicture(RestEntityInterface $picture)
    {
        return $this->em->createQueryBuilder()
            ->select('DATE_DIFF(CURRENT_DATE(), pc.dateComment) daysAgo, pc.id commentId, pc.comment, u.login userLogin, u.avatar userAvatar, u.id userId')
            ->from('AcmeServerBundle:PictureComment', 'pc')
            ->innerJoin('AcmeServerBundle:User', 'u', 'WITH', 'u = pc.user')
            ->where('pc.picture = :picture')
            ->orderBy('pc.dateComment', 'DESC')
            ->setParameter('picture', $picture)
            ->getQuery()
            ->getResult();
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
     * @param PictureComment $obj
     * @param array          $parameters
     * @param string         $method
     *
     * @return RestEntityInterface
     *
     * @throws \Acme\ServerBundle\Exception\InvalidFormException
     */
    private function processForm(PictureComment $obj, array $parameters, $method = 'PUT')
    {
        $form = $this->formFactory->create(new CommentType(), $obj, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $comment = $form->getData();
            $comment->setUser($parameters['user']);

            $this->repository->save($comment, true);

            return $comment;
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
