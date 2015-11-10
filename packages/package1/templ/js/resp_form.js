/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    session();
    var dialog = $("<div>Espere...</div>");
    $("form").ajaxForm({
        beforeSubmit: function() {
            dialog.dialog();
        },
        success: function(data) {
            dialog.dialog("close");
            $("<div>Formulario guardado correctamente</div>").dialog({
                buttons: {
                    Ok: function() {
                        window.location = "./";
                    }
                }
            });
        },
        error: function() {
            dialog.dialog("close");
            $("<div>Ocurrio un error intentando guardar, porfavor intente mas tarde</div>").dialog({
                buttons: {
                    Ok: function() {
                        $(this).dialog("close");
                    }
                }
            });
        }
    });
});

function session() {
    $.ajax({
        url: "?ID=get/session",
        error: function() {
            login();
        }
    });
}

function login() {
    var form = $("<form action='?ID=get/login' method='post'></form>");
    $("<p>Usuario:</p>").appendTo(form);
    $("<input type='text' name='username'/>").appendTo(form);
    $("<p>Clave:</p>").appendTo(form);
    $("<input type='password' name='password'/>").appendTo(form);

    form.submit(function() {
        $(form).dialog("close");
    });

    form.dialog({
        buttons: {
            Validar: function() {
                form.submit();
                $(this).dialog("close");
            }
        }
    });
    form.ajaxForm({
        error: function(code) {
            login()
        }
    });

}