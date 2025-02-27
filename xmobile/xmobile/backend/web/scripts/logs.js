$('document').ready(function(){
    //$("#tabs").tabs();
    $("#tabs").tabs();
    setTimeout(function () {
        
        $("#tabs").show('fade');
        $("#cargandoLoad").hide('fade');
    }, 500);
    
    /*var table2 = $('#dataTables-view-pc-egreso').DataTable( {
        "oLanguage": {
            "sUrl": "../web/datatables/datatable.spanish.txt"                        
        },
        responsive: true
    } );
    new $.fn.dataTable.FixedHeader(table2);*/
    $('#dataTables-view-pc-egreso').DataTable({
        "oLanguage": {
            "sUrl": "../web/datatables/datatable.spanish.txt"                        
        },
        responsive: true,
        pagingType: 'simple',
    });

    $('#dataTables-view-pc-egreso').on('shown.bs.collapse', function () {
        $($.fn.dataTable.tables(true)).DataTable()
           .columns.adjust();
     });
});