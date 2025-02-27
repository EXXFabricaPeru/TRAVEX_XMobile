var Acciones = (function () {
	Acciones.prototype.windowEliminar = function (id) {
		var _this = this;
		var obj = {
			width: 400, height: 250, hide: 'fade', show: 'fade', modal: true, title: 'ALERTA',
			buttons: [{
					text: "SI",
					class: "btn btn-danger",
					click: function () { 
						autorizaAnulacion(id);
						//$("#windowEliminar").dialog("close");
					}
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
});
$(function(){
	var model = new Acciones();
    $(document).on("click", ".btn-grid-action-autoriza", function () {
        var id = $(this).val();
        model.windowEliminar(id);
    });
	
})

function autorizaAnulacion(id){
	//alert(id);
	$.ajax({
		url: 'index.php?r=cabeceradocumentos/autorizaranulacion',
		type: 'POST',
		data: "id="+id,
		success: function (data, status, xhr) {
			//var respuesta=(JSON.parse(data));
			if(data=='CORRECTO'){

			}else if(data=='CERRADO'){
				alert("No se puede autorizar la anulación del documento, revisar SAP.");
			}else if(data=='SIN REGISTROS'){
				console.log("SIN REGISTROS");
			}else if(data=='ERROR'){
				alert("Error al actulizar estado verificar conexion.");
			}
			$("#windowEliminar").dialog("close");
			location.reload();
		},
		error: function (jqXhr, textStatus, errorMessage) {
			console.log(errorMessage);
			$("#windowEliminar").dialog("close");
		}
	});
}