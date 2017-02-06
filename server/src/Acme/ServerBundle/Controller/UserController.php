<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Entity\User;
use Acme\ServerBundle\Exception\InvalidFormException;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;

class UserController extends FOSRestController
{
    /**
     * Get user by id.
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
     * Get user by login.
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
     * Get unfollows users for current user.
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

    /**
     * Create a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a new user",
     *   input = "Acme\ServerBundle\Form\RegistrationType",
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
    public function postUserAction(Request $request)
    {
        try {
            $user = $this->get('rest.user.helper')->post(
                $request->request->all()
            );

            $routeOptions = [
                'id' => $user->getId(),
                '_format' => $request->get('_format'),
            ];

            $view = View::createRouteRedirect('api_1_get_user_by_id', $routeOptions);
        } catch (InvalidFormException $exception) {
            $view = View::create($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Sign in user by email/password and get auth token.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Sign in user by email/password and get auth token",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Get("/users/email/{email}/password/{password}", name="get_user_token_by_email_password", requirements = { "email" = "\S+", "password" = "\S+" }, options = { "method_prefix" = false })
     *
     * @param string $email
     * @param string $password
     *
     * @return Response
     *
     * @throws NotFoundHttpException when user does not exist
     */
    public function getUserTokenByEmailPasswordAction($email, $password)
    {
        try {
            $user = $this->get('rest.user.helper')->processLoginForm(
                [
                    'email' => $email,
                    'password' => $password,
                ]
            );

            if (is_null($user)) {
                throw new NotFoundHttpException();
            }

            $token = $this->get('rest.auth.helper')->getAuthToken($user->getId());

            $user->setToken($token);

            $this->get('rest.user.helper')->patch($user);

            $view = View::create($token, Response::HTTP_OK);
        } catch (InvalidFormException $exception) {
            $view = View::create($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Update user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update user",
     *   input = "Acme\ServerBundle\Form\ProfileType",
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
    public function patchUsersAction(Request $request)
    {
        try {
            $user = $this->get('rest.user.helper')->patch(
                $this->getUser(),
                $request->request->all()
            );

            $routeOptions = [
                'id' => $user->getId(),
                '_format' => $request->get('_format'),
            ];

            $view = View::createRouteRedirect('api_1_get_user_by_id', $routeOptions, Response::HTTP_NO_CONTENT);
        } catch (InvalidFormException $exception) {
            $view = View::create($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
