<?php

/**
 * Description of Constrain
 *
 * @author eXile
 */
class Constrain extends Input {

    const PRIMARY_KEY = 'pk';
    const FORAIN_KEY = 'fk';

    private $type;
    private $refer;

    protected function __construct($name, $type, $refer) {
        parent::__construct(self::HTML_INPUT, array(
            self::VAL_NAME => $name,
            self::VAL_LABEL => ucfirst($name),
            self::VAL_TYPE => 'text',
            self::VAL_VALUE => '',
            self::VAL_PATTERN => '',
            self::VAL_TITLE => '',
            self::VAL_MIN => '',
            self::VAL_MAX => '',
            self::VAL_ACCEPT => ''
        ));
        $this->type = $type;
        $this->refer = $refer;
    }

    public static function fk($name, $package_model) {
        return new Constrain($name, self::FORAIN_KEY, $package_model);
    }

    public static function pk($this, $name = 'id') {
        return new Constrain($name, self::PRIMARY_KEY, $this);
    }

    public function getType() {
        return $this->type;
    }

    public function getRefer() {
        return $this->refer;
    }
    
    public function getFullRefer() {
        $explode = explode(".", $this->refer);
        return $explode[0] . "_" . $explode[1];
    }

    public function getReferPackage() {
        $explode = explode(".", $this->refer);
        return $explode[0];
    }

    public function getReferModel() {
        $explode = explode(".", $this->refer);
        return $explode[1];
    }

    private function as_pk() {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/hidden.html");
        $html = str_replace(array('{{VALUE}}', '{{NAME}}'), array($this->get(), $this->name()), $html);
        return $html;
    }

    public function as_input() {
        if ($this->type == self::PRIMARY_KEY) {
            return $this->as_pk();
        }
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/table.html");
        $selc = file_get_contents(realpath(dirname(__FILE__)) . "/html/selectable.html");
        $selc = str_replace('{{MORE}}', 'required', $selc);
        $cont = "";

        $model = Kernel::instance($this->refer);
        $models = $model->filter();
        if (isset($models[0])) {
            $show = new Show($models[0]);
            $head = $show->as_headrow(false, false, '#');
            foreach ($models as $model) {
                $inps = "";
                $pk = $model->get_pk();
                $reflex = new ReflectionClass($model->get_called_class());
                $properts = $reflex->getProperties();
                $val = $this->val();
                if ($val == $pk->get()) {
                    $sel = str_replace('required', 'required checked ', $selc);
                } else {
                    $sel = $selc;
                }
                foreach ($properts as $propert) {
                    $name = $propert->getName();
                    $value = $model->{"get" . ucfirst($name)}();
                    if (($value instanceof Input && !($value instanceof Constrain)) || ($value instanceof Constrain && $value->getType() != Constrain::PRIMARY_KEY)) {
                        $inps .= $value->as_list();
                    }
                }
                $cont .= str_replace(array('{{NAME}}', '{{VALUE}}', '{{CONTENT}}'), array($this->name(), $pk->get(), $inps), $sel);
            }
            $html = str_replace(array('{{HEAD}}', '{{BODY}}'), array($head, $cont), $html);
        } else {
            $html = '<<-empty->>';
        }
        return $html;
    }

    public function get() {
        if ($this->type == self::FORAIN_KEY) {
            $model = Kernel::instance($this->refer);
            $pk = $model->get_pk();
            $models = $model->filter(array($pk->name() => parent::get()));
            return isset($models[0]) ? $models[0] : '---';
        } else
        if ($this->type == self::PRIMARY_KEY) {
            return parent::get();
        }
    }

    public function val() {
        return parent::get();
    }

    public function __toString() {
        return $this->val();
    }

}

?>
