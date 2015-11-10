<?php

/**
 * Description of Admin
 *
 * @author eXile
 */
require_once realpath(dirname(__FILE__)) . '/../../model/AdminUser.php';

class Admin extends View {

    public function __construct() {
        parent::__construct(realpath(dirname(__FILE__)) . "/../../templ/");
    }

    private $admin = array(
        'package1' => array(
            'Datos_personales',
            'Datos_publicos',
            'Encuestador',
            'Formulario',
            'Pregunta',
            'Responde',
            'Pregunta_abierta',
            'Pregunta_cerrada',
            'Respuesta_abierta',
            'Respuesta_cerrada',
            'Usuario'
        )
    );

    public function index() {
        header("Location: ?ID=packs");
    }

    public function login() {
        return parent::render('login.html', array('action' => '?ID=dologin'));
    }

    public function logout() {
        Session::destroy();
        header("Location: ?ID=login");
    }

    public function do_login() {
        $admin = new AdminUser();
        $admin->setArray($_POST);
        if ($admin->autenticate()) {
            Session::start($admin);
            header("Location: ?ID=packs");
        } else {
            header("Location: ?ID=login");
        }
    }

    public function packs() {
        $user = Session::get_user();
        if ($user instanceof AdminUser && $user != null && $user->getVerified()) {
            $out = array();
            foreach ($this->admin as $key => $package) {
                foreach ($package as $model) {
                    if ($user->access($package, $model, Access::SELECT)) {
                        array_push($out, array('name' => $model, 'href' => '?ID=show&model=' . $key . '.' . $model, 'image' => 'none'));
                    }
                }
            }
            return parent::render("package.html", array('user' => $user->getUsername()->val(), 'models' => $out));
        } else {
            header("Location: ?ID=login");
        }
        
    }

    public function show() {
        $user = Session::get_user();
        $html = "";
        if ($user instanceof AdminUser && $user != null && $user->getVerified()) {
            $user = $user->getUsername()->get();
            $model = $_GET['model'];

            $object = Kernel::instance($model);
            $all = $object->filter();
            if (count($all)) {
                $html = Show::as_table($all, '?ID=set&model=' . $model, '?ID=del&model=' . $model);
            }
        } else {
            header("Location: ?ID=login");
        }
        return parent::render("show.html", array('content' => $html, 'new_url' => '?ID=add&model=' . $model, '?ID=add&model=' . $model, 'user' => $user));
    }

    public function add() {
        $user = Session::get_user();
        if ($user instanceof AdminUser && $user != null && $user->getVerified()) {
            $user = $user->getUsername()->get();
            $model = $_GET['model'];

            $object = Kernel::instance($model);

            $show = new Show($object);

            $html = $show->as_from_create('?ID=doadd&model=' . $model, 'post');
        } else {
            header("Location: ?ID=login");
        }
        return parent::render("index.html", array('content' => $html, 'user' => $user));
    }

    public function do_add() {
        $user = Session::get_user();
        if ($user instanceof AdminUser && $user != null && $user->getVerified()) {
            $model = $_GET['model'];

            $object = Kernel::instance($model);
            $object->setArray($_POST);
            $pk = $object->get_pk()->val();
            if ($pk != null) {
                $r = $object->edit();
            } else {
                $r = $object->save();
            }
            if ($r) {
                header('HTTP/1.0 200 OK');
                header("Location: ?ID=show&model=$model");
                exit(0);
            }
            header('HTTP/1.0 406 Not Acceptable');

            header("Location: ?ID=add&model=$model");
            exit(0);
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    public function set() {
        $user = Session::get_user();
        if ($user instanceof AdminUser && $user != null && $user->getVerified()) {
            $user = $user->getUsername()->get();
            $model = $_GET['model'];
            $pk = $_GET['pk'];

            $object = Kernel::instance($model);

            $object->get_me($pk);

            $show = new Show($object);

            $html = $show->as_from_edit('?ID=doadd&model=' . $model, 'post');
        } else {
            header("Location: ?ID=login");
        }
        return parent::render("index.html", array('content' => $html, 'user' => $user));
    }

    public function del() {
        $user = Session::get_user();
        if ($user instanceof AdminUser && $user != null && $user->getVerified()) {
            $user = $user->getUsername()->get();
            $model = $_GET['model'];
            $pk = $_GET['pk'];

            $object = Kernel::instance($model);

            $object->get_me($pk);

            $prk = $object->get_pk();

            $array = array(
                'action' => '?ID=dodel&model=' . $model, 'name' => $prk->name(),
                'cancel' => '?ID=show&model=' . $model,
                'pk' => $pk,
                'user' => $user
            );
        } else {
            header("Location: ?ID=login");
        }
        return parent::render("delete.html", $array);
    }

    public function do_del() {
        $user = Session::get_user();
        if ($user instanceof AdminUser && $user != null && $user->getVerified()) {
            $model = $_GET['model'];

            $object = Kernel::instance($model);

            $object->setArray($_POST);
            $r = $object->delete();
            //var_dump($r);
            if ($r) {
                header('HTTP/1.0 200 OK');
                header("Location: ?ID=show&model=$model");
                exit(0);
            }
            header('HTTP/1.0 406 Not Acceptable');
            $pk = $object->get_pk()->val();
            header("Location: ?ID=del&model=$model&pk=" . $pk);
        } else {
            header("Location: ?ID=login");
        }
    }

    public function img() {
        $url = $_GET['src'];
        return parent::response("$url", "image/png");
    }

    public function css() {
        $url = $_GET['src'];
        return parent::response("$url", "text/css");
    }

    public function js() {
        $url = $_GET['src'];
        return parent::response("$url", "text/javascript");
    }

}

?>
