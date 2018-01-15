<?php
/**
 * Created by PhpStorm.
 * User: chrismedina
 * Date: 3/3/14
 * Time: 12:33 AM
 */

abstract class BaseModel {
    protected $database;
    public function __construct() {
        //$this->database = new PDO("mysql:host=localhost;dbname=test", "username", "password");
    }
}