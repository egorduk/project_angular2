<?php


header("Access-Control-Allow-Origin: *");

if (isset($_GET['action']) && $_GET['action'] == 'get_pictures') {

    $db = new PDO('mysql:dbname=project_angular2;host=127.0.0.1', 'root', '');
    $query = $db->query("select * from picture");
    if ($query->rowCount() > 0) {
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($data);die;
    }

    /*$data = array(
        array('id' => '1', 'name' => 'Cynthia', 'user_id' => '1', 'date_upload' => '2016-06-06', 'link' => 'img/1.jpg'),
        array('id' => '2', 'name' => 'Keith', 'user_id' => '2', 'date_upload' => '2016-06-06', 'link' => 'img/2.jpg'),
        array('id' => '3', 'name' => 'Robert', 'user_id' => '3', 'date_upload' => '2016-06-06', 'link' => 'img/3.jpg'),
        array('id' => '4', 'name' => 'Theresa', 'user_id' => '4', 'date_upload' => '2016-06-06', 'link' => 'img/4.jpg'),
        array('id' => '5', 'name' => 'Margaret', 'user_id' => '5', 'date_upload' => '2016-06-06', 'link' => 'img/5.jpg')
    );*/

    echo json_encode($data);
}
