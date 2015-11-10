<?php

/**
 * Description of View
 *
 * @author eXile
 */
class Free {

    private $obj;

    function __construct($obj) {
        $this->obj = $obj;
    }

    public function __get($name) {
        return $this->obj->{'get' . ucfirst($name)}();
    }

    public function __toString() {
        return $this->obj->__toString();
    }

}

class View {

    private $base;
    protected $language = array();
    protected $system = array(
        'html' => '?ID=',
        'img' => '?ID=img&src=img/',
        'css' => '?ID=css&src=css/',
        'js' => '?ID=js&src=js/',
        'l-css' => '?ID=css&src=../../../lib/css',
        'l-js' => '?ID=js&src=../../../lib/js'
    );

    function __construct($base) {
        $this->base = $base;
        $this->setLanguage(Config::lang);
    }

    public function response($url, $content_type = "text/html") {
        header('Content-Type: ' . $content_type);
        $html = $this->render($url, array(), true);
        return $html;
    }

    public function getPOST($key, $callback) {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        } else {
            return $callback;
        }
    }
    
    public function getFILES() {
        return $_FILES;
    }

    public function getGET($key, $callback) {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        } else {
            return $callback;
        }
    }

    public function response_ok() {
        header('HTTP/1.0 200 Ok');
    }

    public function response_error() {
        header('HTTP/1.0 400 Bad Request');
    }

    public function response_notfound() {
        header('HTTP/1.0 404 Not found');
    }

    public function free(&$obj) {
        if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                if (is_object($value)) {
                    $obj[$key] = new Free($value);
                } else
                if (is_array($value)) {
                    $this->free($obj[$key]);
                }
            }
        }
    }

    public function render($url, $array = array(), $static = false) {
        //$this->free($array);
        $html = file_get_contents($this->base . "$url");
        /*$keys = array();
        $values = array();
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                array_push($keys, "{{" . $key . "}}");
                array_push($values, $value);
            }
        }
        $html = str_replace($keys, $values, $html);*/
        //$html = preg_replace("/\{\{\w+}\}/", "", $html);
        $keys = array();
        $values = array();
        foreach ($this->language as $key => $value) {
            array_push($keys, "[[$key]]");
            array_push($values, $value);
        }
        $html = str_replace($keys, $values, $html);
        $keys = array();
        $values = array();
        foreach ($this->system as $key => $value) {
            array_push($keys, "{!$key!}");
            array_push($values, $value);
        }
        $html = str_replace($keys, $values, $html);
        if (!$static) { 
            $html = $this->template($html, $array);
        }
        return $html;
    }

    function template($html, $array) {
        foreach ($array as $key => $value) {
            if (!is_numeric($key)) {
                $val = $value;
                eval('$' . $key . ' = $val;');
            }
        }
        $nocode = "(\w|[á-úÁ-Ú]|\n|\t| |\<|\>|\/|\{|\}|\}|\$|\(|\)|#|-|\[|\]|!|\"|\'|:|\?|&|=|\.|;|\+)+";
        $sicode = "(\w|\$|=| |;|\(|\)|<|>|\+|\n|\t|\[|\]|\"|-|\')+";
        $regexp = "/(^|%})(?P<cont>($nocode))($|{%)/";
        $matchs = array();
        $auxil = $html;
        preg_match($regexp, $auxil, $matchs);
        $templt = "";
        while (isset($matchs['cont']) && $matchs['cont'] != '') {
            $auxil = preg_replace($regexp, '', $auxil, 1);
            $cont = str_replace("'", "\'", $matchs['cont']);
            $templt .= '$render .= \'' . $cont . '\';<PHP>';
            $matchs = array();
            preg_match($regexp, $auxil, $matchs);
        }
        $regexp = '/{%(?P<cont>((\w|\$|=| |;|\(|\)|<|>|\+|\n|\[|]|"|\t)+))%}/';
        $matchs = array();
        
        $auxil = $html;
        preg_match($regexp, $auxil, $matchs);
        $i = 0;
        while (isset($matchs['cont']) && $matchs['cont'] != '') {
            $auxil = preg_replace($regexp, '', $auxil, 1);
            if ($matchs['cont'] == "end") {
                $repla = "}";
            } else {
                $repla = $matchs['cont'] . "{";
            }
            $templt = preg_replace("/<PHP>/", $repla, $templt, 1);
            $matchs = array();
            preg_match($regexp, $auxil, $matchs);
            $i++;
        }
        
        $vreg = '/{{(?P<var>(' . $sicode . '))}}/';
        $vres = array();
        preg_match_all($vreg, $templt, $vres);
        foreach ($vres["var"] as $key => $var) {
            $templt = str_replace("{{" . $var . "}}", '\';$render .= $' . $var . ';$render .= \'', $templt);
        }
        $templt = str_replace("<PHP>", "", $templt);
        $render = "";
        eval($templt);
        //var_dump($templt);
        return $render;
    }

    //$reg = '/{%(?P<php>(\w|\$|=| |;|\(|\)|<|>|\+|\n|\t)*)%}(?P<cont>(\w|\n|\t| |\<|\>|\/|\{|\}|\$|\(|\)|-|\[|\]|\"|\'|:|\?|&|=|\.|;|\+)*){%end%}/';

    function logic_render($html, $array) {
        foreach ($array as $key => $value) {
            if (!is_numeric($key)) {
                $val = $value;
                eval('$' . $key . ' = $val;');
            }
        }
        $res = array();
        $reg = '/{%(?P<php>(\w|\$|=| |;|\(|\)|<|>|\+|\n|\t)*)%}(?P<cont>(\w|\n|\t| |\<|\>|\/|\{[^%]|%[^\}]|\}|\$|\(|\)|-|\[|\]|\"|\'|:|\?|&|=|\.|;|\+)*){%end%}/';
        preg_match($reg, $html, $res);

        while (isset($res['cont']) && isset($res['php'])) {
            $phpc = $res['php'];
            $cont = $res['cont'];

            $text = $this->logic_render($cont, $array);

            $vreg = '/{{(?P<var>\w+)}}/';
            $vres = array();
            preg_match_all($vreg, $text, $vres);

            foreach ($vres["var"] as $key => $var) {
                $text = str_replace("{{" . $var . "}}", '" . (isset($' . $var . ')?$' . $var . ':"{{' . $var . '}}") . "', $text);
            }

            $repl = "";
            $code = $phpc . '{$repl .= "' . $text . '";}';
            //echo $code;
            eval($code);

            $html = preg_replace($reg, $repl, $html, 1);
            preg_match($reg, $html, $res);
        }

        return $html;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $file = realpath(dirname(__FILE__)) . '/../config/languages/' . $language . '.json';
        if (file_exists($file)) {
            $json = file_get_contents($file);
            if ($json != null) {
                $this->language = json_decode($json);
            }
        }
    }

}

?>
