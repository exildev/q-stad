<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Access
 *
 * @author temp
 */
class Access {

    const All = 30;
    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 5;
    
    private $package;
    private $model;
    private $access;
    
    private function __construct($package, $model, $access) {
        $this->package = $package;
        $this->model = $model;
        $this->access = $access;
    }

    public static function access_to($package, $model, $access) {
        return new Access($package, $model, $access);
    }
    
    public function getPackage() {
        return $this->package;
    }

    public function getModel() {
        return $this->model;
    }

    public function getAccess() {
        return $this->access;
    }

}

?>
