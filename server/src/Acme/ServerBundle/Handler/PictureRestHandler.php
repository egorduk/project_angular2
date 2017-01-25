<?php

namespace Acme\ServerBundle\Handler;

use Acme\ServerBundle\Entity\Picture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
//use Acme\BlogBundle\Model\PageInterface;
use Acme\ServerBundle\Form\PictureType;
use Acme\ServerBundle\Exception\InvalidFormException;

class PictureRestHandler implements RestHandlerInterface
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
     * Get a picture
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
     * Get a list of pictures
     *
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
     * Create a new picture
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
     * Edit a Page.
     *
     * @param Picture $picture
     * @param array         $parameters
     *
     * @return Picture
     */
    public function put(Picture $picture, array $parameters)
    {
        return $this->processForm($picture, $parameters, 'PUT');
    }

    /**
     * Partially update a picture
     *
     * @param Picture $picture
     * @param array         $parameters
     *
     * @return Picture
     */
    public function patch(Picture $picture, array $parameters)
    {
        return $this->processForm($picture, $parameters, 'PATCH');
    }

    /**
     * Processes the form
     *
     * @param Picture       $picture
     * @param array         $parameters
     * @param String        $method
     *
     * @return Picture
     *
     * @throws \Acme\ServerBundle\Exception\InvalidFormException
     */
    private function processForm(Picture $picture, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new PictureType(), $picture, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $picture = $form->getData();
            $picture->setDateUpload(new \DateTime());
            $picture->setIsShowHost(true);

            $this->repository->save($picture, true);

            return $picture;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createPicture()
    {
        return new $this->entityClass();
    }

    public function delete($picture)
    {
        return $this->repository->remove($picture, true);
    }
}