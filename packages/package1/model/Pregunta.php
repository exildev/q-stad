<?php

/**
 *   @name: Pregunta
 */
Kernel::package('package1');

class Pregunta extends Model {

    const CERRADA = '0';
    const ABIERTA = '1';

    private $id;
    private $enunciado;
    private $tipo;
    private $formulario_id;

    public function __construct() {
        $this->id = Constrain::pk($this);
        $this->enunciado = Input::create_text('enunciado');
        $this->tipo = Input::create_select('tipo', array(self::CERRADA => 'Cerrada', self::ABIERTA => 'Abierta'));
        $this->formulario_id = Constrain::fk('formulario_id', 'package1.formulario');
    }

    public function __toString() {
        return " id: " . $this->id->val() . " enunciado: " . $this->enunciado->val() . " tipo: " . $this->tipo->val() . " formulario_id: " . $this->formulario_id->val() . '';
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setEnunciado($enunciado) {
        $this->enunciado = $enunciado;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setFormulario_id($formulario_id) {
        $this->formulario_id = $formulario_id;
    }

    public function getId() {
        return $this->id;
    }

    public function getEnunciado() {
        return $this->enunciado;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getFormulario_id() {
        return $this->formulario_id;
    }

    public function get_called_class() {
        return "Pregunta";
    }

}

?>