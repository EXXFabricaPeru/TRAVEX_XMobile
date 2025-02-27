var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
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
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
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
var Usuarioconfiguracion = /** @class */ (function () {
    function Usuarioconfiguracion() {
        this.element = $(".window");
        this.url = $("#PATH").attr("name");
        this.Objet = Object;
        this.elementGrid = "#Usuarioconfiguracion-list";
        this.setting = { width: '80%', height: 500, hide: 'fade', show: 'fade', modal: true };
    }
    Usuarioconfiguracion.prototype.windowCreate = function () {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'NUEVO REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'usuarioconfiguracion/create');
            },
            buttons: [{
                    text: "REGISTRAR",
                    "class": "btn btn-success",
                    click: function () { return __awaiter(_this, void 0, void 0, function () {
                        var data, respt, key;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    data = $('#Usuarioconfiguracion-form').serialize();
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
                                        this.element.dialog("close");
                                    }
                                    return [2 /*return*/];
                            }
                        });
                    }); }
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
    Usuarioconfiguracion.prototype.windowEliminar = function (id) {
        var _this = this;
        var obj = {
            width: 350, height: 200, hide: 'fade', show: 'fade', modal: true, title: 'ALERTA',
            buttons: [{
                    text: "SI",
                    "class": "btn btn-danger",
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
                                        position: 'bottom-center'
                                    });
                                    return [3 /*break*/, 3];
                                case 3: return [2 /*return*/];
                            }
                        });
                    }); }
                }, {
                    text: "NO",
                    "class": "btn btn-success",
                    click: function () {
                        $("#windowEliminar").dialog("close");
                    }
                }]
        };
        $("#windowEliminar").dialog(obj);
    };
    Usuarioconfiguracion.prototype.windowEdit = function (id) {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'ACTUALIZAR REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'usuarioconfiguracion/update&id=' + id, function () {
                    _this.loadFrom();
                });
            },
            buttons: [{
                    text: "GUARDAR",
                    "class": "btn btn-success",
                    click: function () { return __awaiter(_this, void 0, void 0, function () {
                        var data, respt, key;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    data = $('#Usuarioconfiguracion-form').serialize();
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
                                            text: 'El registro fue modificado correctamente..',
                                            showHideTransition: 'fade',
                                            icon: 'success'
                                        });
                                        $.pjax.reload({ container: this.elementGrid, async: false });
                                        this.element.dialog("close");
                                    }
                                    return [2 /*return*/];
                            }
                        });
                    }); }
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
    Usuarioconfiguracion.prototype.windowPdf = function (id) {
        var url = this.url + 'usuarioconfiguracion/report&id=' + id;
        $("#windowpdf").dialog({
            width: '100%', height: 500, modal: true, open: function () {
                $("#windowpdf").html('<embed src="' + url + '" type="application/pdf" width="100%" height="100%" />');
            }
        });
    };
    Usuarioconfiguracion.prototype.loadFrom = function (id) {
        $("#usuarioconfiguracion-iduser").val(id);
        $(".selectCheboxListaPrecios").click(function () {
            var listArr = [];
            $(".selectCheboxListaPrecios").each(function (index, value) {
                var dx = $(value).is(':checked');
                if (dx == true) {
                    listArr.push($(value).val());
                }
            });
            $("#usuarioconfiguracion-multilistaprecios").val(JSON.stringify(listArr));
        });
        var cont = $("#usuarioconfiguracion-multilistaprecios").val();
        var _loop_1 = function (itx) {
            $(".selectCheboxListaPrecios").each(function (index, value) {
                var dxx = $(value).val();
                if (dxx == itx) {
                    $(value).attr("checked", true);
                }
            });
        };
        console.log("cont ", cont);
		if(cont!=''){
            for (var _i = 0, _a = JSON.parse(cont); _i < _a.length; _i++) {
                var itx = _a[_i];
                _loop_1(itx);
            }
        }

        $(".selectCheboxCamposusuario").click(function () {
            var listArr2 = [];
            $(".selectCheboxCamposusuario").each(function (index, value) {
                var dx = $(value).is(':checked');
                if (dx == true) {
                    listArr2.push($(value).val());
                }
            });
            console.log(JSON.stringify(listArr2));
            $("#usuarioconfiguracion-multicamposusuarios").val(JSON.stringify(listArr2));
        });


        var cont2 = $("#usuarioconfiguracion-multicamposusuarios").val();

        var _loop_2 = function (itx) {
            $(".selectCheboxCamposusuario").each(function (index, value) {
                var dxx = $(value).val();
                if (dxx == itx) {
                    $(value).attr("checked", true);
                }
            });
        };
        //if(cont2){
            console.log("cont2 ", cont2);
            if(cont2!='' ){
                for (var _i = 0, _a = JSON.parse(cont2); _i < _a.length; _i++) {
                    var itx = _a[_i];
                    _loop_2(itx);
                }
            }
        //}
    };
    Usuarioconfiguracion.prototype.windowSetting = function (id) {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'CONFIGURACIONES DEL USUARIO',
            open: function () {
                _this.element.load(_this.url + 'usuarioconfiguracion/view&id=' + id, function () {
                    _this.loadFrom(id);
                });
            },
            buttons: [{
                    text: "GUARDAR",
                    "class": "btn btn-success",
                    click: function () { return __awaiter(_this, void 0, void 0, function () {
                        var data, rx, respt, key;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    data = $('#Usuarioconfiguracion-form').serialize();
                                    rx = $("#exxisapp").attr("name");
                                    if (!(rx == 0)) return [3 /*break*/, 2];
                                    return [4 /*yield*/, this.requestPost(data)];
                                case 1:
                                    respt = _a.sent();
                                    return [3 /*break*/, 4];
                                case 2: return [4 /*yield*/, this.requestPut(rx, data)];
                                case 3:
                                    respt = _a.sent();
                                    _a.label = 4;
                                case 4:
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
                                        this.element.dialog("close");
                                    }
                                    return [2 /*return*/];
                            }
                        });
                    }); }
                }, {
                    text: "CANCELAR",
                    "class": "btn btn-warning",
                    click: function () {
                        _this.element.dialog("close");
                    }
                }]
        };
        var opt = this.Objet.assign({ width: "90%", height: 500, hide: 'fade', show: 'fade', modal: true }, option);
        this.element.dialog(opt);
    };
    Usuarioconfiguracion.prototype.requestPut = function (id, data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            var cond = '[]';
            if ($('#hdCondiciones').val() != "") {
                cond = $('#hdCondiciones').val();
            }
            var cc = '';
            console.log("data send 2 ", data);
            if ($('#ddlCC')[0] != undefined)
                cc = $('#ddlCC')[0].value;
			console.log(cc);
            $.ajax({
                url: _this.url + 'usuarioconfiguracion/update&id=' + id + '&condiciones=' + cond + '&centro=' + cc,
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
    Usuarioconfiguracion.prototype.requestDelete = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.get(_this.url + 'usuarioconfiguracion/eliminar&id=' + data).done(function (data) {
                resolve(data);
            }).fail(function (err) {
                reject(err);
            });
        });
    };
    Usuarioconfiguracion.prototype.requestPost = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            var cond = '[]';
            if ($('#hdCondiciones').val() != "") {
                cond = $('#hdCondiciones').val();
            }
            var cc = '';
            if ($('#ddlCC')[0] != undefined)
                cc = $('#ddlCC')[0].value;
            $.ajax({
                url: _this.url + 'usuarioconfiguracion/create&condiciones=' + cond + '&centro=' + cc,
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
    return Usuarioconfiguracion;
}());
$(function () {
    var model = new Usuarioconfiguracion();
    $(document).on("click", ".btn-grid-action-config", function () {
        var id = $(this).val();
        model.windowSetting(id);
    });
    /* $("#btn-create").on('click', () => {
         model.windowCreate();
     });
     $(document).on("click", ".btn-grid-action-delete", function () {
         let id = $(this).val();
         model.windowEliminar(id);
     });
     $(document).on("click", ".btn-grid-action-edit", function () {
         let id = $(this).val();
         model.windowEdit(id);
     });
     $(document).on("click", ".btn-grid-action-pdf", function () {
         let id = $(this).val();
         model.windowPdf(id);
     });*/
    /*$(document).on("click", ".btn-grid-action-accesos", function () {
        let id = $(this).val();
        model.windowAccesos(id);
    });*/
});
