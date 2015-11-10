<?php
/**
*   @name: Encuestador
*/

Kernel::package('package1');

class Encuestador extends Model{
    private $id;
    private $datos_personales_id;
    private $user_id;

    public function __construct(){
        $this->id = Constrain::pk($this);
        $this->datos_personales_id = Constrain::fk('datos_personales_id', 'package1.datos_personales');
        $this->user_id = Constrain::fk('user_id', 'package1.usuario');

    }

    public function __toString(){
       return " id: " . $this->id->val() .  " datos_personales_id: " . $this->datos_personales_id->val() .  " user_id: " . $this->user_id->val() . '';
    }


    public function setId($id){
       $this->id = $id;
    }

    public function setDatos_personales_id($datos_personales_id){
       $this->datos_personales_id = $datos_personales_id;
    }

    public function setUser_id($user_id){
       $this->user_id = $user_id;
    }


    public function getId(){
       return $this->id;
    }

    public function getDatos_personales_id(){
       return $this->datos_personales_id;
    }

    public function getUser_id(){
       return $this->user_id;
    }

    public function get_called_class(){
       return "Encuestador";
    }

}
?>