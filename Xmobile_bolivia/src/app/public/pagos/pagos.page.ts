import { Component, OnInit, ViewChild } from '@angular/core';
import { AlertController, MenuController, ModalController, NavController } from "@ionic/angular";
import { Documentopago } from "../../models/documentopago";
import { NativeService } from "../../services/native.service"
import { ModalpagosPage } from "../modalpagos/modalpagos.page";
import { Tiempo } from "../../models/tiempo";
import { ConfigService } from "../../models/config.service";
import { DataService } from '../../services/data.service';
import { Pagos } from '../../models/V2/pagos';
import { PagosService } from '../../services/pagos.service';
import { IPagos } from '../../types/IPagos';
import { Toast } from '@ionic-native/toast/ngx';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { Clientes } from 'src/app/models/clientes';

@Component({
    selector: 'app-pagos',
    templateUrl: './pagos.page.html',
    styleUrls: ['./pagos.page.scss'],
})
export class PagosPage implements OnInit {
    public items: any;
    @ViewChild('datePicker') datePicker;
    public searchData: any;
    public monedaAc: string;

    constructor(private native: NativeService, private spinnerDialog: SpinnerDialog, public modalController: ModalController, private toast: Toast, public alertController: AlertController, public dataservis: DataService, public pagosService: PagosService, private configService: ConfigService,
        private navCrl: NavController, private menu: MenuController) {
        this.items = [];
        this.searchData = '';
    }

    public async ngOnInit() {
        this.menu.enable(true, 'menuxmobile');
        this.menu.close('menuxmobile');
        let servi: any = await this.configService.getSession();
        console.log("Session", servi);        
        this.monedaAc = servi[0].config[0].moneda;
        // this.native.mensaje('Presiona el botón amarillo de la parte de inferior para crear un nuevo pago.', '3000');
    }

    public ionViewWillEnter() {
        this.searchData = '';
        this.listarDocumentosPagos();
    }

    public async buscar(event: any) {
        this.items = [];
        console.log("event ", event);
        console.log("event ", event.detail.value);

        this.listarDocumentosPagos(event.detail.value);
    }

    public async searchCalendar() {
        this.datePicker.open();
    }

    public accionFecha(event: any) {
        this.items = [];
        console.log("event.detail.value ", event.detail.value);

        this.searchData = Tiempo.formatFecha(event.detail.value);
        // this.buscar(null);
        this.listarDocumentosPagos(this.searchData);
    }

    public async detallePagos(item: any) {

        let mcproducto: any = { component: ModalpagosPage, componentProps: item };
        let modalpoper: any = await this.modalController.create(mcproducto);
        modalpoper.onDidDismiss().then(async (data: any) => {
            if (typeof data.data != "undefined")
                this.listarDocumentosPagos();
        });
        return await modalpoper.present();
    }

    public async listarDocumentosPagos(textsearch = '') {
        this.spinnerDialog.show();
        let modelPagos = new Pagos();
        //console.log("NEDIOS ALL ", await modelPagos.selectAllMediosPago());
        let datoscliente = new Clientes();
        try {
            // let documentopago = new Documentopago();
            // this.items = await documentopago.findPagos(textsearch);

            this.items = await modelPagos.selectAllCabezera(textsearch, '');
            console.log("this.items  ", this.items);

            for (let d of this.items) {
                if(d.razon_social == "null"){
                    let datocli = await datoscliente.find(d.cliente_carcode);
                    console.log("DATOS DEL CLIENTE",datocli);
                    d.razon_social = datocli[0].CardName;
                }   
            }

        } catch (e) {
            console.log(e);

            this.items = [];
        }

        this.items.map(function (item) {
            console.log("map item  item.codigo ", item.estado)

            if (item.estado == 0) {
                item.status = "MOV"
                item.statusColor = "#8E8E89"
            }
            if (item.estado == 1) {
                item.status = 'MID'
                item.statusColor = "#ffc727"

            }
            if (item.estado == 2) {
                item.status = 'MID'
                item.statusColor = "#ffc727"
            }
            if (item.estado == 3) {
                item.status = 'SAP'
                item.statusColor = "#17CF15"
            }


            if (item.cancelado == 3) {
                item.statusColor = "#FB3925"
            }


        });

        // const responseStatus: any = await this.dataservis.checkDataStatusMid()
        // this.items.map(function (item) {
        //     console.log("map item  item.codigo ", item.codigo)

        //     const found = responseStatus.find(element => element.recibo == item.codigo);

        //     found ? item.status = found.descripcion : item.status = "MOV"
        //     if (item.status == 'SAP') {
        //         item.statusColor = "#17CF15"
        //     }
        //     if (item.status == 'MID') {
        //         item.statusColor = "#ffc727"
        //     }
        //     if (item.status == 'MOV') {
        //         item.statusColor = "#8E8E89"
        //     }
        //     if (item.status == 'ANULADO') {
        //         item.statusColor = "#FB3925"
        //     }

        //     console.log("found ", found);
        // });
        this.spinnerDialog.hide();
        console.log("this.items order ", this.items);
    }


    public async seleccionarCliente(tipo = 'null') {
        this.navCrl.navigateForward(`detallepago/${tipo}`);
    }

    public pageSincronizar() {
        this.navCrl.navigateForward(`sincronizacion`);
    }

    /**
     * REENVIAR PAGO EN ESTADO CERO -> NO SE ENVIÓ A MID
     */

    resendPay = async (item: IPagos) => {
        console.log("enviar ", item);
        if (item.mediosPago.length > 0) {

            const alert = await this.alertController.create({
                cssClass: "my-custom-class",
                header: "Enviar ",
                message: "¿Está seguro de reenviar el pago?",
                buttons: [
                    {
                        text: "Cancelar",
                        role: "cancel",
                        cssClass: "secondary",
                        handler: (blah) => {
                            console.log("Confirm Cancel: blah");
                            // this.estadoBtnRegister = false;
                        },
                    },
                    {
                        text: "Confirmar",
                        handler: async () => {
                            console.log("Confirm Okay");

                            let response = await this.pagosService.resendPayService(item);
                            console.log("response +=============== ", response);
                            this.toast.show(response.mensaje, '8000', 'top').subscribe(toast => {});

                            if(response.estado == 3){
                                console.log("actualiza pago en documento",item);
                                let pagos = new Pagos();
                                pagos.updateSaldoFacturas2(item);
                            }

                            this.searchData = '';
                            this.listarDocumentosPagos();

                        },
                    },
                ],
            });

            await alert.present();
        } else {
            this.toast.show(`Medios de pago no encontrado.`, '4000', 'bottom').subscribe(toast => {
            });
        }
    }

    /**
     * TOOLTIP PARA MOSTRAR AL USUARIO EN ESTADO DEL PAGO
     */
    showInfo = async (status, item) => {
        let statusText = '';
        let message = '';
        if (status == 'SAP') {
            statusText = 'SAP';
            message = 'El pago se migró correctamente a SAP.'
        }
        if (status == 'MID') {
            statusText = 'MIDLEWARE';
            message = 'El pago se registró en midleware, no pasó a SAP.';
        }
        if (status == 'MOV') {
            statusText = 'MÓVIL';
            message = 'Pago no migrado, revisa tu conexión a internet e intenta enviar nuevamente.'
        }
        if (status == 'ANULADO') {
            statusText = 'ANULADO';
            message = 'Pago anulado';
        }
        const alert = await this.alertController.create({
            cssClass: "my-custom-class",
            header: `Pago en ${statusText}`,
            message: `${message}`,
            buttons: [
                {
                    text: "Aceptar",
                    handler: () => {
                        console.log("Confirm Okay");

                    },
                },
            ],
        });
        await alert.present();
    }
}
