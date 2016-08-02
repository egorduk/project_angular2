<?php


header("Access-Control-Allow-Origin: *");

if (isset($_GET['action']) && $_GET['action'] == 'get_pictures') {

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
}
