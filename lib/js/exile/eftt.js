/* 
 * @author: eXile
 * @date: 08-07-2013
 * @description: Esta librería sirve para dibujar gráficas estadísticas.
 * @requiere: jquery.js(jquery), lienzo.js(exile)
 */

var style = {
    bar: "#11f",
    txt: "#000",
    lin: "#f11",
    cur: "#aff"
};

$.fn.eftt = function(data,options) {
    return this.each(function() {
        var elmns = Object.keys(data).length;
        var width = $(this).width() < elmns * 50 ? elmns * 50 : $(this).width();
        var height = $(this).height();
        var canvas = $("<canvas></canvas>").appendTo(this);
        canvas.attr("width", width);
        canvas.attr("height", height - 20);
        height = height - 30;

        var estado = 0;
        var intervalo = 10;
        var maximo = options.maximo || 100;
        var zoom = elmns;
        var barw = 20;
        var method = options.method || "bar";
        var oldx = 0;
        var oldy = 0;

        bg(10);
        for (var o in data) {
            stad(data[o]);
        }
    //    

        function stad(val) {
            var fn = eval(method);
            canvas.lienzo(fn(val));
            var ez = parseZoomW(estado - 1);
            var text = Object.keys(data)[estado - 1];
            canvas.lienzo({
                fillStyle: style.txt,
                font: "15px Arial",
                fillText: [text, ez * intervalo + 20, height]
            });
        }

        function parseZoomW(w) {
            return (w) * width / (zoom * intervalo);
        }

        function parseZoomH(h) {
            return (maximo - h) * height / maximo;
        }

        function next() {
            estado++;
        }

        function bg(num) {
            var step = height / (num);
            for (var i = 0; i <= num; i++) {
                canvas.lienzo({
                    beginPath: [],
                    moveTo: [0, (i) * step],
                    lineTo: [width, (i) * step],
                    lineWidth: 1,
                    strokeStyle: "#111",
                    stroke: []
                });
                canvas.lienzo({
                    fillText: [(maximo - maximo * i / (num) + "").substr(0, 3), 0, i * step + 10]
                });
            }
        }

        function bar(h) {
            var hz = parseZoomH(h);
            var ez = parseZoomW(estado);
            var bar = {
                fillStyle: style.bar,
                fillRect: [ez * intervalo + 20, hz, barw, height - hz]
            };
            next();
            return bar;
        }

        function line(h) {
            var hz = parseZoomH(h);
            var ez = parseZoomW(estado);
            var newx = ez * intervalo + 20;
            var newy = hz;

            if (oldy > 0 || oldx > 0) {
                var line = {
                    lineTo: [newx, newy],
                    strokeStyle: style.lin,
                    stroke: []
                };
            }else{
                var line = {
                    moveTo: [newx, newy],
                };
            }
            oldx = newx;
            oldy = newy;
            next();
            return line;
        }
        function cur(h) {
            var hz = parseZoomH(h);
            var ez = parseZoomW(estado);
            var newx = ez * intervalo + 20;
            var newy = hz;
            if (oldy > 0 || oldx > 0) {

                var line = {
                    quadraticCurveTo: [oldx, oldy, newx - (newx - oldx) / 4, newy - (newy - oldy) / 4, newx, newy],
                    
                    stroke: []
                };
            }else{
                
            }
            oldx = newx;
            oldy = newy;
            next();
            return line;
        }

    });
};



