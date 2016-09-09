<?php

/**
 * Users model
 */
class UsersModel extends MainModel
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUserByLogin($login)
    {
        $query = $this->pdo->prepare("select u.login, u.avatar, u.id, u.info, u.page_photo, u.email
                        from user u
                        where u.login = ?");
        $query->execute(array($login));

        if ($query->rowCount() > 0) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            return array('response' => true, 'user' => $user);
        } else {
            return array('response' => false);
        }
    }

    public function getUserById($id)
    {
        $query = $this->pdo->prepare("select u.login, u.avatar, u.id, u.info, u.page_photo
                        from user u
                        where u.id = ?");
        $query->execute(array($id));

        if ($query->rowCount() > 0) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            return array('response' => true, 'user' => $user);
        } else {
            return array('response' => false);
        }
    }

    public function createUser($email, $password)
    {
        $query = $this->pdo->prepare("insert into user(email, password) values(?, ?)");

        return $query->execute(array($email, $password));
    }

    public function getUserIdByEmailPassword($email, $password)
    {
        $query = $this->pdo->prepare("select id from user where email = ? and password = ?");
        $query->execute(array($email, $password));

        if ($query->rowCount() > 0) {
            $data = $query->fetch(PDO::FETCH_ASSOC);

            return $data['id'];
        } else {
            //header('HTTP/1.1 401 The email or password don\'t match');
            //return;
        }
    }

    public function getUnfollowUsers($userId)
    {
        $query = $this->pdo->prepare("select u.id, u.login, u.avatar, GROUP_CONCAT(p.filename) pictures, count(u.id) as cnt_picture  from user u
                    inner join picture p on p.user_id = u.id
                    where u.id not in (select f.friend_id from friend f where f.user_id = ?) and u.id != ?
                    group by u.id
                    order by u.id
                    limit 3");
        $query->execute(array($userId, $userId));

        if ($query->rowCount() > 0) {
            $users = $query->fetchAll(PDO::FETCH_ASSOC);

            return array('response' => true, 'users' => $users);
        } else {
            return array('response' => false);
        }
    }

    public function createFriend($friendId, $userId)
    {
        $query = $this->pdo->prepare("insert into friend(user_id, friend_id) values(?, ?)");
        $response = $query->execute(array($userId, $friendId));

        return array('response' => $response);
    }

    public function deleteFriend($userId, $friendId)
    {
        $query = $this->pdo->prepare("delete from friend where user_id = ? and friend_id = ? ");
        $response = $query->execute(array($userId, $friendId));

        return array('response' => $response);
    }

    public function updateUserInfo($userId, $login, $email, $info)
    {
        $query = $this->pdo->prepare("update user set login = ?, email = ?, info = ?  where id = ?");
        $response = $query->execute(array($login, $email, $info, $userId));

        return array('response' => $response);
    }
}