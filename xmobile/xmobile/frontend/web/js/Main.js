var Main = (function () {
    function Main() {
        var _this = this;
        $(function () {
            _this.path = $('#datapath').attr('name');
            $("#actionPasoUno").click(function () {
                $("#actionPasoUno").attr("disabled", true);
                _this.actionPaso1();
            });
            $("#btnactionPasoDos").click(function () {
                _this.actionPaso2();
            });
            $("#btnactionPasoTres").click(function () {
                _this.actionPaso3();
            });
        });
    }
    Main.prototype.actionPaso3 = function () {
        $(".spinner").show();
        var data = $("#actionPasoTres").serialize();
        console.log(data);
        
        $.post(this.path + "site/monedas", data).done(function (data) {
            location.href = $('meta[name="baseUrl"]').attr('content');
            $(".spinner").hide();
        }).fail(function (err) {
            console.log(err);
        });
    };
    Main.prototype.actionPaso2 = function () {
        $(".spinner").show();
        var data = $("#actionPasoDos").serialize();
        $.post(this.path + "site/appsap", data).done(function (data) {
            $("#pasoDos").hide();
            $("#pasoTres").show();
            $(".spinner").hide();
            $("#actionPasoDos").attr("disabled", false);
            $html = '<option value="">Seleccione...</option>';
            data.forEach(element => {
                console.log(element);
                $html += '<option value="'+element.Code+'">'+element.InternationalDescription+'</option>'
            });
            $('#moneda-sistema').html($html);
            $('#moneda-local').html($html);
            $('#moneda-otro').html($html);
        }).fail(function (err) {
            console.log(err);
        });
    };
    Main.prototype.actionPaso1 = function () {
        $(".spinner").show();
        var data = $("#form-data-1").serialize();
        $.post(this.path + "site/verifica", data).done(function (data) {
            if (data == 0) {
                $("#pasoUno").hide();
                $("#pasoDos").show();
            }
            else {
                $("#textMsm").html('<div class="alert alert-danger">' + data + '</div>');
            }
            $(".spinner").hide();
            $("#actionPasoUno").attr("disabled", false);
        }).fail(function (err) {
            $(".spinner").hide();
            $("#actionPasoUno").attr("disabled", false);
        });
    };
    return Main;
}());
new Main();
