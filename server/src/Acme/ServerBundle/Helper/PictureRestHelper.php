<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\GalleryHasPicture;
use Acme\ServerBundle\Entity\Picture;
use Acme\ServerBundle\Entity\PictureGallery;
use Acme\ServerBundle\Entity\User;
use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\ServerBundle\Form\PictureType;
use Acme\ServerBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureRestHelper implements RestHelperInterface
{
    private $em;
    private $formFactory;
    private $imgThumbHelper;
    private $entityClass;
    private $parameters;
    private $repository;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, ImageThumbnailHelper $imgThumbHelper, $entityClass, $parameters)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
        $this->imgThumbHelper = $imgThumbHelper;
        $this->parameters = $parameters;
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
     * @param array $parameters
     *
     * @return RestEntityInterface
     */
    public function getOneBy(array $parameters)
    {
        return $this->repository->findOneBy($parameters);
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
     * Get gallery pictures.
     *
     * @param PictureGallery $gallery
     *
     * @return Picture[]
     */
    public function getByGallery(PictureGallery $gallery)
    {
        return $this->em->createQueryBuilder()
            ->select('p.id, p.name, p.dateUpload, p.isShowHost, p.resizeHeight, p.resizeWidth, p.filename')
            ->from('AcmeServerBundle:GalleryHasPicture', 'ghp')
            ->innerJoin('AcmeServerBundle:Picture', 'p', 'WITH', 'p = ghp.picture')
            ->where('ghp.gallery = :gallery')
            ->setParameter('gallery', $gallery)
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
        return $this->repository->findBy([], null, $limit, $offset);
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
     * @param RestEntityInterface $obj
     * @param array               $parameters
     *
     * @return Picture
     */
    public function put(RestEntityInterface $obj, array $parameters)
    {
        return $this->processForm($obj, $parameters, 'PUT');
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
     * @param RestEntityInterface $obj
     * @param array               $parameters
     * @param string              $method
     *
     * @return Picture
     *
     * @throws InvalidFormException
     */
    private function processForm(RestEntityInterface $obj, array $parameters, $method = 'PUT')
    {
        $form = $this->formFactory->create(new PictureType(), $obj, ['method' => $method]);
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $picture = $form->getData();

            if ($method === 'POST') {
                /** @var UploadedFile $file **/
                $file = $parameters['file'];

                $fileName = $file->getClientOriginalName();

                $uploadedFile = $file->move(
                    $this->parameters['original'],
                    $fileName
                );

                $sourceImagePath = $uploadedFile->getLinkTarget();
                $sourceImageMimeType = $uploadedFile->getMimeType();

                $isSuccess = $this->imgThumbHelper->createImageThumbnail($sourceImagePath, $sourceImageMimeType);

                if ($isSuccess) {
                    $picture->setFilename($fileName);
                    $picture->setResizeWidthHeight(
                        $this->imgThumbHelper->getThumbnailWidth(),
                        $this->imgThumbHelper->getThumbnailHeight()
                    );
                    $picture->setDateUploadIsShowHost();
                    $picture->setUser($parameters['user']);
                } else {
                    throw new FileException();
                }
            } elseif ($method === 'PATCH') {
                $picture->setNameIsShowHost($parameters['name'], $parameters['isShowHost']);
            }

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
     * @param RestEntityInterface $obj
     *
     * @return bool
     */
    public function delete(RestEntityInterface $obj)
    {
        return $this->repository->remove($obj, true);
    }

    public function deleteFromGallery(RestEntityInterface $obj)
    {
    }
}
