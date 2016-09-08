<?php

/**
 * Tags model
 */
class TagsModel extends MainModel
{

    //private $pdo = null;

    public function __construct()
    {
        //$this->pdo = new PDO('mysql:dbname=project_angular2;host=127.0.0.1', 'root', '');
        parent::__construct();
    }

    public function getTags()
    {
        $query = $this->pdo->prepare("select * from tag order by name");
        $query->execute();

        if ($query->rowCount() > 0) {
            $tags = $query->fetchAll(PDO::FETCH_ASSOC);
            return array('response' => true, 'tags' => $tags);
        } else {
            return array('response' => false);
        }
    }

}