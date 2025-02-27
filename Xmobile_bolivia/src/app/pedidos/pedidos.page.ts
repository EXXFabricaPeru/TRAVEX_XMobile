import { Component, ComponentFactoryResolver, OnInit, ViewChild } from '@angular/core';
import { MenuController, ModalController, NavController } from "@ionic/angular";
import { ConfigService } from "../models/config.service";
import { Documentos } from "../models/documentos";
import { ModalpagosPage } from "../public/modalpagos/modalpagos.page";
import { ActivatedRoute, Router } from "@angular/router";
import { Toast } from "@ionic-native/toast/ngx";
import * as moment from 'moment';
import { NativeStorage } from "@ionic-native/native-storage/ngx";
import { Dialogs } from "@ionic-native/dialogs/ngx";
import 'lodash';
import { Detalle } from "../models/detalle";
import { SpinnerDialog } from "@ionic-native/spinner-dialog/ngx";
import { Bonificaciones as Bonificacion_ca } from '../models/V2/bonificaciones';
import { Geolocalizacion } from '../models/geolocalizacion';
import { DataService } from '../services/data.service';
import { GlobalConstants } from "../../global";
import { Clientes } from '../models/clientes';
import { Clientessucursales } from '../models//clientessucursales';

declare var _: any;

@Component({
    selector: 'app-pedidos',
    templateUrl: './pedidos.page.html',
    styleUrls: ['./pedidos.page.scss'],
})
export class PedidosPage implements OnInit {
    public Bonificacion_ca = new Bonificacion_ca;
    public ventas: any;
    public tipo: string;
    public tituloTipo: string;
    public minPiker: string;
    public documentosdata: Documentos;
    public load: boolean;
    public cantidadDoc: boolean;
    public searchData: string;
    public fechaHoy: string;
    public origen: string;
    private tc: any;
    public userdata: any;
    public searchDataAux: string;
    fromMarker: boolean = false;
    @ViewChild('datePicker') datePicker;

    constructor(private navCrl: NavController, public modalController: ModalController, private toast: Toast, private dialogs: Dialogs,
        private spinnerDialog: SpinnerDialog, private menu: MenuController, public dataservis: DataService,
        private activatedRoute: ActivatedRoute, private configService: ConfigService, private nativeStorage: NativeStorage) {
        this.documentosdata = new Documentos();
        this.tituloTipo = '';
        this.origen = 'inner';
        this.ventas = [];
        this.load = false;
        this.cantidadDoc = false;
        this.fechaHoy = this.documentosdata.getFechaView();
    }

    public async ngOnInit() {

        let geomodel = new Geolocalizacion();
        console.log("geomodel ", await geomodel.select());

        this.menu.enable(true, 'menuxmobile');
        this.menu.close('menuxmobile');
        this.userdata = await this.configService.getSession();
        this.run();
    }

    public async run(tox = '') {
        console.log(" tox ", tox);
        console.log("DEVD this.activatedRoute.snapshot.paramMap.get('id') ", this.activatedRoute.snapshot.paramMap.get('id'));
        (tox == '') ? this.tipo = this.activatedRoute.snapshot.paramMap.get('id') : this.tipo = tox;
        if (this.tipo == 'DOE') this.origen = 'inner';
        this.minPiker = this.documentosdata.getFechaPicker();
        await this.configService.setTipo(this.tipo);
        this.tipoDocumento();
    }

    public async ionViewWillEnter() {

        console.log("ionViewWillEnter");
        let documentosdata = new Documentos();
        //console.log("ClienteTodos ", await documentosdata.ClienteTodos());

        this.loadOnError();
        setTimeout(() => {
            console.log("ionViewWillEnter 2 ");
            if (this.ventas.length == 0) {
                this.loadOnError();
            }
        }, 2000)


    }

    async loadOnError() {
        this.searchData = "";
        let verImportadosMarker = "NO";
        let cardCodeMarker = "";
        try {
            verImportadosMarker = localStorage.getItem("verImportadosMarker");
            cardCodeMarker = localStorage.getItem("cardCodeMarker");

        } catch (error) {
            console.error("error get localstorage marker ", error);
        }
        console.log("DEVD verImportadosMarker ", verImportadosMarker);
        console.log("DEVD cardCodeMarker ", cardCodeMarker);
        if (verImportadosMarker == "SI") {

            setTimeout(() => {
                localStorage.setItem("verImportadosMarker", "SI");
                localStorage.setItem("cardCodeMarker", cardCodeMarker);
            }, 2000)
            try {
                let tox: any = await this.configService.getDocSC();
                this.tipo = tox;
                this.toast.show(`Clonando documento...`, '1000', 'center').subscribe(toast => {
                });
                this.tipoDocumento();
                await this.navCrl.navigateRoot(`/pedidos/${tox}`);
                await this.configService.removeDocSC();
                let docx: any = await this.documentosdata.findAllCloneUltimo(tox);
                await this.detalleItem(docx[0]);
            } catch (e) {
                if(this.tipo == 'DFA' && GlobalConstants.Facturaruta == 1){
                    this.origen = 'inner';
                }else{
                    this.origen = 'outer';
                }

                this.load = true;
                this.ventas = [];

                console.log("this.activatedRoute.snapshot.paramMap ", this.activatedRoute.snapshot.paramMap);
                this.tipo = this.activatedRoute.snapshot.paramMap.get('id');
                
                console.log("DEVD  this.tipo ", this.tipo);
                this.tipoDocumento();
                this.ventas = await this.documentosdata.documentos('' + cardCodeMarker, this.tipo, this.origen);
                console.log(" this.ventas  ", this.ventas);

                this.fromMarker = true;
                localStorage.removeItem('verImportadosMarker');
                localStorage.removeItem('cardCodeMarker'); // 1004100009

                if (this.ventas.length == 0)
                    this.cantidadDoc = true;
                if (this.ventas.length > 0)
                    this.cantidadDoc = false;
                this.load = false;
                let documentosdata = new Documentos();
                this.ventas.map(async function (item) {
                    let facturacode = 0;
                    let ver: any = await documentosdata.findFacturaFromPedido(item.cod);
                    console.log("ver ", ver)
                    if (ver.length > 0) {
                        facturacode = ver[0].codFacturado;
                    }

                    item.facturacode = facturacode;
                    // item.DocumentTotalPay = item.DocumentTotalPay;
                });
                console.log("devd this.ventas before ", this.ventas);
            }


        } else {
            console.log("DEVD listar docs sin marker");

            try {
                let tox: any = await this.configService.getDocSC();
                this.tipo = tox;
                this.toast.show(`Clonando documento...`, '1000', 'center').subscribe(toast => {
                });
                this.tipoDocumento();
                await this.navCrl.navigateRoot(`/pedidos/${tox}`);
                await this.configService.removeDocSC();
                let docx: any = await this.documentosdata.findAllCloneUltimo(tox);
                await this.detalleItem(docx[0]);
            } catch (e) {
                console.log(e);
                this.listar();
            }
        }

    }

    public filtroInnerOuter() {
        if (this.origen == 'inner') {
            this.origen = 'outer';
            this.toast.show(`Documentos importados .`, '2000', 'center').subscribe(toast => {
            });
        } else {
            this.origen = 'inner';
            this.toast.show(`Documentos locales .`, '2000', 'center').subscribe(toast => {
            });
        }
        this.listar();
    }

    public doRefresh(event) {
        setTimeout(() => {
            this.listar();
            event.target.complete();
        }, 200);
    }

    public tipoDocumento() {
        let tituloTipo = "";
        switch (this.tipo) {
            case ('DOF'):
                tituloTipo = 'COTIZACION';
                break;
            case ('DOP'):
                tituloTipo = 'PEDIDOS';
                break;
            case ('DFA'):
                tituloTipo = 'FACTURAS';
                break;
            case ('DOE'):
                tituloTipo = 'ENTREGAS';
                break;
        }
        this.tituloTipo = tituloTipo;
    }

    public async searchCalendar() {
        this.searchData = this.searchDataAux;
        this.datePicker.open();
    }

    public async listar(search = '') {
        console.log("listar()");
        console.log("this.tipo ", this.tipo);
        console.log("this.origen ", this.origen);
        console.log("listar()");

        try {
            this.load = true;
            this.ventas = [];
            if (search == '') {
                this.ventas = await this.documentosdata.documentos('', this.tipo, this.origen);
            } else {
                this.ventas = await this.documentosdata.documentos(search, this.tipo, this.origen);
            }
            console.log("this.ventas  ", this.ventas);
            if (this.ventas.length == 0)
                this.cantidadDoc = true;
            if (this.ventas.length > 0)
                this.cantidadDoc = false;
            this.load = false;
        } catch (e) {
            this.load = false;
        }
        let responseStatus: any = []
        try {
            responseStatus = await this.dataservis.servisStatesDocuments()
            console.log("from view , ", responseStatus);
        } catch (error) {
            console.log("SERVICIO NO DISPONIBLE ", error);

        }


        console.log("devd this.ventas  ", this.ventas);
        let documentosdata = new Documentos();
        let xtipoDocNext: string = "";
        this.ventas.map(async function (item) {
            let facturacode: any = 0;
            let ver: any = await documentosdata.findFacturaFromPedido(item.cod);
            console.log("ver ----> ", ver)
            if (ver.length > 0) {
                facturacode = ver[0].codFacturado;
                const xTipo = facturacode.toString().substring(0,3);
                console.log("xtipo ",xTipo);                
                if(xTipo == "DOE") {                 
                    xtipoDocNext = "Guia: ";
                }
                else {
                    xtipoDocNext = "Factura: ";
                }
            }
            const found = responseStatus.find(element => element.idDocPedido == item.cod);

            found ? item.status = found.descripcion : item.status = "MOV"
            console.log(item);
            
            if (item.estado == '3') {
                item.statusColor = "#17CF15"
            }
            if (item.estado == '2') {
                item.statusColor = "#ffc727"
            }
            if (item.estado == '1') {
                item.statusColor = "#8E8E89"
            }
            //anulacion de documentos estado send 7
            if (item.estado == '4' || item.canceled=='3') {
                item.statusColor = "#FB3925"
            }
            
            if(facturacode!=0)
                item.facturacode = xtipoDocNext + facturacode;
            else
                item.facturacode = facturacode;

            // item.DocumentTotalPay = item.DocumentTotalPay;
        });
        console.log("devd this.ventas before ", this.ventas);
    }

    verFactura = (facturacode) => {
        console.log("facturacode ", facturacode);
    }

    public accionFecha(event: any) {
        let resp: any = moment(event.detail.value).format('YYYY-MM-DD');
        this.ventas = [];
        this.searchData = resp;
        this.buscar(null);
    }

    public async buscar(event: any) {
        this.ventas = [];
        if (event != null) {
            this.searchData = event.detail.value;
            this.searchDataAux = event.detail.value;
        }
        this.listar(this.searchData);
    }

    public pageSincronizar() {
        this.navCrl.navigateForward(`sincronizacion`);
    }

    public async exepedido(id: any) {
        try {
            await this.nativeStorage.getItem(moment().format('YYYY-MM-DD'));
            this.crearPedido(id);
        } catch (e) {
            this.dialogs.confirm("La sincronización diaria es obligatoria si no está  " +
                "actualizada puede cometer faltas en las transacciones \n\n " +
                "Desea sincronizar? ", "Xmobile.", ["SI", "NO"]).then((data) => {
                    switch (data) {
                        case (1):
                            this.pageSincronizar();
                            break;
                        case (2):
                            this.crearPedido(id);
                            break;
                    }
                }).catch((e) => {
                    console.log(e);
                });
            return false;
        }
    }

    public eliminadoc(data: any) {
        console.log("eliminadoc() ", data);
        return new Promise(async (resolve, reject) => {
            this.spinnerDialog.show(null, null, true);
            try {
                let detalles = new Detalle();
                let items: any = await detalles.showAllTable(data.cod);
                console.log("items del pedido a eliminar ", items);
                for await (let item of items)
                    await detalles.eliminar(item.id, data.DocType, data.Reserve);
                this.documentosdata.deletedoc(data.id);
                await detalles.eliminargrupo(data.cod);
                this.spinnerDialog.hide();
                resolve(true);
            } catch (e) {
                this.spinnerDialog.hide();
                reject(false);
            }
        });
    }

    public async crearexe(id: any) {
        console.log("INICIO -------> CREACION DE DOCUMENTOS");
        GlobalConstants.tipeDoc = 'N';
        GlobalConstants.numitems = 0;
        console.log("reiniciado1");
        GlobalConstants.CabeceraDoc = [];
        GlobalConstants.DetalleDoc = [];
        GlobalConstants.auxiliarcloncabeceras = '';
        GlobalConstants.auxiliarclondetalle = '';
        
        console.log(this.userdata[0])

        if (this.tipo == 'DFA') {
            if (this.userdata[0].docificacion[0].fex_offline == 1 && this.userdata[0].docificacion[0].U_NumeroAutorizacion == null) {
                this.toast.show(`No se encontro numero de Autorizacion para crear facturas.`, '2500', 'center').subscribe(toast => { });
                return false;
            }
        }
        this.crearPedido(id);
        localStorage.setItem("stockBoni", "0");


        /* let rxd: any = await this.documentosdata.documentospen();
         console.log("crearexe () rxd ", rxd);
         if (rxd.length > 0) {
             let rxx = rxd[0];
             let docx = '';
             switch (rxx.DocType) {
                 case ('DOF'):
                     docx = 'COTIZACION';
                     break;
                 case ('DOP'):
                     docx = 'PEDIDO';
                     break;
                 case ('DOE'):
                     docx = 'ENTREGA';
                     break;
                 case ('DFA'):
                     docx = 'FACTURA';
                     break;
             }
             this.dialogs.confirm(`Imposible de crear tiene un documento de ${docx} en memoria.`, "Xmobile.", ["Eliminar y continuar", "Cancelar"]).then(async (data) => {
                 switch (data) {
                     case (1):
                         let ra: any = await this.eliminadoc(rxd[0]);
                         console.log("retrn delete ra ", ra);
                         if (ra == true) {
                             this.crearPedido(id);
                         } else {
                             this.toast.show(`Ocurrió un error al eliminar el documento.`, '2500', 'center').subscribe(toast => {
                             });
                         }
                         break;
                 }
             }).catch(async (e) => {
                 return false;
             });
             return false;
         } else {
             if (this.tipo == 'DFA') {
                 if (this.userdata[0].docificacion[0].fex_offline == 1 && this.userdata[0].docificacion[0].U_NumeroAutorizacion == null) {
                     this.toast.show(`No se encontro numero de Autorizacion para crear facturas.`, '2500', 'center').subscribe(toast => { });
                     return false;
                 }
             }
             this.crearPedido(id);
             localStorage.setItem("stockBoni", "0");
         }*/

    }

    public async crearPedido(id: any) {
        console.log("this.tipo", this.tipo);
        console.log("crearPedido id ", id);
        console.log("this.userdata[0] ", this.userdata[0]);
        switch (this.tipo) {
            case ('DOF'):
                if (this.userdata[0].config[0].permisoOferta == '0') {
                    this.toast.show(`No está permitido para crear ofertas .`, '2500', 'center').subscribe(toast => {
                    });
                    return false;
                }
                break;
            case ('DOP'):
                if (this.userdata[0].config[0].permisoPedido == '0') {
                    this.toast.show(`No está permitido para crear pedidos .`, '2500', 'center').subscribe(toast => {
                    });
                    return false;
                }
                break;
            case ('DOE'):
                if (this.userdata[0].config[0].permisoEntrega == '0') {
                    this.toast.show(`No está permitido para realizar entregas .`, '2500', 'center').subscribe(toast => {
                    });
                    return false;
                }
                break;
            case ('DFA'):
                if (this.userdata[0].config[0].permisoFactura == '0') {
                    this.toast.show(`No está permitido para facturar .`, '2500', 'center').subscribe(toast => {
                    });
                    return false;
                }
                break;
        }

        /* VERIFICA SI ES FACTURA Y VALIDA LA DOSIFICACION*/
        if (this.tipo == 'DFA') {
            console.log("this.userdata[0]. ", this.userdata[0]);
            let conx = 0;
            for (let doci of this.userdata[0].docificacion) {
                if (doci.U_FechaLimiteEmision >= moment().format('YYYY-MM-DD')) {
                    conx++;
                }
            }
            if (conx == 0) {
                this.toast.show(`La fecha de dosificación caducada sincronice nuevamente.`, '4000', 'center').subscribe(toast => {
                });
                return false;
            }
            console.log("this.userdata[0].cuentascontables. ", this.userdata[0].cuentascontables);
            console.log("this.userdata[0].cuentascontables.length  ", this.userdata[0].cuentascontables.length);
        }

        let tiposcambio = [];
        let arrcambio = [];
        try {
            tiposcambio = this.userdata[0].tiposcambio.filter((n) => {
                return n.ExchangeRate > 1;
            });
            for (let cambio of tiposcambio) {
                arrcambio.push({
                    ExchangeRate: cambio.ExchangeRate,
                    ExchangeRateDate: cambio.ExchangeRateDate,
                    ExchangeRateFrom: String(cambio.ExchangeRateFrom),
                    ExchangeRateTo: String(cambio.ExchangeRateTo)
                });
            }

            if (typeof this.userdata[0].tipocambioparalelo != "undefined") {
                this.tc = parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio);
                arrcambio.push({
                    ExchangeRate: parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio),
                    ExchangeRateDate: this.userdata[0].tipocambioparalelo.fecha,
                    ExchangeRateFrom: this.userdata[0].tipocambioparalelo.from,
                    ExchangeRateTo: this.userdata[0].tipocambioparalelo.to
                });
            }

            if (arrcambio.length > 0) {
                console.log("`pedido/${id}/${this.tipo}/0` ", `pedido/${id}/${this.tipo}/0`);
                this.navCrl.navigateForward(`pedido/${id}/${this.tipo}/0`);
                if (this.userdata[0].cuentascontables.length == 0) {
                    this.toast.show(`No tiene cuentas contables para realizar pagos a facturas.`, '8000', 'center').subscribe(toast => {
                    });
                }
            }
        } catch (e) {
            this.toast.show(`No tienes tipo de cambio a asignado sincronice e intente nuevamente.`, '4000', 'center').subscribe(toast => {
            });
        }
    }

    public async detalleItem(item: any) {
        if (GlobalConstants.Clon == 0) {
            this.spinnerDialog.show('', 'Eliminando variables locales', true);
            for (let index = 0; index < 10; index++) {
                delete GlobalConstants.CabeceraDoc;
                GlobalConstants.CabeceraDoc = [];
                delete GlobalConstants.DetalleDoc;
                GlobalConstants.DetalleDoc = [];
                
                GlobalConstants.auxiliarcloncabeceras = '';
                GlobalConstants.auxiliarclondetalle = '';

                if(GlobalConstants.auxiliarcloncabeceras != ''){
                    index = 0;
                }else{
                    console.log("variables limpias");
                }
                if(GlobalConstants.auxiliarclondetalle != ''){
                    index = 0;
                }else{
                    console.log("variables limpias");
                }
            }
            this.spinnerDialog.hide();
        }
        GlobalConstants.auxiliarcloncabeceras = '';
        GlobalConstants.auxiliarclondetalle = '';
        GlobalConstants.numeropago = 0;
        if(!GlobalConstants.DetalleDoc.length){
            GlobalConstants.numitems = 0;
        }
        console.log("reiniciado2",GlobalConstants.CabeceraDoc);
        GlobalConstants.tipeDoc = '';
        console.log(GlobalConstants.CabeceraDoc.length);

        if(GlobalConstants.CabeceraDoc.length == 1){
            item = GlobalConstants.CabeceraDoc[0];
        }


        console.log("item ", item);
        let doc = new Documentos;
        let resp = await doc.consultaEnvioEvidencia(item.DocNum);

        console.log("DATOS DEL DOCUMENTO ",resp);

        let detall = new Detalle;
        let auxdeta = await detall.findAll5(item.DocNum);

        console.log("DATOS DEL DETALLE ",auxdeta);


        let respuesta = await this.buscaclienteimportado(item.CardCode);
        //let respuesta =1;
        if(respuesta == 1){
            if (item.PriceListNum == "")
                item.PriceListNum = "1";
            this.navCrl.navigateForward(`pedido/${item.cod}/${item.DocType}/0`);
        }else{
            this.toast.show(`No se ha podido cargar los datos del cliente.`, '4000', 'center').subscribe(toast => {
            });
        }
    }

    public async pagos(item: any) {
        let obj: any = { component: ModalpagosPage, componentProps: item };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then((respuesta: any) => {
            this.listar();
        });
        return await modal.present();
    }

    public async buscaclienteimportado(code){
        this.spinnerDialog.show(null, null, true);
        let clientes = new Clientes();
        let aux = await clientes.findcount(code);
        let resp = 1;
        if(aux[0].cantidad > 0){    
            console.log("CLIENTE EXISTE");
            resp = 1;
        }else{
            try {
                let resp: any = await this.dataservis.getClientesAction(code);

                let xclientx = {
                    data: JSON.stringify({
                        "estado": 200,
                        "respuesta": resp,
                        "mensaje": "OK"
    
                    })
                };
                // JSON.parse(xData.data).respuesta);
    
                let clientes: any = new Clientes();
                let sucursales: any = new Clientessucursales();
                let validExist = [];
                validExist = await clientes.find(code);
                if (validExist.length == 0) {
                    await clientes.insertAll(xclientx, 0, 1);
                    await sucursales.insertAll2(resp[0].sucursales, 1,1)
                }

            } catch (error) {
                resp = 0;
            }
        }
        this.spinnerDialog.hide();
        return resp;
    }

    cerrar() {
        //this.navCrl.pop();
        this.navCrl.navigateBack('ruta');
    }
    
    eventTest = () => {
        alert("asfa");
    }
}
