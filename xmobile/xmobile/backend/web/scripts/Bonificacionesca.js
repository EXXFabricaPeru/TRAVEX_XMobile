var  arrayCodigo = [];
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : new P(function (resolve) { resolve(result.value); }).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = y[op[0] & 2 ? "return" : op[0] ? "throw" : "next"]) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [0, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};

var Bonificacionesca = (function () {
    function Bonificacionesca() {
        this.element = $(".window");
        this.url = $("#PATH").attr("name");
        this.Objet = Object;
        this.elementGrid = "#Bonificacionesca-list";
        this.setting = { width: '80%', height: 560, hide: 'fade', show: 'fade', modal: true };
    }
    Bonificacionesca.prototype.onloadViewForm = function () {
        $("#bonificacionesca-u_fecha_fin").datepicker({ dateFormat: 'yy-mm-dd' });
        $("#bonificacionesca-u_fecha_inicio").datepicker({ dateFormat: 'yy-mm-dd' });
		
		/*$("#Code").onBlur(function () {
            codigoBonificacion();
        });*/
        
    };
    
    Bonificacionesca.prototype.windowCreate = function () {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'NUEVO REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'bonificacionesca/create', function () {
                    _this.onloadViewForm();
					getCodigoBonificacion();
                });
            },
            buttons: [{
                    text: "REGISTRAR",
                    class: "btn btn-success",
					id:"btn-guardarregistro",
                    click: function () { if(validarFormulario()){ return __awaiter(_this, void 0, void 0, function () {
                        if($("#U_REGLATIPO").val()=='GRUPO DE PRODUCTOS')
                            $("#U_TIPO").val('1');
                        else
                            $("#U_TIPO").val('0');
                    
                        var data, respt, key;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    data = $('#Bonificacionesca-form').serialize();
                                   
                                    return [4 /*yield*/, this.requestPost(data)];
                                case 1:
                                    respt = _a.sent();
                                    if (isNaN(respt)) {
                                        $(".text-clear").html('');
                                        for (key in respt)
                                            $("#error-" + key).html(respt[key][0]);
                                    }
                                    else {
                                        $.toast({
                                            heading: 'Success',
                                            text: 'Registrado correctamente.',
                                            showHideTransition: 'fade',
                                            icon: 'success'
                                        });
                                        $.pjax.reload({ container: this.elementGrid, async: false });
                                       //location.reload();
                                        this.element.dialog("close");
                                    }
                                    return [2 /*return*/];
                            }
                        });
                    });
                    }

                    }
                }, {
                    text: "CANCELAR",
                    class: "btn btn-warning",
                    click: function () {
                        _this.element.dialog("close");
                    }
                }]
        };
        var opt = this.Objet.assign(this.setting, option);
  
        this.element.dialog(opt);

    };
    Bonificacionesca.prototype.windowEliminar = function (id) {
        var _this = this;
        var obj = {
            width: 380, height: 230, hide: 'fade', show: 'fade', modal: true, title: 'ALERTA',
            buttons: [{
                    text: "SI",
                    class: "btn btn-danger",
                    click: function () { return __awaiter(_this, void 0, void 0, function () {
                        var e_1;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    _a.trys.push([0, 2, , 3]);
                                    return [4 /*yield*/, this.requestDelete(id)];
                                case 1:
                                    _a.sent();
                                    $.pjax.reload({ container: this.elementGrid, async: false });
                                    $("#windowEliminar").dialog("close");
                                    return [3 /*break*/, 3];
                                case 2:
                                    
                                    e_1 = _a.sent();
                                    //alert("Ocurrio un !ERROR");
                                    $.pjax.reload({ container: this.elementGrid, async: false });
                                    $("#windowEliminar").dialog("close");
                                    return [3 /*break*/, 3];
                                case 3: return [2 /*return*/];
                            }
                        });
                    }); }
                }, {
                    text: "NO",
                    class: "btn btn-success",
                    click: function () {
                        $("#windowEliminar").dialog("close");
                    }
                }]
        };
        $("#windowEliminar").dialog(obj);
    };
    Bonificacionesca.prototype.windowEdit = function (id) {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'ACTUALIZAR REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'bonificacionesca/update&id=' + id, function () {
                    _this.onloadViewForm();
					//getCodigoBonificacion();
                    document.getElementById('Code').disabled=true; 
                    $("#IDCABECERA").val(id);
                    validarCantidad($("#bonificacionesca-u_reglacantidad").val());                  
                });
            },
            buttons: [{
                    text: "ACTUALIZAR",
                    class: "btn btn-success",
					id:"btn-editarregistro",
                    click: function () { if(validarFormulario()){ return __awaiter(_this, void 0, void 0, function () {
                        console.log("ingresa para guardar registros");
                        if($("#U_REGLATIPO").val()=='GRUPO DE PRODUCTOS')
                            $("#U_TIPO").val('1');
                        else
                            $("#U_TIPO").val('0');

                        var data, respt, key;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    data = $('#Bonificacionesca-form').serialize();
                                    return [4 /*yield*/, this.requestPut(id, data)];
                                case 1:
                                    respt = _a.sent();
                                    if (isNaN(respt)) {
                                        $(".text-clear").html('');
                                        for (key in respt)
                                            $("#error-" + key).html(respt[key][0]);
                                    }
                                    else {
                                        $.toast({
                                            heading: 'Success',
                                            text: 'Modificado correctamente.',
                                            showHideTransition: 'fade',
                                            icon: 'success'
                                        });
                                        $.pjax.reload({ container: this.elementGrid, async: false });
                                        //location.reload();
                                        this.element.dialog("close");
                                    }
                                    return [2 /*return*/];
                            }
                        });
                    }); 
                    }
                    }
                }, {
                    
                    text: "CANCELAR",
                    "class": "btn btn-warning",
                    click: function () {
                        _this.element.dialog("close");
                    }
                }]
        };
        var opt = this.Objet.assign(this.setting, option);
        this.element.dialog(opt);
    };
    Bonificacionesca.prototype.windowPdf = function (id) {
        var url = this.url + 'bonificacionesca/report&id=' + id;
        $("#windowpdf").dialog({
            width: '100%', height: 500, modal: true, open: function () {
                $("#windowpdf").html('<embed src="' + url + '" type="application/pdf" width="100%" height="100%" />');
            }
        });
    };
    Bonificacionesca.prototype.requestPut = function (id, data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'bonificacionesca/update&id=' + id,
                type: 'PUT',
                data: data,
                success: function (data, status, xhr) {
                    resolve(JSON.parse(data));
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    reject(errorMessage);
                }
            });
        });
    };
    Bonificacionesca.prototype.requestDelete = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.get(_this.url + 'bonificacionesca/eliminar&id=' + data).done(function (data) {
                resolve(data);
            }).fail(function (err) {
                reject(err);
            });
        });
    };
    Bonificacionesca.prototype.requestPost = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'bonificacionesca/create',
                type: 'POST',
                data: data,
                success: function (data, status, xhr) {
                    resolve(JSON.parse(data));
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    reject(errorMessage);
                }
            });
        });
    };

    //////////////codigo adicional///////////////////
   
       ///////para mostrar la lista de items
       Bonificacionesca.prototype.windowLista = function (id,nombreBonificacion,reglaTipo) {
        
       

            var _this = this;
            var items = document.getElementById("tr-detalle-bonificacion");
            items.innerHTML="";
            //return new Promise(function (resolve, reject) {
                $.ajax({
                    url: _this.url + 'bonificacionesca/consultas',
                    type: 'POST',
                    //dataType: 'json',
                    data: 'CONDICION=DETALLEBONIFICACION&ID='+id,
                    success: function (data) {
                      
                      if(data.trim()!="0"){
                        var ArrayData = data.replace('{"','').replace('"}','');
                        ArrayData=ArrayData.split('","');
                        if(ArrayData.length>=1){
                          for(var i=0;i<ArrayData.length;i++){
                              var valor=ArrayData[i].split('":"');
                              AdicionarRow(i,valor[0],valor[1],'bonificacion');
     
                           }
                        }
                      }
   
                   },
                    error: function (jqXhr, textStatus, errorMessage) {
                        reject(errorMessage);
                    }
                });
            //});

            var obj = {
            width: '70%', height: 550, hide: 'fade', show: 'fade', modal: true, title: 'ARTICULOS BONIFICACION - '+nombreBonificacion.toUpperCase(),
            buttons: [{
                    text: "GUARDAR CAMBIOS",
                    class: "btn btn-danger",
                    click: function () { 
                         if(serializaTabla(_this,'bonificacion',id,reglaTipo))$("#windowLista").dialog("close");
                         
                         

                    }
                }, {
                    text: "CANCELAR",
                    class: "btn btn-success",
                    click: function () {
                        $("#windowLista").dialog("close");
                    }
                }]
        };
        $("#windowLista").dialog(obj);       
    };

    ///////////////////////////////////////////////////////
    Bonificacionesca.prototype.windowListaDescuento = function (id,nombreBonificacion,reglaTipo) {
        
       

        var _this = this;
        var items = document.getElementById("tr-detalle-descuento");
        items.innerHTML="";
        //return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'bonificacionesca/consultas',
                type: 'POST',
                //dataType: 'json',
                data: 'CONDICION=DETALLEBONIFICACION&ID='+id,
                success: function (data) {
                if(data.trim()!="0"){
                  var ArrayData = data.replace('{"','').replace('"}','');
                   ArrayData=ArrayData.split('","');
                  if(ArrayData.length>=1){
                    for(var i=0;i<ArrayData.length;i++){
                        //alert (ArrayData[i]);
                        var valor=ArrayData[i].split('":"');
                        AdicionarRow(i,valor[0],valor[1],'descuento');

                     }
                  }
                }

               },
                error: function (jqXhr, textStatus, errorMessage) {
                    reject(errorMessage);
                }
            });
        //});

            var obj = {
            width: '70%', height: 500, hide: 'fade', show: 'fade', modal: true, title: 'ARTICULOS DESCUENTO - '+nombreBonificacion.toUpperCase(),
            buttons: [{
                    text: "GUARDAR CAMBIOS",
                    class: "btn btn-danger",
                    click: function () {                  
                        if(serializaTabla(_this,'descuento',id,reglaTipo))$("#windowListaDescuento").dialog("close");
                        
                       
                    }
                }, {
                    text: "CANCELAR",
                    class: "btn btn-success",
                    click: function () {
                        $("#windowListaDescuento").dialog("close");
                    }
                }]
            };
            $("#windowListaDescuento").dialog(obj);       
        };

         ///////////////////////////////////////////////////////
    Bonificacionesca.prototype.windowListaCompra = function (id,nombreBonificacion,reglaTipo,idReglaBonificacion,cantidaCompra) {
        
        var _this = this;
        var items = document.getElementById("tr-detalle-compra");
        items.innerHTML="";
        //return new Promise(function (resolve, reject) {
        // se muesta la caja de texto si el id es igual la detalle linea

        //caso N y M - descuentos de linera
        if( idReglaBonificacion==11 || idReglaBonificacion==12){
            
            if(idReglaBonificacion==11){
                $("#td-cantidad").show();
            }
            else{
                $("#td-cantidad").show();
                $("#td-check").show();    
            }
            
        }
        else{
            if(idReglaBonificacion==11){
                $("#td-cantidad").hide();
            }
            else{
               $("#td-cantidad").hide();
               $("#td-check").hide();   
            }  
        }

        $("#CANTIDADCOMPRA").val(cantidaCompra);
            $.ajax({
                url: _this.url + 'bonificacionesca/consultas',
                type: 'POST',
                //dataType: 'json',
                data: 'CONDICION=DETALLECOMPRA&ID='+id,
                success: function (data) {
                    /*if(data.trim()!="0"){
                        var ArrayData = data.replace('{"','').replace('"}','');
                         ArrayData=ArrayData.split('","');
                        if(ArrayData.length>=1){
                            //if(ArrayData[i]!='[]'){
                            for(var i=0;i<ArrayData.length;i++){
                                //alert (ArrayData[i]);
                                var valor=ArrayData[i].split('":"');
                                ///if(valor[0]!='[]'){
                                AdicionarRow(valor[0],valor[1],'compra',valor[2]);
                            }
                            //} 
                        }
                    }*/
                    var ArrayData=JSON.parse(data);
                    console.log("detalle compra");
                    console.log(ArrayData);
                    for(var i=0;i<ArrayData.length;i++){
                       
                        AdicionarRow(i,ArrayData[i].Code,ArrayData[i].Name,'compra',ArrayData[i].Cantidad,ArrayData[i].Estado);
                    }


               },
                error: function (jqXhr, textStatus, errorMessage) {
                    reject(errorMessage);
                }
            });
        //});

            var obj = {
            width: '80%', height: 600, hide: 'fade', show: 'fade', modal: true, title: 'ARTICULOS COMPRA - '+nombreBonificacion.toUpperCase(),
            buttons: [{
                    text: "GUARDAR CAMBIOS",
                    class: "btn btn-danger",
                    click: function () {                  
                        if(serializaTabla(_this,'compra',id,reglaTipo))$("#windowListaCompra").dialog("close");
                        
                        
                    }
                }, {
                    text: "CANCELAR",
                    class: "btn btn-success",
                    click: function () {
                        $("#windowListaCompra").dialog("close");
                    }
                }]
            };
            $("#windowListaCompra").dialog(obj);       
        };

        return Bonificacionesca;
}());
var create="";
var edit="";
$(function () {
    var model = new Bonificacionesca();

    $("#btn-create").on('click', function () {
        create="create";
		edit="";
        model.windowCreate();
    });
    $(document).on("click", ".btn-grid-action-delete", function () {
        var id = $(this).val();
        model.windowEliminar(id);
    });
    $(document).on("click", ".btn-grid-action-edit", function () {
		create="";
		edit="edit";
        var id = $(this).val();
        model.windowEdit(id);
    });
   /* $(document).on("click", ".btn-grid-action-pdf", function () {
        var id = $(this).val();
        model.windowCreateBonificacion1(id);
    });
    */
	$(document).on("click", ".btn-duplicar", function () {
        var valor = $(this).val().split('@');
        var id=valor[0];
		this.url = $("#PATH").attr("name");
        var  nota="Nota:";
        $.ajax({
            url:   $("#PATH").attr("name")+ 'bonificacionesca/getdetallebonificacion',
            type: 'POST',
            data: "id="+id,
            success: function (data, status, xhr) {
                resolve=JSON.parse(data);
                console.log(resolve[0]);
                console.log(resolve[1]);
                if(valor[1]=='1'){
                    if(resolve[0]==0){
                        nota=nota+"\n El registro no tiene Items bonificables";
                    }
                    if(resolve[1]==0){
                        nota=nota+"\n El registro no tiene Items de compra";
                    }
                }
                else if(resolve[1]==0){
                    nota=nota+"\n El registro no tiene Items de compra";
                }  
                

                if(confirm(nota+"\n Seguro que desea duplicar el registro de bonificación..?")){
			  
                    $.post( $("#PATH").attr("name") + 'bonificacionesca/consultas',{CONDICION:'DUPLICARREGISTRO',ID:id},function(data){
                        //alert(data);
                        //respuesta=JSON.stringify(data);
                        if(data.trim()=='TRUE'){
                            location.reload(); 
                        }
                        else{
                            console.log("Error! revise modelo Bonificacion1");
                        }
                        
                    });
                }

            },
            error: function (jqXhr, textStatus, errorMessage) {
                reject(errorMessage);
            }
        });
      
    });

    $(document).on("click", ".btn-adiciona-bono", function () {
        var id = $(this).val();
        var valor=id.split('@');

        var grupoEspecifico = document.getElementById("DIV-GRUPOBONIFICACION");
        var productoEspecifico = document.getElementById("DIV-PRODUCTOBONIFICACION");
        //if(valor[2]=='0'){
            grupoEspecifico.style.display = "none";
            productoEspecifico.style.display = "block";
        /*}  
        else{
            grupoEspecifico.style.display = "block";
            productoEspecifico.style.display = "none";
        }*/
        model.windowLista(valor[0],valor[1],valor[2]);
    });

    $(document).on("click", ".btn-adiciona-descuento", function () {
        var id = $(this).val();
        var valor=id.split('@');

        var grupoEspecifico = document.getElementById("DIV-GRUPODESCUENTO");
        var productoEspecifico = document.getElementById("DIV-PRODUCTODESCUENTO");
        if(valor[2]=='0'){
            grupoEspecifico.style.display = "none";
            productoEspecifico.style.display = "block";
        }  
        else{
            grupoEspecifico.style.display = "block";
            productoEspecifico.style.display = "none";
        }
            
        model.windowListaDescuento(valor[0],valor[1],valor[2]);
    });

    $(document).on("click", ".btn-adiciona-compra", function () {
        var id = $(this).val();
        var valor=id.split('@');

        var grupoEspecifico = document.getElementById("DIV-GRUPOCOMPRA");
        var productoEspecifico = document.getElementById("DIV-PRODUCTOCOMPRA");
        if(valor[2]=='0'){
            grupoEspecifico.style.display = "none";
            productoEspecifico.style.display = "block";
        }  
        else{
            grupoEspecifico.style.display = "block";
            productoEspecifico.style.display = "none";
        }
        model.windowListaCompra(valor[0],valor[1],valor[2],valor[3],valor[4]);
    });

});
///////funciones adicionales////////
function cambiar_color_over(celda){
    colorTr = celda.style.backgroundColor;
    celda.style.backgroundColor="#F8DC3D";
}
function cambiar_color_out(celda){
    celda.style.backgroundColor=colorTr;
} 

function AdicionarFila(id){//formulario de regalo
    //condicionpara adicionar a la tabla dinamica
    var opt = $('option[value="'+$("#"+id).val()+'"]');
    var codigo=opt.length ? opt.attr('id') : 'NO OPTION';
    //alert(usuarioM);
    //////////
    if(codigo!='NO OPTION'){
		
		if(validarTabla('bonificacion',codigo)){
			producto=$("#"+id).val().split('-');
			producto=producto[1];
            var index= $('#table-list-bonificacion').children().length;
			AdicionarRow(index,codigo,producto,'bonificacion');
			$("#"+id).val("");
		}
		else{
			alert("Error-1! El item ya esta adicionado en el detalle\n Seleccione otro item.");
		}
       
    }
    else{
        alert("Seleccione un item");
    }

}

function AdicionarFilaDescuento(id){
    //condicionpara adicionar a la tabla dinamica
    var opt = $('option[value="'+$("#"+id).val()+'"]');
    var codigo=opt.length ? opt.attr('id') : 'NO OPTION';
    //alert(usuarioM);
    //////////
    if(codigo!='NO OPTION'){
		if(validarTabla('descuento',codigo)){
            //verificando si el item esta en otra bonificaciones
            $.ajax({
                url: $("#PATH").attr("name")+'bonificacionesca/itembonificacioncompra',
                type: 'POST',
                //dataType: 'json',
                data: 'item='+codigo,
                success: function (data) {
                    var ArrayDatos=JSON.parse(data);
                    console.log("Item COMPRA");
                    console.log(ArrayDatos);
                    if(ArrayDatos.tipo!=undefined){
                        alert("El Item seleccionado ya se encuentra registrado: "+ArrayDatos.Code+" - "+ArrayDatos.Name );
                    }
                    
                    producto=$("#"+id).val().split('-');
                    producto=producto[1];
                    var index= $('#table-list-descuento').children().length;
                    AdicionarRow(index,codigo,producto,'descuento');
                    $("#"+id).val("");

                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });
		}
		else{
			alert("Error-2! El item ya esta adicionado en el detalle\n Seleccione otro item.");
		}
    }
    else{
        alert("Seleccione un item");
    }

}

function AdicionarFilaCompra(id){
    //condicionpara adicionar a la tabla dinamica
    var opt = $('option[value="'+$("#"+id).val()+'"]');
    var codigo=opt.length ? opt.attr('id') : 'NO OPTION';
    //alert(usuarioM);
    //////////
    if(codigo!='NO OPTION'){
		if(validarTabla('compra',codigo)){
            //verificando si el item esta en otra bonificaciones
            $.ajax({
                url: $("#PATH").attr("name")+'bonificacionesca/itembonificacioncompra',
                type: 'POST',
                //dataType: 'json',
                data: 'item='+codigo,
                success: function (data) {
                    var ArrayDatos=JSON.parse(data);
                    console.log("Item COMPRA");
                    console.log(ArrayDatos);
                    if(ArrayDatos.tipo!=undefined){
                        alert("El Item seleccionado ya se encuentra registrado: "+ArrayDatos.Code+" - "+ArrayDatos.Name );
                    }
                    //condicion
                    producto=$("#"+id).val().split('-');
                    producto=producto[1];
                    var index= $('#table-list-compra tbody').children().length;
                    AdicionarRow(index,codigo,producto,'compra');
                    $("#"+id).val("");


                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });
		}
		else{
			alert("Error! El item ya esta adicionado en el detalle\n Seleccione otro item.");
		}
    }
    else{
        alert("Seleccione un item");
    }

}


function AdicionarRow(idAux,codigo,producto,tabla,cantidad='',valor=''){
    idAux++;
    //alert(tabla);
    if(codigo!=undefined){
        var items = document.getElementById("tr-detalle-"+tabla+"");
        // var aleatorio = Math.round(Math.random()*10000);
     
        var nuevoCampo="";
        var cantidadValor="";

        if($("#td-cantidad").is(":visible") ){
            var idCaja=codigo.replace('&','');
            if(cantidad!=''){
                cantidadValor= ' value="'+cantidad+'" ';   
            }
            if(valor=='0')valor='';
            else valor='checked';

            if($("#td-check").is(":visible") ){

                nuevoCampo='<td style="width:10">  <input type="text"  id="CANTIDAD-'+idCaja+'"  name="CANTIDAD-'+idCaja+'" size="5" '+cantidadValor+' onkeypress="return NumEntero(event, this)" > </td>'
                      +'<td style="width:10" align="center"><input type="checkbox" id="CHECK-'+idCaja+'" value="" '+valor+'  > </td>';
            }
            else{
                nuevoCampo='<td style="width:10">  <input type="text"  id="CANTIDAD-'+idCaja+'"  name="CANTIDAD-'+idCaja+'" size="5" '+cantidadValor+' onkeypress="return NumEntero(event, this)" > </td>';            
            }
        }


        contenido = '<tr  id="tr-fila-'+idAux+'" onmouseover="cambiar_color_over(this)" onMouseOut="cambiar_color_out(this)">'+
                    '<td style="width:10" scope="row">'+idAux+'</td>'+
                                                
                    '<td style="width:10"> '+codigo+'</td>'+
                                                
                    '<td style="width:10"> '+producto+' </td>'+
                                                
                    nuevoCampo+

                    '<td style="width:10" align="center" >  <button title="Eliminar Fila" class="btn-link" value="" onclick="EliminarFila('+idAux+')" ><i class="fas fa-trash-alt text-warning"></i></button>  </td>';                              
                    '</tr>'
        //items.innerHTML =items.innerHTML+ contenido;
        // $("#tr-detalle-"+tabla+" > tbody").prepend(contenido);
        //document.getElementById("tr-detalle-"+tabla+"").insertRow(-1).innerHTML=contenido;
        //alert(tabla);

        $("#table-list-"+tabla).find("tbody").append(contenido);
    }
    else{
            alert("Seleccione un articulo.");
    }
}

   function ListaDetalle(data){

    //alert(decode(data));
   }
   function EliminarFila(id){
      //alert(id);
      var Row = document.getElementById("tr-fila-"+id);
      Row.parentNode.removeChild(Row);
      
    }
     

    function serializaTabla(_this,tabla,id,reglaTipo){
        var nFilas = $("#table-list-"+tabla+" tr").length;
	    //alert(nFilas);
        var tablaItem="";
        var cantidad=0;
        var cols=3;
        var swCantidad=false;
        var cantidaCabecera=0;

        if($("#td-cantidad").is(":visible") ){
            cols=5;
        }   

        for (var i = 1; i < nFilas; i++) {
            for (var j = 1; j < cols; j++) {
                var codigo=document.getElementById("table-list-"+tabla).rows[i].cells[1].innerText;
                var idCaja=codigo.replace('&','');
                if(j==3){
                    
                    if($("#CANTIDAD-"+idCaja).val()=="" || $("#CANTIDAD-"+idCaja).val()=="0"){
                        swCantidad=true;
                    }else{
                        tablaItem=tablaItem+$("#CANTIDAD-"+idCaja).val()+"//";
                        cantidaCabecera=cantidaCabecera+($("#CANTIDAD-"+idCaja).val()/1);
                    }
                    console.log("Antes de entrar: ");
                    console.log("Cantidad rr: "+$("#CANTIDAD-"+idCaja).val());
                }
                else if(j==4){
                    console.log("Valor checkbox");
                    if( $("#CHECK-"+idCaja).prop('checked') ) {
                        tablaItem=tablaItem+"1//";   
                        console.log(1);
                    }
                    else{
                        tablaItem=tablaItem+"0//";
                        console.log(0);
                    }
                    
                  
                }
                else{
                   tablaItem=tablaItem+document.getElementById("table-list-"+tabla).rows[i].cells[j].innerText+"//";
                }
                
            }
            tablaItem=tablaItem+"@";
            
        }
       

        if(swCantidad){
             alert("Error! verifique la cantidad de los articulos, no debe tener valor cero o vacio.");
            return false;
        }

        console.log("condicion cabecera");
        console.log(cantidaCabecera+' > '+$("#CANTIDADCOMPRA").val());
        if(cantidaCabecera!=$("#CANTIDADCOMPRA").val() && cols==5){
            var opcion= confirm("La suma de las cantidades de los articulos de compra es diferente a la cantidad de compra de la cabecera\nNOTA: El descuento o bonificación se habilita cumpliendo ambos parámetros.\nCantidad compra global: "+$("#CANTIDADCOMPRA").val()+"\nTotal cantidad de articulos de compra: "+cantidaCabecera);
            if(opcion) swCantidad=false;
            else  return false;
           
        }
        if(!swCantidad){
            if(tabla=='compra'){
                saveDetalleCompra(_this,tablaItem,id,reglaTipo);
            }
            else{
                saveDetalleBono(_this,tablaItem,id,reglaTipo);
            }
            return true;
        }
       
       
       
    }
      function validaCajaTexto(valor){
        //alert(valor);
        if(valor=='DESCUENTO LINEA')console.log("ingreso descuento linea");
        else console.log("no ingreso descuento linea");
    }

    function saveDetalleBono(_this,tablaItem,id,reglaTipo){
   
        $.post(_this.url + 'bonificacionesca/consultas',{CONDICION:'GUARDARDETALLEBONO',DATA:tablaItem,ID:id,REGLATIPO:reglaTipo},function(data){
			//alert(data);
            //respuesta=JSON.stringify(data);
            
            //if(respuesta=='TRUE'){
                //alert("Cambios Guardados Correctamente");
                location.reload();   
            /*}
            else{
                console.log("Error! revise modelo Bonificacion1");
            }
			*/
		});
    }

    function saveDetalleCompra(_this,tablaItem,id,reglaTipo){
   
        $.post(_this.url + 'bonificacionesca/consultas',{CONDICION:'GUARDARDETALLECOMPRA',DATA:tablaItem,ID:id,REGLATIPO:reglaTipo},function(data){
			//alert(data);
            //respuesta=JSON.stringify(data);
            
            //if(respuesta=='TRUE'){
               // alert("Cambios Guardados Correctamente");  
                location.reload(); 
            /*}
            else{
                console.log("Error! revise modelo Bonificacion1");
            }
			*/
		});
    }
	
	//validar lista tabla
	function validarTabla(tabla,texto){
		var nFilas = $("#table-list-"+tabla+" tr").length;
	    //alert(nFilas);
        var tablaItem="";
        for (var i = 1; i < nFilas; i++) {
			valor=document.getElementById("table-list-"+tabla).rows[i].cells[1].innerText
            if(valor==texto){
				return false;
			}      
        }
		return true;
	}
	///funcion para obtener el codigo de bonificacionesca-u_fecha_fin
	
	function getCodigoBonificacion(){
		
		$.ajax({
			url: $("#PATH").attr("name")+'bonificacionesca/consultas',
			type: 'POST',
			//dataType: 'json',
			data: 'CONDICION=CODIGOBONIFICACION',
			success: function (data) {
			  
			  if(data.trim()!="0"){
				var ArrayData = data.replace('{"','').replace('"}','');
				ArrayData=ArrayData.split('","');
				if(ArrayData.length>=1){
				  for(var i=0;i<ArrayData.length;i++){
					  var valor=ArrayData[i].split('":"');
					  //AdicionarRow(valor[0],valor[1],'bonificacion');
					   arrayCodigo[i]=valor[1];
					  
				   }
				}
			  }

		    },
			error: (jqXhr, textStatus, errorMessage) => {
                console.error("ERROR: " + errorMessage);
            }
		});
	
	}
	
	function codigoBonificacion(){
		var texto=$("#Code").val();
		var valor = false;
		for(var i=0;i<arrayCodigo.length;i++){
			console.log(texto+' = '+arrayCodigo[i])
			if(texto.toLowerCase().trim()==arrayCodigo[i].toLowerCase().trim()){
				valor=true;
			}
		}
		if(valor){
			alert("Error! el codigo ya existe en un registro.");
			if(create=='create'){
				document.getElementById('btn-guardarregistro').disabled=true;
			}
			else{
				document.getElementById('btn-editarregistro').disabled=true;
			}	
		}
		else{
			if(create=='create'){
				document.getElementById('btn-guardarregistro').disabled=false;
			}
			else{
				document.getElementById('btn-editarregistro').disabled=false;
			}
			
		}		
		 
		
	}


    function cargarPagina(){
        // location.reload("dddd");
         location.href = "index.php?r=bonificacionesca&fecha="+$('#fechaFiltro').val()+"&estado="+$('#VIGENTE').val();
    }
    
    function validarFormulario(){
        var day1 = new Date($("#bonificacionesca-u_fecha_inicio").val());
        var day2 = new Date($("#bonificacionesca-u_fecha_fin").val());

        var difference = day2.getTime()-day1.getTime();
        var swError=true; 

        if(difference < 0){
            alert("error! la fecha fin no puede ser menor a la fecha inicio.");
            swError= false;
            console.log("1-fechas");
        }
        if($("#bonificacionesca-montototal").is(":visible") && ($("#bonificacionesca-montototal").val()=='' || $("#bonificacionesca-montototal").val()=='0')){
            $("#error-montoTotal").text("El monto total tiene que ser un número mayor a cero");
            swError= false;
            console.log("2-moto total");
        }
        if($("#bonificacionesca-u_reglacantidad").is(":visible") && ($("#bonificacionesca-u_reglacantidad").val()=='' || $("#bonificacionesca-u_reglacantidad").val()=='0')){
            $("#error-U_reglacantidad").text("La cantidad de compra tiene que ser un número mayor a cero");
            swError= false;
            console.log("3- cantidad compra");
        }
        if($("#bonificacionesca-cantidadmaximacompra").is(":visible") && ($("#bonificacionesca-cantidadmaximacompra").val()=='' || $("#bonificacionesca-cantidadmaximacompra").val()=='0')){
            $("#error-cantidadMaximaCompra").text("La cantidad maxima de compra tiene que ser un número mayor a cero");
            swError= false;
            console.log("4- cantidad maxima de compra");
        }
        if($("#bonificacionesca-u_limitemaxregalo").is(":visible") && $("#bonificacionesca-u_limitemaxregalo").val()==''){
            $("#error-U_limitemaxregalo").text("El limite maximo regalo no puede estar vacío");
            swError= false;
            console.log("5- limite maximo regalo");
        }
        if($("#bonificacionesca-porcentajedescuento").is(":visible") && ($("#bonificacionesca-porcentajedescuento").val()=='' || $("#bonificacionesca-porcentajedescuento").val()=='0')){
            $("#error-porcentajeDescuento").text("El porcentaje tiene que ser mayor a cero");
            swError= false;
            console.log("6- porcentaje");
        }

        if($("#bonificacionesca-u_bonificacioncantidad").is(":visible") && ($("#bonificacionesca-u_bonificacioncantidad").val()=='' || $("#bonificacionesca-u_bonificacioncantidad").val()=='0')){
            if($("#lblCantidadRegalo").text()=='Porcentaje Regalo'){
                $("#error-U_bonificacioncantidad").text("El porcentaje regalo tiene que ser mayor a cero");
            }
            else{
                 $("#error-U_bonificacioncantidad").text("La cantidad regalo tiene que ser mayor a cero");
            }
           
            swError= false;
            console.log("7- "+$("#lblCantidadRegalo").text());
        }
        if($("#bonificacionesca-u_reglacantidad").val()!=$("#CANTIDADDETALLE").val() && $("#CANTIDADDETALLE").val()!=""){
           var opcion = confirm("La cantidad compra configurada es diferente a la suma de las cantidades de los articulos de compra, está seguro de esta configuración?"
           +"\nNOTA: El descuento o bonificación se habilita cumpliendo ambos parámetros.\nCantidad compra global: "+$("#bonificacionesca-u_reglacantidad").val()+"\nTotal cantidad de articulos de compra: "+$("#CANTIDADDETALLE").val());
            if (opcion == false) {
                $("#bonificacionesca-u_reglacantidad").val("");
                $("#bonificacionesca-u_reglacantidad").focus();
                swError= false;
            }
            else{
                swError= true;
            }
        }
        if(($("#bonificacionesca-cantidadmaximacompra").is(":visible") && $("#bonificacionesca-u_reglacantidad").val()/1)>($("#bonificacionesca-cantidadmaximacompra").val()/1)){
             swError= false;
             alert("La cantidad de compra tiene que ser menor o igual a la cantidad maxima de compra");
        }
        //validado que este seleccionado un territorio minimo
        var estadoTerritorio=0;
        $(".selectCheboxTerritorios").each(function () {      
            if ($(this).is(':checked')) 
                estadoTerritorio=1;
        });
        if(estadoTerritorio==0){
            swError= false;
            alert("Seleccionar mínimo una Región");
        }
        //else{
            //swError= true;
        //}
        return swError;
        
    }
function validarCantidad(cantidadGlobal){
    
    console.log("Cantidad global: "+cantidadGlobal);
    console.log("Detalle Especi :"+($("#bonificacionesca-detalleespecifico").val().trim()).toUpperCase());
    if($("#IDCABECERA").val()!="" && ($("#bonificacionesca-detalleespecifico").val().trim()).toUpperCase()=="POR CANTIDAD ESPECIFICA"){
        $.ajax({
            url: $("#PATH").attr("name")+'bonificacionesca/obtenercantdetalle',
            type: 'POST',
            //dataType: 'json',
            data: 'idCabecera='+$("#IDCABECERA").val(),
            success: function (data) { 
                var resultado=JSON.parse(data); 
                console.log("Suma cantidad detalle: "+resultado.cantidad);           
                if(resultado.cantidad!=null){
                    console.log("comparacion: "+cantidadGlobal+" > "+resultado.cantidad);
                    $("#CANTIDADDETALLE").val(resultado.cantidad);
                } 
            },
            error: (jqXhr, textStatus, errorMessage) => {
                console.error("ERROR: " + errorMessage);
            }
        });  
    }
   
}


function exportarExcel(){

    window.open($("#PATH").attr("name")+"bonificacionesca/armahojacalculo&fecha="+$('#fechaFiltro').val()+"&estado="+$('#VIGENTE').val());
               
    //var htmltable= document.getElementById('tabla');
    //var html = htmltable.outerHTML;
    //window.open('data:application/vnd.ms-Excel,' + encodeURIComponent(html));  
}

function delay(ms) {
    var cur_d = new Date();
    var cur_ticks = cur_d.getTime();
    var ms_passed = 0;
    while(ms_passed < ms) {
        var d = new Date();  // Possible memory leak?
        var ticks = d.getTime();
        ms_passed = ticks - cur_ticks;
        // d = null;  // Prevent memory leak?
    }
}

//# sourceMappingURL=Bonificacionesca.js.map
function NumDecimal(evt,input){

    var key = window.Event ? evt.which : evt.keyCode;    
    var chark = String.fromCharCode(key);
    var tempValue = input.value+chark;
    if(key >= 48 && key <= 57){
        if(filterDecimal(tempValue)=== false){
            return false;
        }else{       
            return true;
        }
    }else{
          if(key == 8 || key == 13 || key == 0) {     
              return true;              
          }else if(key == 46){
                if(filterDecimal(tempValue)=== false){
                    return false;
                }else{       
                    return true;
                }
          }else{
              return false;
          }
    }
}
function filterDecimal(__val__){
    var entero=__val__.split('.');
    if(entero[0]<=100){
        console.log('rodrigo77');
        console.log(entero[0]);
        var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
        if(preg.test(__val__) === true){
            return true;
        }else{
           return false;
        } 
    }
    else{
        return false;
    }  
}
/**************************************************/
function NumEntero(evt,input){

    var key = window.Event ? evt.which : evt.keyCode;    
    var chark = String.fromCharCode(key);
    var tempValue = input.value+chark;
    if(key >= 48 && key <= 57){
        if(filterEntero(tempValue)=== false){
            return false;
        }else{       
            return true;
        }
    }else{
          if(key == 8 || key == 13 || key == 0) {     
              return true;              
          }else if(key == 46){
                if(filterEntero(tempValue)=== false){
                    return false;
                }else{       
                    return true;
                }
          }else{
              return false;
          }
    }
}
function filterEntero(__val__){
    var entero=__val__.split('.');
    if(entero[0]<=10000){
        console.log('rodrigo77');
        console.log(entero[0]);
        var preg = /^([0-9]+\.?[0-9]{0,0})$/; 
        if(preg.test(__val__) === true){
            return true;
        }else{
           return false;
        } 
    }
    else{
        return false;
    }  
}
///////////////////////////////////////////////////////////////////////////
function NumEnteroMonto(evt,input){

    var key = window.Event ? evt.which : evt.keyCode;    
    var chark = String.fromCharCode(key);
    var tempValue = input.value+chark;
    if(key >= 48 && key <= 57){
        if(filterEnteroMonto(tempValue)=== false){
            return false;
        }else{       
            return true;
        }
    }else{
          if(key == 8 || key == 13 || key == 0) {     
              return true;              
          }else if(key == 46){
                if(filterEntero(tempValue)=== false){
                    return false;
                }else{       
                    return true;
                }
          }else{
              return false;
          }
    }
}
function filterEnteroMonto(__val__){
    var entero=__val__.split('.');
    if(entero[0]<=1000000000){
        console.log('rodrigo77');
        console.log(entero[0]);
        var preg = /^([0-9]+\.?[0-9]{0,0})$/; 
        if(preg.test(__val__) === true){
            return true;
        }else{
           return false;
        } 
    }
    else{
        return false;
    }  
}
////compaginador  de talas nativas
function doSearch(){
    const tableReg = document.getElementById('tableDetalle');
    const searchText = document.getElementById('searchTerm').value.toLowerCase();
    let total = 0;

    // Recorremos todas las filas con contenido de la tabla
    for (let i = 0; i < tableReg.rows.length; i++) {
        // Si el td tiene la clase "noSearch" no se busca en su cntenido
        if (tableReg.rows[i].classList.contains("noSearch")) {
            continue;
        }

        let found = false;
        const cellsOfRow = tableReg.rows[i].getElementsByTagName('td');
        // Recorremos todas las celdas
        for (let j = 0; j < cellsOfRow.length && !found; j++) {
            const compareWith = cellsOfRow[j].innerHTML.toLowerCase();
            // Buscamos el texto en el contenido de la celda
            if (searchText.length == 0 || compareWith.indexOf(searchText) > -1) {
                found = true;
                total++;
            }
        }
        if (found) {
            tableReg.rows[i].style.display = '';
        } else {
            // si no ha encontrado ninguna coincidencia, esconde la
            // fila de la tabla
            tableReg.rows[i].style.display = 'none';
        }
    }

    // mostramos las coincidencias
   // const lastTR=tableReg.rows[tableReg.rows.length-1];
    //const td=lastTR.querySelector("td");
    //lastTR.classList.remove("hide", "red");
    if (searchText == "") {
       // lastTR.classList.add("hide");
        $("#p-resultado").text("");
    } else if (total) {
        $("#p-resultado").text("Se ha encontrado "+total+" resultado"+((total>1)?"s":""));
       // td.innerHTML="Se ha encontrado "+total+" coincidencia"+((total>1)?"s":"");
    } else {
        //lastTR.classList.add("red");
        $("#p-resultado").text("No se han encontrado resultados");
        //td.innerHTML="No se han encontrado coincidencias";
    }
}
