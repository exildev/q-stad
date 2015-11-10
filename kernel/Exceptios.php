<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Exeptios
 *
 * @author eXile
 */
$__file__ = realpath(__FILE__);
$__self__ = (str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']));

if ($__file__ == $__self__) {
    header('HTTP/1.0 404 Not Found');
    header("Location: index.php");
}

class MaxLengthExceptios extends Exception {

    public function __construct($msg) {
        parent::__construct("El valor asignado al input exede el máximo establecido: " . $msg);
    }

}

class MinLengthExceptios extends Exception {

    public function __construct($msg) {
        parent::__construct("El valor asignado al input exede el mínimo establecido: " . $msg);
    }

}

class TypeExceptios extends Exception {

    public function __construct($msg) {
        parent::__construct("El valor asignado al input no coinside con el tipo establecido: " . $msg);
    }

}

class FileExceptios extends Exception {

    public function __construct($msg) {
        parent::__construct($msg);
    }

}

class FolderNotFoundExceptios extends Exception {

    public function __construct($msg) {
        parent::__construct("La url asignada no coinside con un folder: " . $msg);
    }

}

class UserNotAutoricedFoundExceptios extends Exception {

    public function __construct($msg) {
        parent::__construct("El usuario no esta autorizado para realizar en la accion: " . $msg);
    }

}

?>
