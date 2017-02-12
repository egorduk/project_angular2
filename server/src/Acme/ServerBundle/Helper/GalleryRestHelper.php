<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\GalleryHasPicture;
use Acme\ServerBundle\Entity\PictureGallery;
use Acme\ServerBundle\Entity\User;
use Acme\ServerBundle\Exception\InvalidFormException;
use Acme\ServerBundle\Form\GalleryType;
use Acme\ServerBundle\Form\GhpType;
use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;

class GalleryRestHelper implements RestHelperInterface
{
    private $em;
    private $entityClass;
    private $ghpEntityClass;
    private $repository;
    private $ghpRepository;
    private $formFactory;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, $entityClass, $ghpEntityClass)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->ghpEntityClass = $ghpEntityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
        $this->ghpRepository = $this->em->getRepository($this->ghpEntityClass);
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
     * @param array $parameters
     *
     * @return RestEntityInterface
     */
    public function getOneGhpBy(array $parameters)
    {
        return $this->ghpRepository->findOneBy($parameters);
    }

    /**
     * @param RestEntityInterface $user
     *
     * @return RestEntityInterface[]
     */
    public function getByUser(RestEntityInterface $user)
    {
        return $this->em->createQueryBuilder()
            ->select('pg.name, pg.id as galleryId, COUNT(p.id) as cntPictures, 
            GROUP_CONCAT(p.filename) as pictures, GROUP_CONCAT(p.id) as pictureIds')
            ->from('AcmeServerBundle:GalleryHasPicture', 'ghp')
            ->leftJoin('AcmeServerBundle:PictureGallery', 'pg', 'WITH', 'pg = ghp.gallery')
            ->leftJoin('AcmeServerBundle:Picture', 'p', 'WITH', 'p = ghp.picture')
            ->where('ghp.user = :user')
            ->groupBy('pg.id')
            ->orderBy('pg.name')
            ->setParameter('user', $user)
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
        return $this->repository->findBy([], null, $limit, $offset);
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
     * @param PictureGallery $obj
     * @param array          $parameters
     * @param string         $method
     *
     * @return RestEntityInterface
     *
     * @throws InvalidFormException
     */
    private function processForm(PictureGallery $obj, array $parameters, $method = 'PUT')
    {
        $form = $this->formFactory->create(new GalleryType(), $obj, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $gallery = $form->getData();

            $this->repository->save($gallery, true);

            return $gallery;
        }

        throw new InvalidFormException(
            'Invalid submitted data: '.(string) $form->getErrors(true, false),
            $form
        );
    }

    /**
     * Add picture to gallery.
     *
     * @param array $parameters
     *
     * @return RestEntityInterface
     */
    public function postToGallery(array $parameters)
    {
        $entity = $this->createGhpEntity();

        return $this->processAddToGalleryForm($entity, $parameters, 'POST');
    }

    /**
     * @param GalleryHasPicture $obj
     * @param array             $parameters
     * @param string            $method
     *
     * @return RestEntityInterface
     *
     * @throws InvalidFormException
     */
    private function processAddToGalleryForm(GalleryHasPicture $obj, array $parameters, $method = 'PUT')
    {
        $form = $this->formFactory->create(new GhpType(), $obj, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $obj = $form->getData();
            $obj->setUser($parameters['user']);

            $this->ghpRepository->save($obj, true);

            return $obj;
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

    private function createGhpEntity()
    {
        return new $this->ghpEntityClass();
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

    /**
     * @param RestEntityInterface $obj
     *
     * @return bool
     */
    public function deleteGhp(RestEntityInterface $obj)
    {
        return $this->ghpRepository->remove($obj, true);
    }

    /**
     * @param RestEntityInterface $gallery
     * @param User                $user
     *
     * @return $this
     */
    public function deletePictureFromGallery(RestEntityInterface $gallery, User $user)
    {
        $galleries = $this->ghpRepository->findBy(['gallery' => $gallery, 'user' => $user]);

        foreach ($galleries as $gallery) {
            $this->ghpRepository->remove($gallery);
        }

        return $this;
    }
}
