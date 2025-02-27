import { Component, OnInit } from '@angular/core';
import { ModalController, Platform } from "@ionic/angular";
import { Anular } from "../../models/anular";
import { WheelSelector } from "@ionic-native/wheel-selector/ngx";
import { Toast } from "@ionic-native/toast/ngx";

@Component({
    selector: 'app-anular',
    templateUrl: './anular.page.html',
    styleUrls: ['./anular.page.scss'],
})

export class AnularPage implements OnInit {
    public razones: any;
    public opcionAnular: string;
    public code: string;
    public conceptoAnulacion: string;

    constructor(public modalController: ModalController, private selector: WheelSelector, private toast: Toast, private platform: Platform) {
        this.razones = [];
        this.opcionAnular = "";
        this.conceptoAnulacion = "";
        this.code = "";
        this.platform.backButton.subscribe(() => {
            // do something here
            //alert("back ");
            this.cerrar(false);

        });
    }

    public ngOnInit() {

    }

    public async getOptions() {
         console.log("getOptions")
        let anular = new Anular();
        this.razones = await anular.select();
        console.log(" this.razones ",  this.razones)
        let razonesArrx = [];
        for (let x of this.razones)
            razonesArrx.push({ description: x.Name });
        if (this.razones.length > 0) {
            this.selector.show({
                title: "SELECCIONAR UNA OPCIÓN.",
                items: [razonesArrx],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR"
            }).then(async (result: any) => {
                let xx = this.razones[result[0].index];
                this.opcionAnular = xx.Code;
                this.code = xx.Code;
            }, (err: any) => {
                this.toast.show(`Selecciona una sucursal.`, '4000', 'top').subscribe(toast => {
                });
            });
        }
    }

    public enviarAnulacion() {
        if (this.code == '') {
            this.toast.show(`Motivo de la anulación  no pude ser vacio.`, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        if (this.conceptoAnulacion == '') {
            this.toast.show(`Concepto de la anulación no puede ser vacio.`, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        let data = {
            opcionAnular: this.opcionAnular,
            conceptoAnulacion: this.conceptoAnulacion,
            code: this.code
        };
        this.cerrar(data);
    }

    public cerrar(data: any) {
        this.modalController.dismiss(data);
    }
}
