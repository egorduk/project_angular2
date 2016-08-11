<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Accept');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$encAlgorithm = 'RS256';

switch ($requestMethod) {
    case "GET":
        $data = explode('/api/', $requestUri);
        $action = $data[1];
        require_once "JOSE/autoloader.php";
        $headers = apache_request_headers();
        $token = getFormattedToken($headers['Authorization']);
        $jws = \Namshi\JOSE\SimpleJWS::load($token);
        $publicKey = getPublicKey();

        if ($action == 'get_friends_pictures') {
            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];

                $db = getDb();
                $query = $db->prepare("select p.filename, p.name,  datediff(NOW(), p.date_upload) as days_ago, p.cnt_like, u.login as user_login, u.avatar as user_avatar from friend f
                    inner join picture p on p.user_id = f.friend_id
                    inner join user u on u.id = f.friend_id
                    where f.user_id = ?
                    order by p.date_upload desc");
                $query->execute(array($userId));

                if ($query->rowCount() > 0) {
                    //$arrGroupedPictures = [];
                    $pictures = $query->fetchAll(PDO::FETCH_ASSOC);
                   /* foreach($pictures as $key => $pic) {
                        $arrGroupedPictures[$pic['login']][] = $pic['filename'];
                    }*/
                    echo json_encode($pictures);
                } else {
                    echo json_encode(array('error' => 'Something wrong'));
                    return;
                }
            } else {
                header('HTTP/1.1 401 Unauthorized ');
            }
        } elseif ($action == 'get_unfollow_users') {
            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];

                $db = getDb();
               /* $query = $db->prepare("select u.login, u.avatar, u.id, p.filename from user u
                    inner join picture p on p.user_id = u.id
                    where u.id not in (select f.friend_id from friend f where f.user_id = ?) and u.id != ?
                    order by u.id"); */
                $query = $db->prepare("select u.id, u.login, u.avatar, GROUP_CONCAT(p.filename) pictures, count(u.id) as cnt_picture  from user u
                    inner join picture p on p.user_id = u.id
                    where u.id not in (select f.friend_id from friend f where f.user_id = ?) and u.id != ?
                    group by u.id
                    order by u.id
                    limit 3");
                $query->execute(array($userId, $userId));

                if ($query->rowCount() > 0) {
                    $users = $query->fetchAll(PDO::FETCH_ASSOC);

                    $arr = [];
                    foreach($users as $user) {
                        $arr[$user['login']][] = $user;
                    }

                    //echo json_encode($arr);
                    echo json_encode($users);
                } else {
                    echo json_encode(array('error' => 'Something wrong'));
                    return;
                }
            } else {
                header('HTTP/1.1 401 Unauthorized ');
            }
        }

        break;
    case "POST":
        $post = json_decode(file_get_contents('php://input'));
        //var_dump($post->username);die;
        $data = explode('/api/', $requestUri);
        $action = $data[1];
        if ($action == 'create_session') {
            if (!$post->email || !$post->password) {
                header('HTTP/1.1 400 You must send the email and the password');
                return;
            }

            $email = $post->email;
            $password = md5($post->password);
            $db = new PDO('mysql:dbname=project_angular2;host=127.0.0.1', 'root', '');
            $query = $db->prepare("select * from user where email = ? and password = ?");
            $query->execute(array($email, $password));

            if ($query->rowCount() > 0) {
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
                $userId = reset($data)['id'];
                //var_dump($data);die;
            } else {
                header('HTTP/1.1 401 The email or password don\'t match');
                return;
            }

            require_once "JOSE/autoloader.php";
            $jws  = new \Namshi\JOSE\SimpleJWS(array(
                'alg' => $encAlgorithm
            ));
            $jws->setPayload(array(
                'uid' => $userId,
            ));
            $serverFolder = dirname(__FILE__);
            $privateKey = openssl_pkey_get_private('file://' . $serverFolder . '/key/private.pem', 'pass');
            $jws->sign($privateKey);
            $token = $jws->getTokenString();

            echo json_encode(array('id_token' => $token));
        } elseif ($action == 'create_user') {
            if (!$post->email || !$post->password) {
                header('HTTP/1.1 400 You must send the email and the password');
                return;
            }

            $email = $post->email;
            $password = md5($post->password);
            $db = new PDO('mysql:dbname=project_angular2;host=127.0.0.1', 'root', '');
            $query = $db->prepare("insert into user(email, password) values(?, ?)");
            $response = $query->execute(array($email, $password));

            if ($response) {
                echo json_encode(array('id_token' => $password));
            } else {
                echo json_encode(array('error' => 'Something wrong'));
            }
        }
        //var_dump($action);die;
        break;
    case "DELETE":
        // delete stuff
        break;
    case "OPTIONS":
        var_dump($_POST);die;
        // delete stuff
        break;
}

function getDb() {
    return new PDO('mysql:dbname=project_angular2;host=127.0.0.1', 'root', '');
}

function getFormattedToken($authHeader) {
    return str_replace('Bearer ', '', $authHeader);
}

function getPublicKey() {
    $serverFolder = dirname(__FILE__);
    return openssl_pkey_get_public('file://' . $serverFolder . '/key/public.pem');
}