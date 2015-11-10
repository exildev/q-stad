<?php
/**
*   @name: Datos_publicos
*/

Kernel::package('package1');

class Datos_publicos extends Model{
    private $id;
    private $sexo;
    private $institucion;
    private $labor;
    private $oficio;
    private $user_id;

    public function __construct(){
        $this->id = Constrain::pk($this);
        $this->sexo = Input::create_text('sexo');
        $this->institucion = Input::create_text('institucion');
        $this->labor = Input::create_text('labor');
        $this->oficio = Input::create_text('oficio');
        $this->user_id = Constrain::fk('user_id', 'package1.Usuario');

    }

    public function __toString(){
       return " id: " . $this->id .  " sexo: " . $this->sexo .  " institucion: " . $this->institucion .  " labor: " . $this->labor .  " oficio: " . $this->oficio .  " user_id: " . $this->user_id . '';
    }


    public function setId($id){
       $this->id = $id;
    }

    public function setSexo($sexo){
       $this->sexo = $sexo;
    }

    public function setInstitucion($institucion){
       $this->institucion = $institucion;
    }

    public function setLabor($labor){
       $this->labor = $labor;
    }

    public function setOficio($oficio){
       $this->oficio = $oficio;
    }

    public function setUser_id($user_id){
       $this->user_id = $user_id;
    }


    public function getId(){
       return $this->id;
    }

    public function getSexo(){
       return $this->sexo;
    }

    public function getInstitucion(){
       return $this->institucion;
    }

    public function getLabor(){
       return $this->labor;
    }

    public function getOficio(){
       return $this->oficio;
    }

    public function getUser_id(){
       return $this->user_id;
    }

    public function get_called_class(){
       return "Datos_publicos";
    }

}
?>