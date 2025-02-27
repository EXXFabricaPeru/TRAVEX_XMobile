declare var $;

class Ruta {
    public setting: any;
    public Objet: any;
    protected url: string;
    protected element: any;
    protected elementGrid: string;

    constructor() {
        this.element = $(".window");
        this.url = $("#PATH").attr("name");
        this.Objet = Object;
        this.elementGrid = "#Ruta-list";
        this.setting = {width: '80%', height: 500, hide: 'fade', show: 'fade', modal: true};
    }

    public onloadViewForm() {
        $("#rutacabecera-fecha").datepicker({dateFormat: 'yy-mm-dd'});        
    }

    public windowCreate() {
        this.element.html('<div class="loader">Loading...</div>');
        let option = {
            title: 'NUEVO REGISTRO',
            open: () => {
                this.element.load(this.url + 'ruta/create', () => {
                    this.onloadViewForm();
                });
            },
            buttons: [{
                text: "TRAZAR RUTA",
                class: "btn btn-success",
                click: function () {
                    calcularRuta();
                }
            },{
                text: "REGISTRAR",
                class: "btn btn-success",
                click: function () {
                    guardar();    
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
                        $.pjax.reload({container: this.elementGrid, async: false});
                        $("#windowEliminar").dialog("close");
                    } catch (e) {
                        alert("Ocurrio un !ERROR")
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
                this.element.load(this.url + 'ruta/update&id=' + id);
            },
            buttons: [{
                text: "ACTUALIZAR",
                class: "btn btn-success",
                click: function () {
                        actualizar();
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
        let url = this.url + 'ruta/report&id=' + id;
        $("#windowpdf").dialog({
            width: '100%', height: 500, modal: true, open: () => {
                $("#windowpdf").html('<embed src="' + url + '" type="application/pdf" width="100%" height="100%" />');
            }
        });
    }

    private requestPut(id: any, data: any) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.url + 'ruta/update&id=' + id,
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
            $.get(this.url + 'ruta/eliminar&id=' + data).done((data) => {
                resolve(data);
            }).fail((err) => {
                reject(err);
            });
        });
    }

    private requestPost(data: any) {
        return new Promise((resolve, reject) => {
            alert('aqui 2');
            $.ajax({
                url: this.url + 'ruta/create',
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
    let model = new Ruta();
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