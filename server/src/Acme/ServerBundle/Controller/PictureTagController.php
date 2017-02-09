<?php

namespace Acme\ServerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as RestAnnotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PictureTagController extends FOSRestController
{
    /**
     * Get all tags.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all tags",
     *   output = "Acme\ServerBundle\Entity\PictureTag",
     *   statusCodes = {
     *     Response::HTTP_OK = "Returned when successful",
     *     Response::HTTP_NOT_FOUND = "Returned when the tags are not found"
     *   }
     * )
     *
     * @RestAnnotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing tags")
     * @RestAnnotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many tags to return")
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     *
     * @throws NotFoundHttpException when pictures not exist
     */
    public function getTagsAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        $tags = $this->get('rest.tag.helper')->all($limit, $offset);

        if (count($tags)) {
            $view = $this->view(['tags' => $tags]);

            return $this->handleView($view);
        } else {
            throw new NotFoundHttpException();
        }
    }
}
