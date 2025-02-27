import {Component, OnInit} from '@angular/core';
import {ModalController, NavParams} from "@ionic/angular";
import {Seriesproductos} from "../../models/seriesproductos";
import {BarcodeScanner} from "@ionic-native/barcode-scanner/ngx";

@Component({
    selector: 'app-modalseries',
    templateUrl: './modalseries.page.html',
    styleUrls: ['./modalseries.page.scss'],
})
export class ModalseriesPage implements OnInit {
    public parm: any;
    public series: any;
    public seriesAdd: any;
    public serieproductos: Seriesproductos;
    public textsearch: string;

    constructor(public modalController: ModalController, public navParams: NavParams, private barcodeScanner: BarcodeScanner) {
        this.parm = navParams.data;
        this.seriesAdd = [];
        this.series = [];
        this.serieproductos = new Seriesproductos();
        this.textsearch = '';
    }

    public async ngOnInit() {
        try {
            this.series = await this.serieproductos.select(this.parm.ItemCode, this.parm.WhsCode, '');
        } catch (e) {
            console.log(e);
        }
    }

    public async actionCodeBarrasSeries() {
        try {
            let resp: any = await this.barcodeScanner.scan();
            if (resp.cancelled != true) {
                this.textsearch = resp.text;
                let respx: any = await this.serieproductos.select(this.parm.ItemCode, this.parm.WhsCode, resp.text);
                this.series = respx;
            }
        } catch (e) {
            console.log(e);
        }
    }

    public async buscarseries(event: any) {
        this.series = [];
        let search = event.detail.value;
        let resp: any = await  this.serieproductos.select(this.parm.ItemCode, this.parm.WhsCode, search);
        this.series = resp;
    }

    public selecionarCheket(code: string) {
        let codex = `${code}`;
        console.log("includes array series:", (!this.seriesAdd.includes(codex)));
        if (!this.seriesAdd.includes(codex)) {
            this.seriesAdd.push(codex);
        } else {
            this.seriesAdd.splice(this.seriesAdd.indexOf(codex), 1);
        }
        this.actionControl();
    }

    public actionControl() {
        if (this.seriesAdd.length == this.parm.cantidad)
            this.modalController.dismiss(this.seriesAdd);
    }

    public cerrar() {
        this.modalController.dismiss(1);
    }
}
