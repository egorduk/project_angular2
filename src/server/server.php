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

        if ($action == 'get_friends_pictures') {
            require_once "JOSE/autoloader.php";
            $headers = apache_request_headers();
            $token = getFormattedToken($headers['Authorization']);
            $jws = \Namshi\JOSE\SimpleJWS::load($token);
            $serverFolder = dirname(__FILE__);
            $publicKey = openssl_pkey_get_public('file://' . $serverFolder . '/key/public.pem');

            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];

                $db = getDb();
                $query = $db->prepare("select p.filename, u.login from friend f
                    inner join picture p on p.user_id = f.friend_id
                    inner join user u on u.id = f.friend_id
                    where f.user_id = ?
                    order by p.date_upload desc");
                $query->execute(array($userId));

                if ($query->rowCount() > 0) {
                    $arrGroupedPictures = [];
                    $pictures = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach($pictures as $key => $pic) {
                        $arrGroupedPictures[$pic['login']][] = $pic['filename'];
                    }
                    echo json_encode($arrGroupedPictures);
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