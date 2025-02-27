import { Component, OnInit } from '@angular/core';
import { Clientes } from "../../models/clientes";
import { ModalController, NavParams } from "@ionic/angular";
import { Toast } from "@ionic-native/toast/ngx";
import { DataService } from "../../services/data.service";
import { Detalle } from "../../models/detalle";
import { Documentos } from "../../models/documentos";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { ConfigService } from "../../models/config.service";
import { promocionaes } from '../../models/promociones';
import { Clientessucursales } from '../../models/clientessucursales';
import { DocumentService } from '../../services/documents.service';
import { Contactos } from '../../models/contactos';
import { Network } from "@ionic-native/network/ngx";

@Component({
    selector: 'app-modalcliente',
    templateUrl: './modalcliente.page.html',
    styleUrls: ['./modalcliente.page.scss'],
})
export class ModalclientePage implements OnInit {
    public items: any;
    public loadItem: boolean;
    public textPaso: boolean;
    public searchText: string;
    public statusline: boolean;
    public datatext: any;
    public userdata: any;
    public consolidador: string;
    public docidicaciones: string;
    modelPromo = new promocionaes();


    constructor(public modalController: ModalController, private toast: Toast, private documentService: DocumentService, public navParams: NavParams,
        private spinnerDialog: SpinnerDialog, private dataService: DataService, private configService: ConfigService,private network: Network) {
        this.textPaso = false;
        this.statusline = false;
        this.searchText = '';
        this.docidicaciones = '';
        this.datatext = navParams.data;
    }

    ngOnInit() {
        this.items = [];
        this.loadItem = false;
        this.listApp();
        this.docicficaciones();
    }

    public async docicficaciones() {
        if (this.datatext.tipo == 'DFA') {
            this.userdata = await this.configService.getSession();
            let docix: any = this.userdata[0].docificacion;
            let task_names = docix.map((task) => task.U_GrupoCliente);
            this.docidicaciones = task_names.toString();
            if (this.docidicaciones.trim() == ',') {
                this.docidicaciones = '0';
            }
        }
    }

    public async listApp() {
        let modelPromo = new promocionaes();
        if (this.items.length > 0) {
            this.textPaso = false;
            console.log("this.items. ", this.items)
            this.items.map(async function (item) {
                let promo = 0;
                let dataPromociones: any = await modelPromo.findCurrentAll(item.CardCode);

                if (dataPromociones.length > 0) {
                    promo = 1;
                }

                item.promo = promo;
                // item.DocumentTotalPay = item.DocumentTotalPay;
            });
            console.log("devd this.ventas before ", this.items);

        } else {
            this.textPaso = true;
        }
    }

    public async buscar(event: any) {
        this.searchText = event.detail.value;
        this.statusline = false;
        this.items = [];
        this.loadItem = true;
        let search = event.detail.value;
        let model = new Clientes();
        console.log("dosificacion ", this.docidicaciones);

        this.items = await model.findSearch(search, this.docidicaciones);
        console.log("Lista de clientes", this.items);
        this.loadItem = false;
        this.listApp();
    }

    public cerrar() {
        this.modalController.dismiss(false);
    }

    public async buscarOnline() {
        this.statusline = true;
        this.loadItem = true;
        try {
            this.items = [];
            let resp: any = await this.dataService.getClientesAction(this.searchText);
            if (resp.error && resp.error == 201) {
                this.toast.show(`No se encontraron resultados.`, '4000', 'center').subscribe(toast => {
                });
                this.loadItem = false;
                this.items = [];
            } else {

                this.items = resp;

                this.loadItem = false;
            }
        } catch (e) {
            console.log(e);
            this.loadItem = false;
            this.toast.show(`Ocurrió un problema interno inténtalo nueva mente.`, '4000', 'bottom').subscribe(toast => {
            });
        }
    }

    public async findItem(item: any) {
        console.log("findItem.. () ", item)
        let clientx = [];
        console.log("findItem..", this.statusline);

        if (item.activo == "N") {
            return this.toast.show(`El cliente no está activo. Contactese con el Administrador en SAP`, '4000', 'bottom').subscribe(toast => {
            });
        }
        /* let resp: string = '';
         let cod = new Codigocontrol();
         resp = cod.calcularCUF('123456789','20190113163721231','0','1','1','1','1','1','0 ','A19E23EF34124CD').toString();
         console.log("cuf",resp);*/


        // if (this.statusline == true) {
        try {
            this.spinnerDialog.show();
            clientx.push(item);
            let xclientx = {
                data: JSON.stringify({
                    "estado": 200,
                    "respuesta": clientx,
                    "mensaje": "OK"

                })
            };
            let clientes: any = new Clientes();
            let sucursales: any = new Clientessucursales();
            let validExist = [];
            validExist = await clientes.find(item.CardCode);
            console.log("validExist ", validExist);

            if (validExist.length == 0) {
                    await clientes.insertAll(xclientx, 0, 1);
                    await sucursales.insertAll2(item.sucursales, 1,1)
            }else{

                if (this.network.type != 'none') {
                    try {
                        let resp: any = await this.dataService.getClientesAction(item.CardCode);
                        if (resp.error && resp.error == 201) {
                            console.log("ERROR AL BUSCAR DEUDA DEL CLIENTE");
                        }else{
                            console.log("DATOS CLIENTES",resp[0]);
                            await clientes.updateDataSapClient(resp);
                        }
                    } catch (error) {
                        this.toast.show(`No se pudo descargar la informacion del cliente`, '5000', 'center').subscribe(toast => {
                        });
                    }

                }else{
                    this.toast.show(`Sin conexión, no se pudo descargar los documentos asociados al cliente`, '5000', 'center').subscribe(toast => {
                    });
                }
            }


            try {
                // await this.insertDocumentosPen(item.CardCode)
                await this.documentService.downloadDeudasCliente(item.CardCode)
            } catch (error) {
                console.log("OCURRIO UN ERROR ");

            }
            if (this.datatext.uso == '0') {
                // await this.cosultadaotclientesap(item.CardCode);
                // this.spinnerDialog.hide();
                this.toast.show(`Descargando Sucursales del Cliente`, '3000', 'center').subscribe(toast => {
                });
                this.spinnerDialog.show();
                await this.cosultasucursalsap(item.CardCode);
                this.spinnerDialog.hide();


                this.toast.show(`Descargando Contactos del Cliente`, '3000', 'center').subscribe(toast => {
                });
                this.spinnerDialog.show();
                await this.cosultacontactossap(item.CardCode);
                this.spinnerDialog.hide();
            }
            this.modalController.dismiss(item);
        } catch (e) {
            console.log(e);
            this.spinnerDialog.hide();
        }
        // } else {
        //     console.log("datos que se le pasaron", this.datatext.uso);
        //     if (this.datatext.uso == '0') {

        //         this.toast.show(`Descargando Documentos del Cliente`, '3000', 'center').subscribe(toast => {
        //         });
        //         this.spinnerDialog.show();
        //         await this.cosultadaotclientesap(item.CardCode);
        //         this.spinnerDialog.hide();
        //     }

        //     this.modalController.dismiss(item);
        // }
    }
    // public async cosultadaotclientesap(CardCode) {
    //     try {
    //         let data = {
    //             "codigo": CardCode
    //         }
    //         let xData: any = await this.dataService.Consultasaldoclientesap(data);
    //         let xJson = JSON.parse(xData.data);
    //         console.log("respuesta Consultasaldoclientesap", xJson.respuesta[0]);
    //         let clientes: any = new Clientes();
    //         await clientes.updateDataSapClient(xJson.respuesta);
    //         let dataDocsClient: any = await this.dataService.servisDownloadDocsSap(CardCode);
    //         console.log("dataDocsClient ", dataDocsClient);
    //         let xJsondocs = JSON.parse(dataDocsClient.data);
    //         //console.log("sincronizacion prod");
    //         console.log("xJson.respuesta", xJsondocs);
    //         let documentos: any = new Documentos()
    //         if (xJsondocs.respuesta) {
    //             for await (let documento of xJsondocs.respuesta) {
    //                 let dataInsert: any = [];
    //                 let newData: any = {};
    //                 let idLast = 0;
    //                 newData.origen = 'outer';
    //                 newData.clone = '0';
    //                 newData.cod = documento.DocNum;
    //                 newData.DocEntry = documento.DocEntry;
    //                 newData.DocNum = documento.DocNum;
    //                 newData.DocType = documento.DocType;
    //                 newData.DocDate = documento.DocDate;
    //                 newData.DocDueDate = documento.DocDueDate;
    //                 newData.CardCode = documento.CardCode;
    //                 newData.CardName = documento.CardName;
    //                 newData.DocTotal = documento.DocTotal;
    //                 newData.PaidtoDate = documento.PaidtoDate;

    //                 newData.DateUpdate = documento.DateUpdate;
    //                 newData.U_4NIT = "";
    //                 newData.U_4RAZON_SOCIAL = "";
    //                 newData.PriceListNum = documento.PriceListNum;

    //                 newData.DateUpdate = documento.DateUpdate;
    //                 newData.descuento = documento.descuento;

    //                 newData.tipodescuento = 0;

    //                 newData.ReserveInvoice = documento.ReserveInvoice;
    //                 newData.Saldo = documento.Saldo;
    //                 newData.Pendiente = documento.Pendiente;
    //                 newData.centrocosto = documento.centrocosto;
    //                 newData.unidadnegocio = documento.unidadnegocio;
    //                 newData.reimpresiones = '0';
    //                 newData.U_XMB_AUX1 = documento.U_XMB_AUX1;
    //                 newData.grupoproductodocificacion = documento.grupoproductodocificacion;
    //                 newData.DocEntry = documento.DocEntry;
    //                 dataInsert.push(newData);
    //                 let rx: any = await documentos.existeDocumento(newData.cod);
    //                 console.log("rx existe? ", rx)
    //                 if (rx[0].exits > 0) {

    //                     let returnD = await documentos.deleteDocumento(newData.cod);
    //                     console.log("return ", returnD)
    //                 }
    //                 idLast = await documentos.insertAllSap(dataInsert[0]);
    //             }
    //             this.spinnerDialog.hide();
    //         } else {
    //             console.log("cliente no encontrado en sap")
    //             this.spinnerDialog.hide();
    //         }



    //     } catch (error) {
    //         this.spinnerDialog.hide();
    //         this.toast.show(`Error en el servidor.`, '2500', 'center').subscribe(toast => {
    //         });
    //     }
    // }


    public async cosultasucursalsap(CardCode) {
        try {
            let data = {
                "codigo": CardCode
            }
            let xData: any = await this.dataService.Consultasucursalsap(data);
            let xJson = JSON.parse(xData.data);
            console.log("respuesta Consultasucursalsap", xJson.respuesta);
            let Clientessu: any = new Clientessucursales();
            await Clientessu.insertRegister(xJson.respuesta, CardCode);

        } catch (error) {
            this.spinnerDialog.hide();
            this.toast.show(`Error en el servidor.`, '2500', 'center').subscribe(toast => {
            });
        }
    }

    public async cosultacontactossap(CardCode) {
        try {
            let data = {
                "codigo": CardCode
            }
            let xData: any = await this.dataService.Cosultacontactossap(data);
            let xJson = JSON.parse(xData.data);
            console.log("respuesta Cosultacontactossap", xJson.respuesta);
            let contacto: any = new Contactos();
            await contacto.delete(CardCode);
            await contacto.insertRegister(xJson.respuesta, CardCode);

        } catch (error) {
            this.spinnerDialog.hide();
            this.toast.show(`Error en el servidor.`, '2500', 'center').subscribe(toast => {
            });
        }
    }

    public async descargaPedidos(CardCode: any) {
        return new Promise(async (resolve, reject) => {
            try {
                let documentosImport: any = await this.dataService.getClientesDocument(CardCode);
                if (documentosImport.estado == 201) {
                    this.toast.show(`El cliente no tiene facturas para importar.`, '4000', 'bottom').subscribe(toast => {
                    });
                    resolve(true);
                }
                for await (let documento of documentosImport) {
                    let idLast = 0;
                    documento.factura.origen = 'outer';
                    documento.factura.clone = '0';
                    documento.factura.cod = documento.factura.DocEntry;
                    let documentos: any = new Documentos();
                    let rx: any = await documentos.existeDocumento(documento.factura.DocNum);
                    if (rx[0].exits == 0) {
                        idLast = await documentos.insertAll(documento.factura, 1, 1);
                        if (documento.facturasproductos.length > 0) {
                            for await (let linea of documento.facturasproductos) {
                                try {
                                    let detalle = new Detalle();
                                    await detalle.insertSinc(linea, idLast);
                                } catch (e) {
                                    console.log(e);
                                }
                            }
                        }
                    }
                }
                resolve(true);
            } catch (e) {
                reject(false);
            }
        });
    }

}
