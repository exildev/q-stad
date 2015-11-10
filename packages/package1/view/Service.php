<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Service
 *
 * @author luism
 */
class Service {

    private static $instance = null;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    /**
     * 
     * @return Service
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_stad_cerr(View $view) {
        $cerrada = $view->getGET("cerrada", false);
        if ($cerrada) {
            $res = Objects::create(new Usuario(), "user")
                    ->select("count(user.id) as num")->select("package1_pregunta_cerrada.id as cerrada")->select("package1_respuesta_cerrada.valor as resp")
                    ->join("package1_responde", "package1_responde.user_id = user.id")
                    ->join("package1_respuesta_cerrada", "package1_responde.respuesta_cerrada_id = package1_respuesta_cerrada.id")
                    ->join("package1_pregunta_cerrada", "package1_respuesta_cerrada.pregunta_cerrada_id = package1_pregunta_cerrada.id")
                    ->filter(array("cerrada = $cerrada"));
            $resp = $res->group_by("cerrada")->gets(array("num", "resp", "cerrada"));
            $out = array();
            foreach ($resp as $value) {
                array_push($out, array("name" => $value['resp'], "data" => array((int) $value["num"]), "cerrada" => $value["cerrada"]));
            }
            return $out;
        } else {
            return $view->response_error();
        }
    }

    public function get_stad_abi(View $view) {
        $abierta = $view->getGET("abierta", false);
        if ($abierta) {
            $res = Objects::create(new Usuario(), "user")
                    ->select("count(user.id) as num")->select("package1_pregunta_abierta.id as abierta")->select("package1_respuesta_abierta.cerradura as resp")
                    ->join("package1_respuesta_abierta", "package1_respuesta_abierta.user_id = user.id")
                    ->join("package1_pregunta_abierta", "package1_respuesta_abierta.pregunta_abierta_id = package1_pregunta_abierta.id")
                    ->filter(array("abierta = $abierta"));
            $resp = $res->group_by("resp")->gets(array("num", "resp", "abierta"));
            $out = array();
            foreach ($resp as $value) {
                array_push($out, array("name" => $value['resp'], "data" => array((int) $value["num"]), "abierta" => $value["abierta"]));
            }
            return $out;
        } else {
            return $view->response_error();
        }
    }

    public function get_form(View $view) {
        $form = $view->getPOST('form', false);
        if ($form) {
            $forms = Formulario::persistence()->filter(array('id' => $form));
            if (count($forms)) {
                $form = $forms[0];
                return array(
                    'id' => $form->getId()->val(),
                    'nombre' => $form->getNombre()->val()
                );
            }
        }
        return $view->response_error();
    }

    public function get_all_form(View $view) {
        $encues = Session::get("encuestador");
        if ($encues) {
            return Formulario::objects()->filter(array("encuestador_id__pk = " . $encues->get_pk()->val()));
        }
        return False;
    }

    public function do_cerrar_resp(View $view) {
        $resp = $view->getPOST("resp", false);
        $cerr = $view->getPOST("cerr", false);
        if ($resp && $cerr) {
            if (Respuesta_abierta::objects()->filter(array("pk = $resp"))->update(array("cerradura = '$cerr'"))) {
                return array(
                    'resp' => $resp,
                    'cerr' => $cerr
                );
            }
        }
        return $view->response_error();
    }

    private function get_datas_form($form) {
        $out = array();
        $pregunts = Pregunta::objects()->filter(array("formulario_id__pk = $form"));
        $abiertas = Pregunta_abierta::objects()->filter(array("pregunta_id__formulario_id__pk = $form"));
        $cerradas = Pregunta_cerrada::objects()->filter(array("pregunta_id__formulario_id__pk = $form"));
        $respuest = Respuesta_cerrada::objects()->filter(array("pregunta_cerrada_id__pregunta_id__formulario_id__pk = $form"));
        foreach ($pregunts as $pregunta) {
            $out[$pregunta->get_pk()] = array(
                'id' => $pregunta->get_pk(),
                'enunciado' => $pregunta->getEnunciado()
            );
        }
        foreach ($abiertas as $abierta) {
            $key = $abierta->getPregunta_id()->get_pk();
            $out[$key]['pregunta'] = $abierta;
            $out[$key]['id_preg'] = $abierta->get_pk();
            $out[$key]['tipo'] = 'abierta';
        }
        foreach ($cerradas as $cerrada) {
            $key = $cerrada->getPregunta_id()->get_pk();
            $out[$key]['pregunta'] = $cerrada;
            $out[$key]['id_preg'] = $cerrada->get_pk();
            $out[$key]['tipo'] = 'cerrada';
        }
        foreach ($respuest as $respues) {
            $key = $respues->getPregunta_cerrada_id()->getPregunta_id()->get_pk();

            if (!isset($out[$key]['respuestas'])) {
                $out[$key]['respuestas'] = array();
            }
            array_push($out[$key]['respuestas'], $respues);
        }
        return $out;
    }

    private function get_resps_form($form, $user) {
        $out = array();
        $pregunts = Pregunta::objects()->filter(array("formulario_id__pk = $form"));
        $cerradas = Objects::create(new Responde(), "package1_responde")
                ->select("user.id as user")->select("package1_formulario.id as form")->select("package1_pregunta.id as respuesta_cerrada_id__pregunta_cerrada_id__pregunta_id__pk")->select("package1_respuesta_cerrada.valor as respuesta_cerrada_id__valor")
                ->join("user", "package1_responde.user_id = user.id")
                ->join("package1_respuesta_cerrada", "package1_responde.respuesta_cerrada_id = package1_respuesta_cerrada.id")
                ->join("package1_pregunta_cerrada", "package1_respuesta_cerrada.pregunta_cerrada_id = package1_pregunta_cerrada.id")
                ->join("package1_pregunta", "package1_pregunta_cerrada.pregunta_id = package1_pregunta.id")
                ->join("package1_formulario", "package1_pregunta.formulario_id = package1_formulario.id")
                ->filter(array("form = $form", "user = $user"));
        $abiertas = Objects::create(new Respuesta_abierta(), "package1_respuesta_abierta")->select("package1_pregunta.id as pregunta_abierta_id__pregunta_id__pk")->select("cerradura")->select("valor")
                ->select("package1_respuesta_abierta.id as pk")->select("user.id as user")->select("package1_formulario.id as form")
                ->join("user", "package1_respuesta_abierta.user_id = user.id")
                ->join("package1_pregunta_abierta", "package1_respuesta_abierta.pregunta_abierta_id = package1_pregunta_abierta.id")
                ->join("package1_pregunta", "package1_pregunta_abierta.pregunta_id = package1_pregunta.id")
                ->join("package1_formulario", "package1_pregunta.formulario_id = package1_formulario.id")
                ->filter(array("form = $form", "user = $user"));
        foreach ($pregunts as $pregunta) {
            $out[$pregunta->get_pk()] = array(
                'id' => $pregunta->get_pk(),
                'enunciado' => $pregunta->getEnunciado()
            );
        }
//        var_dump($cerradas->sql_select());
        foreach ($abiertas as $abierta) {
            $key = $abierta->getPregunta_abierta_id()->getPregunta_id()->get_pk();
            $out[$key]['respuesta'] = $abierta->getValor();
            $out[$key]['cerradura'] = $abierta->getCerradura();
            $out[$key]['id_resp'] = $abierta->get_pk();
            $out[$key]['tipo'] = 'abierta';
        }
        foreach ($cerradas as $cerrada) {
            $key = $cerrada->getRespuesta_cerrada_id()->getPregunta_cerrada_id()->getPregunta_id()->get_pk();
//            var_dump($cerrada->getRespuesta_cerrada_id()->getValor());
            $out[$key]['respuesta'] = $cerrada->getRespuesta_cerrada_id()->getValor();
            $out[$key]['tipo'] = 'cerrada';
        }
        return $out;
    }

    public function get_data_form(View $view) {
        $form = $view->getGET('form', false);
        if ($form) {
            return $this->get_datas_form($form);
        }
        return array();
    }

    public function get_resp_form(View $view) {
        $form = $view->getGET('form', false);
        $user = $view->getGET('user', false);
        if ($form && $user) {
            return $this->get_resps_form($form, $user);
        }
        return array();
    }

    public function ver_all_resp_form(View $view) {

        $form = $view->getGET("form", False);
        $encues = Session::get("encuestador");
        $res1 = Objects::create(new Usuario(), "user")
                ->select("user.id")->select("package1_formulario.id as form")
                ->join("package1_responde", "package1_responde.user_id = user.id")
                ->join("package1_respuesta_cerrada", "package1_responde.respuesta_cerrada_id = package1_respuesta_cerrada.id")
                ->join("package1_pregunta_cerrada", "package1_respuesta_cerrada.pregunta_cerrada_id = package1_pregunta_cerrada.id")
                ->join("package1_pregunta", "package1_pregunta_cerrada.pregunta_id = package1_pregunta.id")
                ->join("package1_formulario", "package1_pregunta.formulario_id = package1_formulario.id")
                ->filter(array("form = $form"));
        $res2 = Objects::create(new Usuario(), "user")
                ->select("user.id")->select("package1_formulario.id as form")
                ->join("package1_respuesta_abierta", "package1_respuesta_abierta.user_id = user.id")
                ->join("package1_pregunta_abierta", "package1_respuesta_abierta.pregunta_abierta_id = package1_pregunta_abierta.id")
                ->join("package1_pregunta", "package1_pregunta_abierta.pregunta_id = package1_pregunta.id")
                ->join("package1_formulario", "package1_pregunta.formulario_id = package1_formulario.id")
                ->filter(array("form = $form"));
        $res = $res1->union($res2)->distinct(array("id"));

        return $res;
    }

    public function do_resp_form(View $view) {
        $formul = $view->getPOST('form', false);
        $user = Session::get_user();
        if ($formul && $user) {
            $pregs = $this->get_datas_form($formul);
            foreach ($pregs as $preg) {
                $id_preg = $preg["id_preg"];
                $resp = $view->getPOST("preg$id_preg", false);
                if ($resp) {
                    if ($preg["tipo"] == "cerrada") {
                        $responde = Responde::instance();
                        $responde->setUser_id($user->get_pk()->val());
                        $responde->setRespuesta_cerrada_id($resp);
                        $responde->save();
                    } else
                    if ($preg["tipo"] == "abierta") {
                        $respuesta = Respuesta_abierta::instance();
                        $respuesta->setValor($resp);
                        $respuesta->setUser_id($user->get_pk()->val());
                        $respuesta->setPregunta_abierta_id($id_preg);
                        $respuesta->save();
                    }
                }
            }
            return $view->response_ok();
        }
        return $view->response_error();
    }

    public function do_invite(View $view) {
        $formul = $view->getPOST('form', false);
        $user = Session::get_user();
        if ($formul && $user) {
            $files = $view->getFILES();
            $uploader = new Upload($files);
            $nombres = $uploader->uploadFile();
            foreach ($nombres as $nombre) {
                if (($handle = fopen($nombre, "r")) !== FALSE) {
                    while (($datos = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        $nombre = $datos[0];
                        $apellidos = $datos[1];
                        $identificacion = $datos[2];
                        $email = $datos[3];
                        mail($email, "Inivitacion a responder un formulario en q-stad", 
                                "Cordialmente se le esta invitando la siguiente encuesta que aparece en el link que aparece a continuacion http://q-stad.alwaysdata.net/?ID=ver/resp/form&form=$formul");
                    }
                    fclose($handle);
                }
            }
            return $view->response_ok();
        }
        return $view->response_error();
    }

    public function do_del_form(View $view) {
        $formul = $view->getPOST('form', false);
        $enques = Session::get('encuestador');

        if ($formul && $enques) {
            $forms = Formulario::objects()->filter(array("pk = $formul", "encuestador_id__pk = " . $enques->get_pk()));
            if ($forms->len()) {
                Respuesta_cerrada::objects()->filter(array("pregunta_cerrada_id__pregunta_id__formulario_id__pk = $formul"))->delete();
                Pregunta_cerrada::objects()->filter(array("pregunta_id__formulario_id__pk = $formul"))->delete();
                Pregunta_abierta::objects()->filter(array("pregunta_id__formulario_id__pk = $formul"))->delete();
                Pregunta::objects()->filter(array("formulario_id__pk = $formul"))->delete();
                if (Formulario::objects()->filter(array("pk = $formul", "encuestador_id = " . $enques->get_pk()))->delete()) {
                    $view->response_ok();
                    return array(
                        'id' => $formul,
                        'enquestador_id' => $enques->get_pk()->val()
                    );
                }
            }
        }
        return $view->response_error();
    }

    public function do_edit_form(View $view) {
        $nombre = $view->getPOST('nombre', false);
        $formul = $view->getPOST('form', false);
        $enques = Session::get('encuestador');

        if ($nombre && $formul && $enques) {
            if (Formulario::objects()->filter(array("pk = $formul", "encuestador_id = " . $enques->get_pk()))->update(array("nombre = '$nombre'"))) {
                $view->response_ok();
                return array(
                    'id' => $formul,
                    'nombre' => $nombre,
                    'enquestador_id' => $enques->get_pk()->val()
                );
            }
        }
        return $view->response_error();
    }

    public function do_crea_form(View $view) {
        $nombre = $view->getPOST('nombre', false);
        $enques = Session::get('encuestador');

        if ($nombre && $enques) {
            $form = Formulario::instance();
            $form->setEncuestador_id($enques->get_pk());
            $form->setNombre($nombre);
            $id = $form->save();
            if ($id > 0) {
                $view->response_ok();
                return array(
                    'id' => $id,
                    'nombre' => $nombre,
                    'enquestador_id' => $enques->get_pk()->val()
                );
            }
        }
        return $view->response_error();
    }

    public function do_crea_abierta(View $view) {
        $enunciado = $view->getPOST('enunciado', false);
        $formulari = $view->getPOST('formulario', false);
        $enques = Session::get('encuestador');

        if ($enunciado && $formulari) {
            $forms = Formulario::objects()->filter(array("pk = $formulari", "encuestador_id__pk = " . $enques->get_pk()));
            if ($forms->len()) {
                $preg = Pregunta::instance();
                $preg->setEnunciado($enunciado);
                $preg->setFormulario_id($formulari);
                $pgid = $preg->save();
                if ($pgid > 0) {
                    $abierta = Pregunta_abierta::instance();
                    $abierta->setPregunta_id($pgid);
                    $idab = $abierta->save();
                    if ($idab > 0) {
                        return array(
                            'id_preg' => $pgid,
                            'id_abierta' => $idab,
                            'enunciado' => $enunciado
                        );
                    }
                }
            }
        }
        return $view->response_error();
    }

    public function do_del_pregunta(View $view) {
        $pregunta = $view->getPOST('pregunta', false);
        $enques = Session::get("encuestador");
        if ($pregunta && $enques) {
            $pregs = Pregunta::objects()->filter(array("pk = $pregunta", "formulario_id__encuestador_id__pk = " . $enques->get_pk()));
            if ($pregs->len() > 0) {
                Respuesta_cerrada::objects()->filter(array("pregunta_cerrada_id__pregunta_id__pk = $pregunta"))->delete();
                Pregunta_cerrada::objects()->filter(array("pregunta_id__pk = $pregunta"))->delete();
                Pregunta_abierta::objects()->filter(array("pregunta_id__pk = $pregunta"))->delete();
                if (Pregunta::objects()->filter(array("pk = $pregunta"))->delete()) {
                    return array(
                        'id_preg' => $pregunta
                    );
                }
            }
        }
        return $view->response_error();
    }

    public function do_edit_pregunta(View $view) {
        $pregunta = $view->getPOST('pregunta', false);
        $enunciado = $view->getPOST('enunciado', false);
        $enques = Session::get("encuestador");
        if ($pregunta && $enunciado && $enques) {
            $pregs = Pregunta::objects()->filter(array("pk = $pregunta", "formulario_id__encuestador_id__pk = " . $enques->get_pk()));
            if ($pregs->len() > 0) {
                if (Pregunta::objects()->filter(array("pk = $pregunta"))->update(array("enunciado = '$enunciado'"))) {
                    return array(
                        'id_preg' => $pregunta,
                        'enunciado' => $enunciado
                    );
                }
            }
        }
        return $view->response_error();
    }

    public function do_crea_cerrada(View $view) {
        $enunciado = $view->getPOST('enunciado', false);
        $formulari = $view->getPOST('formulario', false);
        $enques = Session::get('encuestador');

        if ($enunciado && $formulari && $enques) {
            $forms = Formulario::objects()->filter(array("pk = $formulari", "encuestador_id__pk = " . $enques->get_pk()));
            if ($forms->len()) {
                $preg = Pregunta::instance();
                $preg->setEnunciado($enunciado);
                $preg->setFormulario_id($formulari);
                $pgid = $preg->save();
                if ($pgid > 0) {
                    $cerrada = Pregunta_cerrada::instance();
                    $cerrada->setPregunta_id($pgid);
                    $idce = $cerrada->save();
                    if ($idce > 0) {
                        return array(
                            'id_preg' => $pgid,
                            'id_cerrada' => $idce,
                            'enunciado' => $enunciado
                        );
                    }
                }
            }
        }
        return $view->response_error();
    }

    public function do_del_respuesta_cerrada(View $view) {
        $respu = $view->getPOST('respuesta', false);
        $enques = Session::get("encuestador");
        if ($respu && $enques) {
            $resps = Respuesta_cerrada::objects()->filter(array("pk = $respu", "pregunta_cerrada_id__pregunta_id__formulario_id__encuestador_id__pk = " . $enques->get_pk()));
            if ($resps->len() > 0) {
                if (Respuesta_cerrada::objects()->filter(array("pk = $respu"))->delete()) {
                    return array(
                        'id' => $respu
                    );
                }
            }
        }
        return $view->response_error();
    }

    public function do_edit_respuesta_cerrada(View $view) {
        $respu = $view->getPOST('respuesta', false);
        $valor = $view->getPOST('valor', false);
        $enques = Session::get("encuestador");
        if ($respu && $valor && $enques) {
            $resps = Respuesta_cerrada::objects()->filter(array("pk = $respu", "pregunta_cerrada_id__pregunta_id__formulario_id__encuestador_id__pk = " . $enques->get_pk()));
            if ($resps->len() > 0) {
                $respuestas = Respuesta_cerrada::objects()->filter(array("pk = $respu"));
                if ($respuestas->update(array("valor = '$valor'"))) {
                    return array(
                        'id' => $respu,
                        'valor' => $valor
                    );
                }
            }
        }
        return $view->response_error();
    }

    public function do_crea_respuesta_cerrada(View $view) {
        $valor = $view->getPOST('valor', false);
        $pregu = $view->getPOST('pregunta', false);
        $enques = Session::get("encuestador");
        if ($valor && $pregu && $enques) {
            $resps = Pregunta_cerrada::objects()->filter(array("pk = $pregu", "pregunta_id__formulario_id__encuestador_id__pk = " . $enques->get_pk()));
            if ($resps->len() > 0) {
                $respuesta = Respuesta_cerrada::instance();
                $respuesta->setPregunta_cerrada_id($pregu);
                $respuesta->setValor($valor);
                $id = $respuesta->save();
                if ($id > 0) {
                    return array(
                        'id' => $id,
                        'valor' => $valor
                    );
                }
            }
        }
        return $view->response_error();
    }

    function do_register_group(View $view) {
        
    }

}

?>
