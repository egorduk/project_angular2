<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Exception\InvalidFormException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as RestAnnotations;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GalleryController extends FOSRestController
{
    /**
     * Get user galleries.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user galleries",
     *   output = "Acme\ServerBundle\Entity\PictureGallery",
     *   statusCodes = {
     *     Response::HTTP_OK = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when the galleries are not found"
     *   }
     * )
     *
     * @RestAnnotations\Get("/galleries/users", name="get_user_galleries", options = { "method_prefix" = false })
     *
     * @return Response
     *
     * @throws NotFoundHttpException when galleries do not exist
     */
    public function getGalleriesByUserAction()
    {
        if (!($galleries = $this->get('rest.gallery.helper')->getByUser($this->getUser()))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view([
            'galleries' => $galleries,
        ]);

        return $this->handleView($view);
    }

    /**
     * Get gallery by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get gallery",
     *   output = "Acme\ServerBundle\Entity\PictureGallery",
     *   statusCodes = {
     *     Response::HTTP_OK = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when the galleries are not found"
     *   }
     * )
     *
     * @RestAnnotations\Get("/galleries/{galleryId}", requirements = { "galleryId" = "\d+" }, name="get_gallery_by_id", options = { "method_prefix" = false })
     *
     * @param int $galleryId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when gallery does not exist
     */
    public function getGalleryByIdAction($galleryId)
    {
        if (!($gallery = $this->get('rest.gallery.helper')->get($galleryId))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view(['gallery' => $gallery]);

        return $this->handleView($view);
    }

    /**
     * Create a new gallery.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a new gallery",
     *   input = "Acme\ServerBundle\Form\GalleryType",
     *   statusCodes = {
     *     Response::HTTP_CREATED = "Returned when successful",
     *     Response::HTTP_BAD_REQUEST = "Returned when errors"
     *   }
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postGalleryAction(Request $request)
    {
        try {
            $gallery = $this->get('rest.gallery.helper')->post(
                $request->request->all()
            );

            $routeOptions = [
                'galleryId' => $gallery->getId(),
                '_format' => $request->get('_format'),
            ];

            $view = $this->routeRedirectView('api_1_get_gallery_by_id', $routeOptions);
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Delete existing gallery.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete existing gallery",
     *   statusCodes = {
     *     Response::HTTP_NO_CONTENT = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when errors"
     *   }
     * )
     *
     * @param int $galleryId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when picture does not exist
     */
    public function deleteGalleryAction($galleryId)
    {
        if (!($gallery = $this->get('rest.gallery.helper')->get($galleryId))) {
            throw new NotFoundHttpException();
        }

        $this->get('rest.gallery.helper')
            ->deletePictureFromGallery($gallery, $this->getUser())
            ->delete($gallery);

        $view = $this->view(null, Response::HTTP_NO_CONTENT);

        return $this->handleView($view);
    }
}
