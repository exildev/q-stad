<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PModel
 *
 * @author luism
 */
class PModel {

    private $value;
    private $model;
    private $prefx = null;

    function __construct(Model $model, $value) {
        $this->value = $value;
        $this->model = $model;
    }
    
    public function getModel() {
        return $this->model;
    }

    
    private function prefix($prefix) {
        if ($this->prefx == null) {
            $this->prefx = $prefix;
        } else {
            $this->prefx .= "__" . $prefix;
        }
    }

    public function __call($name, $arguments) {
        $reflex = new ReflectionClass($this->model->get_called_class());
        $properts = $reflex->getProperties();
        foreach ($properts as $propert) {
            $pname = $propert->getName();

            if ($name == "get" . ucfirst($pname)) {
                $value = $this->model->{$name}();
                if ($value instanceof Constrain && $value->getType() == Constrain::FORAIN_KEY) {
                    $refer_package = $value->getReferPackage();
                    $refer_model = $value->getReferModel();
                    Kernel::import($refer_package . "." . ucfirst($refer_model));
                    $refer_object = new $refer_model();
                    $clone = clone $this;
                    $clone->model = $refer_object;
                    $clone->prefix($pname);
                    return $clone;
                } else
                if ($value instanceof Input || ($value instanceof Constrain && $value->getType() == Constrain::PRIMARY_KEY)) {
                    if (isset($this->value[($this->prefx != null ? $this->prefx . "__" : "") . $pname])) {
                        return $this->value[($this->prefx != null ? $this->prefx . "__" : "") . $pname];
                    } else {
                        return null;
                    }
                }
            } else
            if ($name == "set" . ucfirst($pname)) {
                $value = $this->model->{"get" . ucfirst($pname)}();
                if ($value instanceof Input) {
                    $value->set($arguments[0]);
                    $this->value[$pname] = $arguments[0];
                } else
                if ($value instanceof Constrain) {
                    $value->set($arguments[0]);
                    $this->value[$pname] = $arguments[0];
                }
            }
        }
        return null;
    }

    public function get_pk() {
        return $this->value[($this->prefx != null ? $this->prefx . "__" : "") . "pk"];
    }

    public function save() {
        return $this->model->objects()->insert($this->model);
    }
    
    public function delete() {
        $pk = $this->model->get_pk();
        return $this->model->objects()->filter(array("pk = $pk"))->delete();
    }

}

?>
