/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    $("input[name='cerradura']").change(function() {
        var respuesta = $(this).attr("respuesta");
        var cerradura = $(this).val();
        $.ajax({
            url: "?ID=do/cerrar/resp",
            type: "POST",
            data: {resp: respuesta, cerr: cerradura},
            success: function(data) {
                var obj = $.parseJSON(data);
                
            },
            error: function(code) {

            }
        });
    });
});
