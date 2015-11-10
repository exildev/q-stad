<?php

/**
 *   @name: Pregunta_abierta
 */
Kernel::package('package1');

class Pregunta_abierta extends Model {

    private $id;
    private $pregunta_id;

    public function __construct() {
        $this->id = Constrain::pk($this);
        $this->pregunta_id = Constrain::fk('pregunta_id', 'package1.pregunta');
    }

    public function __toString() {
        return " id: " . $this->id->val() . " pregunta_id: " . $this->pregunta_id->val() . '';
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setPregunta_id($pregunta_id) {
        $this->pregunta_id = $pregunta_id;
    }

    public function getId() {
        return $this->id;
    }

    public function getPregunta_id() {
        return $this->pregunta_id;
    }

    public function get_called_class() {
        return "Pregunta_abierta";
    }

}

?>