<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin
 *
 * @author eXile
 */
class AdminUser extends User {

    function __construct() {
        parent::__construct();
        $this->super = true;
    }

    protected function hash_type() {
        return "User@AdminUser";
    }
    

}

?>
