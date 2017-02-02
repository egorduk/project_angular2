<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as RestAnnotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;


class UserController extends FOSRestController
{
    /**
     * Get user by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user by id",
     *   output = "Acme\ServerBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Get("/users/id/{id}", name="get_user_by_id", requirements = { "id" = "\d+" }, options = { "method_prefix" = false })
     *
     * @param int $id
     *
     * @return User
     *
     * @throws NotFoundHttpException when user does not exist
     */
    public function getUserByIdAction($id)
    {
        if (!($user = $this->get('rest.user.helper')->get($id))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view([
            'user' => $user,
        ]);

        return $this->handleView($view);
    }

    /**
     * Get user by login
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user by login",
     *   output = "Acme\ServerBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Get("/users/login/{login}", name="get_user_by_login", options={ "method_prefix" = false })
     *
     * @param string $login
     *
     * @return User
     *
     * @throws NotFoundHttpException when user does not exist
     */
    public function getUserByLoginAction($login)
    {
        if (!($user = $this->get('rest.user.helper')->getBy(['login' => $login]))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view([
            'user' => $user,
        ]);

        return $this->handleView($view);
    }

    /**
     * Get unfollows users for current user
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get unfollows users for current user",
     *   output = "Acme\ServerBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when users were not found"
     *   }
     * )
     *
     * @Get("/users/{id}/unfollows", name="get_unfollows_users", requirements = { "id" = "\d+" }, options={ "method_prefix" = false })
     *
     * @param int $id
     *
     * @return User[]
     *
     * @throws NotFoundHttpException when users are not exist
     */
    public function getUnfollowsUsersAction($id)
    {
        if (!($users = $this->get('rest.user.helper')->getUnfollowsUsers($id))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view([
            'users' => $users,
        ]);

        return $this->handleView($view);
    }
}
