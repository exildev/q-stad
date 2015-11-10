<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Objects
 *
 * @author luism
 */
class Objects implements Iterator {

    private $databeses;
    private $tables = array();
    private $joinon = array();
    private $wheres = array();
    private $limits = array();
    private $colums = array();
    private $others = array();
    private $refers = array();
    private $frkeys = array();
    private $result = array();
    private $basest = array();
    private $order;
    private $model;
    private $current = 0;
    private $count;
    private $union = null;
    private $group = null;

    public function __construct() {
        $this->databeses = DataBase::getInstance();
    }

    public function len() {
        $response = $this->databeses->execute($this->sql_len());
        return $response[0]['cont'];
    }

    public function update($values) {
        $response = $this->databeses->execute($this->sql_update($values));
        return $response;
    }

    public function delete() {
        $response = $this->databeses->execute($this->sql_delete());
        return $response;
    }

    public function insert(Model $model) {
        $response = $this->databeses->executeId($this->sql_insert($model));
        return $response;
    }

    public function distinct($array) {
        $response = $this->databeses->execute($this->sql_distinct($array));
        return $response;
    }

    public function gets($array) {
        $response = $this->databeses->execute($this->sql_gets($array));
        return $response;
    }

    /**
     * 
     * @param type $index
     * @return PModel
     */
    public function get($index) {
        if (count($this->result) == 0) {
            $this->result = $this->databeses->execute($this->sql_select());
        }
        return new PModel($this->model, $this->result[$index]);
    }

    /**
     * 
     * @param type $table
     * @param type $on
     * @return Objects
     */
    public function join($table, $on) {
        if (!in_array($table, $this->tables)) {
            array_push($this->tables, $table);
        }
        array_push($this->joinon, $on);
        $clone = clone $this;
        return $clone;
    }

    /**
     * 
     * @param type $index
     * @param type $limit
     * @return \Objects
     */
    public function limit($index, $limit) {
        $clone = clone $this;
        $clone->limits[0] = $index;
        $clone->limits[1] = $limit;
        return $clone;
    }

    /**
     * 
     * @param type $column
     * @return \Objects
     */
    public function select($column) {
        array_push($this->colums, $column);
        return $this;
    }

    /**
     * 
     * @param type $column
     * @return \Objects
     */
    public function others($column) {
        array_push($this->others, $column);
        return $this;
    }

    /**
     * 
     * @param type $refer
     * @param type $where
     * @return \Objects
     */
    public function refer($refer, $where) {
        array_push($this->refers, $refer);
        array_push($this->frkeys, $where);
        return $this;
    }
    
    /**
     * 
     * @param type $group
     * @return \Objects
     */
    public function group_by($group) {
        $clone = clone $this;
        $clone->group = $group;
        return $clone;
    }
    
    /**
     * 
     * @param type $order
     * @return \Objects
     */
    public function order_by($order) {
        $clone = clone $this;
        $clone->order = $order;
        return $clone;
    }
    
    /**
     * 
     * @param Objects $object
     * @return \Objects
     */
    public function union(Objects $object) {
        $this->union = $object;
        return $this;
    }

    /**
     * 
     * @param type $wheres
     * @return Objects
     */
    public function filter($wheres) {
        $clone = clone $this;
        $clone->wheres = array_merge($this->wheres, $wheres);
        return $clone;
    }

    private function implode($array, $AND_OR = false) {
        if (is_array($array)) {
            $str = "";
            foreach ($array as $value) {
                $valor = $this->implode($value, !$AND_OR);
                if ($valor != null && $valor != "()") {
                    if ($str != "") {
                        $str .= ($AND_OR ? " OR " : " AND ");
                    }
                    $str .= $valor;
                }
            }
            return "( $str )";
        } else {
            return $array;
        }
    }

    public function sql_distinct($array) {
        $sql = "SELECT DISTINCT " . implode(", ", $array) . " FROM (SELECT " . (count($this->colums) > 0 ? implode(", ", $this->colums) : "*") . " FROM " . implode(" INNER JOIN ", $this->tables) . (count($this->joinon) > 0 ? " ON (" . $this->implode($this->joinon) . ")" : "") . (isset($this->group) ? " GROUP BY " . $this->group : "") . ") as p " . (count($this->wheres) > 0 ? " WHERE (" . $this->implode($this->wheres) . ")" : "") . (count($this->limits) > 0 ? " LIMIT " . $this->limits[0] . ", " . $this->limits[1] : "") . (isset($this->order) ? " ORDER BY " . $this->order : "");
        if ($this->union != null){
            $union = $this->union->sql_distinct($array);
            $sql .= " UNION ($union)";
        }
        return $sql;
    }

    public function sql_gets($array) {
        $sql = "SELECT " . implode(", ", $array) . " FROM (SELECT " . (count($this->colums) > 0 ? implode(", ", $this->colums) : "*") . " FROM " . implode(" INNER JOIN ", $this->tables) . (count($this->joinon) > 0 ? " ON (" . $this->implode($this->joinon) . ")" : "") . (isset($this->group) ? " GROUP BY " . $this->group : "") . ") as p " . (count($this->wheres) > 0 ? " WHERE (" . $this->implode($this->wheres) . ")" : "") . (count($this->limits) > 0 ? " LIMIT " . $this->limits[0] . ", " . $this->limits[1] : "") . (isset($this->order) ? " ORDER BY " . $this->order : "");
        if ($this->union != null){
            $union = $this->union->sql_distinct($array);
            $sql .= " UNION ($union)";
        }
        return $sql;
    }

    public function sql_select() {
        $sql = "SELECT * FROM (SELECT " . (count($this->colums) > 0 ? implode(", ", $this->colums) : "*") . " FROM " . implode(" INNER JOIN ", $this->tables) . (count($this->joinon) > 0 ? " ON (" . $this->implode($this->joinon) . ")" : "") . (isset($this->group) ? " GROUP BY " . $this->group : "") . ") as p " . (count($this->wheres) > 0 ? " WHERE (" . $this->implode($this->wheres) . ")" : "") . (count($this->limits) > 0 ? " LIMIT " . $this->limits[0] . ", " . $this->limits[1] : "") . (isset($this->order) ? " ORDER BY " . $this->order : "");
        if ($this->union != null){
            $union = $this->union->sql_select();
            $sql .= " UNION ($union)";
        }
        return $sql;
    }

    public function sql_update($values) {
        $sql = "UPDATE " . $this->tables[0] . " SET " . (implode(", ", $values)) . (count($this->wheres) > 0 ? " WHERE (" . $this->implode($this->wheres) . ")" : "");
        $sql = str_replace("pk", $this->model->get_pk()->name(), $sql);
        return $sql;
    }

    public function sql_delete() {//
        $sql = "DELETE FROM " . $this->tables[0] .
                " WHERE EXISTS (
                    SELECT * FROM (
                        SELECT " . (count($this->others) > 0 ? implode(", ", $this->others) : "*") .
                " FROM " . (implode(" INNER JOIN ", $this->refers)) .
                " ON (" .
                (count($this->frkeys) > 0 ? $this->implode($this->frkeys) : "") .
                ")" .
                ") as p WHERE " . (count($this->wheres) ? $this->implode($this->wheres) . " AND " : "") . implode(" AND ", $this->basest) .
                ")";
        $sql = str_replace(" pk ", " " . $this->model->get_pk()->name() . " ", $sql);
        return $sql;
    }

    public function sql_insert(Model $model) {
        $sql = "INSERT INTO " . $this->tables[0] . "(" . implode(", ", array_keys($model->getAsArray())) . ") VALUES (" . implode(", ", $model->getAsArray()) . ")";
        return $sql;
    }

    public function sql_len() {
        $sql = "SELECT COUNT(*) as cont FROM (SELECT " . (count($this->colums) > 0 ? implode(", ", $this->colums) : "*") . " FROM " . implode(" INNER JOIN ", $this->tables) . (count($this->joinon) > 0 ? " ON (" . $this->implode($this->joinon) . ")" : "") . ") as p " . (count($this->wheres) > 0 ? " WHERE (" . $this->implode($this->wheres) . ")" : "") . (count($this->limits) > 0 ? " LIMIT " . $this->limits[0] . ", " . $this->limits[1] : "");
        return $sql;
    }

    public static function objects(Model $model) {
        $object = new Objects();
        $object->model = $model;
        $object->tables[0] = $model->get_fullname();
        return $object;
    }

    public static function create(Model $model, $name) {
        $object = new Objects();
        $object->model = $model;
        $object->tables[0] = $name;
        return $object;
    }

    /**
     * 
     * @param Model $model
     * @param Objects $object
     * @return Objects
     */
    public static function by_model(Model $model, Objects & $object = null, $prefix = null) {
        if ($object == null) {
            $object = Objects::objects($model);
        }
        $reflex = new ReflectionClass($model->get_called_class());
        $properts = $reflex->getProperties();
        $fullname = $model->get_fullname();
        foreach ($properts as $propert) {
            $name = $propert->getName();

            $value = $model->{"get" . $name}();
            if ($value instanceof Constrain && $value->getType() == Constrain::FORAIN_KEY) {
                $refer_package = $value->getReferPackage();
                $refer_model = $value->getReferModel();
                Kernel::import($refer_package . "." . ucfirst($refer_model));
                $fullrefer = $value->getFullRefer();
                $refer_object = new $refer_model();
                $pk = $refer_object->get_pk();
                $pk_name = $pk->name();
                $class = strtolower($refer_object->get_called_class());
                if ($class == "user") {
                    $fullclass = $class;
                    $fullon = "$fullname.$name = $class.$pk_name";
                } else {
                    $fullclass = $refer_package . '_' . $class;
                    $fullon = "$fullname.$name = $fullrefer.$pk_name";
                }

                $object->join($fullclass, "$fullon");
                if ($prefix != null) {
                    $object->refer($fullclass, "$fullon");
                } else {
                    $object->refers[0] = $fullclass;
                    array_push($object->basest, "$name = $name" . "__" . "pk");
                }
                $object = self::by_model($refer_object, $object, ($prefix != null ? $prefix . "__" : "") . $name);
            }
            if ($value instanceof Constrain || $value instanceof Input) {
                if ($value instanceof Constrain && $value->getType() == Constrain::PRIMARY_KEY) {
                    $col = "pk";
                } else {
                    $col = $name;
                }
                $object->select($fullname . "." . $name . " AS " . ($prefix != null ? $prefix . "__" : "") . $col);
                if ($prefix != null) {
                    //var_dump($prefix . "---" . $name . "<br>");
                    $object->others($fullname . "." . $name . " AS " . ($prefix != null ? $prefix . "__" : "") . $col);
                }
            }
        }
        return $object;
    }

    public function current() {
        return $this->get($this->current);
    }

    public function key() {
        return $this->current;
    }

    public function next() {
        $this->current++;
    }

    public function rewind() {
        $this->count = $this->len();
        $this->current = 0;
    }

    public function valid() {
        return $this->current < $this->count;
    }

}

?>
