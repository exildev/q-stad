<?php

/**
 * Description of Show
 *
 * @author eXile
 */
require_once realpath(dirname(__FILE__)) . '/Model.php';

class Show {

    private $model;

    function __construct(Model $model = null) {
        $this->model = $model;
    }

    public function as_from_create($action, $method) {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/form.html");
        $reflex = new ReflectionClass($this->model->get_called_class());
        $properts = $reflex->getProperties();
        $inputs = "";
        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->model->{"get" . ucfirst($name)}();
            if (($value instanceof Input && !($value instanceof Constrain)) || ($value instanceof Constrain && $value->getType() != Constrain::PRIMARY_KEY)) {
                $inputs .= $value->as_input();
            }
        }
        $submit = Input::create_submit();
        $inputs .= $submit->as_input();
        $keys = array('{{CONTENT}}', '{{ACTION}}', '{{METHOD}}');
        $vals = array($inputs, $action, $method, '', '');
        $resp = str_replace($keys, $vals, $html);

        return $resp;
    }

    public function as_from_edit($action, $method) {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/form.html");
        $reflex = new ReflectionClass($this->model->get_called_class());
        $properts = $reflex->getProperties();
        $inputs = "";
        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->model->{"get" . $name}();
            if ($value instanceof Input || $value instanceof Constrain) {
                $inputs .= $value->as_input();
            }
        }
        $submit = Input::create_submit();
        $inputs .= $submit->as_input();
        $keys = array('{{CONTENT}}', '{{ACTION}}', '{{METHOD}}');
        $vals = array($inputs, $action, $method, $this->model->get_called_class(), $this->model->getId());
        $resp = str_replace($keys, $vals, $html);
        
        return $resp;
    }

    public function as_row($edit = null, $delete = null) {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/row.html");
        $cell = file_get_contents(realpath(dirname(__FILE__)) . "/html/cell.html");
        $link = file_get_contents(realpath(dirname(__FILE__)) . "/html/link.html");
        $reflex = new ReflectionClass($this->model->get_called_class());
        $properts = $reflex->getProperties();
        $inputs = "";
        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->model->{"get" . $name}();
            if (($value instanceof Input && $value->type() != Input::HTML_HIDDEN && !($value instanceof Constrain)) || ($value instanceof Constrain && $value->getType() != Constrain::PRIMARY_KEY)) {
                $inputs .= $value->as_list();
            }
        }
        $link = str_replace('{{CONTENT}}', $link, $cell);
	if ($this->model->get_pk() != null){
		$pk = $this->model->get_pk()->get();
		if ($edit != null) {
		    $inputs .= str_replace(array('{{HREF}}', '{{TITLE}}', '{{CONTENT}}'), array("$edit&pk=$pk", '[[edit]]', '[[edit]]'), $link);
		}
		if ($delete != null) {
		    $inputs .= str_replace(array('{{HREF}}', '{{TITLE}}', '{{CONTENT}}'), array("$delete&pk=$pk", '[[delete]]', '[[delete]]'), $link);
		}
	}
        $html = str_replace('{{CONTENT}}', $inputs, $html);
        return $html;
    }

    public function as_headrow($edit = false, $delete = false, $num = false) {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/row.html");
        $cell = file_get_contents(realpath(dirname(__FILE__)) . "/html/headcell.html");
        $reflex = new ReflectionClass($this->model->get_called_class());
        $properts = $reflex->getProperties();
        $inputs = "";
        if ($num) {
            $inputs .= str_replace('{{CONTENT}}', $num, $cell);
        }
        foreach ($properts as $propert) {
            $name = $propert->getName();
            $value = $this->model->{"get" . $name}();
            if (($value instanceof Input && $value->type() != Input::HTML_HIDDEN &&  !($value instanceof Constrain)) || ($value instanceof Constrain && $value->getType() != Constrain::PRIMARY_KEY)) {
                $inputs .= str_replace('{{CONTENT}}', $value->name(), $cell);
            }
        }
        if ($edit) {
            $inputs .= str_replace('{{CONTENT}}', '-', $cell);
        }
        if ($delete) {
            $inputs .= str_replace('{{CONTENT}}', '-', $cell);
        }
        $html = str_replace('{{CONTENT}}', $inputs, $html);
        return $html;
    }

    public static function as_table(Array $models, $edit = null, $delete = null) {
        $html = file_get_contents(realpath(dirname(__FILE__)) . "/html/table.html");
        $show = new Show($models[0]);
        $head = $show->as_headrow($edit, $delete);
        $body = "";
        foreach ($models as $model) {
            $show->model = $model;
            $body .= $show->as_row($edit, $delete);
        }
        $html = str_replace(array('{{HEAD}}', '{{BODY}}'), array($head, $body), $html);
        return $html;
    }

}

?>
