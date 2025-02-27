$(() => {
    $(document).ajaxStart(function () {
        $("#loadingAjax").show();
    });

    $(document).ajaxComplete(function () {
        $("#loadingAjax").hide('fade');
    });
})