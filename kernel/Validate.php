<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Validate
 *
 * @author temp
 */
class Validate {

    private $pattern;
    private $error;

    function __construct($pattern, $error) {
        $this->pattern = $pattern;
        $this->error = $error;
    }
    
    public function validate($value) {
        return preg_match($this->pattern, $value) > 0;
    }
    
}



?>
