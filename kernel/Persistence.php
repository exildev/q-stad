<?php

/**
 * Description of Persistence
 *
 * @author eXile
 */
require_once realpath(dirname(__FILE__)) . '/../config/config.php';
require_once realpath(dirname(__FILE__)) . '/sql/' . Config::dbmg . '.php';
require_once realpath(dirname(__FILE__)) . '/User.php';

class Persistence {

    private static $FILTER = "filter";
    private static $DELETE = "delete";
    private static $SAVE = "save";
    private static $EDIT = "edit";
    private static $INVOKE = "invoke";
    private static $CREATE = "create";

    public function autenticate(User & $user) {
        $table = strtolower($user->get_called_class());
        $array = array(
            'username' => $user->getUsername()->get(),
            'password' => $user->getPassword()->get(),
            'hashtype' => $user->getHashtype()->get()
        );
        $sql = $this->sql(array('query' => self::$FILTER, 'table' => $table, 'data' => $array));
        $db = DataBase::getInstance();

        $resp = $db->execute($sql);

        if ($resp && count($resp)) {
            $user->setVerified(true);
            $user->setArray($resp[0]);
            return true;
        }
        return false;
    }

    public function filter(User $user, $package, $model, $array) {
        if ($user->getVerified() && $user->access($package, $model, Access::SELECT)) {
            $table = isset($package) ? strtolower($package . "_" . $model) : strtolower($model);
            $sql = $this->sql(array('query' => self::$FILTER, 'table' => $table, 'data' => $array));
            $db = DataBase::getInstance();
            //var_dump($sql);
            $r = $db->execute($sql);
            return $r;
        } else {
            header('HTTP/1.0 401 Unauthorized');
            exit(0);
        }
    }

    public function delete(User $user, $package, $model, $array) {
        if ($user->getVerified() && $user->access($package, $model, Access::DELETE)) {
            $table = isset($package) ? strtolower($package . "_" . $model) : strtolower($model);
            $sql = $this->sql(array('query' => self::$DELETE, 'table' => $table, 'data' => $array));
            $db = DataBase::getInstance();
            //var_dump($sql);
            return $db->execute($sql);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            exit(0);
        }
    }

    public function invoke(User $user, $package, $model, $array) {
        if ($user->getVerified() && $user->access($package, $model, Access::SELECT)) {
            $table = isset($package) ? strtolower($package . "_" . $model) : strtolower($model);
            $sql = $this->sql(array('query' => self::$INVOKE, 'routine' => $table, 'data' => $array));
            $db = DataBase::getInstance();
            //var_dump($sql);
            return $db->execute($sql);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            exit(0);
        }
    }

    public function save(User $user, $package, $model, $array) {
        if ($user->getVerified() && $user->access($package, $model, Access::UPDATE)) {
            $table = isset($package) ? strtolower($package . "_" . $model) : strtolower($model);
            $sql = $this->sql(array('query' => self::$SAVE, 'table' => $table, 'data' => $array));
            $db = DataBase::getInstance();
            //var_dump($sql); 
            return $db->executeId($sql);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            exit(0);
        }
    }

    public function edit(User $user, $package, $model, $array) {
        if ($user->getVerified() && $user->access($package, $model, Access::UPDATE)) {
            $table = isset($package) ? strtolower($package . "_" . $model) : strtolower($model);
            $sql = $this->sql(array('query' => self::$EDIT, 'table' => $table, 'data' => $array));
            $db = DataBase::getInstance();
            //var_dump($sql);
            return $db->execute($sql);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            exit(0);
        }
    }

    public function create(User $user, $package, $model, $array) {
        if ($user->getVerified() && $user->access($package, $model, Access::INSERT)) {
            $table = strtolower($package . "_" . $model);
            $sql = $this->sql(array('query' => self::$CREATE, 'table' => $table, 'data' => $array));
            $db = DataBase::getInstance();
            //var_dump($sql);
            return $db->execute($sql);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            exit(0);
        }
    }

    private function make_where(Array $data) {
        $sql = "";
        if (count($data) > 0) {
            $sql.= "WHERE ";
            foreach ($data as $key => $value) {
                if (isset($value)) {
                    if ($sql != "WHERE ") {
                        $sql.= " AND ";
                    }
                    if (is_array($value)) {
                        $split = implode(", ", $value);
                        $sql.= "`$key`" . " IN ($split)";
                    } else {
                        $sql.= "`$key`" . " = '" . $value . "' ";
                    }
                }
            }
        }
        return $sql;
    }

    private function make_call(Array $data) {
        $valus = "";

        foreach ($data as $value) {
            if ($valus != "") {
                $valus.= ", ";
            }
            $valus.= "'$value'";
        }
        return $valus;
    }

    private function make_values(Array $data) {
        $attrs = "";
        $valus = "";

        foreach ($data as $key => $value) {
            if ($value instanceof PArray) {
                $value = $value->getName();
            } else {
                if (isset($value) && $value != '') {
                    $value = "'" . $value . "'";
                } else {
                    $value = "NULL";
                }
            }
            if ($attrs == "") {
                $attrs.= "`$key`";
                $valus.= $value;
            } else {
                $attrs.= ", " . "`$key`";
                $valus.= ", " . $value;
            }
        }
        $sql = "($attrs) VALUES ($valus)";
        return $sql;
    }

    private function make_set(Array $data) {
        $sql = "";
        foreach ($data as $key => $value) {
            if ($key != 'id') {
                if ($sql != "") {
                    $sql.= ", ";
                }
                if ($value instanceof PArray) {
                    $sql.= "`$key`" . " = " . $value->getName() . "";
                } else {
                    if (isset($value) && $value != '') {
                        $sql.= "`$key`" . " = '" . $value . "'";
                    } else {
                        $sql.= "`$key`" . " = NULL";
                    }
                }
            }
        }
        $sql.= " WHERE id = '" . $data['id'] . "'";
        return $sql;
    }

    private function make_columns(Array $data) {
        $sql = "";
        foreach ($data as $key => $value) {
            if ($key != 'id') {
                if ($sql != "") {
                    $sql.= ", ";
                }
                $sql.= "`$key`" . " " . $value["type"] . " " . ($value["ai"] ? 'AUTO_INCREMENT' : '') . " " . $value["index"] . " NOT NULL";
            }
        }
        $sql.= " WHERE id = '" . $data['id'] . "'";
        return $sql;
    }

    public function sql($value) {
        $sql = "";
        switch ($value['query']) {
            case self::$CREATE: {
                    $sql.= "CREATE TABLE " . $value['table'] . " (" . $this->make_columns($value["data"]) . ")  ENGINE = INNODB;";
                    break;
                }
            case self::$FILTER: {
                    $sql.= "SELECT * FROM " . $value['table'] . " " . $this->make_where($value["data"]) . ";";
                    break;
                }
            case self::$DELETE: {
                    $sql.= "DELETE FROM " . $value['table'] . " " . $this->make_where($value["data"]) . ";";
                    break;
                }
            case self::$INVOKE: {
                    $sql.= "CALL " . $value['routine'] . "(" . $this->make_call($value["data"]) . ");";
                    break;
                }
            case self::$EDIT: {
                    $sql.= "UPDATE " . $value['table'] . " SET " . $this->make_set($value["data"]) . ";";
                    break;
                }
            case self::$SAVE: {
                    $sql.= "INSERT INTO " . $value['table'] . " " . $this->make_values($value["data"]) . "";
                    break;
                }
        }
        return $sql;
    }

    /**
     * 
     * @return Persistence
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static $instance;

}

?>
