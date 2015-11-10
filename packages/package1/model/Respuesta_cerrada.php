<?php

/**
 *   @name: Respuesta_cerrada
 */
Kernel::package('package1');

class Respuesta_cerrada extends Model {

    private $id;
    private $valor;
    private $pregunta_cerrada_id;

    public function __construct() {
        $this->id = Constrain::pk($this);
        $this->valor = Input::create_text("valor");
        $this->pregunta_cerrada_id = Constrain::fk('pregunta_cerrada_id', 'package1.pregunta_cerrada');
    }

    public function __toString() {
        return " id: " . $this->id . " valor: " . $this->valor;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getValor() {
        return $this->valor;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function getPregunta_cerrada_id() {
        return $this->pregunta_cerrada_id;
    }

    public function setPregunta_cerrada_id($pregunta_cerrada_id) {
        $this->pregunta_cerrada_id = $pregunta_cerrada_id;
    }

    public function get_called_class() {
        return "Respuesta_cerrada";
    }

}

?>