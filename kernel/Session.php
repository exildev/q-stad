<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Session
 *
 * @author eXile
 */
$__file__ = realpath(__FILE__);
$__self__ = (str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']));

if ($__file__ == $__self__) {
    header('HTTP/1.0 404 Not Found');
    header("Location: index.php");
}

require_once realpath(dirname(__FILE__)) . '/User.php';

class Session {

    const USER = '__user__';

    //put attr code here
    public static function start(User $user = null) {
        if (!isset($_SESSION)) {
            @session_start();
            if ($user != null) {
                self::set(self::USER, $user);
            }
        }
    }

    public static function destroy() {
        self::start();
        @session_destroy();
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        self::start();
        return isset($_SESSION[$key])?$_SESSION[$key]:null;
    }

    /**
     * 
     * @return User
     */
    public static function get_user() {
        $user = self::get(self::USER);
        if ($user != null) {
            return $user;
        }
        return null;
    }

}

?>
