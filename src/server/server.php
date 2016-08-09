<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Accept');
//header("Access-Control-Allow-Origin", '*');
//header('Access-Control-Allow-Methods', 'POST,GET,OPTIONS,PUT,DELETE');
//header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');

/*if (isset($_GET['action']) && $_GET['action'] == 'get_pictures') {

    $db = new PDO('mysql:dbname=project_angular2;host=127.0.0.1', 'root', '');
    $query = $db->query("select * from picture");
    if ($query->rowCount() > 0) {
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($data);die;
    }

    echo json_encode($data);
} elseif (isset($_GET['action']) && $_GET['action'] == 'get_user') {

    $email = $_GET['email'];
    $password = $_GET['password'];
    $password = md5($password);
    $data = false;

    $db = new PDO('mysql:dbname=project_angular2;host=127.0.0.1', 'root', '');
    $query = $db->prepare("select * from user where email = ? and password = ?");
    $query->execute(array($email, $password));
    if ($query->rowCount() > 0) {
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $data = array('user_id' => reset($data)['id']);
        //var_dump($data);die;
    }

    echo json_encode($data);
} elseif (isset($_GET['action']) && $_GET['action'] == 'create_session') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    //$password = md5($password);
    echo json_encode(array('id_token' => '123'));

}*/

//var_dump($_SERVER['REQUEST_METHOD']);die;

/*function __autoload($class) {
    $parts = explode('\\', $class);
    require_once end($parts) . '.php';
}*/

//use Namshi\JOSE;

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

switch ($requestMethod) {
    case "GET":
        // get stuff
        var_dump('get');
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
                'alg' => 'RS256'
            ));
            $jws->setPayload(array(
                'uid' => $userId,
            ));
            $serverFolder = dirname(__FILE__);
            $privateKey = openssl_pkey_get_private('file://' . $serverFolder . '/key/ca.key');
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