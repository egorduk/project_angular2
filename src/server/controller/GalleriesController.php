<?php

/**
 * CommentsController for handling api/galleries
 */
class GalleriesController extends MainController
{

    private $response = '';
    private $gallery = null;
    private $currentUserId = 0;
    private $token = '';

    public function __construct()
    {
        $this->gallery = new GalleriesModel();
        $this->token = $this->getFormattedToken();
        $this->currentUserId = $this->getUserIdFromPayload($this->token);
    }

    /**
     * Manages all GET requests to api/galleries
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function getAction(Request $request)
    {
        if (isset($request->urlElements[5]) && is_numeric($request->urlElements[5])
            && isset($request->urlElements[4]) && $request->urlElements[4] == 'users'
        ) {
            $userId = $request->urlElements[5];

            $this->response = $this->gallery->getUserGalleries($userId);
        } else {
        }

        return $this->response;
    }

    /**
     * Manages all POST requests to api/galleries
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function postAction(Request $request)
    {
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'pictures') {
            $galleryId = $request->parameters['galleryId'];;
            $pictureId = $request->parameters['pictureId'];

            $this->response = $this->gallery->createPictureGalleries($pictureId, $galleryId, $this->currentUserId);
        } else {
            $pictureId = $request->parameters['pictureId'];
            $galleryName = $request->parameters['galleryName'];

            $this->response = $this->gallery->createGallery($pictureId, $galleryName, $this->currentUserId);
        }

        return $this->response;
    }
}