import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from "@angular/router";
import { Documentos } from "../../models/documentos";
import { Detalle } from "../../models/detalle";
import { Toast } from "@ionic-native/toast/ngx";
import { AlertController, ModalController, NavController } from "@ionic/angular";
import { Clientes } from "../../models/clientes";
import { Calculo } from "../../utilsx/calculo";
import { ModalpagosPage } from "../modalpagos/modalpagos.page";
import { ConfigService } from "../../models/config.service";

@Component({
    selector: 'app-detallepedido',
    templateUrl: './detallepedido.page.html',
    styleUrls: ['./detallepedido.page.scss'],
})
export class DetallepedidoPage implements OnInit {
    public id: any;
    public documentosdata: Documentos;
    public data: any;
    public itemsax: any;
    public items: any;
    public idPedido: any;
    public ctrol: number;
    public xmovilectrol: number;
    public iniNum: any;
    public idUser: any;

    constructor(private activatedRoute: ActivatedRoute, private toast: Toast, public modalController: ModalController,
        public alertController: AlertController, private configService: ConfigService, private navCrl: NavController) {
        this.documentosdata = new Documentos();
        this.data = [];
        this.items = [];
        this.itemsax = [];
        this.idPedido = 0;
        this.ctrol = 0;
        this.xmovilectrol = 0;
        this.iniNum = [];
    }

    public async ngOnInit() {
        let id: any = await this.configService.getSession();
        this.idUser = id.idUsuario;
        this.run();
    }

    public async run() {
        this.id = this.activatedRoute.snapshot.paramMap.get('id');
        this.data = await this.documentosdata.find(this.id);
        console.log("caga el data",this.data);
        this.idPedido = this.data.id;
        this.listarEntregas();
    }

    public async listarEntregas() {
        let detalle = new Detalle();
        let iten: any = await detalle.findAll(this.data.id);
        this.itemsax = iten;
        let arr = [];
        for (let im of iten) {
            im.check = false;
            im.entregar = 0;
            im.xmovilectrol = 0;
            arr.push(im)
        }
   
        this.items = arr;

    }

    private async codGenx(tipoDoc: any) {
        let inix: any = await this.configService.getNumeracion();
        this.iniNum = JSON.parse(inix);
        let numerox: any = 0;
        switch (tipoDoc) {
            case ('DFA'): // factura
                numerox = this.iniNum.numdfa += 1;
                break;
            case ('DOP'): // pedido
                numerox = this.iniNum.numdop += 1;
                break;
            case ('DOE'): // entrega
                numerox = this.iniNum.numdoe += 1;
                break;
            case ('DOF'): // oferta
                numerox = this.iniNum.numdof += 1;
                break;
        }
        return numerox;
    }

    public async sumadorNumeracion() {
        return new Promise(async (resolve, reject) => {
            let inix: any = await this.configService.getNumeracion();
            let ini: any = JSON.parse(inix);
            ini.numdoe += 1;
            await this.configService.setNumeracion(JSON.stringify(ini));
            resolve(true);
        })
    }

    public async confirEntrega() {
        let datax: any = await this.codGenx('DOE');
        let cod: any = await this.documentosdata.generaCod('DOE', this.idUser, datax);
        this.data.cod = cod;
        this.data.DocCurrency = this.data.currency;
        this.data.Saldo = this.data.saldo;
        this.data.facreserva = true;
        this.data.origen = 'outercopy';
        this.data.clone = this.data.id;
        this.data.estadoSend = 1;
        this.data.CreationDate = this.documentosdata.getFechaPicker() + ' ' + this.documentosdata.horaActual();
        this.data.UpdateDate = this.documentosdata.getFechaPicker();
        this.data.fechasend = this.documentosdata.getFechaPicker();
        let docclon: any = await this.documentosdata.insertAll(this.data, 1, 1);
        let oJSON = this.items.sort(function (a, b) {
            if (a.xmovilectrol > b.xmovilectrol)
                return -1;
        });
        let contx = 0;
        for await (let lineax of oJSON) {
            try {
                lineax.ItemDescription = lineax.Dscription;
                lineax.BaseLine = lineax.LineNum;
                lineax.LineNum = contx;
                let detalle = new Detalle();
                await detalle.insertSinc(lineax, docclon);
                contx++;
            } catch (e) {
                console.log(e);
            }
        }
        for (let i = 0; i < this.items.length; i++) {
            let x = parseInt(this.itemsax[i].Quantity);
            let y = parseInt(this.items[i].entregar);
            this.itemsax[i].Quantity = (x - y);
        }
        let contx2 = 0;
        for await (let linea of this.itemsax) {
            try {
                linea.ItemDescription = linea.Dscription;
                linea.BaseLine = linea.LineNum;
                linea.LineNum = contx2;
                let detalle = new Detalle();
                await detalle.updateSinc(linea, 1);
                contx2++;
            } catch (e) {
                console.log(e);
            }
        }
        await this.sumadorNumeracion();
        this.toast.show(`La entrega se procesó correctamente.`, '3000', 'top').subscribe(toast => {
        });
        this.navCrl.pop();
    }

    public controlCambia() {
        this.ctrol = 0;
        for (let im of this.items)
            this.ctrol += parseInt(im.entregar);
    }

    public changeCantidad(item: any) {
        this.xmovilectrol++;
        if (item.check) {
            item.entregar = item.Quantity;
            item.xmovilectrol = this.xmovilectrol;
        } else {
            item.entregar = 0;
        }
        this.controlCambia();
    }

    public async cambiarCantidad(item: any, index: number) {
        item.check = true;
        let totalx = parseFloat(item.Quantity);
        let alert: any = await this.alertController.create({
            header: 'INTRODUCIR LA CANTIDAD QUE DESEA ENTREGAR.',
            inputs: [{
                name: 'data',
                type: 'number',
                value: totalx,
                min: 1,
                max: totalx
            }],
            buttons: [{
                text: 'CANCELAR',
                role: 'cancel',
            }, {
                text: 'CONTINUAR',
                handler: (data: any) => {
                    this.xmovilectrol++;
                    if (data.data > 0 && data.data <= totalx) {
                        item.entregar = data.data;
                        item.xmovilectrol = this.xmovilectrol;
                        this.controlCambia();
                    } else {
                        this.toast.show(`El monto introducido es mayor al total verifíquelo.`, '3000', 'top').subscribe(toast => {
                        });
                    }
                }
            }]
        });
        await alert.present();
    }

    public async pagos() {
        let dataDoc: any = await this.documentosdata.find(this.idPedido);
        if (dataDoc.PayTermsGrpCode == -1 || dataDoc.PayTermsGrpCode == '-1') {
            this.exePago(dataDoc, dataDoc.DocTotal);
        } else {
            let alert: any = await this.alertController.create({
                header: 'INTRODUCIR LA CANTIDAD QUE DESEA CANCELAR',
                inputs: [{
                    name: 'data',
                    type: 'number',
                    min: 1,
                    max: dataDoc.DocTotal,
                    value: dataDoc.DocTotal,
                    placeholder: '0'
                }],
                buttons: [{
                    text: 'CANCELAR',
                    role: 'cancel',
                }, {
                    text: 'CONTINUAR',
                    handler: (data: any) => {
                        if (data.data > 0 && data.data <= dataDoc.DocTotal) {
                            this.exePago(dataDoc, data.data);
                        } else {
                            this.toast.show(`El monto introducido es mayor al total verifíquelo.`, '3000', 'top').subscribe(toast => {
                            });
                        }
                    }
                }]
            });
            await alert.present();
        }
    }

    private async exePago(dataDoc: any, pagoparcial: number) {
        let pedidosx: any = [];
        let data: any = [];
        dataDoc.DocTotalx = pagoparcial;
        dataDoc.check = true;
        pedidosx.push(dataDoc);
        let clientes = new Clientes();
        let clientearr: any = await clientes.find(dataDoc.CardCode);
        let inix: any = await this.configService.getNumeracion();
        let id: any = await this.configService.getSession();
        let num: any = JSON.parse(inix);
        let numerox: any = num.numgp += 1;
        let codPago = Calculo.generaCodeRecibo(id.idUsuario.toString(), numerox.toString(), '1');
        data.data = pedidosx;
        data.itemx = clientearr[0];
        data.cod = codPago;
        let obj: any = { component: ModalpagosPage, componentProps: data };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            if (data.data != false) {
                await this.sumadorrecibo();
            }
        });
        return await modal.present();
    }

    public async sumadorrecibo() {
        return new Promise(async (resolve, reject) => {
            let inix: any = await this.configService.getNumeracion();
            let ini: any = JSON.parse(inix);
            ini.numgp += 1;
            await this.configService.setNumeracion(JSON.stringify(ini));
            resolve(true);
        })
    }
}
