<?php

/**
 * Description of User
 *
 * @author temp
 */
require_once realpath(dirname(__FILE__)) . '/Model.php';
require_once realpath(dirname(__FILE__)) . '/Access.php';

Kernel::package('kernerl');

abstract class User extends Model {

    //put attr code here
    private $id;
    private $username;
    private $password;
    private $hashtype;
    private $verified;
    protected $super;

    function __construct() {
        $this->id = Constrain::pk($this);
        $this->username = Input::create_text('username');
        $this->password = Input::create_password('password');
        $this->hashtype = Input::create_hidden('hashtype');
        $this->hashtype->set($this->hash_type());
        $this->super = false;
    }

    public function autenticate() {
        $p = Persistence::getInstance();
        return $p->autenticate($this);
    }
    
    public function getSuper() {
        return $this->super;
    }
    
    public function get_fullname() {
        return "user";
    }

    public function access($package, $model, $access = null) {
        if ($this->super == true){
            if ($access != null) {
                
                return 'true';
            }
            return Access::All;
        }
        $reflex = new ReflectionObject($this);
        $properts = $reflex->getProperties();

        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->{"get" . $name}();
            if (is_object($value) && $value instanceof Access) {
                if ($model == $value->getModel() && $package == $value->getPackage()) {
                    if ($access != null) {
                        return $value->getAccess() % $access == 0;
                    }
                    return $value->getAccess();
                }
            }
        }
        return False;
    }

    abstract protected function hash_type();

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getHashtype() {
        return $this->hashtype;
    }

    public function setHashtype($hashtype) {
        $this->hashtype = $hashtype;
    }

    public function setVerified($verified) {
        $this->verified = $verified;
    }

    public function getVerified() {
        return $this->verified;
    }

    public function get_called_class() {
        return "User";
    }

}

?>