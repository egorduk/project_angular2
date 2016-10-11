<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PictureController extends FOSRestController
{
    public function indexAction()
    {
        return $this->render('AcmeServerBundle:Default:index.html.twig');
    }

    /**
     * Get single picture
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a picture for a given id",
     *   output = "Acme\ServerBundle\Entity\Picture",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the product is not found"
     *   }
     * )
     *
     * @param int     $id      the picture id
     *
     * @return Picture
     *
     * @throws NotFoundHttpException when picture not exist
     */
    public function getPictureAction($id)
    {
        $picture = $this->getDoctrine()
            ->getRepository('AcmeServerBundle:Picture')
            ->find($id);

        if (!$picture) {
            throw new NotFoundHttpException(sprintf('The picture with id = \'%s\' was not found.', $id));
        } else {
            $view = $this->view($picture);

            return $this->handleView($view);
        }

    }

    /**
     * Get all pictures
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets all pictures",
     *   output = "Acme\ServerBundle\Entity\Picture",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the products are not found"
     *   }
     * )
     *
     * @return Picture[]
     *
     * @throws NotFoundHttpException when pictures not exist
     */
    public function getPicturesAction()
    {
        $pictures = $this->getDoctrine()
            ->getRepository('AcmeServerBundle:Picture')
            ->findAll();

        if (!count($pictures)) {
            throw new NotFoundHttpException(sprintf('The pictures were not found.'));
        } else {
            $view = $this->view($pictures);

            return $this->handleView($view);
        }
    }
}
