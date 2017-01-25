<?php

namespace Acme\ServerBundle\Controller;

use Acme\ServerBundle\Entity\Picture;
use Acme\ServerBundle\Exception\InvalidFormException;
use Acme\ServerBundle\Form\PictureType;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as RestAnnotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PictureController extends FOSRestController
{
    /**
     * Gets single picture
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
     * @throws NotFoundHttpException when picture does not exist
     */
    public function getPictureAction($id)
    {
        if (!($picture = $this->container->get('rest.picture.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The picture with id = \'%s\' was not found.', $id));
        }

        $view = $this->view(array('picture' => $picture));

        return $this->handleView($view);
    }

    /**
     * Gets all pictures
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
     * @RestAnnotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pictures")
     * @RestAnnotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many pictures to return")
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return Picture[]
     *
     * @throws NotFoundHttpException when pictures not exist
     */
    public function getPicturesAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        $pictures = $this->container->get('rest.picture.handler')->all($limit, $offset);

        if ( !count($pictures) ) {
            throw new NotFoundHttpException(sprintf('The pictures were not found.'));
        } else {
            $view = $this->view(array('pictures' => $pictures));

            return $this->handleView($view);
        }
    }

    /**
     * Creates a picture
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new picture",
     *   input = "Acme\ServerBundle\Form\PictureType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return View view instance
     *
     */
    public function postPictureAction(Request $request)
    {
        try {
            $picture = $this->container->get('rest.picture.handler')->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $picture->getId(),
                '_format' => $request->get('_format')
            );

            $view = View::createRouteRedirect('api_1_get_picture', $routeOptions);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            //var_dump($exception);die;
            //return $exception->getForm();
            $view = View::createRouteRedirect('api_1_get_picture', $routeOptions);
            return $this->handleView($view);
        }
    }

    private function createPicture(Picture $picture, $request)
    {
        $form = $this->createForm(new PictureType(), $picture);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $pictureId = $picture->getId();

            //if (!isset($pictureId)) {
                $picture->setDateUpload(new \DateTime());
                $picture->setIsShowHost(true);
            //}

            $this->getDoctrine()
                ->getRepository('AcmeServerBundle:Picture')
                ->save($picture, true);

            $routeOptions = array(
                'id' => $picture->getId(),
                //'_format' => $request->get('_format')
            );

            $view = View::createRouteRedirect('api_1_get_picture', $routeOptions);
        } else {
            $view = View::create($form);
        }

        return $this->handleView($view);
    }

    /**
     * Updates existing picture from the submitted data or creates a new picture at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Acme\ServerBundle\Form\PictureType",
     *   statusCodes = {
     *     201 = "Returned when the picture is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the picture id
     *
     * @return View view instance
     *
     * @throws NotFoundHttpException when picture not exist
     */
    public function putPictureAction(Request $request, $id)
    {
        //try {
        $picture = $this->getDoctrine()
            ->getRepository('AcmeServerBundle:Picture')
            ->find($id);

            if (!$picture) {
                $statusCode = 201;
                $this->createPicture(new Picture(), $request);
               /* $page = $this->container->get('acme_blog.page.handler')->post(
                    $request->request->all()
                );*/
            } else {
                $statusCode = 204;

                $this->createPicture($picture, $request);
               /* $page = $this->container->get('acme_blog.page.handler')->put(
                    $page,
                    $request->request->all()
                );*/
            }

            $routeOptions = array(
                'id' => $picture->getId(),
                //'_format' => $request->get('_format')
            );

            $view = View::createRouteRedirect('api_1_get_picture', $routeOptions, $statusCode);

        return $this->handleView($view);

       /* } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }*/
    }

    public function deletePictureAction($id)
    {
        $picture = $this->getDoctrine()
            ->getRepository('AcmeServerBundle:Picture')
            ->find($id);

        if ($picture) {
            $response = $this->getDoctrine()
                ->getRepository('AcmeServerBundle:Picture')
                ->remove($picture, true);

            $view = $this->view(array('response' => $response));

            return $this->handleView($view);
        } else {
            throw new NotFoundHttpException('The picture is not found');
        }
    }
}
