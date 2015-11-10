<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Responde
 *
 * @author luism
 */
Kernel::package('package1');

class Responde extends Model {

    private $user_id;
    private $respuesta_cerrada_id;

    public function __construct() {
        $this->user_id = Constrain::fk('user_id', 'package1.usuario');
        $this->respuesta_cerrada_id = Constrain::fk('respuesta_cerrada_id', 'package1.respuesta_cerrada');
    }

    public function getUser_id() {
        return $this->user_id;
    }

    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function getRespuesta_cerrada_id() {
        return $this->respuesta_cerrada_id;
    }

    public function setRespuesta_cerrada_id($respuesta_cerrada_id) {
        $this->respuesta_cerrada_id = $respuesta_cerrada_id;
    }

    public function get_called_class() {
        return "responde";
    }

}

?>
