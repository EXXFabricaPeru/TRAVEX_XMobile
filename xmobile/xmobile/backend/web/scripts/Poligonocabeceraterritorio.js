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
var Poligonocabeceraterritorio = (function () {
    function Poligonocabeceraterritorio() {
        this.element = $(".window");
        this.url = $("#PATH").attr("name");
        this.Objet = Object;
        this.elementGrid = "#Poligonocabeceraterritorio-list";
        this.setting = { width: '100%', height: 620, hide: 'fade', show: 'fade', modal: true };
    }
    Poligonocabeceraterritorio.prototype.fromaction = function () {
        /*$("#poligonocabeceraterritorio-nombre").keyup(function () {
            var str = $(this).val();
            var res = str.replace(/ /g, "-");
            $("#poligonocabeceraterritorio-cod").val(res.toLowerCase());
        });*/
    };
    Poligonocabeceraterritorio.prototype.onloadViewForm = function () {
        $("#poligonocabeceraterritorio-fecharegistro").datepicker({ dateFormat: 'yy-mm-dd' });
    };
    Poligonocabeceraterritorio.prototype.windowCreate = function () {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'NUEVO REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'poligonocabeceraterritorio/create', function () {
                    _this.onloadViewForm();
                    getDiaSemana();
                });
            },
            buttons: [
               /* {
                    text: "TRAZAR RUTA",
                    class: "btn btn-success",
                    click: function () {
                        //calcularRuta();
                        alert("Se trazo la ruta");
                    }
                }, */   
                {
                    text: "REGISTRAR",
                    id: "BTNREGISTRAR",
                    class: "btn btn-success",
                    click: function () {
                        //asignaciones();
                        if(validarInputs()){
                            guardarDetalle();
                        }   
                       
                        /*
                        $.toast({
                            heading: 'Success',
                            text: 'El registro fue modificado correctamente..',
                            showHideTransition: 'fade',
                            icon: 'success'
                        });
                        $.pjax.reload({ container: this.elementGrid, async: false });
                        _this.element.dialog("close");
                        */
                        //location.reload();
                    }
                },  
                {
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
    Poligonocabeceraterritorio.prototype.windowEliminar = function (id) {
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
    Poligonocabeceraterritorio.prototype.windowEdit = function (id) {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'ACTUALIZAR REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'poligonocabeceraterritorio/update&id=' + id, function () {
                    _this.onloadViewForm();
                    ListarClientesGuardados(id);
                });
            },
            buttons: [
                /*{
                    text: "TRAZAR RUTA",
                    class: "btn btn-success",
                    click: function () {
                        
                        //calcularRuta();
                        alert("Se trazo la ruta");
                    }
                },*/ 
                {
                    text: "ACTUALIZAR",
                    class: "btn btn-success",
                    click: function () {
                        //asignaciones();
                        if(validarInputs()){
                            actualizarDetalle(id); 
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
    Poligonocabeceraterritorio.prototype.windowVer = function (id) {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'ACTUALIZAR REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'poligonocabeceraterritorio/ver&id=' + id, function () {
                    _this.onloadViewForm();
                    ListarClientesGuardados(id);
                });
            }
        };
        var opt = this.Objet.assign(this.setting, option);
        this.element.dialog(opt);
    };
    Poligonocabeceraterritorio.prototype.windowPdf = function (id) {

        var url = this.url + 'poligonocabeceraterritorio/report&id=' + id;
        $("#windowpdf").dialog({

            width: '100%', height: 500, modal: true, open: function () {
                $("#windowpdf").html('<div id="preloader"><div id="loader">&nbsp;<div id="mensaje"  align="center" style="color:#FFFFFF">Espere por favor</div></div></div> <embed src="' + url + '" type="application/pdf" width="100%" height="100%" />');
               
            }

        });
        setTimeout(finLoad, 4000);
        
    };
    Poligonocabeceraterritorio.prototype.requestPut = function (id, data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'poligonocabeceraterritorio/update&id=' + id,
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
    Poligonocabeceraterritorio.prototype.requestDelete = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.get(_this.url + 'poligonocabeceraterritorio/eliminar&id=' + data).done(function (data) {
                resolve(data);
            }).fail(function (err) {
                reject(err);
            });
        });
    };
    Poligonocabeceraterritorio.prototype.requestPost = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'poligonocabeceraterritorio/create',
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
    return Poligonocabeceraterritorio;
}());
$(function () {
    var model = new Poligonocabeceraterritorio();
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
    $(document).on("click", ".btn-grid-action-ver", function () {
        var id = $(this).val();
        model.windowVer(id);
        
    });
});

function asignaciones(){
/*
	var comboVendedor = document.getElementById("poligonocabeceraterritorio-idvendedor");
	
	var vendedor = "";
	if(comboVendedor.options){
		vendedor = comboVendedor.options[comboVendedor.selectedIndex].text;
	}
	$("#poligonocabeceraterritorio-vendedor").val(vendedor);
*/
	
}
function finLoad(){
    $('#preloader').fadeOut('slow');
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

function doSearchSin(){
    const tableReg = document.getElementById('tableDetalleSin');
    const searchText = document.getElementById('searchTermSin').value.toLowerCase();
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
        $("#p-resultado-sin").text("");
    } else if (total) {
        $("#p-resultado-sin").text("Se ha encontrado "+total+" resultado"+((total>1)?"s":""));
       // td.innerHTML="Se ha encontrado "+total+" coincidencia"+((total>1)?"s":"");
    } else {
        //lastTR.classList.add("red");
        $("#p-resultado-sin").text("No se han encontrado resultados");
        //td.innerHTML="No se han encontrado coincidencias";
    }
}

function validarInputs(){
    if($('#poligonocabeceraterritorio-nombreruta').val()==""){
        //alert("El nombre de la ruta es requerido.");
        $("#error-nombreRuta").text("El nombre de la ruta es requerido");
        return false;
    }
    if($('#poligonocabeceraterritorio-fecharegistro').val()==""){
        //alert("El nombre de la ruta es requerido.");
        $("#error-fechaRegistro").text("La fecha de registro es requerido");
        return false;
    }
    if($('#operador').val()==""){
        //alert("El nombre de la ruta es requerido.");
        $("#error-operador").text("El vendedor es requerido");
        return false;
    }
    return true;
}


//# sourceMappingURL=Poligonocabeceraterritorio.js.map