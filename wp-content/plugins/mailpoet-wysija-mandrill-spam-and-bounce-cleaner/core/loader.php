<?php
/**
 * Created by PhpStorm.
 * User: chrismedina
 */

/**
 * Loads controllers
 */

class Loader {
    private $controller;
    private $action;
    private $urlvalues;

    public function __construct($urlvalues) {

        $this->urlvalues = $urlvalues;
        if ($this->urlvalues['controller'] == "") {
            $this->controller = "WNC_Mandrill_Cleaner";
        } else {
            $this->controller = $this->urlvalues['controller'];
        }

        if($this->urlvalues['action'] == "") {
            $this->action = "index";
        } else {
            $this->action = $this->urlvalues['action'];
            //echo $this->action;
        }

    }

    public function CreateController() {
        if(class_exists($this->controller)) {
            $parents = class_parents($this->controller);
            if(in_array("WNC_BaseController", $parents)) {
                //does the class contain the requested method?
                if(method_exists($this->controller, $this->action)) {
                    return new $this->controller($this->action,$this->urlvalues);
                } else {
                    //bad method error
                    throw new Exception("Bad Method call. Method does not exist.");
                    return new Error("badUrl", $this->urlvalues);
                }
            } else {
                //bad controller error
                throw new Exception("Bad Controller.");
                return new Error("badUrl", $this->urlvalues);
            }

        } else {
            //bad controller(class doesn't exist) error
            throw new Exception("Bad Controller. Class:" . $this->controller . ", doesn't exist.");
            return new Error("badUrl", $this->urlvalues);
        }
    }
}