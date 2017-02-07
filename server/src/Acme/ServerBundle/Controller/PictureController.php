<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Entity\Picture;
use Acme\ServerBundle\Exception\InvalidFormException;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as RestAnnotations;
use FOS\RestBundle\Controller\FOSRestController;
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
     *     200 = "Returned when successful",
     *     404 = "Returned when the picture is not found"
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
     * Get all pictures.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all pictures",
     *   output = "Acme\ServerBundle\Entity\Picture",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the products are not found"
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

        $pictures = $this->get('rest.picture.helper')->all($limit, $offset);

        if (count($pictures)) {
            $view = $this->view(array('pictures' => $pictures));

            return $this->handleView($view);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Create a picture.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a new picture",
     *   input = "Acme\ServerBundle\Form\PictureType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when errors"
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
            $picture = $this->get('rest.picture.helper')->post(
                $request->request->all()
            );

            $routeOptions = [
                'id' => $picture->getId(),
                '_format' => $request->get('_format'),
            ];

            $view = View::createRouteRedirect('api_1_get_picture', $routeOptions);
        } catch (InvalidFormException $exception) {
            $view = View::create($exception->getMessage(), Response::HTTP_BAD_REQUEST);
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
     *     201 = "Returned when the picture is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
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
            $picture = $this->getDoctrine()
                ->getRepository('AcmeServerBundle:Picture')
                ->find($id);

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
            $view = View::create($exception->getMessage(), Response::HTTP_BAD_REQUEST);
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
     *     204 = "Returned when successful",
     *     400 = "Returned when errors"
     *   }
     * )
     *
     * @param int $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException when picture does not exist
     */
    public function deletePictureAction($id)
    {
        $picture = $this->getDoctrine()
            ->getRepository('AcmeServerBundle:Picture')
            ->find($id);

        if (!is_null($picture)) {
            $this->get('rest.picture.helper')->delete($picture);

            $view = View::createRouteRedirect('api_1_get_pictures', [], Response::HTTP_NO_CONTENT);

            return $this->handleView($view);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Get friends pictures.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get friends pictures",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the pictures are not found"
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
        if (!($pictures = $this->get('rest.picture.helper')->getFriendsPictures($this->getUser()))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view(['pictures' => $pictures], Response::HTTP_OK);

        return $this->handleView($view);
    }
}
