declare var $;

class Main {
    public path: string;

    constructor() {
        $(() => {
            this.path = $('#datapath').attr('name');
            $("#actionPasoUno").click(() => {
                $("#actionPasoUno").attr("disabled", true);
                this.actionPaso1();
            });

            $("#btnactionPasoDos").click(() => {
                this.actionPaso2();
            })
        })
    }

    public actionPaso2() {
        $(".spinner").show();
        let data = $("#actionPasoDos").serialize();
        $.post(this.path + "site/appsap", data).done((data) => {
            location.href = '/xm/';
            $(".spinner").hide();
        }).fail((err) => {
            console.log(err);
        });
    }

    public actionPaso1() {
        $(".spinner").show();
        let data = $("#form-data-1").serialize();
        $.post(this.path + "site/verifica", data).done((data) => {
            if (data == 0) {
                $("#pasoUno").hide();
                $("#pasoDos").show();
            } else {
                $("#textMsm").html('<div class="alert alert-danger">' + data + '</div>');
            }
            $(".spinner").hide();
            $("#actionPasoUno").attr("disabled", false);
        }).fail((err) => {
            $(".spinner").hide();
            $("#actionPasoUno").attr("disabled", false);
        });
    }
}

new Main();