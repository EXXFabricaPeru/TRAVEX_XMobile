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
var Rutacabecera = (function () {
    function Rutacabecera() {
        this.element = $(".window");
        this.url = $("#PATH").attr("name");
        this.Objet = Object;
        this.elementGrid = "#Rutacabecera-list";
        this.setting = { width: '100%', height: 600, hide: 'fade', show: 'fade', modal: true };
    }
    Rutacabecera.prototype.onloadViewForm = function () {
        $("#rutacabecera-fecha").datepicker({ dateFormat: 'yy-mm-dd' });
        $("#rutacabecera-fechapicking").datepicker({ dateFormat: 'yy-mm-dd' });
    };
    Rutacabecera.prototype.fromaction = function () {
        $("#rutacabecera-nombre").keyup(function () {
            var str = $(this).val();
            var res = str.replace(/ /g, "-");
            $("#rutacabecera-cod").val(res.toLowerCase());
        });
    };
    Rutacabecera.prototype.windowCreate = function () {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'NUEVO REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'rutacabecera/create', function () {
                    //_this.fromaction();
                    _this.onloadViewForm();
                });
            },
            buttons: [
                
                    {
                        text: "TRAZAR RUTA",
                        class: "btn btn-success",
                        click: function () {
                            calcularRuta(0);
                            $('#TRAZORUTA').val(1);
                            //initMap();
                        }
                    },
                
                    {
                    text: "REGISTRAR",
                    class: "btn btn-success",
                    click: function () {
                        if(validarInputs()){
                            //guardar(); 
                            verificarRegistroUsuario(0,0);
                        }
                       
                    }
                    //click: function () { return __awaiter(_this, void 0, void 0, function () {
                        //var data, respt, key;
                        //return __generator(this, function (_a) {
                            //switch (_a.label) {
                                //case 0:
                                    //data = $('#Rutacabecera-form').serialize();
                                    //return [4 /*yield*/, this.requestPost(data)];
                                //case 1:
                                    //respt = _a.sent();
                                    //if (isNaN(respt)) {
                                        //$(".text-clear").html('');
                                        //for (key in respt)
                                            //$("#error-" + key).html(respt[key][0]);
                                    //}
                                    //else {
                                        //$.toast({
                                            //heading: 'Success',
                                            //text: 'Registrado correctamente.',
                                            //showHideTransition: 'fade',
                                            //icon: 'success'
                                        //});
                                        //$.pjax.reload({ container: this.elementGrid, async: false });
                                        //this.element.dialog("close");
                                    //}
                                    //return [2 /*return*/];
                            //}
                        //});
                    //}); }
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
    Rutacabecera.prototype.windowEliminar = function (id) {
        var _this = this;
        var obj = {
            width: 350, height: 200, hide: 'fade', show: 'fade', modal: true, title: 'ALERTA',
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
                                    $.toast({
                                        heading: 'Warning',
                                        text: 'El registro fue eliminado.',
                                        showHideTransition: 'plain',
                                        icon: 'warning'
                                    });
                                    $.pjax.reload({ container: this.elementGrid, async: false });
                                    $("#windowEliminar").dialog("close");
                                    return [3 /*break*/, 3];
                                case 2:
                                    e_1 = _a.sent();
                                    $.toast({
                                        heading: 'Error',
                                        text: 'Ocurrio un !ERROR.',
                                        showHideTransition: 'fade',
                                        icon: 'error',
                                        position: 'bottom-center',
                                    });
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
    Rutacabecera.prototype.windowEdit = function (id) {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'ACTUALIZAR REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'rutacabecera/update&id=' + id, function () {
                    //_this.fromaction();
                    _this.onloadViewForm();
                });
            },
            buttons: [
                        {
                            text: "TRAZAR RUTA",
                            class: "btn btn-success",
                            click: function () {
                                //calcularRuta();
                                 initMap(0);
                                 $('#TRAZORUTA').val(1);
                            }
                        },
                        {
                            text: "ACTUALIZAR",
                            class: "btn btn-success",
                            click: function () {
                                if(validarInputs()){
                                   // actualizar(); 
                                   verificarRegistroUsuario(1,id); 
                                }
                            }
                        },
                        {
                            text: "CANCELAR",
                            class: "btn btn-warning",
                            click: function () {
                                _this.element.dialog("close");
                            }
                        }
                    ]
        };
        var opt = this.Objet.assign(this.setting, option);
        this.element.dialog(opt);
    };
    Rutacabecera.prototype.windowPdf = function (id) {
        var url = this.url + 'rutacabecera/report&id=' + id;
        $("#windowpdf").dialog({
            width: '100%', height: 500, modal: true, open: function () {
                $("#windowpdf").html('<embed src="' + url + '" type="application/pdf" width="100%" height="100%" />');
            }
        });
    };
    Rutacabecera.prototype.requestPut = function (id, data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'rutacabecera/update&id=' + id,
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
    Rutacabecera.prototype.requestDelete = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.get(_this.url + 'rutacabecera/eliminar&id=' + data).done(function (data) {
                resolve(data);
            }).fail(function (err) {
                reject(err);
            });
        });
    };
    Rutacabecera.prototype.requestPost = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'rutacabecera/create',
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
    return Rutacabecera;
}());
$(function () {
    var model = new Rutacabecera();
    $("#btn-create").on('click', function () {
        model.windowCreate();
    });
    $(document).on("click", ".btn-grid-action-delete", function () {
        var id = $(this).val();
        model.windowEliminar(id);
    });
    $(document).on("click", ".btn-grid-action-edit", function () {
        var id = $(this).val();
        model.windowEdit(id);
    });
    $(document).on("click", ".btn-grid-action-pdf", function () {
        var id = $(this).val();
        model.windowPdf(id);
    });
});
//# sourceMappingURL=Rutacabecera.js.map

function agregarDocumentos(valor){
    var opt = $('option[value="'+$("#operador").val()+'"]');
    var idVendedor=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);

    if(idVendedor!='NO OPTION'){
        $('#TRAZORUTA').val(0);
        $("#rutacabecera-idvendedor").val(idVendedor);
        $("#rutacabecera-vendedor").val($("#operador").val());
            this.url = $("#PATH").attr("name");
            $.ajax({
                
                 url: this.url + 'rutacabecera/consultas',
                 type: 'POST',
                 //dataType: 'json',
                 
                 data: 'CONDICION=DOCUMENTOSIMPORTADOS&usuario='+idVendedor+'&fechaPicking='+$("#rutacabecera-fechapicking").val(),
                 success: function (data) {
                    //console.log(data);
                     /*console.log($('#idusuario').val());
                     console.log(data);*/
                     ArrayData = $.parseJSON(data);
                     var clientesError='Los siguientes clientes no tienen coordenadas correctas, estos serán excluidos de la lista de pedidos.\nNOTA: también puede registrar las coordenadas del cliente en el sistema SAP y volver a crear la ruta:\n';
                     var swError=false;
                     //console.log(ArrayData);
                     var items = document.getElementById('tableDetalle');
                     items.innerHTML = '';
                     if(ArrayData.length>=1){

                       for(var i=0;i<ArrayData.length;i++){
                           //var valor=ArrayData[i].CardCode;//.split('":"');
                           //console.log(valor);
                           if(ArrayData[i].U_XMB_Latitud!='0' && ArrayData[i].U_XMB_Latitud!=''){
                               AdicionarRow((i+1),ArrayData[i].CardCode,ArrayData[i].CardName,ArrayData[i].DocType,ArrayData[i].DocEntry,ArrayData[i].U_XMB_Latitud,ArrayData[i].U_XMB_Longitud,ArrayData[i].CardCode,'tableDetalle',ArrayData[i].DocDueDate,ArrayData[i].PickDate,ArrayData[i].AbsEntry);
                           }
                           else{
                               swError=true;
                               clientesError=clientesError+(i+1)+' - Codigo: '+ArrayData[i].CardCode+' Nombre: '+ArrayData[i].CardName+' - Latitud: '+ArrayData[i].U_XMB_Latitud+' - Longitud: '+ArrayData[i].U_XMB_Longitud+'\n';
                           }
                          
  
                        }
                        if(swError){
                            alert(clientesError);
                        }
                       
                        $("#cantidadClientes").text(i);
                     }
                     //initMap(valor);
                     asignaEtiquetas(0);
                },
                 error: function (jqXhr, textStatus, errorMessage) {
                     reject(errorMessage);
                 }
             });
    }
    else{

        alert("Despachador seleccionado no existe");
    }

 }

 
function AdicionarRow(idAux,cardcode,cardname,doctype,id,latitud,longitud,idcliente,tabla,docduedate,fechapicking,nropicking){
    //alert(tabla);
        var items = document.getElementById(tabla);
        var valor = '';
        if(idAux == 1)
        {
            valor = 'checked';
        }

        contenido = '<tr  id="tr-fila-'+idAux+'"  onclick="mostrarEtiqueta('+idAux+')">'
                    +'<td style="width:10"> <input type="radio" '+valor+' id="radio_'+idAux+'" name="name" value="'+cardcode+'*'+doctype+'*'+id+'*'+latitud+'*'+longitud+'*'+cardname+'*'+doctype+'"> </td>'
                    +'<td style="width:10"> '+cardcode+'</td>'
                                                
                    +'<td style="width:10"> '+cardname+' </td>'
                    +'<td style="width:10"> '+doctype+' </td>'
                    +'<td style="width:10"> '+id+' </td>'
                    +'<td style="width:10">  </td>'                          

                    +'<td style="display: none;">  </td>'                           

                    + "<td style='display: none;'>" + latitud + "</td>"//latitude
                    + "<td style='display: none;'>" + longitud + "</td>" //longitude      
                    + "<td style='display: none;'>" + idcliente + "</td>" //idcliente      
                    + "<td>" + docduedate + "</td>" //docduedate    
                    + "<td>" + nropicking + "</td>" //docduedate      
                    + '<td style="width:10" align="center" >  <button title="Eliminar Fila" type="button" class="btn-link" value="" onclick="EliminarFila('+idAux+')" ><i class="fas fa-trash-alt text-warning"></i></button>  </td>'
                    +'</tr>';
        items.innerHTML =items.innerHTML+ contenido;
        
   }

   function EliminarFila(id){
       //alert(id);
       var Row = document.getElementById("tr-fila-"+id);
       Row.parentNode.removeChild(Row);
       var cantidad= $('#tableDetalle').children().length;
       $("#cantidadClientes").text(cantidad);
      
    }

    ////compaginador  de talas nativas///
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

function validarInputs(){
   
    if($('#rutacabecera-nombre').val()==""){
        //alert("El nombre de la ruta es requerido.");
        $("#error-nombre").text("El nombre de la ruta es requerido");
        return false;
    }
    if($('#rutacabecera-fecha').val()==""){
        //alert("El nombre de la ruta es requerido.");
        $("#error-fecha").text("La fecha de registro es requerido");
        return false;
    }
    if($('#operador').val()==""){
        //alert("El nombre de la ruta es requerido.");
        $("#error-operador").text("El Despachador es requerido");
        return false;
    }
    if($('#TRAZORUTA').val()=='0'){
        alert("Error! primero trazar la ruta para guardar los registros");
        return false;
    }
    return true;
}