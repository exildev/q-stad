<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Package1
 *
 * @author luism
 */
Kernel::import('package1.Formulario');
Kernel::import('package1.Usuario');
Kernel::import('package1.Pregunta');
Kernel::import('package1.Respuesta_cerrada');
Kernel::import('package1.Respuesta_abierta');
Kernel::import('package1.Pregunta_cerrada');
Kernel::import('package1.Pregunta_abierta');
Kernel::import('package1.Responde');
Kernel::import('package1.Encuestador');

require_once realpath(dirname(__FILE__)) . "/Service.php";
require_once realpath(dirname(__FILE__)) . "/Upload.class.php";

class Package1 extends View {

    public function __construct() {
        parent::__construct(realpath(dirname(__FILE__)) . "/../templ/");
    }

    public function main() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            return parent::render('menu.html', array());
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

    public function do_cerrar_resp() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_cerrar_resp($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    public function get_stad_cerr() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->get_stad_cerr($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    public function get_stad_abi() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->get_stad_abi($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }
    
    public function invite() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $form = parent::getGET("form", false);
            $forms = Formulario::objects()->filter(array("pk = $form"));
            if ($forms->len() > 0) {
                $form = $forms->get(0);
                return parent::render("index.html", array('content' => parent::render('invite.html', array('form' => $form))));
            }
            parent::response_notfound();
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }
    
    public function do_invite() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return $service->do_invite($this);
        } else {
            return parent::response_error();
        }
    }
    
    public function ver_resp() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            $pregs = $service->get_resp_form($this);
            $form = parent::getGET("form", false);
            if ($pregs) {
                $forms = Formulario::objects()->filter(array("pk = $form"));
                if ($forms->len() > 0) {
                    $form = $forms->get(0);
                    return parent::render("index.html", array('content' => parent::render('resp.html', array('pregs' => $pregs, 'form' => $form))));
                }
            }
            parent::response_notfound();
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    public function ver_all_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            $forms = $service->get_all_form($this);
            if ($forms) {
                return parent::render("index.html", array('content' => parent::render('all_form.html', array('forms' => $forms))));
            } else {
                parent::response_notfound();
            }
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    public function do_resp_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return $service->do_resp_form($this);
        } else {
            return parent::response_error();
        }
    }

    public function ver_all_resp_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            $users = $service->ver_all_resp_form($this);
            $form = parent::getGET("form", false);
            if ($form) {
                return parent::render("index.html", array('content' => parent::render('all_resp_form.html', array('form' => $form, 'users' => $users))));
            }
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    public function ver_stad_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $form = parent::getGET('form', false);
            $service = Service::getInstance();
            $forms = Formulario::objects()->filter(array("pk = $form"));
            if ($forms->len() > 0) {
                $form = $forms->get(0);
                $pregs = $service->get_data_form($this);
                return parent::render("index.html", array('content' => parent::render('stad_form.html', array('form' => $form, 'pregs' => $pregs))));
            }
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    public function ver_resp_form() {
        $form = parent::getGET('form', false);
        $service = Service::getInstance();
        $forms = Formulario::objects()->filter(array("pk = $form"));
        if ($forms->len() > 0) {
            $form = $forms->get(0);
            $pregs = $service->get_data_form($this);
            return parent::render("index.html", array('content' => parent::render('resp_form.html', array('form' => $form, 'pregs' => $pregs))));
        }
    }

    public function ver_edit_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $form = parent::getGET('form', false);
            $service = Service::getInstance();
            $forms = Formulario::objects()->filter(array("pk = $form"));
            if ($forms->len() > 0) {
                $form = $forms->get(0);
                $pregs = $service->get_data_form($this);
                return parent::render("index.html", array('content' => parent::render('edit_form.html', array('form' => $form, 'pregs' => $pregs))));
            }
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    public function ver_crea_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $form = parent::getGET('form', false);
            return parent::render("index.html", array('content' => parent::render('crea_form.html', array('form' => $form))));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function get_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->get_form($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function get_session() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            return parent::response_ok();
        } else {
            return parent::response_error();
        }
    }

    function get_login() {
        $user = new Usuario();
        $user->setArray($_POST);
        if ($user->autenticate()) {
            Session::start($user);
            $enqs = Encuestador::persistence()->filter(array('user_id' => $user->get_pk()));
            if (count($enqs)) {
                $enq = $enqs[0];
                Session::set('encuestador', $enq);
            }
            return parent::response_ok();
        } else {
            return parent::response_error();
        }
    }

    function do_crea_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_crea_form($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_edit_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_edit_form($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_del_form() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_del_form($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_del_pregunta() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_del_pregunta($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_edit_pregunta() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_edit_pregunta($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_crea_abierta() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_crea_abierta($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_crea_cerrada() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_crea_cerrada($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_del_respuesta_cerrada() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_del_respuesta_cerrada($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_edit_respuesta_cerrada() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_edit_respuesta_cerrada($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function do_crea_respuesta_cerrada() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            $service = Service::getInstance();
            return json_encode($service->do_crea_respuesta_cerrada($this));
        } else {
            header("Location: ?ID=login");
            exit(0);
        }
    }

    function login() {
        $user = Session::get_user();
        if ($user != null && $user->getVerified()) {
            header("Location: ./");
        } else {
            return parent::render('index.html', array(
                'content' => parent::render('login.html', array('action' => '?ID=dologin'))
            ));
        }
    }

    function dologin() {
        $user = new Usuario();
        $user->setArray($_POST);
        if ($user->autenticate()) {
            Session::start($user);
            $enqs = Encuestador::persistence()->filter(array('user_id' => $user->get_pk()));
            if (count($enqs)) {
                $enq = $enqs[0];
                Session::set('encuestador', $enq);
            }
            header("Location: ./");
        } else {
            header("Location: ?ID=login");
        }
    }

    public function logout() {
        Session::destroy();
        header("Location: ?ID=login");
    }

}

?>
