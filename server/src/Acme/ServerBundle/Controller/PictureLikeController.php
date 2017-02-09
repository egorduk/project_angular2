<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Exception\InvalidFormException;
use FOS\RestBundle\Controller\Annotations as RestAnnotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PictureLikeController extends FOSRestController
{
    /**
     * Create a picture like.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a picture like",
     *   input = "Acme\ServerBundle\Form\LikeType",
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
    public function postPictureLikeAction(Request $request)
    {
        try {
            $like = $this->get('rest.like.helper')->post(
                array_merge($request->request->all(), ['user' => $this->getUser()])
            );

            $view = $this->view(['likeId' => $like->getId()], Response::HTTP_CREATED);
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Delete existing picture like.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete existing picture like",
     *   statusCodes = {
     *     Response::HTTP_NO_CONTENT = "Returned when successful",
     *     Response::HTTP_BAD_REQUEST = "Returned when errors"
     *   }
     * )
     *
     * @RestAnnotations\Delete("/pictures/likes/{likeId}", requirements = { "likeId" = "\d+" }, options = { "method_prefix" = false })
     *
     * @param int $likeId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when likes does not exist
     */
    public function deletePictureCommentAction($likeId)
    {
        $like = $this->get('rest.like.helper')->getOneBy(['id' => $likeId, 'user' => $this->getUser()]);

        if (!is_null($like)) {
            $this->get('rest.like.helper')->delete($like);

            $view = $this->view(null, Response::HTTP_NO_CONTENT);

            return $this->handleView($view);
        } else {
            throw new NotFoundHttpException();
        }
    }
}
