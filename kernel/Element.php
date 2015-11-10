<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Element
 *
 * @author luism
 */
class Element {
    public static $objects;
    
    public function __construct() {
        
    }
    //put your code here
    public function __wakeup() {
        Element::objects();
    }
}

?>
