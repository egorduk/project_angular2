<?php

/**
 * PicturesController for handling api/pictures
 */
class PicturesController extends MainController
{

    private $token = '';
    private $currentUserId = 0;
    private $picture = null;
    private $response = '';
    private $pictureBasePath = '';
    private $pictureOriginalPath = '';
    private $pictureResizePath = '';

    public function __construct()
    {
        $this->token = $this->getFormattedToken();
        $this->currentUserId = $this->getUserIdFromPayload($this->token);
        $this->picture = new PicturesModel();
        $this->pictureBasePath = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pictures';
        $this->pictureOriginalPath =  $this->pictureBasePath . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR;
        $this->pictureResizePath =  $this->pictureBasePath . DIRECTORY_SEPARATOR . 'resized' . DIRECTORY_SEPARATOR;
    }

    /**
     * Manages all GET requests to api/pictures
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function getAction(Request $request)
    {
        if (isset($request->urlElements[4]) && $request->urlElements[4]  == 'friends'
            && isset($request->urlElements[5]) && $request->urlElements[5]  == 'users'
            && isset($request->urlElements[6]) && is_numeric($request->urlElements[6])
        ) {
            $this->response = $this->picture->getUserFriendsPictures($request->urlElements[6]);
        } elseif (isset($request->urlElements[4]) && $request->urlElements[4]  == 'users'
            && isset($request->urlElements[5]) && is_numeric($request->urlElements[5])
        ) {
            $this->response = $this->picture->getUserPictures($this->currentUserId, $request->urlElements[5]);
        } elseif (isset($request->urlElements[5]) && $request->urlElements[5]  == 'comments'
            && isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
        ) {
            $this->response = $this->picture->getPictureComments($request->urlElements[4]);
        } else {

        }
        //var_dump($request);

        return $this->response;
    }

    /**
     * Manages all POST requests to api/pictures
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function postAction(Request $request)
    {
        if (isset($request->urlElements[5]) && $request->urlElements[5]  == 'comments'
            && isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
        ) {
            $pictureId = $request->urlElements[4];
            $comment = $request->parameters['comment'];

            $response = $this->picture->createComment($this->currentUserId, $pictureId, $comment);

            $this->response = array('response' => $response);
        } elseif (isset($request->urlElements[5]) && $request->urlElements[5]  == 'likes'
            && isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
        ) {
            $pictureId = $request->urlElements[4];

            $response = $this->picture->createLike(6, $pictureId);

            $this->response = array('response' => $response);
        } else {
            $filename = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $error = $_FILES['file']['error'];
            $size = $_FILES['file']['size'];
            $type = $_FILES['file']['type'];

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $response = false;
            $errorMsg = '';

            switch ($error) {
                case UPLOAD_ERR_OK:
                    $valid = true;

                    if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
                        $valid = false;
                        $errorMsg = 'Invalid file extension.';
                    }

                    $uploadMaxFileSize = ini_get("upload_max_filesize");
                    $uploadMaxFileSize = $this->getFileSizeBytes($uploadMaxFileSize);

                    //validate file size
                    if ($size > $uploadMaxFileSize) {
                        $valid = false;
                        $response = 'File size is exceeding maximum allowed size.';
                    }

                    if ($valid) {
                        $targetOriginalPath = $this->pictureOriginalPath . $filename;
                        $targetResizePath = $this->pictureResizePath . $filename;

                        move_uploaded_file($tmpName, $targetOriginalPath);

                        $maxWidth = 450;
                        $maxHeight = 900;
                        $newWidth = $maxWidth;
                        $newHeight = $maxHeight;

                        list($width, $height) = getimagesize($targetOriginalPath);
                        $ratioOriginal = $width / $height;

                        if ($maxWidth / $maxHeight > $ratioOriginal) {
                            $newWidth = $maxHeight * $ratioOriginal;
                        } else {
                            $newHeight = $maxWidth / $ratioOriginal;
                        }

                        $thumb = imagecreatetruecolor($newWidth, $newHeight);

                        if ($type == 'image/jpeg') {
                            header('Content-Type: image/jpeg');
                            $source = imagecreatefromjpeg($targetOriginalPath);
                        } elseif ($type == 'image/png') {
                            header('Content-Type: image/png');
                            $source = imagecreatefrompng($targetOriginalPath);
                        } elseif ($type == 'image/gif') {
                            header('Content-Type: image/gif');
                            $source = imagecreatefromgif($targetOriginalPath);
                        }

                        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagejpeg($thumb, $targetResizePath);

                        $name = preg_replace('/.jpg|.jpeg|.png|.gif/', '', $filename);

                        $picture = $this->picture->getPictureByName($name, $this->currentUserId);
                        $pictureId = $picture['id'];

                        try {
                            $this->picture->beginTransaction();
                            //$db = getDb();
                            //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            //$db->beginTransaction();

                            if ($pictureId) {
                                $this->picture->updatePicture($newHeight, $newWidth, $pictureId);
                            } else {
                                $pictureId = $this->picture->createPicture($this->currentUserId, $name, $filename, $newHeight, $newWidth);
                            }

                            $tagIds = $_POST['tags'];

                            if ($tagIds != "null") {
                                $tagIds = explode(',', $tagIds);

                                foreach ($tagIds as $tagId) {
                                    $this->picture->createPictureTag($pictureId, $tagId);
                                }
                            }

                            //$response = $db->commit();
                            $response = $this->picture->commitTransaction();
                        } catch(PDOException $e) {
                            // $db->rollback();
                            $this->picture->rollbackTransaction();
                            echo $e->getMessage();
                        }
                    }

                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $errorMsg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMsg = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMsg = 'The uploaded file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMsg = 'No file was uploaded.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMsg = 'Missing a temporary folder.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMsg = 'Failed to write file to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMsg = 'File upload stopped by extension.';
                    break;
                default:
                    $errorMsg = 'Unknown error';
                    break;
            }

            $this->response = (array('response' => $response, 'errorMsg' => $errorMsg));
        }

        return $this->response;
    }

    /**
     * Manages all DELETE requests to api/pictures
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function deleteAction(Request $request)
    {
        if (isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
            && isset($request->urlElements[5]) && $request->urlElements[5]  == 'likes'
        ) {
            $pictureId = $request->urlElements[4];

            $this->response = $this->picture->deleteLike($this->currentUserId, $pictureId);
        } elseif (isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
            && isset($request->urlElements[5]) && $request->urlElements[5]  == 'comments'
            && isset($request->urlElements[6]) && is_numeric($request->urlElements[6])
        ) {
            $commentId = $request->urlElements[6];
            $pictureId = $request->urlElements[4];

            $this->response = $this->picture->deleteComment($this->currentUserId, $pictureId, $commentId);
        } else {

        }

        return $this->response;
    }

    /**
     * Manages all PUT requests to api/pictures
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function putAction(Request $request)
    {
        if (isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
            && isset($request->urlElements[5]) && $request->urlElements[5]  == 'status'
        ) {
            $pictureId = $request->urlElements[4];

            $this->response = $this->picture->updatePictureStatus($this->currentUserId, $pictureId);
        } elseif (isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
            && isset($request->urlElements[5]) && $request->urlElements[5]  == 'name'
        ) {
            $pictureId = $request->urlElements[4];
            $pictureName = $request->parameters['name'];

            $this->response = $this->picture->updatePictureName($this->currentUserId, $pictureId, $pictureName);
        }
    }
}