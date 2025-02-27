import { Component, OnInit } from '@angular/core';
import { AlertController, LoadingController, ModalController, NavParams } from "@ionic/angular";
import { Toast } from "@ionic-native/toast/ngx";
import { ConfigService } from "../../models/config.service";
import { WheelSelector } from "@ionic-native/wheel-selector/ngx";
import { Pagos } from "../../models/V2/pagos";
import { Clientes } from "../../models/clientes";
import { ReportService } from "../../services/report.service";
import { Tiempo } from "../../models/tiempo";
import { Reimpresion } from "../../models/reimpresion";
import { Dialogs } from "@ionic-native/dialogs/ngx";
import { Documentopago } from "../../models/documentopago";
import * as moment from 'moment';
import { DataService } from "../../services/data.service";
import { FacturasPagos } from '../../models/facturasPagos';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { PagosService } from '../../services/pagos.service';
import { IDataPagoPdf, IPagos } from '../../types/IPagos';
import { ICliente } from 'src/app/types/IClientes';
import { ReportStyleUnoService } from 'src/app/services/report-style-uno.service';

@Component({
    selector: 'app-modalpagos',
    templateUrl: './modalpagos.page.html',
    styleUrls: ['./modalpagos.page.scss'],
})
export class ModalpagosPage implements OnInit {
    public item: any;
    public cliente: any;
    public pagos: any = [];
    public userdata: any;
    public monedaUser: any;
    public mipago: any;
    coddoc: any = ""
    cabecera = [];
    dataPagos: IPagos;
    dataCliente: ICliente[];
    private reportService: any;
    constructor(public modalController: ModalController, private configService: ConfigService, private spinnerDialog: SpinnerDialog, public pagosService: PagosService, private _reportService: ReportService,
        public navParams: NavParams, private toast: Toast, public alertController: AlertController, public dataservis: DataService, private dialogs: Dialogs,
        public loadingCtrl: LoadingController,private _reportStyleUno:ReportStyleUnoService
    ) {
        this.item = navParams.data;
        console.log(" this.item ", this.item);
        delete this.item.modal;
        this.dataPagos = { ...this.item };
        console.log(" this.dataPagos  ", this.dataPagos);

        this.cliente = [];
        // this.pagos = [];
        this.userdata = [];
    }

    async ngOnInit() {
        this.userdata = await this.configService.getSession();
        console.log("this.userdata ", this.userdata)
        this.monedaUser = this.userdata[0].config[0].moneda;
        let clientes = new Clientes();
        this.cliente = await clientes.find(this.dataPagos.cliente_carcode);

        console.log("data cliente", this.cliente);
        this.dataCliente = [...this.cliente];
        let pagos = new Pagos();
        let $pago = await pagos.consultapago(this.item.id);
        console.log("this.item.codigo ", $pago[0]);
        this.item = $pago[0];
        let styleLayaut = this.userdata[0].layautConfig ? this.userdata[0].layautConfig : null;
        console.log("  this.userdata 2 ", styleLayaut);
        
        switch (Number(styleLayaut))
        {
            case 0:
                this.reportService = this._reportService;
                break;
            case 1:
           console.log("  this.userdata 3 ", styleLayaut);

                this.reportService = this._reportStyleUno;
                break;
            case 2:
            
                break;
            default:
                this.reportService = this._reportService;
                break;
        }

        //let cabecera=await pagos.consultacabecera(this.item.nro_recibo);
        //let medios=await pagos.consultaMedios(this.item.nro_recibo);
        // let facturas=await pagos.consultaFacturas(this.item.nro_recibo);
        /*
      
        if (this.item.dx == 'cuenta' || this.item.dx == 'factura') {
            this.pagos = await pagos.findAllPagosCuenta(this.item.codigo);
            console.log("findAllPagosUNO ", await pagos.findAllPagosUNO());

        } else {
            console.log("new logic ");
            // this.pagos = await pagos.findAllPagos(this.item.codigo);

            this.pagos = await pagos.selectOne(this.item.codigo);
            console.log(" selectAllF ", await pagos.selectAllF())
        }
        if (this.pagos.length == 0) {
            return this.toast.show(`Ocurrió un problema, no se encontraron pagos o documentos que pertenecen al recibo.`, '4000', 'top').subscribe(toast => {
            });
        }

        console.log("this.pagos ", this.pagos);

        console.log("selectAll facturas  ", await pagos.selectAllF());
        */
    }

    cancelPay = async () => {
        const loadingCtrl = await this.loadingCtrl.create({ message: "Anulando...!" });
        console.log("this.item ", this.item);

        if (this.item.cancelado > 0) {
            return this.toast.show(`El documento de pago ya fue anulado.`, '4000', 'top').subscribe(toast => {
            });
        }
        let fecha = moment().format('YYYY-MM-DD');
        console.log("fecha ", fecha)
        console.log("fecha documento ", this.item.fecha);
        console.log("Listo para cancelar ", this.item.fecha);

        //if (this.item.fecha === fecha) {
        const confirmAlert = await this.alertController.create({
            cssClass: "custom-alert",
            header: "Mensaje",
            message: "<p>¿Esta seguro de anular el pago.?</p>",
            buttons: [
                {
                    text: "Cancelar",
                    role: "cancel",
                    cssClass: "secondary",
                    handler: async (blah) => {
                        console.log(" Cancel: blah");
                        return false;
                    },
                },
                {
                    text: "Anular",

                    handler: async (blah) => {
                        console.log("Confirm Okay ", blah);
                        try {

                            loadingCtrl.present();
                            let response: any = await this.pagosService.cancelPayService(this.dataPagos);
                            let pagos:Pagos = new Pagos();
                            //respose estado 0 = sin errores todo ok. estado 1= con errores
                            if (response && response.estado == 3) {
                                console.log(this.dataPagos);
                                let updatePago = await pagos.updatePayCanceled(this.dataPagos.nro_recibo);
                                let updatePag =  await pagos.updateSaldoFacturasAnula(this.dataPagos);
                            }

                            console.log("response ", response);
                            this.toast.show("" + response.mensaje ? response.mensaje : 'Error de conexión', "4000", "center").subscribe();
                            console.log({ response });
                            loadingCtrl.dismiss();


                        } catch (error) {
                            console.log("error ", error)
                            loadingCtrl.dismiss();
                            this.toast.show(`Error inesperado.`, '4000', 'center').subscribe(toast => {
                            });
                        }


                    },
                },
            ],
        });

        await confirmAlert.present();
        //  } else {
        /*   const alert = await this.alertController.create({
              cssClass: "custom-alert",
              header: "Operación requiere una autorización",
              message: "<p>Se verificará la autorización de anulación del documento.</p>",
              buttons: [
                  {
                      text: "Cancelar",
                      role: "cancel",
                      cssClass: "secondary",
                      handler: async (blah) => {
                          console.log(" Cancel: blah");
                          return false;
                      },
                  },
                  {
                      text: "Confirmar",

                      handler: async (blah) => {
                          console.log("Confirm Okay ", blah);
                          try {
                              let response: any = await this.pagosService.cancelPayAuthorizationService(this.dataPagos);
                              console.log({ response });


                          } catch (error) {
                              console.log("error ", error)
                              this.toast.show(`Error inesperado.`, '4000', 'center').subscribe(toast => {
                              });
                          }


                      },
                  },
              ],
          });

          await alert.present(); */

        // }

    }
    reporte = async () =>{
        if (this.item.estado!=3){
            return this.toast.show("No se puede imprimir, la transaccion fue fallida..!", "4000", "center").subscribe(t => t);
        }
        let dataGeneratePdf: IDataPagoPdf = {
            "fechahora": Tiempo.fecha() + " " + Tiempo.hora(),
            "tipodocumento": "DPG",
            "iddocumento": this.item.codigo,
            "usuario": this.userdata[0].idUsuario,
            "equipo": this.userdata[0].equipoId,
            "dataPago": this.dataPagos,
            "dataCliente": this.cliente[0]
        };
        /* let reimpresion = new Reimpresion();
        await reimpresion.insert(rx);
        let num_imp = await reimpresion.buscarreimpresion(this.item.codigo); */
        //this.item.reimpresion = num_imp;
        this.item.reimpresion = 0;
        let loading = await this.loadingCtrl.create();
        loading.present();

        try {
            let resp: any = await this.reportService.generareciboV2(dataGeneratePdf, this.userdata);
            if (resp) this.reportService.generateEXE(this.dataPagos.nro_recibo);
            this.loadingCtrl.dismiss();
        } catch (error) {
            this.loadingCtrl.dismiss();
            console.log(error);

            this.toast.show(`Error al generar el reporte.`, '4000', 'top').subscribe(toast => {
            });

        }

    }
    /*
    public async anularpago() {
        console.log("this.item. ", this.item);

        if (this.item.anulado > 0) {
            return this.toast.show(`El documento de pago ya fue anulado.`, '4000', 'top').subscribe(toast => {
            });
        }
        let monx: number = 0;
        let documentopago = new Documentopago();
        let rxx: any = await documentopago.findOne(this.item.codigo);
        console.log("all docs cabezera ", await documentopago.alldocPagos());

        let r = (rxx[0].tipo == 'cuenta' || rxx[0].tipo == 'pedido' || rxx[0].tipo == 'oferta');
        let pagos = new Pagos();
        let rx: any = await pagos.findDocAnulacion(this.item.codigo);
        
        console.log("rx ", rx);

        (typeof rx[0].monto == 'undefined') ? monx = 0 : monx = rx[0].monto;

        let fecha = moment().format('YYYY-MM-DD');
        console.log("fecha ", fecha)
        console.log("fecha documento ", this.item.fecha)

        if (fecha == this.item.fecha) {
            this.dialogs.confirm("Si anula el documento de pago habilitara la anulación al documento base\n" +
                "Está seguro de anular este documento de pago.\n", "Xmobile.", ["SI ANULAR", "NO"]).then(async (data) => {
                    if (data == 1) {

                        if (rxx[0].estado == 0) {
                            let respxx: any = await documentopago.anulardocumento(this.item.codigo, fecha);
                            respxx = await documentopago.getPgoAnuladoDoc(this.item.codigo, fecha);
                            console.log("respxx ", respxx);
                            console.log("await documentopago.findall ", await documentopago.allPagos());
                            if (respxx.length == 0) {//typeof respxx.insertId == 'undefined'
                                this.toast.show(`Solo se pueden anular documentos creados en la fecha.`, '3000', 'top').subscribe(toast => {
                                });
                                return false;
                            } else {
                                this.cancelarPago(this.item.codigo, r, this.item.CardCode, monx)
                                this.modalController.dismiss(false);
                            }
                        } else {
                            this.toast.show(`El documento de pago ya fue anulado.`, '3000', 'top').subscribe(toast => {
                            });
                        }
                        return false;
                    }
                }).catch(async (e) => {
                    console.log(e);
                });
        } else {
            const alert = await this.alertController.create({
                cssClass: "custom-alert",
                header: "Operación requiere una autorización",
                message: "<p>Se verificará la autorización de anulación del documento.</p>",
                buttons: [
                    {
                        text: "Cancelar",
                        role: "cancel",
                        cssClass: "secondary",
                        handler: async (blah) => {
                            console.log(" Cancel: blah");
                            return false;
                        },
                    },
                    {
                        text: "Confirmar",

                        handler: async (blah) => {
                            console.log("Confirm Okay ", blah);
                            try {
                                let response: any = await this.dataservis.serviceAutorization({ idUsuario: this.userdata[0].idUsuario, tipoDoc: "PAGO", codDoc: this.item.codigo });
                                console.log("response ", response)
                                let responseJson: any = JSON.parse(response.data);
                                console.log("responseJson ", responseJson)
                                if (responseJson.mensaje) {
                                    console.log("cancelando DOCUMENTO");
                                    this.toast.show(`Anulando pago..`, '4000', 'center').subscribe(toast => {
                                    });
                                    this.cancelarPago(this.item.codigo, r, this.item.CardCode, monx)
                                    this.modalController.dismiss(false);
                                } else {
                                    this.toast.show(`Anulación no autorizada.`, '4000', 'center').subscribe(toast => {
                                    });
                                }

                            } catch (error) {
                                console.log("error ", error)
                                this.toast.show(`Error inesperado.`, '4000', 'center').subscribe(toast => {
                                });
                            }
                          

                        },
                    },
                ],
            });

            await alert.present();
        }
       
 
    }

    public cancelarPago = async (codigo, r, CardCode, monx) => {
        let pago = new Pagos();
        await pago.anularpago(codigo, r, CardCode, monx);
        console.log("exportPagosAsyncCancela()");

        this.dataservis.exportPagosAsyncCancela();
    }
    */
    /* public async reporte() {
        let rx = {
            "fechahora": Tiempo.fecha() + " " + Tiempo.hora(),
            "tipodocumento": "DPG",
            "iddocumento": this.item.codigo,
            "usuario": this.userdata[0].idUsuario,
            "equipo": this.userdata[0].equipoId
        };
        let reimpresion = new Reimpresion();
        await reimpresion.insert(rx);
        let num_imp = await reimpresion.buscarreimpresion(this.item.codigo);
        this.item.reimpresion = num_imp;
        let resp: any = await this.reportService.generarecibo(this.item, this.cliente[0], true, this.pagos, this.userdata);
        if (resp) this.reportService.generateEXE(this.item.codigo);
    } */


    /*
    public async reporte() {
        let rx = {
            "fechahora": Tiempo.fecha() + " " + Tiempo.hora(),
            "tipodocumento": "DPG",
            "iddocumento": this.item.codigo,
            "usuario": this.userdata[0].idUsuario,
            "equipo": this.userdata[0].equipoId
        };
        let reimpresion = new Reimpresion();
        console.log("this.item. ", this.item)
        if (this.item.anulado == 0) await reimpresion.insert(rx);

        let num_imp = await reimpresion.buscarreimpresion(this.item.codigo);
        this.item.reimpresion = num_imp;
        let pagos = new Pagos();
        // let aux_pago_detalle;


        // if (this.pagos[0].dx == "FACTURAS") {
        //     console.log("this.item.codigo ", this.item.codigo);
        //     aux_pago_detalle = await pagos.findAllPagos(this.coddoc);
        //     console.log("aux_pago_detalle ", aux_pago_detalle);
        // }
        let pagosRecibo: any = await pagos.findAllPagosCuenta(this.item.codigo);
        console.log("this.item. ", this.item)
        if (pagosRecibo[0].otpp == 2) {
            let auxFacturas = await pagos.selectOne(this.item.codigo);
            pagosRecibo.facturas = auxFacturas;
        }
        console.log("pagosRecibo ", pagosRecibo)
        // ny = data.data.dx == 'cuenta' ? await detpago.findAllPagosCuenta(this.coddoc) :


        let resp: any = await this.reportService.generarecibo(this.item, this.cliente[0], true, pagosRecibo, this.userdata);
        if (resp) this.reportService.generateEXE(this.item.codigo);
    }
    */
    public cerrar() {
        this.modalController.dismiss(false);
    }
}
