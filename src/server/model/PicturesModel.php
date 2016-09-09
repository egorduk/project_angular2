<?php

/**
 * Pictures model
 */
class PicturesModel extends MainModel
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUserFriendsPictures($userId)
    {
        $query = $this->pdo->prepare("select p.id as picture_id, p.filename, p.name, datediff(NOW(), p.date_upload) as days_ago,
                   (select count(id) from picture_like pl1 where pl1.picture_id = p.id) as cnt_like, u.login as user_login, u.avatar as user_avatar, u.id as user_id,
                    EXISTS (select pl1.id from picture_like pl1 where pl1.user_id = f.user_id and pl1.picture_id = p.id) as is_liked, GROUP_CONCAT(distinct t.name) as tags,
                    GROUP_CONCAT(distinct ghp.gallery_id) as gallery_ids
                    from friend f
                    inner join picture p on p.user_id = f.friend_id
                    inner join user u on u.id = f.friend_id
                    left join picture_like pl on p.id = pl.picture_id
                    left join picture_tag pt on p.id = pt.picture_id
                    left join tag t on t.id = pt.tag_id
                    left join gallery_has_picture ghp on ghp.picture_id = p.id
                    where f.user_id = ?
                    group by p.id
                    order by p.date_upload desc");
        $query->execute(array($userId));

        if ($query->rowCount() > 0) {
            $pictures = $query->fetchAll(PDO::FETCH_ASSOC);
            return (array('response' => true, 'pictures' => $pictures));
        } else {
            return (array('response' => false));
        }
    }

    public function getUserPictures($currentUserId, $userId)
    {
        $query = $this->pdo->prepare("select p.name, p.filename, p.resize_height, p.resize_width, p.id as picture_id,
                        EXISTS(select pl.id from picture_like pl where pl.user_id = ? and pl.picture_id = p.id) as is_liked,
                        DATE_FORMAT(p.date_upload, '%b %d %Y %h:%i %p') as uploaded, GROUP_CONCAT(distinct t.name) as tags
                        from picture p
                        left join picture_tag pt on p.id = pt.picture_id
                        left join tag t on t.id = pt.tag_id
                        where p.is_show_host = 1 and p.user_id = ?
                        group by p.id");
        $query->execute(array($currentUserId, $userId));

        if ($query->rowCount() > 0) {
            $pictures = $query->fetchAll(PDO::FETCH_ASSOC);
            return (array('response' => true, 'pictures' => $pictures));
        } else {
            return (array('response' => false));
        }
    }

    public function getPictureByName($name, $userId)
    {
        $query = $this->pdo->prepare("select p.id
                        from picture p
                        inner join user u on u.id = p.user_id
                        where p.name = ? and u.id = ?");
        $query->execute(array($name, $userId));

        return ($query->rowCount() > 0) ? $query->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function updatePicture($newHeight, $newWidth, $pictureId)
    {
        $query = $this->pdo->prepare("update picture set date_upload = NOW(), resize_height = ?, resize_width = ? where id = ?");
        $query->execute(array($newHeight, $newWidth, $pictureId));
    }

    public function createPicture($userId, $name, $filename, $newHeight, $newWidth)
    {
        $query = $this->pdo->prepare("insert into picture(user_id, name, filename, resize_height, resize_width) values(?, ?, ?, ?, ?)");
        $query->execute(array($userId, $name, $filename, $newHeight, $newWidth));
        return $this->pdo->lastInsertId();
    }

    public function createPictureTag($pictureId, $tagId)
    {
        $this->pdo->exec("INSERT INTO picture_tag(picture_id, tag_id) VALUES ('$pictureId', '$tagId')");
    }

    public function getPictureComments($pictureId)
    {
        $query = $this->pdo->prepare("select datediff(NOW(), pc.date_comment) as days_ago, pc.id as comment_id, pc.comment, u.login as user_login, u.avatar as user_avatar, u.id as user_id
                    from picture_comment pc
                    inner join user u on u.id = pc.user_id
                    where pc.picture_id = ?
                    order by pc.date_comment desc");
        $query->execute(array($pictureId));

        if ($query->rowCount() > 0) {
            $comments = $query->fetchAll(PDO::FETCH_ASSOC);

            return array('response' => true, 'comments' => $comments);
        } else {
            return array('response' => false);
        }
    }

    public function createComment($currentUserId, $pictureId, $comment)
    {
        $query = $this->pdo->prepare("insert into picture_comment(user_id, picture_id, comment) values(?, ?, ?)");
        $response = $query->execute(array($currentUserId, $pictureId, $comment));

        return $response;
    }

    public function createLike($currentUserId, $pictureId)
    {
        $query = $this->pdo->prepare("insert into picture_like(user_id, picture_id) values(?, ?)");
        $response = $query->execute(array($currentUserId, $pictureId));

        return $response;
    }

    public function deleteLike($userId, $pictureId)
    {
        $query = $this->pdo->prepare("delete from picture_like where user_id = ? and picture_id = ? ");
        $response = $query->execute(array($userId, $pictureId));

        return array('response' => $response);
    }

    public function deleteComment($userId, $pictureId, $commentId)
    {
        $query = $this->pdo->prepare("delete from picture_comment where user_id = ? and id = ? and picture_id = ?");
        $response = $query->execute(array($userId, $pictureId, $commentId));

        return array('response' => $response);
    }

    public function updatePictureName($userId, $pictureId, $pictureName)
    {
        $query = $this->pdo->prepare("update picture set name = ? where user_id = ? and id = ? ");
        $response = $query->execute(array($pictureName, $userId, $pictureId));

        return array('response' => $response);
    }

    public function updatePictureStatus($userId, $pictureId)
    {
        $query = $this->pdo->prepare("update picture set is_show_host = 0 where user_id = ? and id = ? ");
        $response = $query->execute(array($userId, $pictureId));

        return array('response' => $response);
    }

    public function beginTransaction()
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->beginTransaction();
    }

    public function commitTransaction()
    {
        return $this->pdo->commit();
    }

    public function rollbackTransaction()
    {
        $this->pdo->rollBack();
    }
}