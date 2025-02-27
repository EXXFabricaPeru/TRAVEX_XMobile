declare var $;

class Configuracion {
    public setting: any;
    public Objet: any;
    protected url: string;
    protected element: any;
    protected elementGrid: string;

    constructor() {
        this.element = $(".window");
        this.url = $("#PATH").attr("name");
        this.Objet = Object;
        this.elementGrid = "#Configuracion-list";
        this.setting = {width: '80%', height: 500, hide: 'fade', show: 'fade', modal: true};
    }

    public windowCreate() {
        this.element.html('<div class="loader">Loading...</div>');
        let option = {
            title: 'NUEVO REGISTRO',
            open: () => {
                this.element.load(this.url + 'configuracion/create');
            },
            buttons: [{
                text: "REGISTRAR",
                class: "btn btn-success",
                click: async () => {
                    let data = $('#Configuracion-form').serialize();
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
                this.element.load(this.url + 'configuracion/update&id=' + id);
            },
            buttons: [{
                text: "GUARDAR",
                class: "btn btn-success",
                click: async () => {
                    let data = $('#Configuracion-form').serialize();
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
        let url = this.url + 'configuracion/report&id=' + id;
        $("#windowpdf").dialog({
            width: '100%', height: 500, modal: true, open: () => {
                $("#windowpdf").html('<embed src="' + url + '" type="application/pdf" width="100%" height="100%" />');
            }
        });
    }

    private requestPut(id: any, data: any) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.url + 'configuracion/update&id=' + id,
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
            $.get(this.url + 'configuracion/eliminar&id=' + data).done((data) => {
                resolve(data);
            }).fail((err) => {
                reject(err);
            });
        });
    }

    private requestPost(data: any) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.url + 'configuracion/create',
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
    let model = new Configuracion();
    $("#btn-create").on('click', () => {
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
    });
})