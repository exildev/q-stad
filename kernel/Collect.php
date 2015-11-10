<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collect
 *
 * @author luism
 */
class Collect {

    private static $sql = "SELECT * {{model}}";
    private $filter = [];
    private $model;
    private $tables = [];
    private $constr = [];
    private $by = null;
    private $index = null;
    private $limit = null;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    /**
     * 
     * @param type $array
     * @return Collect
     */
    public function filter($array) {
        $this->filter = array_merge($this->filter, $array);
        return clone $this;
    }

    public function order_by($by) {
        $this->by = $by;
        $resp = [];
        preg_match("/(?<constrain>\w+)__(?P<column>\w+)(?P<value>((\w|\$|=| |;|\(|\)|<|>|\+|\n|\[|]|\"|'|\t)+))/", $by, $resp);

        if (isset($resp['constrain']) && isset($resp['column']) && isset($resp['value'])) {
            $const = $this->model->{'get' . $resp['constrain']}();
            array_push($this->tables, str_replace(".", "_", $const->getRefer()));
            array_push($this->constr, str_replace(".", "_", $const->getRefer() . ".id = "));
            $this->by = str_replace(".", "_", $const->getRefer()) . "." . $resp['column'] . $resp['value'];
        }
    }

    public function limit($limit, $index = 0) {
        $this->index = $index;
        $this->limit = $limit;
    }

    public function implode($array, $AND_OR = false) {
        if (is_array($array)) {
            $str = "";
            foreach ($array as $key => $value) {
                $valor = $this->implode($value, !$AND_OR);
                if ($valor != null && $valor != "()") {
                    if ($str != "") {
                        $str .= ($AND_OR ? " OR " : " AND ");
                    }
                    $str .= $valor;
                }
            }
            return "($str)";
        } else {
            return $array;
        }
    }

    public function sql() {
        $this->tables = [];

        array_push($this->tables, $this->model->get_package() . "_" . lcfirst($this->model->get_called_class()));
        $impls = $this->implode($this->filter);
        $tabls = implode(", ", $this->tables);
        $sql = "SELECT * FROM " . $tabls;
        if ($impls != "()") {
            $sql .= " WHERE " . $impls;
        }
        return $sql;
    }

    public function get() {
        $user = Session::get_user();
        $p = Persistence::getInstance();
        $_model = $this->model->get_called_class();
        $array = $this->filter;
        if ($this->model instanceof User) {
            $_packa = null;
            $array['hashtype'] = $this->model->getHashType()->val();
        } else {
            $_packa = $this->model->get_package();
        }
        $resp = $p->filter($user, $_packa, strtolower($_model), $array);
        $arrayo = array();
        $class = get_class($this->model);
        if ($resp) {
            foreach ($resp as $array) {
                $object = new $class();
                $object->setArray($array);
                array_push($arrayo, $object);
            }
        }
        return $arrayo;
    }

}
?>
