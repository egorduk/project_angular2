<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\Picture;
use Acme\ServerBundle\Entity\User;
use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\ServerBundle\Form\PictureType;
use Acme\ServerBundle\Exception\InvalidFormException;

class PictureRestHelper implements RestHelperInterface
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
     * Get a picture.
     *
     * @param mixed $id
     *
     * @return Picture
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get pictures.
     *
     * @param User $user
     *
     * @return Picture[]
     */
    public function getFriendsPictures(User $user)
    {
        return $this->em->createQueryBuilder()
            ->select('p.id AS pictureId, p.filename, p.name, DATE_DIFF(CURRENT_DATE(), p.dateUpload) daysAgo, 
                u.login userLogin, u.avatar userAvatar, u.id userId,
                CASE WHEN (SELECT pl2.id FROM AcmeServerBundle:PictureLike pl2 WHERE pl2.user = f.user AND pl2.picture = p) > 0 THEN true ELSE false AS isLiked,
                (SELECT COUNT(pl1.id) from AcmeServerBundle:PictureLike pl1 where pl1.picture = p) cntLikes,
                GROUP_CONCAT(DISTINCT t.name) tags, GROUP_CONCAT(DISTINCT ghp.id) galleryIds')
            ->from('AcmeServerBundle:Friend', 'f')
            ->innerJoin('AcmeServerBundle:Picture', 'p', 'WITH', 'p.user = f.friend')
            ->innerJoin('AcmeServerBundle:User', 'u', 'WITH', 'u = f.friend')
            ->leftJoin('AcmeServerBundle:PictureLike', 'pl', 'WITH', 'p = pl.picture')
            ->leftJoin('AcmeServerBundle:PictureTag', 'pt', 'WITH', 'p = pt.picture')
            ->leftJoin('AcmeServerBundle:Tag', 't', 'WITH', 't = pt.tag')
            ->leftJoin('AcmeServerBundle:GalleryHasPicture', 'ghp', 'WITH', 'ghp.picture = p')
            ->where('f.user = :user')
            ->groupBy('p.id')
            ->orderBy('p.dateUpload', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get a list of pictures.
     *
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
     * Create a new picture.
     *
     * @param array $parameters
     *
     * @return RestEntityInterface
     */
    public function post(array $parameters)
    {
        $entity = $this->createPicture();

        return $this->processForm($entity, $parameters, 'POST');
    }

    /**
     * Edit a picture.
     *
     * @param RestEntityInterface $picture
     * @param array               $parameters
     *
     * @return Picture
     */
    public function put(RestEntityInterface $picture, array $parameters)
    {
        return $this->processForm($picture, $parameters, 'PUT');
    }

    /**
     * Partially update a picture.
     *
     * @param RestEntityInterface $obj
     * @param array               $parameters
     *
     * @return Picture
     */
    public function patch(RestEntityInterface $obj, array $parameters)
    {
        return $this->processForm($obj, $parameters, 'PATCH');
    }

    /**
     * Process the form.
     *
     * @param RestEntityInterface $obj
     * @param array               $parameters
     * @param string              $method
     *
     * @return Picture
     *
     * @throws \Acme\ServerBundle\Exception\InvalidFormException
     */
    private function processForm(RestEntityInterface $obj, array $parameters, $method = 'PUT')
    {
        $form = $this->formFactory->create(new PictureType(), $picture, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $picture = $form->getData();
            $picture->setDateUploadAndIsShowHost();

            $this->repository->save($picture, true);

            return $picture;
        }

        throw new InvalidFormException(
            'Invalid submitted data: '.(string) $form->getErrors(true, false),
            $form
        );
    }

    private function createPicture()
    {
        return new $this->entityClass();
    }

    /**
     * @param RestEntityInterface $picture
     *
     * @return bool
     */
    public function delete(RestEntityInterface $picture)
    {
        return $this->repository->remove($picture, true);
    }
}
