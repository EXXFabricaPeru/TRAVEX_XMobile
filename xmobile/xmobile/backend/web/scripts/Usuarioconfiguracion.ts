declare var $;

class Usuarioconfiguracion {
    public setting: any;
    public Objet: any;
    protected url: string;
    protected element: any;
    protected elementGrid: string;

    constructor() {
        this.element = $(".window");
        this.url = $("#PATH").attr("name");
        this.Objet = Object;
        this.elementGrid = "#Usuarioconfiguracion-list";
        this.setting = {width: '80%', height: 500, hide: 'fade', show: 'fade', modal: true};
    }

    public windowCreate() {
        this.element.html('<div class="loader">Loading...</div>');
        let option = {
            title: 'NUEVO REGISTRO',
            open: () => {
                this.element.load(this.url + 'usuarioconfiguracion/create');
            },
            buttons: [{
                text: "REGISTRAR",
                class: "btn btn-success",
                click: async () => {
                    let data = $('#Usuarioconfiguracion-form').serialize();
                    let respt: any = await this.requestPost(data);
                    if (isNaN(respt)) {
                        $(".text-clear").html('');
                        for (let key in respt)
                            $("#error-" + key).html(respt[key][0]);
                    } else {
                        $.toast({
                            heading: 'Success',
                            text: 'Registrado correctamente.',
                            showHideTransition: 'fade',
                            icon: 'success'
                        });
                        $.pjax.reload({container: this.elementGrid, async: false});
                        this.element.dialog("close");
                    }
                }
            }, {
                text: "CANCELAR",
                class: "btn btn-warning",
                click: () => {
                    this.element.dialog("close");
                }
            }]
        };
        let opt: any = this.Objet.assign(this.setting, option);
        this.element.dialog(opt);
    }

    public windowEliminar(id: any) {
        let obj = {
            width: 350, height: 200, hide: 'fade', show: 'fade', modal: true, title: 'ALERTA',
            buttons: [{
                text: "SI",
                class: "btn btn-danger",
                click: async () => {
                    try {
                        await this.requestDelete(id);
                        $.toast({
                            heading: 'Warning',
                            text: 'El registro fue eliminado.',
                            showHideTransition: 'plain',
                            icon: 'warning'
                        });
                        $.pjax.reload({container: this.elementGrid, async: false});
                        $("#windowEliminar").dialog("close");
                    } catch (e) {
                        $.toast({
                            heading: 'Error',
                            text: 'Ocurrio un !ERROR.',
                            showHideTransition: 'fade',
                            icon: 'error',
                            position: 'bottom-center',
                        });
                    }
                }
            }, {
                text: "NO",
                class: "btn btn-success",
                click: () => {
                    $("#windowEliminar").dialog("close");
                }
            }]
        };
        $("#windowEliminar").dialog(obj);
    }

    public windowEdit(id: any) {
        this.element.html('<div class="loader">Loading...</div>');
        let option = {
            title: 'ACTUALIZAR REGISTRO',
            open: () => {
                this.element.load(this.url + 'usuarioconfiguracion/update&id=' + id, () => {
                    this.loadFrom();
                });
            },
            buttons: [{
                text: "GUARDAR",
                class: "btn btn-success",
                click: async () => {
                    let data = $('#Usuarioconfiguracion-form').serialize();
                    let respt: any = await this.requestPut(id, data);
                    if (isNaN(respt)) {
                        $(".text-clear").html('');
                        for (let key in respt)
                            $("#error-" + key).html(respt[key][0]);
                    } else {
                        $.toast({
                            heading: 'Success',
                            text: 'El registro fue modificado correctamente..',
                            showHideTransition: 'fade',
                            icon: 'success'
                        })
                        $.pjax.reload({container: this.elementGrid, async: false});
                        this.element.dialog("close");
                    }
                }
            }, {
                text: "CANCELAR",
                class: "btn btn-warning",
                click: () => {
                    this.element.dialog("close");
                }
            }]
        };
        let opt: any = this.Objet.assign(this.setting, option);
        this.element.dialog(opt);
    }

    public windowPdf(id: any) {
        let url = this.url + 'usuarioconfiguracion/report&id=' + id;
        $("#windowpdf").dialog({
            width: '100%', height: 500, modal: true, open: () => {
                $("#windowpdf").html('<embed src="' + url + '" type="application/pdf" width="100%" height="100%" />');
            }
        });
    }

    public loadFrom(id: any) {
        $("#usuarioconfiguracion-iduser").val(id);
        $(".selectCheboxListaPrecios").click(() => {
            let listArr: any = [];
            $(".selectCheboxListaPrecios").each((index: any, value: any) => {
                let dx: boolean = $(value).is(':checked');
                if (dx == true) {
                    listArr.push($(value).val());
                }
            });
            $("#usuarioconfiguracion-multilistaprecios").val(JSON.stringify(listArr));
        });
        let cont: any = $("#usuarioconfiguracion-multilistaprecios").val();
        for (let itx of  JSON.parse(cont)) {
            $(".selectCheboxListaPrecios").each((index: any, value: any) => {
                let dxx = $(value).val();
                if (dxx == itx) {
                    $(value).attr("checked", true);
                }
            });
        }

         $(".selectCheboxCamposusuario").click(() => {
            let listArr: any = [];
            $(".selectCheboxCamposusuario").each((index: any, value: any) => {
                let dx: boolean = $(value).is(':checked');
                if (dx == true) {
                    listArr.push($(value).val());
                }
            });
            $("#usuarioconfiguracion-multiCamposUsuarios").val(JSON.stringify(listArr));
        });
        let cont2: any = $("#usuarioconfiguracion-multiCamposUsuarios").val();
        for (let itx of  JSON.parse(cont2)) {
            $(".selectCheboxCamposusuario").each((index: any, value: any) => {
                let dxx = $(value).val();
                if (dxx == itx) {
                    $(value).attr("checked", true);
                }
            });
        }


    }

    public windowSetting(id) {
        this.element.html('<div class="loader">Loading...</div>');
        let option = {
            title: 'CONFIGURACIONES DEL USUARIO',
            open: () => {
                this.element.load(this.url + 'usuarioconfiguracion/view&id=' + id, () => {
                    this.loadFrom(id);
                });
            },
            buttons: [{
                text: "GUARDAR",
                class: "btn btn-success",
                click: async () => {
                    let data = $('#Usuarioconfiguracion-form').serialize();


                    let rx: any = $("#exxisapp").attr("name");
                    let respt: any;
                    if (rx == 0)
                        respt = await this.requestPost(data);
                    else
                        respt = await this.requestPut(rx, data);
                    if (isNaN(respt)) {
                        $(".text-clear").html('');
                        for (let key in respt)
                            $("#error-" + key).html(respt[key][0]);
                    } else {
                        $.toast({
                            heading: 'Success',
                            text: 'Registrado correctamente.',
                            showHideTransition: 'fade',
                            icon: 'success'
                        });
                        this.element.dialog("close");
                    }
                }
            }, {
                text: "CANCELAR",
                class: "btn btn-warning",
                click: () => {
                    this.element.dialog("close");
                }
            }]
        };
        let opt: any = this.Objet.assign({width: "90%", height: 500, hide: 'fade', show: 'fade', modal: true}, option);
        this.element.dialog(opt);
    }

    private requestPut(id: any, data: any) {
        return new Promise((resolve, reject) => {
            var cond = '[]';
            if ($('#hdCondiciones').val() != "") {
                cond = $('#hdCondiciones').val();
            }
            var cc = '';
            if ($('#ddlCC')[0] != undefined) cc = $('#ddlCC')[0].value;
            $.ajax({
				url: this.url + 'usuarioconfiguracion/update&id=' + id + '&condiciones=' + cond + '&centro=' + cc,
                type: 'PUT',
                data: data,
                success: (data, status, xhr) => {
                    resolve(JSON.parse(data));
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    reject(errorMessage);
                }
            });
        });
    }

    private requestDelete(data: any) {
        return new Promise((resolve, reject) => {
            $.get(this.url + 'usuarioconfiguracion/eliminar&id=' + data).done((data) => {
                resolve(data);
            }).fail((err) => {
                reject(err);
            });
        });
    }

    private requestPost(data: any) {
        console.log("requestPost() ");
        return new Promise((resolve, reject) => {
            var cond = '[]';
            if ($('#hdCondiciones').val() != "") {
                cond = $('#hdCondiciones').val();
            }
            var cc = '';
            if ($('#ddlCC')[0] != undefined) cc = $('#ddlCC')[0].value;
            console.log("send data conifiguraciones  ", data);
            $.ajax({
				url: this.url + 'usuarioconfiguracion/create&condiciones=' + cond + '&centro=' + cc,
                type: 'POST',
                data: data,
                success: (data, status, xhr) => {
                    resolve(JSON.parse(data));
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    reject(errorMessage);
                }
            });
        });
    }
}

$(() => {
    let model = new Usuarioconfiguracion();
    $(document).on("click", ".btn-grid-action-config", function () {
        let id = $(this).val();
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
})