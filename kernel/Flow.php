<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Flow
 *
 * @author eXile
 */
class Flow {

    const FOMR = 1;
    const F = "__F__";
    const S = "__S__";
    const K = "__K__";
    const T = "__T__";
    const D = "__D__";
    const R = "__R__";

    private $a = 0;
    private $r = "";
    private $c = false;
    private $k = null;
    private $t = null;
    private $d = null;

    public function __construct() {
        $this->k = "__Flow@" . get_called_class() . "__";
        if (!isset($_SESSION)) {
            @session_start();
        }
        if (!isset($_SESSION[self::S])) {
            $_SESSION[self::S] = array();
        }
        if (!isset($_SESSION[self::F])) {
            $_SESSION[self::F] = array();
        }
        if (!isset($_SESSION[self::S][$this->k])) {
            $_SESSION[self::S][$this->k] = array();
        }
        if (!isset($_SESSION[self::F][$this->k])) {
            $_SESSION[self::F][$this->k] = array();
        }
        if (isset($_GET[self::K])) {
            $this->kill_step($_GET[self::K]);
        }
        if (isset($_GET[self::R])) {
            $this->edit_step($_GET[self::R]);
        }
    }

    public function refresh() {
        $this->render('<meta http-equiv="Refresh" content="0;url={{this}}" />');
    }

    public function kill_data() {
        $_SESSION[self::S][$this->k][$this->a] = null;
    }

    public function edit_step($step) {
        $_SESSION[self::S][$this->k][$step] = null; //$_SESSION[self::S][$this->k][$i + 1];
        $this->refresh();
    }

    public function remove_depend($step) {
        for ($i = $step; $i < count($_SESSION[self::S][$this->k]); $i++) {
            if ($_SESSION[self::S][$this->k][$step][self::S] == $_SESSION[self::S][$this->k][$i + 1][self::D]) {
                //echo "d $i = " . ($i + 1) . "<br>";
                unset($_SESSION[self::S][$this->k][$i + 1]);
            }
        }
        $_SESSION[self::S][$this->k] = array_values($_SESSION[self::S][$this->k]);
    }

    public function kill_step($step) {
        //$this->remove_depend($step);
        for ($i = $step; $i < count($_SESSION[self::S][$this->k]); $i++) {
            $_SESSION[self::S][$this->k][$i] = $_SESSION[self::S][$this->k][$i + 1];
        }
        $_SESSION[self::S][$this->k][$i] = null;
        $this->refresh();
    }

    public function end() {
        if (!$this->c) {
            $_SESSION[self::S][$this->k] = null;
            $_SESSION[self::F][$this->k] = null;
        }
    }

    public function go_to($index) {
        $_SESSION[self::F][$this->k] = $index;
    }

    public function get_f() {
        if (isset($_POST[self::F])) {
            $_SESSION[self::F][$this->k] = $_POST[self::F];
            return $_POST[self::F];
        } else
        if (isset($_SESSION[self::F])) {
            return $_SESSION[self::F][$this->k];
        }
        $_SESSION[self::F][$this->k] = 1;
        return 1;
    }

    public function next() {
        $this->go_to($this->get_f() + 1);
    }

    public function prev() {
        $this->go_to($this->get_f() - 1);
    }

    public function get() {
        if (isset($_POST[self::S . $this->a])) {
            $_SESSION[self::S][$this->k][$this->a] = $_POST;
            return $_POST;
        } else
        if (isset($_SESSION[self::S][$this->k][$this->a])) {
            return $_SESSION[self::S][$this->k][$this->a];
        }
        return false;
    }

    public function __call($name, $arguments) {
        $this->a++;
        $this->t = isset($arguments[1]) ? $arguments[1] : $name;
        $this->d = isset($arguments[2]) ? $arguments[2][Flow::S] : null;
        if (!$this->c) {
            if (isset($arguments[0]) && $arguments[0] == self::FOMR) {
                $get = $this->get();
                if ($get) {
                    if ($this->t == $get[self::T]) {
                        return $this->get();
                    } else {
                        $this->a--;
                        return null;
                    }
                } else {
                    $this->render($this->{"form_$name"}($arguments));
                    $this->c = true;
                }
            } else {
                if ($this->get_f() == $this->a) {
                    $this->render($this->{"flow_$name"}($arguments));
                    $this->c = true;
                }
            }
        }
    }

    public function render($html) {
        $keys = array('{{flow_token}}', '{{kill}}', '{{edit}}', '{{this}}');
        $vals = array($this->flow_token(), self::K . '=' . $this->a, self::R . '=' . $this->a, '?ID=' . $_GET['ID']);
        $this->r .= str_replace($keys, $vals, $html);
    }

    public function get_response() {
        return $this->r;
    }

    public function flow_token() {
        return "<input type='hidden' name='" . self::F . "' value='" . ($this->a + 1) . "'>" .
                "<input type='hidden' name='" . self::S . $this->a . "' value='200 ok'>" .
                "<input type='hidden' name='" . self::D . "' value='" . $this->d . "'>" .
                "<input type='hidden' name='" . self::S . "' value='" . $this->a . "'>" .
                "<input type='hidden' name='" . self::T . "' value='" . $this->t . "'>";
    }

    /**
     * only for the eXile kernel
     */
    private $p = "";

    public function render_form(View $view, $url, $array = array(), $depend = null) {
        $this->p = $view->render($url, $array);
        return $this->exile_render(Flow::FOMR, $url, $depend);
    }

    private function form_exile_render() {
        return $this->p;
    }

}

?>
