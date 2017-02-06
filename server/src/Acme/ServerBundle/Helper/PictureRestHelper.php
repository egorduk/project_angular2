<?php

namespace Acme\ServerBundle\Helper;

use Acme\ServerBundle\Entity\Picture;
use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Acme\ServerBundle\Form\PictureType;
use Acme\ServerBundle\Exception\InvalidFormException;

class PictureRestHelper implements RestHelperInterface
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, FormFactoryInterface $formFactory, $entityClass)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
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
     * Get a list of pictures.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return Picture[]
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
     * @return Picture
     */
    public function post(array $parameters)
    {
        $picture = $this->createPicture();

        return $this->processForm($picture, $parameters, 'POST');
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
     * @param RestEntityInterface $picture
     * @param array               $parameters
     *
     * @return Picture
     */
    public function patch(RestEntityInterface $picture, array $parameters)
    {
        return $this->processForm($picture, $parameters, 'PATCH');
    }

    /**
     * Process the form.
     *
     * @param RestEntityInterface $picture
     * @param array               $parameters
     * @param string              $method
     *
     * @return Picture
     *
     * @throws \Acme\ServerBundle\Exception\InvalidFormException
     */
    private function processForm(RestEntityInterface $picture, array $parameters, $method = 'PUT')
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
