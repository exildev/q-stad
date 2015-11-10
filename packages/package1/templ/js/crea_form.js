/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    var form = $("input[name='form']").val();
    if (form === '') {
        crear_form();
    } else {
        update(form);
    }

    $("a.nrc").click(function() {
        var pregunta = $(this).attr("pregunta");
        var ol = $(this).parent().find("ol");
        crea_resp(ol, pregunta);
        return false;
    });
    $("a.erc").click(function() {
        var respuesta = $(this).attr("respuesta");
        edit_resp(respuesta, $("span[respuesta='" + respuesta + "']").html());
        return false;
    });
    $("a.drc").click(function() {
        var respuesta = $(this).attr("respuesta");
        del_resp(respuesta);
        return false;
    });

    $("a.ep").click(function() {
        var pregunta = $(this).attr("pregunta");
        edit_pregunta(pregunta, $("li[pregunta='" + pregunta + "'] p").html());
        return false;
    });
    $("a.dp").click(function() {
        var pregunta = $(this).attr("pregunta");
        del_pregunta(pregunta);
        return false;
    });    
    $("a.ef").click(function() {
        var form = $("input[name='form']").val();
        edit_form(form, $("h3#nombre").html());
        return false;
    });
    $("a.df").click(function() {
        var form = $("input[name='form']").val();
        del_form(form);
        return false;
    });
});


function update(form) {
    $.ajax({
        type: "POST",
        url: '?ID=get/form',
        data: {'form': form},
        success: function(data) {
            return succsess(data);
        }
    });
}

function crear_form() {
    var form = $("<form action='?ID=do/crea/form' method='post'></form>");
    $("<p>Nombre:</p>").appendTo(form);
    $("<input type='text' name='nombre'/>").appendTo(form);

    form.submit(function() {
        $(form).dialog("close");
    });

    form.dialog({
        title: 'Crear formulario',
        buttons: {
            Guardar: function() {
                form.submit();
                $(this).dialog("close");
            },
            Cancelar: function() {
                $(this).dialog("close");
            }
        }
    });
    form.ajaxForm({
        success: function(data) {
            return succsess(data);
        },
        error: function(code) {
            alert(code);
        }
    });

}

function edit_form(form_pk, nombre) {
    var form = $("<form action='?ID=do/edit/form' method='post'></form>");
    $("<p>Nombre:</p>").appendTo(form);
    $("<input type='hidden' name='form' value='" + form_pk  +"'/>").appendTo(form);
    $("<input type='text' name='nombre' value='" + nombre + "'/>").appendTo(form);

    form.submit(function() {
        $(form).dialog("close");
    });

    form.dialog({
        title: 'Editar formulario',
        buttons: {
            Guardar: function() {
                form.submit();
                $(this).dialog("close");
            },
            Cancelar: function() {
                $(this).dialog("close");
            }
        }
    });
    form.ajaxForm({
        success: function(data) {
            var formulario = $.parseJSON(data);
            $("h3#nombre").html(formulario.nombre);
        },
        error: function(code) {
            alert(code);
        }
    });

}

function succsess(data) {
    var formulario = $.parseJSON(data);
    $("h3#nombre").html(formulario.nombre);
    $("a#npa").click(function() {
        crea_abierta(formulario.id);
        return false;
    });
    $("a#npc").click(function() {
        crea_cerrada(formulario.id);
        return false;
    });
    $("input[name='form']").val(formulario.id);
    return true;
}

function crea_abierta(formulario_pk) {

    var form = $("<form action='?ID=do/crea/abierta' method='post'></form>");
    $("<p>Enunciado:</p>").appendTo(form);
    $("<input type='hidden' name='formulario' value='" + formulario_pk + "'/>").appendTo(form);
    $("<textarea name='enunciado'></textarea>").appendTo(form);

    form.submit(function() {
        $(form).dialog("close");
    });
    form.dialog({
        buttons: {
            Guardar: function() {
                form.submit();
                $(this).dialog("close");
            },
            Cancelar: function() {
                $(this).dialog("close");
            }
        }
    });
    form.ajaxForm({
        success: function(data) {
            var abierta = $.parseJSON(data);
            var li = $("<li pregunta='" + abierta.id_preg + "'><p>" + abierta.enunciado + "</p></li>").appendTo("ol#preguntas");
            var bo = $("<a href='#'>Borrar</a>").prependTo(li);
            var ed = $("<a href='#'>Editar</a>").prependTo(li);
            ed.click(function() {
                edit_pregunta(abierta.id_preg, abierta.enunciado);
                return false;
            });
            bo.click(function() {
                del_pregunta(abierta.id_preg);
                return false;
            });
        }
    });

}

function crea_cerrada(formulario_pk) {
    var form = $("<form action='?ID=do/crea/cerrada' method='post'></form>");
    $("<p>Enunciado:</p>").appendTo(form);
    $("<input type='hidden' name='formulario' value='" + formulario_pk + "'/>").appendTo(form);
    $("<textarea name='enunciado'></textarea>").appendTo(form);

    form.submit(function() {
        $(this).dialog("close");
    });

    form.dialog({
        buttons: {
            Guardar: function() {
                form.submit();
                $(this).dialog("close");
            },
            Cancelar: function() {
                $(this).dialog("close");
            }
        }
    });
    form.ajaxForm({
        success: function(data) {
            var cerrada = $.parseJSON(data);
            var li = $("<li pregunta='" + cerrada.id_preg + "'></p>" + cerrada.enunciado + "</p>R/</li>").appendTo("ol#preguntas");
            var ol = $("<ol></ol>").appendTo(li);
            var ad = $("<a href='#'>Agregar respuesta</a>").appendTo(li);
            var bo = $("<a href='#'>Borrar</a>").prependTo(li);
            var ed = $("<a href='#'>Editar</a>").prependTo(li);
            ed.click(function() {
                edit_pregunta(cerrada.id_preg, cerrada.enunciado);
                return false;
            });
            bo.click(function() {
                del_pregunta(cerrada.id_preg);
                return false;
            });

            ad.click(function() {
                crea_resp(ol, cerrada.id_cerrada);
                return  false;
            });
        }
    });
}

function crea_resp(ol, cerrada) {
    var form = $("<form action='?ID=do/crea/respuesta/cerrada' method='post'></form>");
    $("<p>Valor:</p>").appendTo(form);
    $("<input type='hidden' name='pregunta' value='" + cerrada + "'/>").appendTo(form);
    $("<textarea name='valor'></textarea>").appendTo(form);
    form.submit(function() {
        $(this).dialog("close");
    });

    form.dialog({
        buttons: {
            Guardar: function() {
                form.submit();
                $(this).dialog("close");
            },
            Cancelar: function() {
                $(this).dialog("close");
            }
        }
    });
    form.ajaxForm({
        success: function(data) {
            var respuesta = $.parseJSON(data);
            var li = $("<li respuesta='" + respuesta.id + "'><span respuesta='" + respuesta.id + "'>" + respuesta.valor + "</span>   </li>").appendTo(ol);
            var ed = $("<a href='#'>Editar</a>").appendTo(li);
            var bo = $("<a href='#'>Borrar</a>").appendTo(li);
            ed.click(function() {
                edit_resp(respuesta.id, $("span[respuesta='" + respuesta.id + "']").html());
                return false;
            });
            bo.click(function() {
                del_resp(respuesta.id);
                return false;
            });
        }
    });

}

function edit_resp(respuesta_pk, respuesta_valor) {
    var form = $("<form action='?ID=do/edit/respuesta/cerrada' method='post'></form>");
    $("<p>Valor:</p>").appendTo(form);
    $("<input type='hidden' name='respuesta' value='" + respuesta_pk + "'/>").appendTo(form);
    $("<textarea name='valor'>" + respuesta_valor + "</textarea>").appendTo(form);
    form.submit(function() {
        $(this).dialog("close");
    });

    form.dialog({
        buttons: {
            Guardar: function() {
                form.submit();
                $(this).dialog("close");
            },
            Cancelar: function() {
                $(this).dialog("close");
            }
        }
    });
    form.ajaxForm({
        success: function(data) {
            var respuesta = $.parseJSON(data);
            $("span[respuesta='" + respuesta.id + "']").html(respuesta.valor);
        }
    });

}

function del_resp(respuesta_pk) {
    $("<div>¿Borrar la respuesta?</div>").dialog({
        buttons: {
            Si: function() {
                $.ajax({
                    url: '?ID=do/del/respuesta/cerrada',
                    type: 'POST',
                    data: {respuesta: respuesta_pk},
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                        $("li[respuesta='" + respuesta.id + "']").remove();
                    }
                });
                $(this).dialog("close");
            },
            No: function() {
                $(this).dialog("close");
            }
        }
    });
}

function edit_pregunta(pregunta_pk, enunciado) {
    var form = $("<form action='?ID=do/edit/pregunta' method='post'></form>");
    $("<p>Enunciado:</p>").appendTo(form);
    $("<input type='hidden' name='pregunta' value='" + pregunta_pk + "'/>").appendTo(form);
    $("<textarea name='enunciado'>" + enunciado + "</textarea>").appendTo(form);

    form.submit(function() {
        $(this).dialog("close");
    });

    form.dialog({
        buttons: {
            Guardar: function() {
                form.submit();
                $(this).dialog("close");
            },
            Cancelar: function() {
                $(this).dialog("close");
            }
        }
    });
    form.ajaxForm({
        success: function(data) {
            var pregunta = $.parseJSON(data);
            $("li[pregunta='" + pregunta.id_preg + "'] p").html(pregunta.enunciado);
        }
    });
}

function del_pregunta(pregunta_pk) {
    $("<div>¿Borrar la pregunta?</div>").dialog({
        buttons: {
            Si: function() {
                $.ajax({
                    url: '?ID=do/del/pregunta',
                    type: 'POST',
                    data: {pregunta: pregunta_pk},
                    success: function(data) {
                        var pregunta = $.parseJSON(data);
                        $("li[pregunta='" + pregunta.id_preg + "']").remove();
                    }
                });
                $(this).dialog("close");
            },
            No: function() {
                $(this).dialog("close");
            }
        }
    });
}

function del_form(form_pk) {
    $("<div>¿Borrar el formulario?</div>").dialog({
        buttons: {
            Si: function() {
                $.ajax({
                    url: '?ID=do/del/form',
                    type: 'POST',
                    data: {form: form_pk},
                    success: function(data) {
                        window.location = "?ID=ver/all/form";
                    }
                });
                $(this).dialog("close");
            },
            No: function() {
                $(this).dialog("close");
            }
        }
    });
}

