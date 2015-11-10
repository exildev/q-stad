/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function (){
    $("div[cerrada]").each(function (index, elm){
        var cerrada = $(this).attr("cerrada");
        var self = $(this);
        var enunciado = self.attr("name");
        $.ajax({
            url: "?ID=get/stad/cerr",
            data: {"cerrada": cerrada},
            success: function (data){
                
                var json = $.parseJSON(data);
                
                renderChart(json,enunciado,self);
            }
        });
    });
    
    $("div[abierta]").each(function (index, elm){
        var abierta = $(this).attr("abierta");
        var self = $(this);
        var enunciado = self.attr("name");
        $.ajax({
            url: "?ID=get/stad/abi",
            data: {"abierta": abierta},
            success: function (data){
                var json = $.parseJSON(data);
                renderChart(json,enunciado,self);
            }
        });
    });
});

function renderChart(series,pregunta,div) {
        div.highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: pregunta
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:['']
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Cantidad '
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} Personas</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series:series
        });
    }