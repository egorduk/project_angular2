<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Exception\InvalidFormException;
use FOS\RestBundle\Controller\Annotations as RestAnnotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendController extends FOSRestController
{
    /**
     * Follow the user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Follow the user",
     *   statusCodes = {
     *     Response::HTTP_CREATED = "Returned when successful",
     *     Response::HTTP_BAD_REQUEST = "Returned when errors",
     *     Response::HTTP_NOT_FOUND = "Returned when user does not exist"
     *   }
     * )
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @RestAnnotations\RequestParam(name="friendId", requirements="\d+", description="Friend id")
     *
     * @return Response
     *
     * @throws NotFoundHttpException when user does not exist
     */
    public function postFollowAction(ParamFetcherInterface $paramFetcher)
    {
        try {
            $friendId = $paramFetcher->get('friendId');

            if (!($friend = $this->get('rest.user.helper')->get($friendId))) {
                throw new NotFoundHttpException();
            }

            $this->get('rest.friend.helper')->post(
                [
                    'friend' => $friend,
                    'user' => $this->getUser(),
                ]
            );

            $view = $this->view(null, Response::HTTP_CREATED);
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Delete existing following user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete existing following user",
     *   statusCodes = {
     *     Response::HTTP_NO_CONTENT = "Returned when successful",
     *     Response::HTTP_BAD_REQUEST = "Returned when errors"
     *   }
     * )
     *
     * @param int $friendId
     *
     * @return Response
     *
     * @throws NotFoundHttpException when friend does not exist
     */
    public function deleteFollowsAction($friendId)
    {
        try {
            if (!($friend = $this->get('rest.friend.helper')->getOneBy(
                [
                    'friend' => $this->get('rest.user.helper')->get($friendId),
                    'user' => $this->getUser(),
                ]
            ))) {
                throw new NotFoundHttpException();
            }

            $this->get('rest.friend.helper')->delete($friend);

            $view = $this->view(null, Response::HTTP_NO_CONTENT);
        } catch (InvalidFormException $exception) {
            $view = $this->view($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
