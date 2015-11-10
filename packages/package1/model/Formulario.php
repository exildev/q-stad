<?php
/**
*   @name: Formulario
*/

Kernel::package('package1');

class Formulario extends Model{
    private $id;
    private $nombre;
    private $fecha_creacion;
    private $encuestador_id;

    public function __construct(){
        $this->id = Constrain::pk($this);
        $this->nombre = Input::create_text('nombre');
        $this->fecha_creacion = Input::create_datetime('fecha_creacion');
        $this->encuestador_id = Constrain::fk('encuestador_id', 'package1.encuestador');
        $this->fecha_creacion->set(date("Y-m-d H:i:s"));
    }

    public function __toString(){
       return " id: " . $this->id->val() .  " nombre: " . $this->nombre->val() .  " fecha_creacion: " . $this->fecha_creacion->val() .  " encuestador_id: " . $this->encuestador_id->val() . '';
    }


    public function setId($id){
       $this->id = $id;
    }

    public function setNombre($nombre){
       $this->nombre = $nombre;
    }

    public function setFecha_creacion($fecha_creacion){
       $this->fecha_creacion = $fecha_creacion;
    }

    public function setEncuestador_id($encuestador_id){
       $this->encuestador_id = $encuestador_id;
    }


    public function getId(){
       return $this->id;
    }

    public function getNombre(){
       return $this->nombre;
    }

    public function getFecha_creacion(){
       return $this->fecha_creacion;
    }

    public function getEncuestador_id(){
       return $this->encuestador_id;
    }

    public function get_called_class(){
       return "Formulario";
    }

}
?>