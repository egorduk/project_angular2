<?php

/**
 * Main controller extended by all other models
 */
class MainModel
{

    protected $pdo = null;

    protected function __construct()
    {
        $this->pdo = new PDO('mysql:dbname=project_angular2;host=127.0.0.1', 'root', '');
    }

}