<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Accept');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$encAlgorithm = 'RS256';
define('ENC_ALG' , 'RS256');
$pictureBasePath = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pictures';
$pictureOriginalPath = $pictureBasePath . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR;
$pictureResizedPath = $pictureBasePath . DIRECTORY_SEPARATOR . 'resized' . DIRECTORY_SEPARATOR;

switch ($requestMethod) {
    case "GET":
        $data = explode('/', $requestUri);
        $action = $data[3];
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
                $query = $db->prepare("select p.id as picture_id, p.filename, p.name, datediff(NOW(), p.date_upload) as days_ago,
                   (select count(id) from picture_like pl1 where pl1.picture_id = p.id) as cnt_like, u.login as user_login, u.avatar as user_avatar, u.id as user_id,
                    EXISTS (select pl1.id from picture_like pl1 where pl1.user_id = f.user_id and pl1.picture_id = p.id) as is_liked, GROUP_CONCAT(distinct t.name) as tags,
                    /*IF (ISNULL(ghp.picture_id), 0, 1) as is_marked*/ GROUP_CONCAT(distinct ghp.gallery_id) as gallery_ids
                    from friend f
                    inner join picture p on p.user_id = f.friend_id
                    inner join user u on u.id = f.friend_id
                    left join picture_like pl on p.id = pl.picture_id
                    left join picture_tag pt on p.id = pt.picture_id
                    left join tag t on t.id = pt.tag_id
                    left join gallery_has_picture ghp on ghp.picture_id = p.id
                    /*left join picture_gallery pg on pg.id = ghp.gallery_id*/
                    where f.user_id = ?
                    group by p.id
                    order by p.date_upload desc");
                $query->execute(array($userId));

                if ($query->rowCount() > 0) {
                    $pictures = $query->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(array('response' => true, 'pictures' => $pictures));
                } else {
                    echo json_encode(array('response' => false));
                    return;
                }
            } else {
                header('HTTP/1.1 401 Unauthorized ');
            }
        } elseif ($action == 'get_unfollow_users') {
            if (isValidToken($token)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];

                $db = getDb();
                $query = $db->prepare("select u.id, u.login, u.avatar, GROUP_CONCAT(p.filename) pictures, count(u.id) as cnt_picture  from user u
                    inner join picture p on p.user_id = u.id
                    where u.id not in (select f.friend_id from friend f where f.user_id = ?) and u.id != ?
                    group by u.id
                    order by u.id
                    limit 3");
                $query->execute(array($userId, $userId));

                if ($query->rowCount() > 0) {
                    $users = $query->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(array('response' => true, 'users' => $users));
                } else {
                    echo json_encode(array('response' => false));
                    return;
                }
            } else {
                header('HTTP/1.1 401 Unauthorized ');
            }
        } elseif ($action == 'comments') {
            $pictureId = $data[4];

            $db = getDb();
            $query = $db->prepare("select datediff(NOW(), pc.date_comment) as days_ago, pc.id as comment_id, pc.comment, u.login as user_login, u.avatar as user_avatar, u.id as user_id
                    from picture_comment pc
                    inner join user u on u.id = pc.user_id
                    where pc.picture_id = ?
                    order by pc.date_comment desc");
            $query->execute(array($pictureId));

            if ($query->rowCount() > 0) {
                $comments = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(array('response' => true, 'comments' => $comments));
            } else {
                echo json_encode(array('response' => false));
            }

            return;
        } elseif ($action == 'galleries') {
            if ($data[4] == 'users') {
                if (isValidToken($token)) {
                    $payload = $jws->getPayload();
                    $userId = $payload['uid'];

                    $db = getDb();
                    /*$query = $db->prepare("select pg.name, pg.id as gallery_id
                    from user_has_gallery uhg
                    inner join picture_gallery pg on pg.id = uhg.gallery_id
                    where uhg.user_id = ?
                    order by pg.name asc");*/
                    $query = $db->prepare("select pg.name, pg.id as gallery_id, GROUP_CONCAT(p.id) as picture_ids
                        from gallery_has_picture ghp
                        left join picture_gallery pg on pg.id = ghp.gallery_id
                        left join picture p on p.id = ghp.picture_id
                        where ghp.user_id = ?
                        group by pg.id
                        order by pg.name asc");
                    $query->execute(array($userId));

                    if ($query->rowCount() > 0) {
                        $galleries = $query->fetchAll(PDO::FETCH_ASSOC);
                        echo json_encode(array('response' => true, 'galleries' => $galleries));
                    } else {
                        echo json_encode(array('response' => false));
                    }
                }
            }

            return;
        }  elseif ($action == 'users') {
            if ($data[4] == 'login') {
                $login = $data[5];

                $db = getDb();
                $query = $db->prepare("select u.login, u.avatar, u.id, u.info, u.page_photo
                        from user u
                        where u.login = ?");
                $query->execute(array($login));

                if ($query->rowCount() > 0) {
                    $user = $query->fetch(PDO::FETCH_ASSOC);
                    echo json_encode(array('response' => true, 'user' => $user));
                } else {
                    echo json_encode(array('response' => false));
                }
            }
        } elseif ($action == 'pictures') {
            if ($data[4] == 'users') {
                $userId = $data[5];

                $db = getDb();
                $query = $db->prepare("select p.name, p.filename, p.resize_height
                        from picture p
                        where p.user_id = ?");
                $query->execute(array($userId));

                if ($query->rowCount() > 0) {
                    $pictures = $query->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(array('response' => true, 'pictures' => $pictures));
                } else {
                    echo json_encode(array('response' => false));
                }
            }
        } elseif ($action == 'tags') {
            $db = getDb();
            $query = $db->prepare("select * from tag order by name");
            $query->execute();

            if ($query->rowCount() > 0) {
                $tags = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(array('response' => true, 'tags' => $tags));
            } else {
                echo json_encode(array('response' => false));
            }
        }

        break;

    case "POST":
        $post = json_decode(file_get_contents('php://input'));
        $data = explode('/', $requestUri);
        $action = $data[3];
        //var_dump($data);die;

        require_once "JOSE/autoloader.php";
        $headers = apache_request_headers();
        $token = getFormattedToken($headers['Authorization']);

        if ($token) {
            $jws = \Namshi\JOSE\SimpleJWS::load($token);
        }

        $publicKey = getPublicKey();

        if ($action == 'create_session') {
            if (!$post->email || !$post->password) {
                header('HTTP/1.1 400 You must send the email and the password');
                return;
            }

            $email = $post->email;
            $password = md5($post->password);
            $db = getDb();
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
            $privateKey = getPrivateKey();
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
            $db = getDb();
            $query = $db->prepare("insert into user(email, password) values(?, ?)");
            $response = $query->execute(array($email, $password));

            if ($response) {
                echo json_encode(array('id_token' => $password));
            } else {
                echo json_encode(array('error' => 'Something wrong'));
            }
        } elseif ($action == 'follow_user') {
            if (!$post->id) {
                header('HTTP/1.1 400 You must send the id');
                return;
            }

            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];

                $friendId = $post->id;

                $db = getDb();
                $query = $db->prepare("insert into friend(user_id, friend_id) values(?, ?)");
                $response = $query->execute(array($userId, $friendId));
                echo json_encode(array('response' => $response));
            } else {
                header('HTTP/1.1 401 Unauthorized');
            }
        } elseif ($action == 'likes') {
            if (!$post->pictureId) {
                header('HTTP/1.1 400 You must send the picture id');
                return;
            }

            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];
                $pictureId = $post->pictureId;

                $db = getDb();
                $query = $db->prepare("insert into picture_like(user_id, picture_id) values(?, ?)");
                $response = $query->execute(array($userId, $pictureId));
                echo json_encode(array('response' => $response));
            }
        } elseif ($action == 'comments') {
            if (!$post->pictureId || !$post->comment) {
                header('HTTP/1.1 400 You must send the picture id and comment text');
                return;
            }

            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];
                $pictureId = $post->pictureId;
                $comment = $post->comment;

                $db = getDb();
                $query = $db->prepare("insert into picture_comment(user_id, picture_id, comment) values(?, ?, ?)");
                $response = $query->execute(array($userId, $pictureId, $comment));
                echo json_encode(array('response' => $response));
            }
        } elseif ($action == 'galleries') {
            if ($data[4] == 'pictures') {
                if (!$post->pictureId || !$post->galleryId) {
                    header('HTTP/1.1 400 You must send the picture id and gallery id');
                    return;
                }

                if ($jws->isValid($publicKey, $encAlgorithm)) {
                    $payload = $jws->getPayload();
                    $userId = $payload['uid'];
                    $pictureId = $post->pictureId;
                    $galleryId = $post->galleryId;

                    $db = getDb();

                    $query = $db->prepare("select * from gallery_has_picture where picture_id = ? and gallery_id = ? and user_id = ?");
                    $query->execute(array($pictureId, $galleryId, $userId));

                    if ($query->rowCount() > 0) {
                        $row = $query->fetch(PDO::FETCH_ASSOC);
                        $query = $db->prepare("delete from gallery_has_picture where id = ?");
                        $response = $query->execute(array($row['id']));
                    } else {
                        $query = $db->prepare("insert into gallery_has_picture(picture_id, gallery_id, user_id) values(?, ?, ?)");
                        $response = $query->execute(array($pictureId, $galleryId, $userId));
                    }

                    echo json_encode(array('response' => $response));
                }
            } else {
                if (!$post->pictureId || !$post->gallery) {
                    header('HTTP/1.1 400 You must send the picture id and gallery text');
                    return;
                }

                if ($jws->isValid($publicKey, $encAlgorithm)) {
                    $payload = $jws->getPayload();
                    $userId = $payload['uid'];
                    $pictureId = $post->pictureId;
                    $galleryName = $post->gallery;

                    $db = getDb();
                    $queryCreateGallery = $db->prepare("insert into picture_gallery(name) values(?)");
                    $queryCreateRelationsGHP = $db->prepare("insert into gallery_has_picture(picture_id, gallery_id, user_id) values(?, ?, ?)");
                    $response = $queryCreateGallery->execute(array($galleryName));
                    $galleryId = $db->lastInsertId();
                    $query = $db->prepare("insert into gallery_has_picture(gallery_id, user_id) values(?, ?)");
                    $response = $response && $queryCreateRelationsGHP->execute(array($pictureId, $galleryId, $userId)) && $query->execute(array($galleryId, $userId));

                    echo json_encode(array('response' => $response));
                }
            }
        } elseif ($action == 'pictures') {
            if ($jws->isValid($publicKey, $encAlgorithm)) {
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
                        $uploadMaxFileSize = getFileSizeBytes($uploadMaxFileSize);

                        //validate file size
                        if ($size > $uploadMaxFileSize) {
                            $valid = false;
                            $response = 'File size is exceeding maximum allowed size.';
                        }

                        if ($valid) {
                            $targetOriginalPath = $pictureOriginalPath . $filename;
                            $targetResizedPath = $pictureResizedPath . $filename;

                            move_uploaded_file($tmpName, $targetOriginalPath);

                            $percent = 0.5;
                            $_width = 450;
                            $_height = 450;


                            list($width, $height) = getimagesize($targetOriginalPath);
                            $newWidth = $width * $percent;
                            $newHeight = $height * $percent;
                            $ratio_orig = $newWidth/$newHeight;

                            if ($_width/$_height > $ratio_orig) {
                                $_width = $_height*$ratio_orig;
                            } else {
                                $_height = $_width/$ratio_orig;
                            }

                            //$thumb = imagecreatetruecolor($newWidth, $newHeight);
                            $thumb = imagecreatetruecolor($_width, $_height);

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

                            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $_width, $_height, $width, $height);
                            imagejpeg($thumb, $targetResizedPath);

                            $payload = $jws->getPayload();
                            $userId = $payload['uid'];

                            $name = preg_replace('/.jpg|.jpeg|.png|.gif/', '', $filename);

                            $picture = getPictureByName($name, $userId);
                            $pictureId = $picture['id'];

                            try {
                                $db = getDb();
                                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $db->beginTransaction();

                                if ($pictureId) {
                                    $query = $db->prepare("update picture set date_upload = NOW() where id = ?");
                                    $query->execute(array($pictureId));
                                } else {
                                    $query = $db->prepare("insert into picture(user_id, name, filename) values(?, ?, ?)");
                                    $query->execute(array($userId, $name, $filename));
                                    $pictureId = $db->lastInsertId();
                                }

                                $tagIds = $_POST['tags'];

                                if ($tagIds != "null") {
                                    $tagIds = explode(',', $tagIds);

                                    foreach ($tagIds as $tagId) {
                                        $db->exec("INSERT INTO picture_tag(picture_id, tag_id) VALUES ('$pictureId', '$tagId')");
                                    }
                                }

                                $response = $db->commit();
                            } catch(PDOException $e) {
                                $db->rollback();
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

                echo json_encode(array('response' => $response, 'errorMsg' => $errorMsg));
            }
        }

//var_dump($action);die;
        break;
    case "DELETE":
        require_once "JOSE/autoloader.php";
        $headers = apache_request_headers();
        $token = getFormattedToken($headers['Authorization']);
        $jws = \Namshi\JOSE\SimpleJWS::load($token);
        $publicKey = getPublicKey();

        $data = explode('/', $requestUri);
        $action = $data[3];
        $id = $data[4];
        //var_dump($data);
        if ($action == 'likes') {
            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];

                $db = getDb();
                $query = $db->prepare("delete from picture_like where user_id = ? and picture_id = ? ");
                $response = $query->execute(array($userId, $id));
                echo json_encode(array('response' => $response));
            }
        } elseif ($action == 'comments') {
            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];

                $db = getDb();
                $query = $db->prepare("delete from picture_comment where user_id = ? and id = ? ");
                $response = $query->execute(array($userId, $id));
                echo json_encode(array('response' => $response));
            }
        } elseif ($action == 'users') {
            if ($jws->isValid($publicKey, $encAlgorithm)) {
                $payload = $jws->getPayload();
                $userId = $payload['uid'];

                $db = getDb();
                $query = $db->prepare("delete from friend where user_id = ? and friend_id = ? ");
                $response = $query->execute(array($userId, $id));
                echo json_encode(array('response' => $response));
            }
        }


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

function getPrivateKey() {
    $serverFolder = dirname(__FILE__);
    return openssl_pkey_get_private('file://' . $serverFolder . '/key/private.pem', 'pass');
}

function isValidToken($token) {
    $jws = \Namshi\JOSE\SimpleJWS::load($token);
    $publicKey = getPublicKey();
    return $jws->isValid($publicKey, ENC_ALG);
}

function getFileSizeBytes($fileSize) {
    $val = trim($fileSize);
    $last = strtolower($val[strlen($val)-1]);

    switch($last) {
        case 'g':
            $val *= 1024 * 1024 * 1024;
            break;
        case 'm':
            $val *= 1024 * 1024;
            break;
        case 'k':
            $val *= 1024;
            break;
        default:
            break;
    }

    return $val;
}

function getPictureByName($name, $userId) {
    $db = getDb();
    $query = $db->prepare("select p.id
                        from picture p
                        inner join user u on u.id = p.user_id
                        where p.name = ? and u.id = ?");
    $query->execute(array($name, $userId));

    return ($query->rowCount() > 0) ? $query->fetch(PDO::FETCH_ASSOC) : null;

}