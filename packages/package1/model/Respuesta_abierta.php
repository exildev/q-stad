<?php

/**
 *   @name: Respuesta_abierta
 */
Kernel::package('package1');

class Respuesta_abierta extends Model {

    private $id;
    private $valor;
    private $user_id;
    private $cerradura;
    private $pregunta_abierta_id;

    public function __construct() {
        $this->id = Constrain::pk($this);
        $this->valor = Input::create_text('valor');
        $this->user_id = Constrain::fk('user_id', 'package1.usuario');
        $this->cerradura = Input::create_text('cerradura');
        $this->pregunta_abierta_id = Constrain::fk('pregunta_abierta_id', 'package1.pregunta_abierta');
    }

    public function __toString() {
        return " id: " . $this->id . " valor: " . $this->valor . " user_id: " . $this->user_id . '';
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function getId() {
        return $this->id;
    }

    public function getValor() {
        return $this->valor;
    }

    public function getUser_id() {
        return $this->user_id;
    }

    public function getPregunta_abierta_id() {
        return $this->pregunta_abierta_id;
    }

    public function setPregunta_abierta_id($pregunta_abierta_id) {
        $this->pregunta_abierta_id = $pregunta_abierta_id;
    }

    public function getCerradura() {
        return $this->cerradura;
    }

    public function setCerradura($cerradura) {
        $this->cerradura = $cerradura;
    }

    public function get_called_class() {
        return "Respuesta_abierta";
    }

}

?>