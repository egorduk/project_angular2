<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Entity\Picture;
use Acme\ServerBundle\Exception\InvalidFormException;
use Acme\ServerBundle\Form\PictureType;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as RestAnnotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PictureController extends FOSRestController
{
    /**
     * Get a picture by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get a picture by id",
     *   output = "Acme\ServerBundle\Entity\Picture",
     *   statusCodes = {
     *     Response::HTTP_OK = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when the picture is not found"
     *   }
     * )
     *
     * @RestAnnotations\Get("/pictures/{pictureId}", requirements = { "pictureId" = "\d+" }, name="get_picture_by_id", options = { "method_prefix" = false })
     *
     * @param int $pictureId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when picture does not exist
     */
    public function getPictureAction($pictureId)
    {
        if (!($picture = $this->get('rest.picture.helper')->get($pictureId))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view([
            'picture' => $picture,
        ]);

        return $this->handleView($view);
    }

    /**
     * Get gallery pictures.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get gallery pictures",
     *   output = "Acme\ServerBundle\Entity\Picture",
     *   statusCodes = {
     *     Response::HTTP_OK = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when the pictures are not found"
     *   }
     * )
     *
     * @RestAnnotations\Get("/pictures/galleries/{galleryId}", requirements = { "galleryId" = "\d+" }, name="get_gallery_pictures", options = { "method_prefix" = false })
     *
     * @param int $galleryId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when pictures do not exist
     */
    public function getGalleryPicturesAction($galleryId)
    {
        if (!($gallery = $this->get('rest.gallery.helper')->get($galleryId))) {
            throw new NotFoundHttpException();
        }

        if (!($picture = $this->get('rest.picture.helper')->getByGallery($gallery))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view(['picture' => $picture]);

        return $this->handleView($view);
    }

    /**
     * Get all pictures.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all pictures",
     *   output = "Acme\ServerBundle\Entity\Picture",
     *   statusCodes = {
     *     Response::HTTP_OK = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when the products are not found"
     *   }
     * )
     *
     * @RestAnnotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pictures")
     * @RestAnnotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many pictures to return")
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     *
     * @throws NotFoundHttpException when pictures not exist
     */
    public function getPicturesAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        if ($pictures = $this->get('rest.picture.helper')->all($limit, $offset)) {
            $view = $this->view(['pictures' => $pictures]);

            return $this->handleView($view);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Create a new picture.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a new picture",
     *   input = "Acme\ServerBundle\Form\PictureType",
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
    public function postPictureAction(Request $request)
    {
        try {
            $this->get('rest.picture.helper')->post(
                array_merge($request->request->all(), [
                    'user' => $this->getUser(),
                    'file' => $request->files->get('file'),
                ])
            );

            $view = $this->view(null, Response::HTTP_CREATED);
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Update existing picture from the submitted data or create a new picture.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update existing picture or create it",
     *   input = "Acme\ServerBundle\Form\PictureType",
     *   statusCodes = {
     *     Response::HTTP_CREATED = "Returned when the picture is created",
     *     Response::HTTP_NO_CONTENT = "Returned when successful",
     *     Response::HTTP_BAD_REQUEST = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function putPictureAction(Request $request, $id)
    {
        try {
            $picture = $this->get('rest.picture.helper')->get($id);

            if (!is_null($picture)) {
                $statusCode = Response::HTTP_NO_CONTENT;

                $picture = $this->get('rest.picture.helper')->put(
                    $picture,
                    $request->request->all()
                );
            } else {
                $statusCode = Response::HTTP_CREATED;

                $picture = $this->get('rest.picture.helper')->post(
                    $request->request->all()
                );
            }

            $routeOptions = [
                'id' => $picture->getId(),
                '_format' => $request->get('_format'),
            ];

            $view = View::createRouteRedirect('api_1_get_picture', $routeOptions, $statusCode);
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Delete existing picture.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete existing picture",
     *   statusCodes = {
     *     Response::HTTP_NO_CONTENT = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when errors"
     *   }
     * )
     *
     * @param int $pictureId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when picture does not exist
     */
    public function deletePictureAction($pictureId)
    {
        if (!$picture = $this->get('rest.picture.helper')->get($pictureId)) {
            throw new NotFoundHttpException();
        }

        $this->get('rest.picture.helper')->delete($picture);

        $view = $this->view([], Response::HTTP_NO_CONTENT);

        return $this->handleView($view);
    }

    /**
     * Get friends pictures.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get friends pictures",
     *   statusCodes = {
     *     Response::HTTP_OK = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when the pictures are not found"
     *   }
     * )
     *
     * @RestAnnotations\Get("/pictures/friends", name="get_friends_pictures", options={ "method_prefix" = false })
     *
     * @return Response
     *
     * @throws NotFoundHttpException when pictures are not found
     */
    public function getFriendPicturesAction()
    {
        if (!$pictures = $this->get('rest.picture.helper')->getFriendsPictures($this->getUser())) {
            throw new NotFoundHttpException();
        }

        $view = $this->view(['pictures' => $pictures], Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Update picture name.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update picture name",
     *   input = "Acme\ServerBundle\Form\PictureType",
     *   statusCodes = {
     *     Response::HTTP_NO_CONTENT = "Returned when successful",
     *     Response::HTTP_BAD_REQUEST = "Returned when errors",
     *     Response::HTTP_NOT_FOUND = "Returned when picture does not found"
     *   }
     * )
     *
     * @RestAnnotations\Patch("/pictures/{pictureId}/name", requirements = { "pictureId" = "\d+" }, options={ "method_prefix" = false })
     *
     * @param Request $request
     * @param int     $pictureId
     *
     * @return Response
     */
    public function patchPictureNameAction(Request $request, $pictureId)
    {
        try {
            if ($picture = $this->get('rest.picture.helper')->getOneBy(['id' => $pictureId, 'user' => $this->getUser()])) {
                $picture = $this->get('rest.picture.helper')->patch($picture, $request->request->all());

                $routeOptions = [
                    'pictureId' => $picture->getId(),
                    '_format' => $request->get('_format'),
                ];

                $view = View::createRouteRedirect('api_1_get_picture_by_id', $routeOptions, Response::HTTP_NO_CONTENT);
            } else {
                $view = $this->view(null, Response::HTTP_NOT_FOUND);
            }
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Update picture status.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update picture status",
     *   input = "Acme\ServerBundle\Form\PictureType",
     *   statusCodes = {
     *     Response::HTTP_NO_CONTENT = "Returned when successful",
     *     Response::HTTP_BAD_REQUEST = "Returned when errors",
     *     Response::HTTP_NOT_FOUND = "Returned when picture does not found"
     *   }
     * )
     *
     * @RestAnnotations\Patch("/pictures/{pictureId}/status", requirements = { "pictureId" = "\d+" }, options={ "method_prefix" = false })
     *
     * @param Request $request
     * @param int     $pictureId
     *
     * @return Response
     */
    public function patchPictureStatusAction(Request $request, $pictureId)
    {
        try {
            if ($picture = $this->get('rest.picture.helper')->getOneBy(['id' => $pictureId, 'user' => $this->getUser()])) {
                $picture = $this->get('rest.picture.helper')->patch($picture, $request->request->all());

                $routeOptions = [
                    'pictureId' => $picture->getId(),
                    '_format' => $request->get('_format'),
                ];

                $view = View::createRouteRedirect('api_1_get_picture_by_id', $routeOptions, Response::HTTP_NO_CONTENT);
            } else {
                $view = $this->view(null, Response::HTTP_NOT_FOUND);
            }
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Delete existing picture from gallery.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete existing picture from gallery",
     *   statusCodes = {
     *     Response::HTTP_NO_CONTENT = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when errors"
     *   }
     * )
     *
     * @RestAnnotations\Delete("/pictures/{pictureId}/galleries/{galleryId}", requirements = { "pictureId" = "\d+", "galleryId" = "\d+" }, options = { "method_prefix" = false })
     *
     * @param int $pictureId
     * @param int $galleryId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when picture does not exist
     */
    public function deletePictureFromGalleryAction($pictureId, $galleryId)
    {
        if (!($picture = $this->get('rest.picture.helper')->get($pictureId))) {
            throw new NotFoundHttpException();
        }

        if (!($gallery = $this->get('rest.gallery.helper')->get($galleryId))) {
            throw new NotFoundHttpException();
        }

        if (!($ghp = $this->get('rest.gallery.helper')->getOneGhpBy([
            'picture' => $picture,
            'gallery' => $gallery,
            'user' => $this->getUser(),
        ]))) {
            throw new NotFoundHttpException();
        }

        $this->get('rest.gallery.helper')->deleteGhp($ghp);

        $view = $this->view(null, Response::HTTP_NO_CONTENT);

        return $this->handleView($view);
    }

    /**
     * Add a picture to gallery.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Add a picture to gallery",
     *   input = "Acme\ServerBundle\Form\GhpType",
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
    public function postPicturesGalleriesAction(Request $request)
    {
        try {
            $picture = $this->get('rest.gallery.helper')->postToGallery(
                array_merge($request->request->all(), ['user' => $this->getUser()])
            );

            $routeOptions = [
                'pictureId' => $picture->getId(),
                '_format' => $request->get('_format'),
            ];

            $view = $this->routeRedirectView('api_1_get_picture_by_id', $routeOptions);
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
