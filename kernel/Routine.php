<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Routine
 *
 * @author eXile
 */
$__file__ = realpath(__FILE__);
$__self__ = (str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']));

if ($__file__ == $__self__) {
    header('HTTP/1.0 404 Not Found');
    header("Location: index.php");
}

require_once realpath(dirname(__FILE__)) . '/Model.php';

abstract class Routine extends Model{
    
    public function invoke() {
        
        $p = Persistence::getInstance();
        $_model = $this->get_called_class();
        $params = $this->getArray();
        $resp = $p->invoke($_model, $params);
        $arrayo = array();
        if ($resp) {
            foreach ($resp as $array) {
                
                array_push($arrayo, $array);
            }
        }
        return $arrayo;
    }
}

?>
