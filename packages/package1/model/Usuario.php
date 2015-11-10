<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usuario
 *
 * @author luism
 */
Kernel::package('package1');
class Usuario extends User{

    public function __construct() {
        parent::__construct();
        $this->super = true;
    }
    
    public function get_called_class() {
        return "user";
    }
    
    protected function hash_type() {
        return "User@Usuario";
    }
   
}

?>
