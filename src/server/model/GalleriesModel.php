<?php

/**
 * Galleries model
 */
class GalleriesModel extends MainModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUserGalleries($userId)
    {
        $query = $this->pdo->prepare("select pg.name, pg.id as gallery_id, count(p.id) as cnt_pictures,
                        GROUP_CONCAT(p.filename) as pictures, GROUP_CONCAT(p.id) as picture_ids
                        from gallery_has_picture ghp
                        left join picture_gallery pg on pg.id = ghp.gallery_id
                        left join picture p on p.id = ghp.picture_id
                        where ghp.user_id = ?
                        group by pg.id
                        order by pg.name asc");

        $query->execute(array($userId));

        if ($query->rowCount() > 0) {
            $galleries = $query->fetchAll(PDO::FETCH_ASSOC);

            return array('response' => true, 'galleries' => $galleries);
        } else {
           return array('response' => false);
        }
    }

    public function createPictureGalleries($pictureId, $galleryId, $userId)
    {
        $response = $this->createRelationsWithPicture($pictureId, $galleryId, $userId);

        return array('response' => $response);
    }

    public function getGalleryHasPictureRelationsIdByIds($pictureId, $galleryId, $userId)
    {
        $query = $this->pdo->prepare("select id from gallery_has_picture where picture_id = ? and gallery_id = ? and user_id = ?");
        $query->execute(array($pictureId, $galleryId, $userId));

        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $response = $row['id'];
        } else {
            $response = -1;
        }

        return $response;
    }

    public function deletePictureFromGallery($pictureId)
    {
        $query = $this->pdo->prepare("delete from gallery_has_picture where id = ?");
        $response = $query->execute(array($pictureId));

        return array('response' => $response);
    }

    public function createPictureInGallery($pictureId, $galleryId, $userId)
    {
        $query = $this->pdo->prepare("insert into gallery_has_picture(picture_id, gallery_id, user_id) values(?, ?, ?)");
        $response = $query->execute(array($pictureId, $galleryId, $userId));

        return array('response' => $response);
    }

    public function createGallery($galleryName)
    {
        $query = $this->pdo->prepare("insert into picture_gallery(name) values(?)");
        $response = $query->execute(array($galleryName));

        $galleryId = ($response) ? $this->pdo->lastInsertId() : -1;

        return $galleryId;
    }

    public function createRelationsWithPicture($pictureId, $galleryId, $userId)
    {
        $queryCreateEmptyRecord = $this->pdo->prepare("insert into gallery_has_picture(gallery_id, user_id) values(?, ?)");
        $queryCreateRelationsGHP =  $this->pdo->prepare("insert into gallery_has_picture(picture_id, gallery_id, user_id) values(?, ?, ?)");
        $response = $queryCreateEmptyRecord->execute(array($galleryId, $userId)) && $queryCreateRelationsGHP->execute(array($pictureId, $galleryId, $userId));

        return array('response' => $response);
    }

    public function getGalleryPictures($galleryId)
    {
        $query = $this->pdo->prepare("select p.*
                        from gallery_has_picture ghp
                        inner join picture p on p.id = ghp.picture_id
                        where ghp.gallery_id = ?");
        $query->execute(array($galleryId));

        if ($query->rowCount() > 0) {
            $pictures = $query->fetchAll(PDO::FETCH_ASSOC);
            $response = array('response' => true, 'pictures' => $pictures);
        } else {
            $response = array('response' => false);
        }

        return $response;
    }

    public function deleteGallery($userId, $galleryId)
    {
        $queryDeleteGalleryPictures = $this->pdo->prepare("delete from gallery_has_picture where gallery_id = ? and user_id = ?");
        $queryDeleteGallery = $this->pdo->prepare("delete from picture_gallery where id = ?");

        $response = $queryDeleteGalleryPictures->execute(array($galleryId, $userId)) && $queryDeleteGallery->execute(array($galleryId));

        return array('response' => $response);
    }

}