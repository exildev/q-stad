<?php

/**
 * Description of Input
 *
 * @author temp
 */
class Input {

    const VAL_NAME = '{{NAME}}';
    const VAL_LABEL = '{{LABEL}}';
    const VAL_TYPE = '{{TYPE}}';
    const VAL_VALUE = '{{VALUE}}';
    const VAL_PATTERN = '{{PATTERN}}';
    const VAL_TITLE = '{{TITLE}}';
    const VAL_MIN = '{{MIN}}';
    const VAL_MAX = '{{MAX}}';
    const VAL_ACCEPT = '{{ACCEPT}}';
    const HTML_INPUT = 'input';
    const HTML_HIDDEN = 'hidden';
    const HTML_SUBMIT = 'submit';
    const HTML_SELECT = 'select';
    const HTML_TEXTAREA = 'textarea';

    //special type
    const FILE = 'file';

    private $html;
    protected $vals;
    private $inps;
    private $vald;

    protected function __construct($html, $vals = array(), $inps = array(), $vald = array()) {
        $this->html = $html;
        $this->vals = $vals;
        $this->inps = $inps;
        $this->vald = $vald;
    }

    public function set($value) {
        $this->vals[self::VAL_VALUE] = $value;
    }

    public function type() {
        return isset($this->vals[self::VAL_TYPE])?$this->vals[self::VAL_TYPE]:null;
    }

    public function set_array($array) {
        if (isset($this->vals[self::VAL_TYPE]) && $this->vals[self::VAL_TYPE] == self::FILE) {
            $url = "/" . $this->vals['folder'] . "/" . $_FILES['tmp_name'];
            move_uploaded_file($_FILES['tmp_name'], $url);
            $this->set($url);
        } else {
            if (isset($array[$this->vals[self::VAL_NAME]])) {
                $this->set($array[$this->vals[self::VAL_NAME]]);
            }
        }
    }

    public function get() {
        if ($this->html == self::HTML_SELECT) {
            foreach ($this->inps as $inp) {
                if ($inp->vals['{{KEY}}'] == $this->val()) {
                    return $inp->vals['{{VALUE}}'];
                }
            }
        }
        return $this->vals[self::VAL_VALUE];
    }

    public function val() {
        return $this->vals[self::VAL_VALUE];
    }

    public function name() {
        return $this->vals[self::VAL_NAME];
    }

    public static function create_submit($value = '[[send]]') {
        return new Input(self::HTML_SUBMIT, array(
            self::VAL_NAME => '',
            self::VAL_VALUE => $value,
        ));
    }

    public static function create_integer($name, $title = null, $default = '0', $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        return new Input(self::HTML_INPUT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_TYPE => 'numeric',
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '^[0-9]*$',
            self::VAL_TITLE => $title,
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => ''
                ), array(
            new Validate('/^[0-9]*$/', $title)
        ));
    }

    public static function create_decimal($name, $title = null, $default = '0', $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        return new Input(self::HTML_INPUT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_TYPE => 'numeric',
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '^([0-9]*)(|(.([0-9]+)))$',
            self::VAL_TITLE => $title,
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => ''
                ), array(
            new Validate('/^([0-9]*)(|(.([0-9]+)))$/', $title)
        ));
    }

    public static function create_datetime($name, $format = 'y-m-d h:M:s', $default = null, $title = null, $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        if ($default == null) {
            $default = str_replace(array('d', 'm', 'y', 'h', 'M', 's'), array('00', '00', '0000', '00', '00', '00'), $format);
        }
        $format = str_replace(array('d', 'm', 'y', 'h', 'M', 's'), array('((0|1|2)[0-9]|3(0|1))', '(0[0-9]|1(0|1|2))', '[0-9]{4}', '((0|1)[0-9]|2(0|1|2|3|4))', '((0|1|2|3|4|5)[0-9])', '((0|1|2|3|4|5)[0-9])'), $format);
        return new Input(self::HTML_INPUT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_TYPE => 'numeric',
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '^' . $format . '$',
            self::VAL_TITLE => $title,
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => ''
                ), array(
            new Validate('/^' . $format . '$/', $title)
        ));
    }

    public static function create_date($name, $format = 'd-m-y', $default = null, $title = null, $label = null) {
        return self::create_datetime($name, $format, $default, $title, $label);
    }

    public static function create_time($name, $format = 'h-M-s', $default = null, $title = null, $label = null) {
        return self::create_datetime($name, $format, $default, $title, $label);
    }

    public static function create_text($name, $title = null, $default = '', $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        return new Input(self::HTML_INPUT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_TYPE => 'text',
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '',
            self::VAL_TITLE => $title,
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => ''
        ));
    }

    public static function create_password($name, $title = null, $default = '', $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        return new Input(self::HTML_INPUT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_TYPE => 'password',
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '',
            self::VAL_TITLE => $title,
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => ''
        ));
    }

    public static function create_hidden($name, $title = null, $default = '', $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        return new Input(self::HTML_HIDDEN, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_TYPE => 'hidden',
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '',
            self::VAL_TITLE => $title,
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => ''
        ));
    }

    public static function create_email($name, $title = null, $default = 'email@domin.do', $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        $format = '(([a-z]|[A-Z]|[0-9]|.|_){3,})@(([a-z]|[A-Z]|[0-9]|.|_){3,}).(([a-z]|[A-Z]|[0-9]|.|_){2,})';
        return new Input(self::HTML_INPUT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_TYPE => 'text',
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '^' . $format . '$',
            self::VAL_TITLE => $title,
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => ''
                ), array(
            new Validate('/^' . $format . '$/', $title)
        ));
    }

    public static function create_file($name, $folder, $accept = '*', $title = null, $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        return new Input(self::HTML_INPUT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_TYPE => self::FILE,
            self::VAL_VALUE => '',
            self::VAL_PATTERN => '',
            self::VAL_TITLE => $title,
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => $accept,
            'folder' => $folder
        ));
    }

    public static function create_textarea($name, $title = null, $default = '0', $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        return new Input(self::HTML_TEXTAREA, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '',
            self::VAL_TITLE => $title,
        ));
    }

    public static function create_select($name, $options, $title = null, $default = '0', $label = null) {
        if ($label == null) {
            $label = ucfirst($name);
        }
        if ($title == null) {
            $title = ucfirst($name);
        }
        $inps = array();
        foreach ($options as $key => $value) {
            $inp = new Input('option', array(
                '{{KEY}}' => $key,
                '{{VALUE}}' => $value
            ));
            array_push($inps, $inp);
        }
        return new Input(self::HTML_SELECT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => $label,
            self::VAL_VALUE => $default,
            self::VAL_PATTERN => '',
            self::VAL_TITLE => $title
                ), $inps);
    }

    public function as_input() {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/" . $this->html . ".html");
        foreach ($this->vals as $key => $value) {
            $html = str_replace($key, $value, $html);
        }
        $html = str_replace('{{VALUE}}', $this->get(), $html);
        if (isset($this->vals[self::VAL_PATTERN]) && $this->vals[self::VAL_PATTERN] == '') {
            $html = str_replace(' pattern=""', '', $html);
        }
        $html = str_replace('{{MORE}}', 'required', $html);
        if (!empty($this->inps)) {
            $cont = "";
            foreach ($this->inps as $input) {
                if ($input instanceof Input) {
                    $conts = $input->as_input();
                    if ($this->val() == $input->vals['{{KEY}}']) {
                        $conts = str_replace('{{REQ}}', 'selected', $conts);
                    } else {
                        $conts = str_replace('{{REQ}}', '', $conts);
                    }
                    $cont.= $conts;
                }
            }
            $html = str_replace('{{CONTENT}}', $cont, $html);
        }
        return $html;
    }

    public function as_show() {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/" . $this->html . ".html");
        foreach ($this->vals as $key => $value) {
            $html = str_replace($key, $value, $html);
        }
        $html = str_replace('{{VALUE}}', $this->get(), $html);
        $html = str_replace('{{MORE}}', 'disabled', $html);
        if (!empty($this->inps)) {
            $cont = "";

            foreach ($this->inps as $input) {
                $cont .= $input->html();
            }
            $html = str_replace('{{CONTENT}}', $cont, $html);
        }
        return $html;
    }

    public function as_list() {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/cell.html");
        foreach ($this->vals as $key => $value) {
            $html = str_replace($key, $value, $html);
        }
        $html = str_replace('{{CONTENT}}', $this->get(), $html);
        return $html;
    }

    public function validate() {
        $falied = array();
        if (!empty($this->vald)) {
            foreach ($this->vald as $valid) {
                if ($valid->validate($this->get())) {
                    array_push($falied, $valid);
                }
            }
        }
        return $falied;
    }
    
    public function __toString() {
        return $this->val();
    }

}

?>
