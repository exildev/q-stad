/* 
 * @author: eXile
 * @date: 08-07-2013
 * @description: Esta libreria sirve para el manejo de la etiqueta canvas.
 * @requiere: jquery.js(jquery)
 */

$.fn.lienzo = function (options){
    return this.each(function (){
        if (this.ctx === undefined){
            this.ctx = this.getContext("2d");
        }
        for (var i in options){
            if (this.ctx[i]["apply"] !== undefined){
                console.log("call[" + i + "]: " +  options[i]);
                this.ctx[i].apply(this.ctx, options[i]);
            }else{
                console.log("set[" + i + "]: " +  options[i]);
                this.ctx[i] = options[i];
            }
        }
    });
};
