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

class PictureCommentController extends FOSRestController
{
    /**
     * Get picture comment.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get picture comment",
     *   output = "Acme\ServerBundle\Entity\PictureComment",
     *   statusCodes = {
     *     Response::HTTP_OK = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when the comment is not found"
     *   }
     * )
     *
     * @RestAnnotations\Get("/pictures/comments/{commentId}", requirements = { "commentId" = "\d+" }, name="get_picture_comment", options = { "method_prefix" = false })
     *
     * @param int $commentId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when comments do not exist
     */
    public function getCommentAction($commentId)
    {
        if (!($comment = $this->get('rest.comment.helper')->get($commentId))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view([
            'comment' => $comment,
        ]);

        return $this->handleView($view);
    }

    /**
     * Get picture comments.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get picture comments",
     *   output = "Acme\ServerBundle\Entity\PictureComment",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the comments are not found"
     *   }
     * )
     *
     * @RestAnnotations\Get("/pictures/{pictureId}/comments", requirements = { "pictureId" = "\d+" }, name="get_picture_comments", options = { "method_prefix" = false })
     *
     * @param int $pictureId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when comments do not exist
     */
    public function getCommentsAction($pictureId)
    {
        if (!($picture = $this->get('rest.picture.helper')->get($pictureId))) {
            throw new NotFoundHttpException();
        }

        if (!($comments = $this->get('rest.comment.helper')->getByPicture($picture))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view([
            'comments' => $comments,
        ]);

        return $this->handleView($view);
    }

    /**
     * Create a picture comment.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a picture comment",
     *   input = "Acme\ServerBundle\Form\CommentType",
     *   statusCodes = {
     *     201 = "Returned when successful",
     *     400 = "Returned when errors"
     *   }
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postPictureCommentAction(Request $request)
    {
        try {
            $comment = $this->get('rest.comment.helper')->post(
                array_merge($request->request->all(), ['user' => $this->getUser()])
            );

            $routeOptions = [
                'commentId' => $comment->getId(),
                '_format' => $request->get('_format'),
            ];

            $view = $this->routeRedirectView('api_1_get_picture_comment', $routeOptions);
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Delete existing comment.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete existing comment",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when errors"
     *   }
     * )
     *
     * @RestAnnotations\Delete("/pictures/comments/{commentId}", requirements = { "commentId" = "\d+" }, options = { "method_prefix" = false })
     *
     * @param int $commentId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when comment does not exist
     */
    public function deletePictureCommentAction($commentId)
    {
        $comment = $this->get('rest.comment.helper')->getOneBy(['id' => $commentId, 'user' => $this->getUser()]);

        if (!is_null($comment)) {
            $this->get('rest.comment.helper')->delete($comment);

            $view = View::create(null, Response::HTTP_NO_CONTENT);

            return $this->handleView($view);
        } else {
            throw new NotFoundHttpException();
        }
    }
}
