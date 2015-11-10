<?php
/**
*   @name: Datos_personales
*/

Kernel::package('package1');

class Datos_personales extends Model{
    private $id;
    private $identificacion;
    private $nombres;
    private $apellidos;
    private $telefono;
    private $email;
    private $direccion;

    public function __construct(){
        $this->id = Constrain::pk($this);
        $this->identificacion = Input::create_text('identificacion');
        $this->nombres = Input::create_text('nombres');
        $this->apellidos = Input::create_text('apellidos');
        $this->telefono = Input::create_text('telefono');
        $this->email = Input::create_text('email');
        $this->direccion = Input::create_text('direccion');

    }

    public function __toString(){
       return " id: " . $this->id->val() .  " identificacion: " . $this->identificacion->val() .  " nombres: " . $this->nombres->val() .  " apellidos: " . $this->apellidos->val() .  " telefono: " . $this->telefono->val() .  " email: " . $this->email->val() .  " direccion: " . $this->direccion->val() . '';
    }


    public function setId($id){
       $this->id = $id;
    }

    public function setIdentificacion($identificacion){
       $this->identificacion = $identificacion;
    }

    public function setNombres($nombres){
       $this->nombres = $nombres;
    }

    public function setApellidos($apellidos){
       $this->apellidos = $apellidos;
    }

    public function setTelefono($telefono){
       $this->telefono = $telefono;
    }

    public function setEmail($email){
       $this->email = $email;
    }

    public function setDireccion($direccion){
       $this->direccion = $direccion;
    }


    public function getId(){
       return $this->id;
    }

    public function getIdentificacion(){
       return $this->identificacion;
    }

    public function getNombres(){
       return $this->nombres;
    }

    public function getApellidos(){
       return $this->apellidos;
    }

    public function getTelefono(){
       return $this->telefono;
    }

    public function getEmail(){
       return $this->email;
    }

    public function getDireccion(){
       return $this->direccion;
    }

    public function get_called_class(){
       return "Datos_personales";
    }

}
?>