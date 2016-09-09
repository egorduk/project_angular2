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
        //  api/galleries/users/{user_id}
        if (isset($request->urlElements[5]) && is_numeric($request->urlElements[5])
            && isset($request->urlElements[4]) && $request->urlElements[4] == 'users'
        ) {
            $userId = $request->urlElements[5];

            $this->response = $this->gallery->getUserGalleries($userId);
        } elseif (isset($request->urlElements[4]) && is_numeric($request->urlElements[4])       //  api/galleries/{gallery_id}/pictures
            && isset($request->urlElements[5]) && $request->urlElements[5] == 'pictures'
        ) {
            $galleryId = $request->urlElements[4];

            $this->response = $this->gallery->getGalleryPictures($galleryId);
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
        //  api/galleries/pictures/
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'pictures') {
            $galleryId = $request->parameters['galleryId'];;
            $pictureId = $request->parameters['pictureId'];

            $relationsId = $this->gallery->getGalleryHasPictureRelationsIdByIds($pictureId, $galleryId, $this->currentUserId);

            if ($this->isValidId($relationsId)) {
                $this->response = $this->gallery->deletePictureFromGallery($relationsId);
            } else {
                $this->response = $this->gallery->createPictureInGallery($pictureId, $galleryId, $this->currentUserId);
            }
        } else {    //  api/galleries/
            $pictureId = $request->parameters['pictureId'];
            $galleryName = $request->parameters['galleryName'];

            $galleryId = $this->gallery->createGallery($galleryName);

            if ($this->isValidId($galleryId)) {
                $this->response = $this->gallery->createRelationsWithPicture($pictureId, $galleryId, $this->currentUserId);
            } else {
                $this->response = array('response' => false);
            }
        }

        return $this->response;
    }

    /**
     * Manages all DELETE requests to api/galleries
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function deleteAction(Request $request)
    {
        //  api/galleries/{gallery_id}
        if (isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
        ) {
            $galleryId = $request->urlElements[4];

            $this->response = $this->gallery->deleteGallery($this->currentUserId, $galleryId);
        } else {

        }

        return $this->response;
    }
}