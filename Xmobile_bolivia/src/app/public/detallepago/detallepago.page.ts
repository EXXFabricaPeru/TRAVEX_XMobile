import { Component, OnInit, ViewChild } from '@angular/core';
import { AlertController, ModalController, NavController, NavParams } from "@ionic/angular";
import { ModalclientePage } from "../modalcliente/modalcliente.page";
import { ActivatedRoute } from "@angular/router";
import { Documentos } from "../../models/documentos";
import { ConfigService } from "../../models/config.service";
import { Documentopago } from "../../models/documentopago";
import { Pagos } from "../../models/pagos";
import { Clientes } from "../../models/clientes";
import { Calculo } from "../../utilsx/calculo";
import { NativeService } from "../../services/native.service";
import { ReportService } from "../../services/report.service";
import { FrompagosPage } from "../frompagos/frompagos.page";
import { Toast } from "@ionic-native/toast/ngx";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import * as moment from 'moment';
import { connectableObservableDescriptor } from 'rxjs/internal/observable/ConnectableObservable';
import { DataService } from '../../services/data.service';
import { PagosService } from '../../services/pagos.service'

@Component({
    selector: 'app-detallepago',
    templateUrl: './detallepago.page.html',
    styleUrls: ['./detallepago.page.scss'],
    providers: [NavParams]

})
export class DetallepagoPage implements OnInit {
    public data: any;
    public id: any;
    public deudasFactura: any;
    public arr: any;
    public codPago: any;
    public pagoData: any;
    public pagosArr: any;
    public userdata: any;
    public tc: any;
    public searchData: any;
    public documentosFactura: any;
    public total: number;
    public numerox: number;
    public totalUSD: number;
    public idUser: any;
    idParam: any;
    public estadomensaje1: Boolean;
    public estadomensaje2: Boolean;
    public estadopagocuenta: boolean;
    public estadopagoa: boolean;
    public monedalocal: string;

    @ViewChild('datePickerfill') datePickerfill;

    constructor(private native: NativeService, public modalController: ModalController, private dataService: DataService, private configService: ConfigService, private reportService: ReportService, public navParams: NavParams,
        private activatedRoute: ActivatedRoute, private pagosservice: PagosService, private navCrl: NavController, public alertController: AlertController, private toast: Toast, private spinnerDialog: SpinnerDialog) {
        this.data = [];
        this.deudasFactura = [];
        this.arr = [];
        this.pagoData = [];
        this.documentosFactura = [];
        this.pagosArr = [];
        this.userdata = [];
        this.monedalocal = '';
        this.total = 0;
        this.tc = 0;
        this.numerox = 0;
        this.totalUSD = 0;
        this.id = this.activatedRoute.snapshot.paramMap.get('id');
        this.idParam = activatedRoute.snapshot.paramMap.get('id');
        this.estadomensaje1 = true;
        this.estadopagocuenta = false;

        this.estadomensaje2 = true;
        this.estadopagoa = false;
        console.log("DEVD this.activatedRoute.snapshot.paramMap.get('id') ", this.activatedRoute.snapshot.paramMap.get('id'));
        if (!this.id) {


            let facturasPenMarker = "NO";
            let cardCodeMarker = "";
            try {
                facturasPenMarker = localStorage.getItem("facturasPenMarker");
                cardCodeMarker = localStorage.getItem("cardCodeMarker");

            } catch (error) {
                console.error("error get localstorage marker ", error);
            }
            if (facturasPenMarker == "SI") {
                this.id = cardCodeMarker;
            }

        }
    }

    public ngOnInit() {
        this.indexDetalles(true);
    }
    async ionViewDidEnter() {
        console.log("this.data.CardCode ", this.data.CardCode);

        console.log("ionViewDidEnter ");



    }

    public async indexDetalles(tick: boolean) {
        this.userdata = await this.configService.getSession();

        for (let monedas of this.userdata[0].monedas) {
            if(monedas.Type == 'L'){
                this.monedalocal = monedas.Code;
            }
        }


        if (this.userdata[0].config[0].permisoPagosAnticipados == "1") {
            this.estadomensaje1 = true;
            this.estadopagocuenta = false;
        } else {
            this.estadomensaje1 = false;
            this.estadopagocuenta = true;
        }

        if (this.userdata[0].config[0].permisoPagoFacturasImportadas == "1") {
            this.estadomensaje2 = true;
            this.estadopagoa = false;
        } else {
            this.estadomensaje2 = false;
            this.estadopagoa = true;
        }

        this.idUser = this.userdata[0].idUsuario;
        this.tc = parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio);


        this.run();
    }

    public run() {
        this.generaCod();
        if (this.id == 'null') {
            setTimeout(() => {
                this.seleccionarCliente();
            }, 100);
        } else {
            this.selectCliente();
            this.listarfacturas();
        }
    }
    public cerrar() {
        console.log("cerrar ");
        this.navCrl.pop();
        try {
            this.modalController.dismiss(true);
        } catch (error) {
            console.log("error al cerrar ");


        }

    }
    public async seleccionarCliente() {
        let obj: any = { component: ModalclientePage, componentProps: { tipo: '' } };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then((data: any) => {
            if (typeof data.data != "undefined")
                if (typeof data.data.CardCode != 'undefined') {
                    this.id = data.data.CardCode;
                    this.selectCliente();
                    this.listarfacturas();
                } else {
                    this.navCrl.pop();
                }
        });
        return await modal.present();
    }

    public async selectCliente() {
        try {
            let clientes = new Clientes();
            let rx: any = await clientes.find(this.id);
            this.data = rx[0];
        } catch (e) {
            console.log(e);
        }
    }

    public async listarfacturas() {
        console.log("listarfacturas()");
        this.spinnerDialog.show();
        let documentos = new Documentos();
        try {

            this.documentosFactura = await documentos.deudasCliente(this.id);
            console.log("deudasClienteAll ", this.documentosFactura);
        } catch (e) {
            console.log("ERROR ", e);
        }
        this.spinnerDialog.hide();
    }

    public async generaCod() {
        try {
            let inix: any = await this.pagosservice.getNumeracionpago();
            this.numerox = (inix + 1);
        } catch (e) {
            this.numerox = 1;
        }
        let id: any = await this.configService.getSession();
        this.codPago = Calculo.generaCodeRecibo(id[0].idUsuario.toString(), this.numerox.toString(), '1');
    }



    public numberapp(num: any) {
        return parseFloat(num);
    }

    public async accionFecha(event: any) {
        let resp: any = moment(event.detail.value).format('YYYY-MM-DD');
        this.searchData = resp;
        this.buscarinput(null);
    }

    public async buscarinput(event: any) {
        this.documentosFactura = [];
        if (event != null)
            this.searchData = event.detail.value;
        let documentos = new Documentos();
        this.documentosFactura = await documentos.deudasCliente(this.id, this.searchData);
    }


    public async formPagoCliente(tipo: number, monto = 1) {

        console.log("this.data; ", this.data);
        let numerox: number = 0;
        let inix: any = await this.pagosservice.getNumeracionpago();
        numerox = (inix + 1);
        let codPago = Calculo.generaCodeRecibo(this.idUser.toString(), numerox.toString(), '1');
        let datospago = {
            modo: 'CLIENTE',
            cod: codPago,
            cliente: this.data.CardCode,
            tipo: tipo,
            monto: monto,
            documento: [],
            dataCliente: this.data,
            correlativo: numerox
        };
        console.log("data pago listo para registrar", datospago)
        let obj: any = { component: FrompagosPage, componentProps: datospago };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            if (typeof data.data != "undefined")
                if (data.data != 0) {
                    await this.sumadorrecibo();
                    let documentopago = new Documentopago();
                    let pagos = new Pagos();
                    let aux_pago_cab = await documentopago.findPagos(data.data.documentoPagoId);
                    let aux_pago_detalle: any = [];
                    if (data.data.dx == 'cuenta') aux_pago_detalle = await pagos.findAllPagosCuenta(data.data.documentoPagoId);
                    else aux_pago_detalle = await pagos.findAllPagos(data.data.documentoPagoId);
                    try {
                        let resp: any = await this.reportService.generarecibo(aux_pago_cab[0], this.data, true, aux_pago_detalle, this.userdata);
                        if (resp) this.reportService.generateEXE(data.data.documentoPagoId);
                        this.toast.show(`El pago se realizó correctamente.`, '4000', 'top').subscribe(toast => {
                        });

                    } catch (error) {

                        this.toast.show(`El pago se realizó correctamente.`, '4000', 'top').subscribe(toast => {
                        });
                    }


                }
        });
        return await modal.present();
    }

    public async pagos() {
        if (this.userdata[0].config[0].permisoPagosAnticipados == 0) {
            this.toast.show(`No está permitido para realizar pagos de anticipo.`, '2500', 'center').subscribe(toast => {
            });
            return false;
        }
        let alert: any = await this.alertController.create({
            header: 'INTRODUZCA EL MONTO A CANCELAR.',
            mode: 'ios',
            inputs: [{
                name: 'data',
                type: 'number',
                min: 0,
                max: 10000,
                value: "",
                placeholder: '0'
            }],
            buttons: [{
                text: 'SOLES (SOL)',
                handler: (data: any) => {
                    if (data.data > 0) {
                        this.formPagoCliente(2, data.data);
                    } else {
                        this.toast.show(`El monto introducido no es válido.`, '4000', 'top').subscribe(toast => {
                        });
                        return false;
                    }
                }
            }/*, {
                text: 'DOLARES (USD)',
                handler: (data: any) => {
                    if (data.data > 0) {
                        this.formPagoCliente(1, data.data);
                    } else {
                        this.toast.show(`El monto introducido no es válido.`, '4000', 'top').subscribe(toast => {
                        });
                        return false;
                    }
                }
            }*/
            ]
        });
        await alert.present();
    }

    public openCalendar() {
        this.datePickerfill.open();
    }

    public async cambioDePrecio(item: any, index: number) {
        let alert: any = await this.alertController.create({
            header: 'INTRODUCIR EL MONTO QUE DESEA CANCELAR.',
            inputs: [{
                name: 'data',
                type: 'number',
                value: 0,
                min: 1
            }],
            buttons: [{
                text: 'CANCELAR',
                role: 'cancel',
            }, {
                text: 'CONTINUAR',
                handler: (data: any) => {
                    let monex = parseFloat(data.data);
                    if (monex <= item.saldox) {
                        item.pagarx = parseFloat(data.data);
                        item.check = true;
                        this.deudasFactura[index] = item;
                        this.xngListar();
                        this.toast.show(`La factura ${item.DocEntry} se pagara ${monex}.`, '3000', 'top').subscribe(toast => {
                        });
                    } else {
                        this.toast.show(`El monto supero al el saldo.`, '4000', 'top').subscribe(toast => {
                        });
                        return false;
                    }
                }
            }]
        });
        await alert.present();
    }

    public xngListar() {
        let cx = 0;
        for (let item of this.documentosFactura)
            if (typeof item.check !== "undefined" && item.check == true)
                cx += parseFloat(item.pagarx);
        this.total = Calculo.round(cx);
        this.totalUSD = Calculo.round(cx / this.tc);
    }

    /**********PAGO START*********/
    public async formPago(tipo: number, monto: number) {
        let arrax = [];
        let numerox: number = 0;
        let inix: any = await this.pagosservice.getNumeracionpago();
        console.log("inix NUMERACION EXISTENTE ", inix);

        numerox = (inix + 1);
        console.log("NUEVI NUMERO ", numerox);

        let codPago = await Calculo.generaCodeRecibo(this.idUser.toString(), numerox.toString(), '1');
        console.log("codPago CODIGO GENERADO ", codPago);


        for (let item of this.documentosFactura) {
            if (typeof item.check !== "undefined" && item.check == true) {
                console.log("data item-->", item);
                arrax.push(
                    item
                    // cod: item.cod,
                    // coddoc: item.cod,
                    // pagarx: parseFloat(item.pagarx),
                    // recibo: codPago,
                    // clienteId: this.data.CardCode,
                    // docentry: item.DocEntry,
                    // docnum: item.DocNum,
                    // cuota: item.cuota

                );
            }
        }


        let datospago = {
            modo: 'FACTURAS',
            cod: codPago,
            cliente: this.data.CardCode,
            tipo: tipo,
            monto: monto,
            documento: arrax,
            dataCliente: this.data,
            correlativo: numerox
        };
        console.log("data params send", datospago);
        let obj: any = { component: FrompagosPage, componentProps: datospago };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            console.log("data.data dissmis ", data.data);

            if (data.data != 0) {
                let detpago = new Pagos();
                this.totalUSD = 0;
                this.total = 0;
                await this.sumadorrecibo();
                this.listarfacturas();
                // let auxmonto = [];
                // data.data.total = data.data.monto;
                // auxmonto.push(data.data);
                // console.log("dataparams", data);
                // let documentopago = new Documentopago();

                // let aux_pago_cab = await documentopago.findPagos(data.data.documentoPagoId);
                // console.log("tipo de pago anticipo o cobro", data.data.dx);

                // let aux_pago_detalle: any = data.data.dx == 'cuenta' ? await detpago.findAllPagosCuenta(data.data.documentoPagoId) : await detpago.findAllPagos(data.data.documentoPagoId);
                // console.log("data detalle pago", aux_pago_detalle);
                // console.log("data cabezera pago", aux_pago_cab);
                // if (data.data.otpp == 2) {

                //     let auxFacturas = await detpago.selectOne(data.data.documentoPagoId);
                //     aux_pago_detalle.facturas = auxFacturas;
                // }
                // console.log("llega aqui2");
                // let rex: any = await this.reportService.generarecibo(aux_pago_cab[0], this.data, true, aux_pago_detalle, this.userdata);

                // if (rex) this.reportService.generateEXE(data.data.documentoPagoId);
                // this.toast.show(`El pago se realizó correctamente.`, '4000', 'top').subscribe(toast => { });
            }
        });
        return await modal.present();
    }
    /* public async formPago(tipo: number, monto: number) {
        let arrax = [];
        for (let item of this.documentosFactura) {
            if (typeof item.check !== "undefined" && item.check == true) {
                arrax.push({
                    cod: item.cod,
                    coddoc: item.cod,
                    pagarx: parseFloat(item.pagarx)
                });
            }
        }
        let numerox: number = 0;
        let inix: any = await this.configService.getNumeracionpago();
        numerox = (inix + 1);
        let codPago = Calculo.generaCodeRecibo(this.idUser.toString(), numerox.toString(), '1');
        let datospago = {
            modo: 'FACTURAS',
            cod: codPago,
            cliente: this.data.CardCode,
            dataCliente: this.data,
            tipo: tipo,
            monto: monto,
            documento: arrax,
            dataCliente: this.data
        };
        console.log("data enviada",datospago);
        console.log("data enviada",this.data);
        let obj: any = { component: FrompagosPage, componentProps: datospago };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            if (data.data != 0) {
                this.totalUSD = 0;
                this.total = 0;
                await this.sumadorrecibo();
                this.listarfacturas();
                let auxmonto = [];
                data.data.total = data.data.monto;
                auxmonto.push(data.data);
                let documentopago = new Documentopago();
                let detpago = new Pagos();
                let aux_pago_cab = await documentopago.findPagos(data.data.documentoPagoId);
                let aux_pago_detalle = await detpago.findAllPagos(data.data.documentoPagoId);
                let rex: any = await this.reportService.generarecibo(aux_pago_cab[0], this.data, true, aux_pago_detalle, this.userdata);
                if (rex) this.reportService.generateEXE(data.data.documentoPagoId);
                this.toast.show(`El pago se realizó correctamente.`, '4000', 'top').subscribe(toast => {
                });
            }
        });
        return await modal.present();
    } */

    public async modalPagos(tipoxpagax: number) {
        let tipx = '';
        let apagax = 0;
        if (this.totalUSD == 0 && this.total == 0) {
            this.toast.show(`El monto tiene que ser mayor a cero.`, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        console.log("tipoxpagax", tipoxpagax);
        
        if (tipoxpagax == 2) {
            apagax = this.total;
            tipx = this.monedalocal;
        } else {
            apagax = this.totalUSD;
            tipx = ' USD ';
        }
        let alert: any = await this.alertController.create({
            header: '(' + tipx + ') CONFIRME EL MONTO A PAGAR. ',
            inputs: [{
                name: 'data',
                type: 'number',
                min: 0,
                disabled:true,
                value: apagax,
                placeholder: '0'
            }],
            buttons: [{
                text: 'CANCELAR',
            }, {
                text: 'CONTINUAR...',
                handler: (data: any) => {
                    if (tipoxpagax == 2) {
                        if (data.data >= this.total) {
                            this.formPago(tipoxpagax, data.data);
                        } else {
                            this.toast.show(`El monto introducido no es válido.`, '4000', 'top').subscribe(toast => {
                            });
                            return false;
                        }
                    } else {
                        if (data.data >= this.totalUSD) {
                            this.formPago(tipoxpagax, data.data);
                        } else {
                            this.toast.show(`El monto introducido no es válido.`, '4000', 'top').subscribe(toast => {
                            });
                            return false;
                        }
                    }
                }
            }]
        });
        await alert.present();
    }

    public async sumadorrecibo(): Promise<any> {
        try {
            let inix: any = await this.pagosservice.getNumeracionpago();
            let xc: number = (inix + 1);
            await this.configService.setNumeracionpago(xc);
            return true;
        } catch (e) {
        }
    }
}
