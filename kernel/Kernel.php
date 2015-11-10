<?php
/**
 * Description of Kernel
 *
 * @author eXile
 */
require_once realpath(dirname(__FILE__)) . '/Access.php';
require_once realpath(dirname(__FILE__)) . '/Input.php';
require_once realpath(dirname(__FILE__)) . '/Constrain.php';
require_once realpath(dirname(__FILE__)) . '/Model.php';
require_once realpath(dirname(__FILE__)) . '/Routine.php';
require_once realpath(dirname(__FILE__)) . '/User.php';
require_once realpath(dirname(__FILE__)) . '/Session.php';
require_once realpath(dirname(__FILE__)) . '/Show.php';
require_once realpath(dirname(__FILE__)) . '/User.php';
require_once realpath(dirname(__FILE__)) . '/View.php';
require_once realpath(dirname(__FILE__)) . '/Validate.php';
require_once realpath(dirname(__FILE__)) . '/PModel.php';
require_once realpath(dirname(__FILE__)) . '/Objects.php';
require_once realpath(dirname(__FILE__)) . '/Flow.php';

class Kernel {
    private static $model;
    
    public static function import($import) {
        $parts = explode('.', $import);
        $packg = $parts[0];
        $model = $parts[1];
        self::$model = $model;
        require_once realpath(dirname(__FILE__)) . '/../packages/' . $packg . '/model/' . ucfirst($model) . '.php';
    }

    /**
     * 
     * @param type $import
     * @return Model
     */
    public static function instance($import) {
        $parts = explode('.', $import);
        $packg = $parts[0];
        $model = $parts[1];
        self::$model = ucfirst($model);
        if ($packg == 'kernel'){
            require_once realpath(dirname(__FILE__)) . '/' . self::$model . '.php';
        }else{
            require_once realpath(dirname(__FILE__)) . '/../packages/' . $packg . '/model/' . self::$model . '.php';
        }
        
        $ref = new ReflectionClass($model);
        return $ref->newInstanceArgs();
    }

    public static function package($package) {
        Model::package($package, self::$model);
    }
    
    public static function model(){
        return self::$model;
    }

}

?>
