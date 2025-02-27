declare var $;

class Viewrol {
    public idx: any = $("#idview").data('id');
    protected url: string;

    constructor() {
        this.url = $("#PATH").attr("name");
    }

    public async select(id: any) {
        let obj = {
            "rol": this.idx,
            "idaction": id,
            "est": true,
            "_csrf-backend": $('meta[name="csrf-token"]').attr("content")
        }
        await this.requestPost(obj);
    }

    public async disselect(id: any) {
        let obj = {
            "id": id,
            "_csrf-backend": $('meta[name="csrf-token"]').attr("content")
        }
        await this.requestPost(obj);
    }

    private requestPost(data: any) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.url + 'permisosx/create',
                type: 'POST',
                data: $.param(data),
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
    let model = new Viewrol();
    $(".selectChebox").on('click', function () {
        model.select($(this).val());
    });
});