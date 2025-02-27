$(function () {
    $(document).on("click", ".btn-grid-action-upload", function () {
        var id = $(this).data('id');
        $("#namefoto").val(id);
        $("#dialogupload").dialog({wisth: 600, height: 250, modal: true});
    });
    $("#subirfoto").dropzone();
})