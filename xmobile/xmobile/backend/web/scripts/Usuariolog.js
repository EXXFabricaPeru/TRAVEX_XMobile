

$(function () {
    $("#btn-detallesincro").on('click', function () {

    });
});
function CargarServicios(fechaInicio,fechaFin,idUsuario,user,horaSincronizacion){
    
    this.url = $("#PATH").attr("name");
    _this=this;
    ObternerListaSincronizacion(_this,fechaInicio,fechaFin,idUsuario,user,horaSincronizacion);
}

function ObternerListaSincronizacion(_this,fechaInicio,fechaFin,idUsuario,user,horaSincronizacion){
    //var fechaUltimo=fecha.split('-');
   // fechaUltimo=fechaUltimo[2]+"/"+fechaUltimo[1]+"/"+fechaUltimo[0];
    $("#NOMBRE-FECHA").text("Usuario: "+user+horaSincronizacion);
    $.ajax({
        url: _this.url + 'usuariolog/create',
        type: 'POST',
        //dataType: 'json',
        data: 'CONDICION=SINCRONIZACION&FECHAINICIO='+fechaInicio+'&FECHAFIN='+fechaFin+'&IDUSUARIO='+idUsuario,
        success: function (data) {
            //alert(data);
            if(data.trim()!="0"){
                ArrayData=$.parseJSON(data.trim());
               
                var complemento="";
                var color="";
                var divReport = document.getElementById("DIV-SERVICIOS");
        
                var cadena="<table  class='table table-striped table-bordered table-hover'>";
                
                for(var i=0;i<ArrayData.length;i++){
                   
                    if(i%2==0){
                        cadena=cadena+"<tr>";
                    }
                   //alert(data[i].descripcion);
                    if(ArrayData[i].servicio>0){
                        complemento ="<span style='color:GREEN'  class='glyphicon glyphicon-ok'></span>";
                        color="";
                    } 
                    else{
                        complemento="<span  class='glyphicon glyphicon-ban-circle'></span>";
                        color="style='color:RED'";
                    }

                    cadena=cadena+ "<td class='col-lg-6' "+color+" ><div class='row'> <div class='col-lg-10'>"+ArrayData[i].descripcion+":</div> <div class='col-lg-2'> "+complemento+" </div></div></td>"; 
                    
                    if(i%2==1){
                        cadena=cadena+"</tr>";
                    }
                   
                }
                cadena=cadena+"</table>";
                divReport.innerHTML=cadena;
            }
            //return ArrayData;
       },
        error: function (jqXhr, textStatus, errorMessage) {
            reject(errorMessage);
        }
    });
}

function cargarPagina(){
    // location.reload("dddd");
    var valor=$('#ESTADO').val();
    var sincronizo=0;
    if($('#SINCRONIZO').is(':checked')){
        sincronizo=1;
     }
  
     location.href = "index.php?r=usuariolog&fechaInicio="+$('#FECHAINICIO').val()+"&fechaFin="+$('#FECHAFIN').val()+"&id="+$('#USUARIO').val()+"&estado="+valor+"&sincronizo="+sincronizo;
}

function cambiaEstado(valor){
   if(valor==1)document.getElementById("SINCRONIZO").disabled = false;
   else{
        document.getElementById("SINCRONIZO").disabled = true;
        document.getElementById("SINCRONIZO").checked = false;
   }
}

function doSearch(){
    const tableReg = document.getElementById('datos');
    const searchText = document.getElementById('searchTerm').value.toLowerCase();
    let total = 0;

    // Recorremos todas las filas con contenido de la tabla
    for (let i = 1; i < tableReg.rows.length; i++) {
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

$("#USUARIO").val($("#USUARIO_").val());
