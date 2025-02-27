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
var Poligonocliente = /** @class */ (function () {
    function Poligonocliente() {
        this.element = $(".window");
        this.url = $("#PATH").attr("name");
        this.Objet = Object;
        this.elementGrid = "#Poligonocliente-list";
        this.setting = { width: '80%', height: '700',  padding:'2000',
       hide: 'fade', show: 'fade', modal: true };
    }
    Poligonocliente.prototype.windowCreate = function () {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'NUEVO REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'poligonocliente/create');
            },
            buttons: [{
                    text: "REGISTRAR",
                    "class": "btn btn-success",
                    click: function () { return __awaiter(_this, void 0, void 0, function () {
                        var data, respt, key;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    data = $('#Poligonocliente-form').serialize();
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
    Poligonocliente.prototype.windowEliminar = function (id) {
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
    Poligonocliente.prototype.windowEdit = function (id) {
        var _this = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'ACTUALIZAR REGISTRO',
            open: function () {
                _this.element.load(_this.url + 'poligonocliente/update&id=' + id);
            },
            buttons: [{
                    text: "GUARDAR",
                    "class": "btn btn-success",
                    click: function () { return __awaiter(_this, void 0, void 0, function () {
                        var data, respt, key;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    data = $('#Poligonocliente-form').serialize();
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
    Poligonocliente.prototype.windowPdf = function (id) {
        var url = this.url + 'poligonocliente/report&id=' + id;
        $("#windowpdf").dialog({
            width: '100%', height: 500, modal: true, open: function () {
                $("#windowpdf").html('<embed src="' + url + '" type="application/pdf" width="100%" height="100%" />');
            }
        });
    };
    Poligonocliente.prototype.requestPut = function (id, data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'poligonocliente/update&id=' + id,
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
    Poligonocliente.prototype.requestDelete = function (data) {
        
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.get(_this.url + 'poligonocliente/eliminar&id=' + data).done(function (data) {
                resolve(data);
            }).fail(function (err) {
                reject(err);
            });
        });
    };
    Poligonocliente.prototype.requestPost = function (data) {
        var _this = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: _this.url + 'poligonocliente/create',
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


    Poligonocliente.prototype.windowView = function (id) {
        
        console.log("es el ID"+id);
        var _this = this;
        var _thisx = this;
        this.element.html('<div class="loader">Loading...</div>');
        var option = {
            title: 'CLIENTES POR POLIGONO',
            open: function () {
                //this.element.load(this.url + 'poligonocliente/view&id=' + id, () => {
                _this.element.load(_this.url + 'poligonocliente/recuperarclientes&id=' + id, function () {
                });
            }
        };
        var opt = this.Objet.assign(this.setting, option);
        this.element.dialog(opt);
    };

    
    return Poligonocliente;
}());




$(function () {
    var model = new Poligonocliente();
    $(document).on("click", ".btn-grid-action-poligonocliente", function () {
        var id = $(this).val();
        model.windowView(id);

    });
    /*$("#btn-create").on('click', () => {
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
});
