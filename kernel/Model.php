<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Model
 *
 * @author eXile
 */
require_once realpath(dirname(__FILE__)) . '/Persistence.php';
require_once realpath(dirname(__FILE__)) . '/../admin/model/AdminUser.php';

abstract class Model {

    private static $package = array();

    /**
     * Use Model::objects();
     * this method will be removed in the next version
     * @deprecated since version 2.0
     * @return \Model
     */
    public static function persistence() {
        $class = get_called_class();
        return new $class();
    }

    /**
     * Use Model::objects()->filter([...]);
     * this method will be removed in the next version
     * @deprecated since version 2.0
     * @param type $array
     * @return array
     */
    public function filter($array = array()) {
        $user = Session::get_user();
        $p = Persistence::getInstance();
        $_model = $this->get_called_class();
        if ($this instanceof User) {
            $_packa = null;
            $array['hashtype'] = $this->getHashType()->val();
        } else {
            $_packa = $this->get_package();
        }
        $resp = $p->filter($user, $_packa, strtolower($_model), $array);
        $arrayo = array();
        $class = get_class($this);
        if ($resp) {
            foreach ($resp as $array) {
                $object = new $class();
                $object->setArray($array);
                array_push($arrayo, $object);
            }
        }
        return $arrayo;
    }

    public function get_fullname() {
        return strtolower($this->get_package() . "_" . $this->get_called_class());
    }

    public static function instance() {
        $class = get_called_class();
        return new PModel(new $class(), array());
    }

    public static function objects() {
        $class = get_called_class();
        return Objects::by_model(new $class());
    }

    public function get_me($primaryk) {
        $pk = $this->get_pk();
        $user = Session::get_user();
        $p = Persistence::getInstance();
        $_model = $this->get_called_class();
        $_packa = $this->get_package();
        if (is_array($primaryk)) {
            $resp = $p->filter($user, $_packa, $_model, $primaryk);
        } else {
            $resp = $p->filter($user, $_packa, $_model, array($pk->name() => $primaryk));
        }
        if ($resp) {
            foreach ($resp as $array) {
                $this->setArray($array);
            }
        }
    }

    public function get_package() {
        if (isset(self::$package[ucfirst($this->get_called_class())])) {
            return self::$package[ucfirst($this->get_called_class())];
        } else {
            return null;
        }
    }

    public static function package($package, $model) {
        self::$package[$model] = $package;
    }

    public abstract function get_called_class();

    public function __toString() {
        return $this->get_package() . '@' . $this->get_called_class();
    }

    public function save() {
        $p = Persistence::getInstance();
        $user = Session::get_user();
        return $p->save($user, $this->get_package(), $this->get_called_class(), $this->getArray());
    }

    public function edit() {
        $p = Persistence::getInstance();
        $user = Session::get_user();
        return $p->edit($user, $this->get_package(), $this->get_called_class(), $this->getArray());
    }

    public function delete() {
        $p = Persistence::getInstance();
        $user = Session::get_user();
        return $p->delete($user, $this->get_package(), $this->get_called_class(), $this->getArray());
    }

    public function setArray(Array $array) {
        $reflex = new ReflectionClass($this->get_called_class());
        $properts = $reflex->getProperties();

        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->{"get" . ucfirst($name)}();

            if ($value instanceof Input) {

                $value->set_array($array);
            } else {
                $name = $propert->getName();
                if (isset($array[$name])) {
                    $this->{"set" . $name}($array[$name]);
                }
            }
        }
    }

    /**
     * @deprecated since version 2.0
     * @return \Input
     */
    public function getArray() {
        $reflex = new ReflectionClass($this->get_called_class());
        $properts = $reflex->getProperties();
        $array = array();
        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->{"get" . $name}();
            if ($value instanceof Constrain) {
                $array[$name] = $value->val();
            } else
            if ($value instanceof Input) {
                $array[$name] = $value->val();
            } else {
                //$array[$name] = $value;
            }
        }
        return $array;
    }

    public function getAsArray() {
        $reflex = new ReflectionClass($this->get_called_class());
        $properts = $reflex->getProperties();
        $array = array();
        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->{"get" . $name}();
            
            if ($value instanceof Input || ($value instanceof Constrain && $value->getType() == Constrain::FORAIN_KEY)){
                $val = $value->val();
                if ($val != null){
                    $array[$name] = "'$val'";
                }
            }
        }
        return $array;
    }

    public function get_pk() {
        $reflex = new ReflectionClass($this->get_called_class());
        $properts = $reflex->getProperties();

        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->{"get" . $name}();

            if ($value instanceof Constrain && $value->getType() == Constrain::PRIMARY_KEY) {
                return $value;
            }
        }
        return null;
    }

}

?>
