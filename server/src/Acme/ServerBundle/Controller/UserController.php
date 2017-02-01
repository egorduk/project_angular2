<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class UserController extends FOSRestController
{
    /**
     * Login user by email and password
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Sign in user",
     *   output = "Acme\ServerBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $email
     * @param string $password
     *
     * @return User
     *
     * @throws NotFoundHttpException when user does not exist
     */
    public function getLoginAction($email, $password)
    {
        if (!($picture = $this->get('rest.picture.helper')->get(1))) {
            throw new NotFoundHttpException();
        }

        $view = $this->view([
            'picture' => $picture,
        ]);

        return $this->handleView($view);
    }
}
