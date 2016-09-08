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
        $response = $this->getUserGalleriesByIds($pictureId, $galleryId, $userId);

        return array('response' => $response);
    }

    public function getUserGalleriesByIds($pictureId, $galleryId, $userId)
    {
        $query = $this->pdo->prepare("select * from gallery_has_picture where picture_id = ? and gallery_id = ? and user_id = ?");
        $query->execute(array($pictureId, $galleryId, $userId));

        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $response = $this->deletePictureFromGallery($row['id']);
        } else {
            $response = $this->createPictureInGallery($pictureId, $galleryId, $userId);
        }

        return $response;
    }

    public function deletePictureFromGallery($pictureId)
    {
        $query = $this->pdo->prepare("delete from gallery_has_picture where id = ?");

        return $query->execute(array($pictureId));
    }

    public function createPictureInGallery($pictureId, $galleryId, $userId)
    {
        $query = $this->pdo->prepare("insert into gallery_has_picture(picture_id, gallery_id, user_id) values(?, ?, ?)");

        return $query->execute(array($pictureId, $galleryId, $userId));
    }

    public function createGallery($pictureId, $galleryName, $userId)
    {
        $queryCreateGallery = $this->pdo->prepare("insert into picture_gallery(name) values(?)");
        $queryCreateRelationsGHP =  $this->pdo->prepare("insert into gallery_has_picture(picture_id, gallery_id, user_id) values(?, ?, ?)");
        $response = $queryCreateGallery->execute(array($galleryName));
        $galleryId =  $this->pdo->lastInsertId();
        $query =  $this->pdo->prepare("insert into gallery_has_picture(gallery_id, user_id) values(?, ?)");
        $response = $response && $queryCreateRelationsGHP->execute(array($pictureId, $galleryId, $userId)) && $query->execute(array($galleryId, $userId));

        return array('response' => $response);
    }

}