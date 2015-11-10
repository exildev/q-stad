<?php

/**
 * Description of Colection
 *
 * @author luism
 */
class Colection implements Iterator {

    Const TABLES = "__TABLES__";
    Const ON = "__ON__";

    /**
     * array(Alias => ColumnName, ...)
     * @var Array
     */
    private $selects = [];

    /**
     * <pre>array(<br>  array(<br>      "TABLES" => array(<br>          Alias => TableName,<br>          ...<br>      ),<br>      "ON" => array(Condition, ...)<br> ),<br>  ...<br>)</pre>
     * @var Array 
     */
    private $froms = [];

    /**
     * array(Contion, ...)
     * @var Array
     */
    private $wheres = [];

    public function __construct() {
        
    }

    public function select($table, $alias = false) {
        $this->selects[($alias ? $alias : $table)] = $table;
    }

    public function join($table1, $alias1, $table2, $alias2, $on) {
        $alias1 = ($alias1 == null? $table1:$alias1);
        $alias2 = ($alias2 == null? $table2:$alias2);
        array_push($this->froms, [self::TABLES => [$alias1 => $table1, $alias2 => $table2], self::ON => $on]);
    }

    public function from($table, $alias) {
        array_push($this->froms, [self::TABLES => [$alias => $table]]);
    }

    public function where($condition) {
        array_push($this->wheres, $condition);
    }

    public function sql_selects() {
        $sql = "";
        if (count($this->selects) == 0) {
            return "*";
        }
        else
            foreach ($this->selects as $alias => $select) {
                $sql .= ($sql != "" ? ", " : "");
                $sql .= "$select as $alias";
            }
        return $sql;
    }

    public function sql_froms() {
        $sql = "";
        foreach ($this->froms as $table_on) {
            $sql_tables = "";
            $tables = $table_on[self::TABLES];
            foreach ($tables as $alias => $table) {
                $sql_tables .= ($sql_tables != "" ? " INNER JOIN " : "");
                $sql_tables .= "$table as $alias";
            }
            $sql .= ($sql != "" ? " INNER JOIN " : "");
            if (isset($table_on[self::ON])) {
                $sql_ons = "";
                $ons = $table_on[self::ON];
                foreach ($ons as $on) {
                    $sql_ons .= ($sql_ons != "" ? " AND " : "");
                    $sql_ons .= "$on";
                }
                $sql .= "($sql_tables ON ($sql_ons))";
            }else{
                $sql .= "($sql_tables)";
            }
            
            
        }
        return $sql;
    }

    public function sql_wheres() {
        return $this->implode($this->wheres);
    }

    public function get_sql_select() {
        $select = $this->sql_selects();
        $from = $this->sql_froms();
        $where = $this->sql_wheres();
        $sql = "SELECT $select FROM $from WHERE $where";
        return $sql;
    }

    public function get_sql_update($table, $updates) {
        $select = $this->sql_selects();
        $from = $this->sql_froms();
        $where = $this->sql_wheres();
        $update = implode(", ", $updates);
        $sql = "UPDATE $table SET $update WHERE EXIST (SELECT $select FROM $from WHERE $where)";
        return $sql;
    }

    public function get_sql_delete($table) {
        $select = $this->sql_selects();
        $from = $this->sql_froms();
        $where = $this->sql_wheres();
        $sql = "DELETE FROM $table WHERE EXIST (SELECT $select FROM $from WHERE $where)";
        return $sql;
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
    
    public function update(Model $model, $array, $update) {
        foreach ($array as $key => $value) {
            $colsn = explode(" = ", $value);
            $cols = explode("__", $colsn[0]);
            $object = $model;
            foreach ($cols as $col) {
                $const = $object->{"get" . ucfirst($col)}();
                if ($const instanceof Constrain){
                    $refer = $const->getRefer();
                    Kernel::import($refer);
                    $class = $const->getReferModel();
                    $fulln = $const->getFullRefer();
                    $name = $const->name();
                    
                    if ($object == $model){
                        $this->where("$col = $refer.$name");
                    }else{
                        $this->join($object->get_fullname(), null, $fulln, null, "$col = $refer.$name");
                    }
                    $object = new $class();
                }else{
                    return false;
                }
            }
        }
        return $this->get_sql_update($model->get_fullname(), $update);
    }

    public function current() {
        
    }

    public function key() {
        
    }

    public function next() {
        
    }

    public function rewind() {
        
    }

    public function valid() {
        
    }

}

?>
