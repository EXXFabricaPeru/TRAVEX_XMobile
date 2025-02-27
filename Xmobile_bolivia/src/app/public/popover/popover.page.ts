import { Component, NgZone, OnInit,Renderer2 } from '@angular/core';
import { ActionSheetController, AlertController, ModalController, NavController, NavParams } from "@ionic/angular";
import { Documentos } from "../../models/documentos";
import { Clientes } from "../../models/clientes";
import { WheelSelector } from "@ionic-native/wheel-selector/ngx";
import { Toast } from "@ionic-native/toast/ngx";
import { Calculo } from "../../utilsx/calculo";
import { ConfigService } from "../../models/config.service";
import { FrompagosPage } from "../frompagos/frompagos.page";
import { Documentopago } from "../../models/documentopago";
import { promocionaes } from "../../models/promociones";
import { Tiempo } from "../../models/tiempo";
import { Reimpresion } from "../../models/reimpresion";
import { ReportService } from "../../services/report.service";
import 'lodash';
import { Detalle } from '../../models/detalle';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';
import { Dialogs } from '@ionic-native/dialogs/ngx';
import { bonificacionesDocCabezera } from '../../models/bonificacionDocCabezera.';
//import { ConsoleReporter } from 'jasmine';
import BonoFactory from '../../models/patterns/BonificacionesDocumentos';
import { Network } from "@ionic-native/network/ngx";
import { DataService } from "../../services/data.service";
import { ModalclientePage } from "../modalcliente/modalcliente.page";
import { ModalNitPage } from "../modal-nit/modal-nit.page";
import { GlobalConstants } from "../../../global";
import { BonificacionesService } from '../../services/bonificaciones.service';
import { PagosService } from '../../services/pagos.service'
import { Bolivia } from "../../utilsx/bolivia";
import { Companex } from "../../utilsx/companex";
import { Chile } from "../../utilsx/chile";
import { Paraguay } from "../../utilsx/paraguay";

declare var _: any;

@Component({
    selector: 'app-popover',
    templateUrl: './popover.page.html',
    styleUrls: ['./popover.page.scss'],
})
export class PopoverPage implements OnInit {
    public idfrom = 1;
    public nit: string;
    public localizacion_calculo: any;
    public razonsocial: string;
    public complemento: string;
    public plazospago: string;
    public condicion: string;
    public tipoDocu: string;
    public comentariox: string;
    public iniNum: any;
    public cuenta: string;
    public datosConsolidador: string;
    public carcodeConso: string;
    public productodata: any;
    public minPiker: string;
    public condicionName: string;
    public fex_tipo: string;
    public consolidador: string;
    public tablanit: string;
    public tipoDocumentoName: string;
    public limitTipo: any;
    public dataCliente: any;
    public reserva: boolean;
    public estadodox: boolean;
    public arrinputs: any;
    public cantidadCuotas: any;
    public diasAdicional: any;
    public ultimoDia: string;
    public docType: string;
    public estadoFechaentrega: string;
    public datosconf: any;
    public userdata: any;
    public datadocx: any;
    public controlStatus: number;
    public cantidadItems: boolean;
    public cantidadItemsTexto: boolean;
    public CardCode: string;
    public totalBruto: any;
    public totalDescuento: any;
    public totalNeto: any;
    public dataPromociones: any = [];
    public dataPromocionesSelected: any = {};
    public saldoPromo: any = 0;
    public isAddPromocion: boolean = false;
    public notSend: boolean = false;
    public promoUseAux: any = 0;
    itemsAux: any;
    public btnSave: boolean = false;
    modificarInfTributaria = false;
    Currency: String = "";
    eventoClick = null;
    public monedalocal: string;
    //Campos de usuario
    
    constructor(private toast: Toast, public modalController: ModalController, public actionSheetController: ActionSheetController, 
        private bonificacionesService: BonificacionesService, public navParams: NavParams, private selector: WheelSelector, public alertController: AlertController, 
        private navCrl: NavController, private network: Network, private dataService: DataService, private configService: ConfigService,
        private pagosservice:PagosService ,private renderer: Renderer2, private dialogs: Dialogs, private reportService: ReportService, private spinnerDialog: SpinnerDialog) {
        this.productodata = navParams.data;
        console.log("<------paramas----->", this.productodata);
        
        this.reserva = false;
        this.estadodox = false;
        this.nit = '';
        this.razonsocial = '';
        this.complemento = '';
        this.plazospago = '';
        this.condicion = '';
        this.tipoDocu = '';
        console.log("stockBoni ", localStorage.getItem("stockBoni"));
        if (localStorage.getItem("stockBoni") == "1" || localStorage.getItem("stockBoni") == "2") {
            this.comentariox = 'Existe Bonificación sin stock.';
        } else {
            this.comentariox = '';
        }

        this.cuenta = '';
        this.monedalocal = '';
        this.condicionName = '';
        this.tipoDocumentoName = '';
        this.estadoFechaentrega = '';
        this.ultimoDia = '';
        this.arrinputs = [];
        this.userdata = [];
        this.cantidadCuotas = 0;
        this.diasAdicional = 0;
        this.docType = this.productodata.DocType;

        this.datosconf = [
            {
                code: '99001',
                text: '[99001] Entidades que no esten obligadas a inscribirse en el Padrón Nacional de Contribuyentes.',
                label: 'Entidades que no esten obligadas a inscribirse en el Padrón Nacional de Contribuyentes.',
                handler: () => {
                    this.selectAttr(this.datosconf[0]);
                }
            }, {
                code: '99002',
                text: '[99002] Comprador que no proporciona sus datos o CONSUMIDOR FINAL.',
                label: 'CONSUMIDOR FINAL.',
                handler: () => {
                    this.selectAttr(this.datosconf[1]);
                }
            }, {
                code: '99004',
                text: '[99004] Procedimientos de Control Tributario.',
                label: 'Procedimientos de Control Tributario.',
                handler: () => {
                    this.selectAttr(this.datosconf[2]);
                }
            }, {
                code: '99003',
                text: '[99003] Emisión de Documentos Fiscales.',
                label: 'Emisión de Documentos Fiscales.',
                handler: () => {
                    this.selectAttr(this.datosconf[3]);
                }
            }, {
                code: '99005',
                text: '[99005] Factura Comercial de Exportación en Libre Consignación.',
                label: 'Factura Comercial de Exportación en Libre Consignación.',
                handler: () => {
                    this.selectAttr(this.datosconf[4]);
                }
            }
        ];
    }

    public async ngOnInit() {
        let documentos = new Documentos();
        let detalle = new Detalle();

        let servi = await this.configService.getSession();

        switch (parseInt(servi[0].localizacion)) {
            case (1):
                this.localizacion_calculo = new Bolivia();
                break;
            case (2):
                this.localizacion_calculo = new Companex();
                break;
            case (3):
                this.localizacion_calculo = new Paraguay();
                break;
            case (4):
                this.localizacion_calculo = new Chile();
                break;
        }

        await this.carga_camposusuario('');

        this.getTotalesResume();
        console.log("this.productodata. ", this.productodata);
        if (this.productodata.DocType == 'DFA') {

            this.promocionesEnabled();
        }

        this.reserva = (this.productodata.Reserve == 1) ? true : false;
        let cliente = new Clientes();
        this.dataCliente = await cliente.selectOnline(this.productodata.CardCode);
        console.log("this.dataCliente ", this.dataCliente);
        console.log("this.productodata.cardNameAux ", this.productodata.cardNameAux);

        this.minPiker = documentos.getFechaPicker();
        (this.productodata.federalTaxId == '') ? this.nit = this.dataCliente[0].FederalTaxId : this.nit = this.productodata.federalTaxId;
        console.log("ENTRA AQUI 0");

        (this.dataCliente[0].razonsocial != '') ? this.razonsocial = this.dataCliente[0].razonsocial : this.razonsocial = this.productodata.cardNameAux;

        this.complemento = this.dataCliente[0].Fex_complemento;

        this.limitTipo = parseFloat(this.dataCliente.PayTermsGrpCode);

        if (this.productodata.clone != 0 || this.productodata.cloneaux != 0) {
            this.nit = this.productodata.U_4NIT;
            console.log("ENTRA AQUI 1");
            this.razonsocial = this.productodata.U_4RAZON_SOCIAL;
        }
        
        console.log("this.razonsocial ", this.razonsocial);

        if (this.razonsocial == "" || this.razonsocial == null) {
            this.razonsocial = this.dataCliente[0].razonsocial;
            this.nit = this.dataCliente[0].FederalTaxId;
            console.log("ENTRA AQUI 2");
            
        }

        if(this.productodata.codeConsolidador != null && this.productodata.codeConsolidador != undefined && this.productodata.codeConsolidador != '' && this.productodata.codeConsolidador != "undefined"){
            this.carcodeConso = this.productodata.codeConsolidador;
            let cliente = new Clientes();
            let cli: any = await cliente.find(this.carcodeConso);
            this.datosConsolidador = cli[0].CardCode+' - '+cli[0].CardName;
        }

        await this.getOptionsIdent(false);
        let conarr: any = await this.configService.getSession();

        for (let monedas of conarr[0].monedas) {
            if(monedas.Type == 'L'){
                this.monedalocal = monedas.Code;
            }
        }        

        let tipoDocumento: any = conarr[0].fex_tipoDocumento;
        if (tipoDocumento.length > 0) {
            for (let x of tipoDocumento) {
                if (x.id == this.dataCliente[0].Fex_tipodocumento) {
                    this.tipoDocumentoName = x.descripcion;
                    this.tipoDocu = x.id;
                }
            }
        }

        let uso_fex: any = conarr[0].uso_fex;
        let usa_consolidador: any = conarr[0].usa_consolidador;
        this.fex_tipo = uso_fex;
        this.consolidador = usa_consolidador;
        this.tablanit = conarr[0].usa_tabla_nit;

        this.getOptions(false);
        //(this.productodata.fechasend == 'undefined') ? this.plazospago = this.productodata.fechaupdate : this.plazospago = this.productodata.fechasend;
        this.plazospago =Tiempo.fecha();
        this.comentariox = this.productodata.comentario;
        this.cuenta = this.productodata.cuenta;
        this.userdata = await this.configService.getSession();

        if (this.userdata[0].config[0].modInfTributaria == '0' && this.nit != '0') {
            if(this.nit != null && this.razonsocial != null){
                this.modificarInfTributaria = true;
            }
        }

        if (this.productodata.PayTermsGrpCode) {
            console.log("this.userdata[0].condicionespago ", this.userdata[0].condicionespago);
            let filterArrayPagos = this.userdata[0].condicionespago.filter(value => value.GroupNumber == this.productodata.PayTermsGrpCode)
            console.log("filterArrayPagos ", filterArrayPagos)
            if (filterArrayPagos.length > 0) {
                this.condicionName = filterArrayPagos[0].PaymentTermsGroupName;
                this.condicion = filterArrayPagos[0].GroupNumber;
            }

        }
        
        console.group("DEVD LOGICA BONOS");
        console.log("DEVD after bonosDocVigentes ");

        let bonosDocVigentes: any = [];

        if (this.productodata.clone == 0 || localStorage.getItem('mofificarBonificacion') === "SI") {
            bonosDocVigentes = await this.getBonosDocVigentes();

        }
        console.log("DEVD before bonosDocVigentes ", bonosDocVigentes);
        if (bonosDocVigentes.length > 0) {

            for (let element of bonosDocVigentes) {
                let detalle = new Detalle();

                // let totalx: any = await detalle.sumaTotal(this.productodata.cod);

                // let total = (totalx.totalNeto - totalx.descuentos).toFixed(2);

                // this.totalBruto = totalx.totalNeto;
                // this.totalDescuento = totalx.descuentos;
                // this.totalNeto = totalx.total;


                let descuentoDelTotal = 0;
                console.log("totalx ", this.totalBruto);
                console.log("total - descuentos a calcular el descuento: ", this.totalNeto);
                console.log("DEVD  element open modal  ", element);
                // console.log(await this.createModalBono(element));
                // await this.createModalBono(element);
                if (element.showModalDesc) {
                    // let modalSingleton = ModalSingleton.getInstance();
                    // console.log("modalSingleton ", modalSingleton);
                    let addButton: any;
                    if (element.OPCIONAL == 'OPCIONAL') {
                        addButton = {
                            text: 'OMITIR',
                            handler: async (blah) => {
                                console.log('DEVD OMITIR Confirm Cancel: blah');
                            }
                        };
                    } else {
                        addButton = '';
                    }
                    let alert2: any = await this.alertController.create({
                        header: '% DESCUENTO: ' + element.bonoRefact.cantidad_regalo + '%',
                        subHeader: 'Descuento:' + element.bonoRefact.nombre,
                        backdropDismiss: false,

                        inputs: [{
                            name: 'data',
                            type: 'number',
                            value: "",
                            min: 1,
                            max: 100,
                            placeholder: element.bonoRefact.porcentaje
                        }],
                        buttons: [addButton, {
                            text: 'CONTINUAR',
                            handler: (data: any) => {
                                console.log('DEVD OMITIR Confirm : data ', data);
                                let value = parseFloat(data.data);
                                
                                if (value > 0 && value < 100) {
                                    if (value <= Number(element.bonoRefact.cantidad_regalo)) {
                                        console.log("validar ");
                                        console.log("value ", value, " > ");

                                        descuentoDelTotal = (parseFloat(value.toString()) / 100) * parseFloat(this.totalNeto);
                                        console.log("****** normal ");

                                        this.calculofinal(descuentoDelTotal, value, this.productodata.cod, 2,0);

                                    } else {
                                        this.toast.show(`El valor no debe pasar los ${element.bonoRefact.cantidad_regalo}%.`, '4000', 'top').subscribe(toast => {
                                        });
                                        // await alert2.present();
                                        return false;
                                    }

                                    // this.newBonificacionesUsados = [];
                                } else {

                                    this.toast.show(`El número no se encuentra en el rango permitido.`, '4000', 'top').subscribe(toast => {
                                    });
                                    // await alert2.present();
                                    return false;
                                }


                            }
                        }]
                    });
                    await alert2.present();
                    // return resolve("creado");

                }
            }
        }
        console.groupEnd();

        let detalledoc: any;
        detalledoc = await detalle.showTable(this.productodata.cod);
        console.log('datos del detalle', detalledoc);
        let detalledoc2: any;
        detalledoc2 = await detalle.find(this.productodata.cod);
        console.log('datos del detalle', detalledoc2);

        this.cargadatosclonados();
    }

    public async calculofinal(descuentoDelTotal :any,value:any,cod:any,aux: any,aux1:any){

        await this.bonificacionesService.descuentoICEdocumento(descuentoDelTotal, value, cod,aux,aux1);
        await this.getTotalesResume();
    }

    async generateDesc(descuentoDelTotal, value, cod, estado) {
        let documentos = new Documentos();
        try {
            await documentos.descuentoICE(descuentoDelTotal, value, cod, estado);

            this.getTotalesResume();
            this.toast.show(`Descuento adicionado.`, '4000', 'top').subscribe(toast => {
            });
        } catch (error) {
            this.toast.show(`Ocurrió un error inesperado.`, '4000', 'top').subscribe(toast => {
            });
        }

    }

    getBonosDocVigentes = async () => {

        let modelBonos = new bonificacionesDocCabezera();
        let dataBonos: any = [];
        let dataBonosValid: any = [];
        return new Promise(async (resolve, reject) => {
            console.log("DEVD in promise ");
            dataBonos = await modelBonos.selectAll(this.dataCliente[0].rutaterritorisap);
            console.log("DEVD bonos encontrados ", dataBonos);

            if (dataBonos.length > 0) {
                // Así debemos recorrer los arrays a partír de ahora :)
                for (var element of dataBonos) {
                    // console.log("DEVD element each ", element);
                    const objetoBono = BonoFactory.createBono(
                        element.id_regla_bonificacion,
                        element,
                        this.itemsAux,
                        {
                            CardCode: this.dataCliente[0].CardCode,
                            GroupName: this.dataCliente[0].GroupName,
                            codeCanal: this.dataCliente[0].codeCanal
                        });
                      console.log("DEVD objetoBono ", objetoBono);
                    if (objetoBono) {
                        console.log("pasa");
                        dataBonosValid.push(objetoBono);
                    }
                    // ¡¡¡Con break, continue y return!!!
                }
                resolve(dataBonosValid);
            } else {
                reject(dataBonos)
            }
        });
    }

    getTotalesResume = async () => {

        GlobalConstants.CabeceraDoc;
        GlobalConstants.DetalleDoc;
  
        let totalx: any = [{
            total: 0,
            totalNeto: 0,
            descuentos: 0,
            ICEes: 0,
            ICEps: 0,
            ICEtotales: 0,
        }];

        this.itemsAux = GlobalConstants.DetalleDoc;

        let ices = 0;
        let descuentoPromocion = 0;

        /*let totalNetoaux = 0;
        let totalaux = 0;
        let descuentoaux = 0;
        let iceeaux = 0;
        let icepaux = 0;
        let icetotalesaux = 0;*/

        console.log("CONSOLA: LLAMA A LA DUNCION sumaTotalLocal PARA RECALCULAR LOS TOTALES 485");
        console.log("Cabecera----->", GlobalConstants.CabeceraDoc);
        console.log("Detalle------>", GlobalConstants.DetalleDoc);
        
        let totales: any = await this.localizacion_calculo.sumaTotalLocal(GlobalConstants.CabeceraDoc, GlobalConstants.DetalleDoc);

        console.log("totales---->", totales);
        
        
        totalx.total = totales.total;
        totalx.totalNeto = totales.totalNeto;
        totalx.descuentos = totales.descuentos;
        totalx.ICEes = totales.ICEes;
        totalx.ICEps = totales.ICEps;
        totalx.ICEtotales = totales.ICEtotales;

        if (ices > 0) {
            totalx.totalNeto = (totalx.totalNeto + ices).toFixed(2);
        }

        if (descuentoPromocion > 0) {
            console.log(descuentoPromocion);
            totalx.descuentos = totalx.descuentos - descuentoPromocion;
            totalx.descuentos = totalx.descuentos + (descuentoPromocion).toFixed(2);
        }

        this.totalBruto = totalx.totalNeto;
        this.totalDescuento = totalx.descuentos;
        this.totalNeto = totalx.totalNeto - totalx.descuentos;

        GlobalConstants.CabeceraDoc[0].DocTotal = this.totalNeto;
        GlobalConstants.CabeceraDoc[0].DocumentTotalPay = this.totalNeto;
        

    }

    promocionesEnabled = async () => {
        console.log("promocionesEnabled ");
        let modelPromo = new promocionaes();
        this.dataPromociones = await modelPromo.findCurrent(this.productodata.CardCode);
        console.log("dataPromociones ", this.dataPromociones);
        if (this.dataPromociones.length > 0) {
            let listArray = [];
            for (let item of this.dataPromociones) {
                console.log(item);
                listArray.push({ description: " - " + item.name + ":" + " Saldo: " + item.U_Saldo + " Bs." });
            }
            this.selector.show({
                title: "SELECCIONAR CAMPAÑA.",
                items: [listArray],
                positiveButtonText: "Seleccionar",
                negativeButtonText: "Cancelar"
            }).then(async (result: any) => {
                let value = this.dataPromociones[result[0].index];
                /// this.selectSucursal.AddresName = '';
                this.dataPromocionesSelected = value;
                this.saldoPromo = 0;
                console.log("  this.dataPromocionesSelected  ", this.dataPromocionesSelected);

            }, (err: any) => {
                //  this.toast.show(`Debes seleccionar una promoción, intenta nuevamente.`, '4000', 'top').subscribe(toast => {
                //   });
                //  this.notSend = true;
                this.dataPromociones = [];
            });

        }
    }

    aplicatePromocion = async () => {
        console.log("this.totalDescuento ", this.totalDescuento);
        const regexNumber = /^[0-9]*$/;

        // returns true
        if (!regexNumber.test(this.saldoPromo)) {
            return this.toast.show(`Valor inválido ingresa número entero.`, '4000', 'top').subscribe(toast => {
            });
        }


        if (this.isAddPromocion) {
            return this.toast.show(`Ya se adicionó la campaña.`, '4000', 'top').subscribe(toast => {
            });
        }
        if (this.saldoPromo > 0) {


            console.log("DEVD this.saldoPromo ", this.saldoPromo);
            console.log("DEVD this.dataPromocionesSelected.U_Saldo ", this.dataPromocionesSelected.U_Saldo);
            if (this.saldoPromo > 0 && Number(this.saldoPromo) <= this.dataPromocionesSelected.U_Saldo) {
                console.log("adicionar descuento ", this.saldoPromo);
                if (this.saldoPromo >= this.totalBruto) {
                    return this.toast.show(`El descuento no puede ser igual o mayor al monto bruto.`, '4000', 'top').subscribe(toast => {
                    });
                }
                try {
                    const alert = await this.alertController.create({
                        cssClass: "my-custom-class",
                        header: "Confirmar",
                        message: "Está seguro de adicionar el descuento monetario a todas las lineas del documento.",
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
                                    console.log("Confirm Okay");

                                    //let descuentoDelTotal = this.saldoPromo;
                                    this.promoUseAux = this.saldoPromo;
                                    console.log(" this.promoUseAux  ", this.promoUseAux);

                                    let descuentoDelTotalPorcentual = 0;
                                    this.spinnerDialog.show(null, null, true);

                                    //this.totalDescuento = Number(this.totalDescuento) + Number(descuentoDelTotal);
                                   // console.log(" descuentoDelTotal  ", descuentoDelTotal);
                                   // console.log(" this.       this.totalDescuento   ", this.totalDescuento);
                                   // this.totalNeto = (this.totalNeto - descuentoDelTotal).toFixed(2);
                                    console.log("restado ", this.totalNeto)
                                    this.saldoPromo = 0;
                                    this.isAddPromocion = true;
                                    this.toast.show(`Descuento Adicionado.`, '4000', 'top').subscribe(toast => {
                                    });
                                    

                                    

                                    GlobalConstants.CabeceraDoc[0].U_CodigoCampania = this.dataPromocionesSelected.U_CodigoCampania;
                                    //GlobalConstants.CabeceraDoc[0].U_Saldo = this.dataPromocionesSelected.U_Saldo;
                                    GlobalConstants.CabeceraDoc[0].U_MontoCampania = this.promoUseAux;

                                    let porcentaje = (parseFloat(this.promoUseAux.toString()) * 100) / parseFloat(this.totalNeto);
                                    let descuentoDelTotal = (parseFloat(porcentaje.toString()) / 100) * parseFloat(this.totalNeto);

                                    this.bonificacionesService.descuentoICEdocumento(descuentoDelTotal, porcentaje, this.productodata.cod, 2,1);
                                    this.getTotalesResume();
                                    
                                    this.spinnerDialog.hide();
                                },
                            },
                        ],
                    });

                    await alert.present();


                } catch (error) {
                    return this.toast.show(`Ocurrió un error inesperado.`, '4000', 'top').subscribe(toast => {
                    });
                }


            } else {
                return this.toast.show(`Monto inválido,  ingrese número entero`, '4000', 'top').subscribe(toast => {
                });
            }
        } else {
            return this.toast.show(`Monto inválido,  ingrese número entero`, '4000', 'top').subscribe(toast => {
            });
        }
    }

    deletePromocion = () => {
        if (this.isAddPromocion) {
            console.log("reset");
            this.deletePromocionUsada();
        } else {
            console.log("muy tarde");
            return this.toast.show(`No se aplicó una campaña.`, '4000', 'top').subscribe(toast => {
            });
        }

    }

    async deletePromocionUsada() {
        this.dialogs.confirm(`Esta seguro de eliminar la campaña asignada?.`, "Xmobile.", ["Continuar", "Cancelar"]).then(async (data) => {
            switch (data) {
                case (1):
                    this.toast.show(`Promocion eliminada`, '4000', 'top').subscribe(toast => {
                    });

                    this.totalDescuento = this.totalDescuento - this.promoUseAux;
                    console.log("this.promoUseAux ", this.promoUseAux);
                    console.log("this.totalNeto ", this.totalNeto);
                    console.log("this.totalDescuento ", this.totalDescuento);
                   
                    this.totalNeto = this.totalNeto + Number(this.promoUseAux);
                    this.promoUseAux = 0;
                    this.isAddPromocion = false;
                    //this.SWaddProducto = true;
                    break;
            }
        });

    }

    public async cargadatosclonados(){
        let documentos = new Documentos();
        let cod = GlobalConstants.CabeceraDoc[0].clone;
        let resultado: any = await documentos.findexe(cod);
        console.log("cliente generico0->",resultado)
        try {
            let arrxx = [];
            let conarr: any = await this.configService.getSession();
            let tipoDocumento: any = conarr[0].fex_tipoDocumento;
            console.log("cliente generico1->",tipoDocumento)
            if (tipoDocumento.length > 0) {
                for (let x of tipoDocumento) {
                    arrxx.push({
                        description: x.descripcion,
                        PaymentTermsGroupName: x.descripcion,
                        GroupNumber: x.id,
                    });
                }
            }
    
            if(resultado){
                for await (let data of arrxx){
                    console.log("cliente generico2->",data)
                    if(resultado.Fex_tipodocumento){
                        if(data.GroupNumber == resultado.Fex_tipodocumento){
                            this.tipoDocumentoName = data.PaymentTermsGroupName;
                            this.tipoDocu = data.GroupNumber;
                        }
                    }
                }
            }
        } catch (e) {
            console.log(e);
        }
        console.log("cliente generico3->",resultado)

       /*  if(resultado && resultado.U_4NIT && resultado.U_4NIT != undefined && resultado.U_4NIT != null && resultado.U_4NIT != 'undefined'){
        this.nit = resultado.U_4NIT;
        }else{
            this.nit = '999999999';
        } */

       /*  if(resultado && resultado.U_4RAZON_SOCIAL && resultado.U_4RAZON_SOCIAL != undefined && resultado.U_4RAZON_SOCIAL != null && resultado.U_4RAZON_SOCIAL != 'undefined'){
            this.razonsocial = resultado.U_4RAZON_SOCIAL;
        }else{
            this.razonsocial = 'CLIENTE GENERICO';
        } */


        if(resultado && resultado.comentario && resultado.comentario != undefined && resultado.comentario != null && resultado.comentario != 'undefined'){
            this.comentariox = resultado.comentario;
        }else{
            this.comentariox = '';
        }

        if(resultado && resultado.fechasend && resultado.fechasend != undefined && resultado.fechasend != null && resultado.fechasend != 'undefined'){
            this.plazospago = resultado.fechasend;
        }

        try {
            let arrxx = [];
            let conarr: any = await this.configService.getSession();
            let condiciones: any = conarr[0].condicionespago;
            
            if (conarr[0].ctrl_conPago == 2) {
                this.dataCliente[0].PayTermsGrpCode
                let dias = 0;
                if (condiciones.length > 0) {
                    for (let y of condiciones) {
                        if (y.GroupNumber == this.dataCliente[0].PayTermsGrpCode) {
                            dias = ((parseInt(y.NumberOfAdditionalMonths) * 30) + parseInt(y.NumberOfAdditionalDays));
                        }
                    }
                    for (let x of condiciones) {
                        if (((parseInt(x.NumberOfAdditionalMonths) * 30) + parseInt(x.NumberOfAdditionalDays)) <= dias) {
                            arrxx.push({
                                description: x.PaymentTermsGroupName,
                                PaymentTermsGroupName: x.PaymentTermsGroupName,
                                GroupNumber: x.GroupNumber,
                            });
                        }
                    }
                }
            } else {
                if (this.dataCliente[0].cndpago != '') {
                    arrxx.push({
                        description: this.dataCliente[0].cndpagoname,
                        PaymentTermsGroupName: this.dataCliente[0].cndpagoname,
                        GroupNumber: this.dataCliente[0].cndpago
                    });
                }
                if (condiciones.length > 0) {
                    for (let x of condiciones) {
                        arrxx.push({
                            description: x.PaymentTermsGroupName,
                            PaymentTermsGroupName: x.PaymentTermsGroupName,
                            GroupNumber: x.GroupNumber,
                        });
                    }
                }
            }
           
            for await (let data of arrxx){
                if(data.GroupNumber == resultado.PayTermsGrpCode){
                    this.condicionName = data.PaymentTermsGroupName;
                    this.condicion = data.GroupNumber;
                }

            }
        } catch (e) {
            console.log(e);
        }

        let conarr: any = await this.configService.getSession();
        let camposusuario = conarr[0].campodinamicos;
        for await (let campos of camposusuario){
            if(campos.Objeto == '1'){
                let campo = "campousu" + campos.Nombre;
                (document.getElementById(campo) as HTMLInputElement).value = resultado[campo];
            }
        }
    }

    public async mensajes() {
        const actionSheet: any = await this.actionSheetController.create({
            header: 'Seleccionar',
            buttons: this.datosconf
        });
        await actionSheet.present();
    }

    public selectAttr(arr: any) {
        this.nit = arr.code;
        console.log("ENTRA AQUI 4");
        this.razonsocial = arr.label;
        this.estadodox = true;
    }

    public requerido(valor: string) {
        if (valor == 'null' || valor == null || valor.length == 0 || /^\s+$/.test(valor)) {
            return false;
        } else {
            return true;
        }
    }

    public async formPago(tipo: number, monto: any) {
        console.log("tipo para enviar  ", tipo);
        let numerox: number = 0;
        this.iniNum = await this.configService.getNumeracion();
        console.log("DATOS DE LA NUMERACION", this.iniNum);
        console.log("DATOS DE LA NUMERACION 1", GlobalConstants.numeropago);

        if (GlobalConstants.numeropago == 0) {
            let inix: any = await this.pagosservice.getNumeracionpago();
            console.log("DATOS DE LA NUMERACION 2", inix);

            if (inix > 0 && this.iniNum.numgp <= inix) {
                numerox = (inix + 1);
            } else {
                numerox = (inix + this.iniNum.numgp);
            }
        } else {
            numerox = GlobalConstants.numeropago;
        }

        console.log("DATOS DE LA NUMERACION 3", numerox);


        let codPago = Calculo.generaCodeRecibo(this.userdata[0].idUsuario.toString(), numerox.toString(), '1');
        let datospago = {
            dataCliente: this.dataCliente[0],
            modo: 'FACTURA',
            cod: codPago,
            cliente: this.productodata.CardCode,
            tipo: tipo,
            monto: monto,
            documento: [{
                cod: this.productodata.cod,
                coddoc: this.productodata.cod,
                pagarx: monto
            }],
            correlativo: numerox
        };
        console.log("DEVD datospago ", datospago);
        let obj: any = { component: FrompagosPage, componentProps: datospago };
        let modal: any = await this.modalController.create(obj);
        modal.onDidDismiss().then(async (data: any) => {
            if (data.data != 0) {
                console.log("data pago pagos", data);
                console.log("documento en memoria ", GlobalConstants.CabeceraDoc);
                GlobalConstants.CabeceraDoc[0].pagos = data.data; //-> DocumentId tiene el codigo de la factura 
                console.log("documento en memoria mutado ", GlobalConstants.CabeceraDoc);
                this.modalController.dismiss(this.datadocx, null, "modalpedido");
            }
        });
        return await modal.present();


    }

    private numeros(s) {
        let rgx = /^[0-9]*\.?[0-9]*$/;
        if (s.match(rgx) == null) {
            return false;
        } else {
            return true;
        }
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

    public async pagosForm(monto: any) {
        let alert: any = await this.alertController.create({
            header: ` BS ${Calculo.formatMoney(monto)}`,
            mode: 'ios',
            buttons: [{
                text: 'PAGAR ', //CON BS
                handler: (data: any) => {
                    let mon = Number(monto);
                    if (mon > 0) {
                        this.formPago(2, monto);
                    } else {
                        this.toast.show(`El valor  introducido no es valido. `, '2000', 'top').subscribe(toast => {
                        });
                        return false;
                    }
                }
            }]
        });
        await alert.present();
    }

    public async registrar() {
        //debugger;
        for (let i = 0; i < GlobalConstants.DetalleDoc.length; i++) {
            
            if (GlobalConstants.DetalleDoc[i].Tbonificacion == 1) {
                console.log("SE ACTUALIZA EL DESCUENTO");
                
                GlobalConstants.DetalleDoc[i].U_4DESCUENTO = GlobalConstants.DetalleDoc[i].U_4DESCUENTOBoni;
                GlobalConstants.DetalleDoc[i].ICEe = GlobalConstants.DetalleDoc[i].ICEeBoni;
                GlobalConstants.DetalleDoc[i].ICEp = GlobalConstants.DetalleDoc[i].ICEpBoni;
                GlobalConstants.DetalleDoc[i].LineTotalPay = GlobalConstants.DetalleDoc[i].LineTotalPayBoni;
            }
        }

        
        if (GlobalConstants.CabeceraDoc[0].Tbonificacion == 1) {
            
            GlobalConstants.CabeceraDoc[0].DocumentTotalPay = GlobalConstants.CabeceraDoc[0].DocumentTotalPayBoni;
            console.log("datos guardados6",JSON.stringify(GlobalConstants.CabeceraDoc));

        } else {
            let auxtotal = 0;
            
            for await (let item of GlobalConstants.DetalleDoc) {
                //auxtotal += ((item.Quantity * item.Price) - item.U_4DESCUENTO) + Number(item.ICEe) + Number(item.ICEp);
                auxtotal += ((item.Price * item.Quantity) - (item.U_4DESCUENTOBoni)) + item.ICEeBoni + item.ICEpBoni
            }
            
            //GlobalConstants.CabeceraDoc[0].DocumentTotalPay = auxtotal;
        }

        this.productodata = GlobalConstants.CabeceraDoc[0];

        if ((this.razonsocial).trim() == '') {
            this.toast.show(`Razón social no puede ser nulo. `, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        console.log("this.razonsocial.length ", this.razonsocial.length);
        if (this.razonsocial.length < 3 && this.razonsocial != "" && this.razonsocial != "SIN NOMBRE") {
            this.toast.show(`Razón social no es válido `, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        if (this.requerido(this.nit) == false) {
            this.toast.show(`NIT no puede ser nulo. `, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        if (this.nit.length < 5 && this.nit != "" && this.nit != "0") {
            this.toast.show(`Nit no es válido `, '4000', 'top').subscribe(toast => {
            });
            return false;
        }
        if (this.nit.length >= 5) {
            if (this.razonsocial == "SIN NOMBRE" || this.razonsocial == "") {
                return this.toast.show(`Debe completar la razon social para el NIT : ${this.nit}.`, "4000", "top").subscribe(data => data);
            }
        }
        let conarr: any = await this.configService.getSession();
        let uso_fex: any = conarr[0].uso_fex;
        let camposusuario = await this.datacamposusuario();

        let data = {
            documentofex: uso_fex,
            tipodocumento: this.tipoDocu,
            razonsocial: this.razonsocial,
            complemento: this.complemento,
            nit: this.nit,
            plazospago: this.plazospago,
            condicion: this.condicion,
            comentario: this.comentariox,
            cuenta: this.cuenta,
            reserva: this.reserva,
            cuotasArr: this.arrinputs,
            carcodeConso: this.carcodeConso,
            camposusuario: camposusuario
        };

        console.log(data);
        this.datadocx = data;

        if (this.productodata.DocType == 'DFA') {
            /*if (this.promoUseAux > 0) {
                let documentos = new Documentos();
                let modelPromociones = new promocionaes();
                await documentos.descuentoICE(this.promoUseAux, 0, this.productodata.cod, false);

                await modelPromociones.insertUse(this.dataPromocionesSelected, this.productodata.cod, this.promoUseAux);
                console.log("promociones usadas ", await modelPromociones.showAllUses());
            }*/
            GlobalConstants.Campaña = this.dataPromocionesSelected;
            console.log("CAMPAÑA",GlobalConstants.Campaña);

            console.log("LA CONDICION ES",this.condicion);
            this.productodata.DocumentTotalPay = this.totalNeto;

            if (Number(this.dataCliente[0].CreditLimit) > 0 && parseFloat(this.productodata.DocumentTotalPay) > parseFloat(this.dataCliente[0].CreditLimit) && this.condicion != '-1' ) {
                this.toast.show(`El crédito  del cliente supero límite permitido.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            } else {
                if (data.condicion == '-1') {
                    await this.pagosForm(parseFloat(this.productodata.DocumentTotalPay));
                } else {
                    this.modalController.dismiss(this.datadocx);
                    return false;
                }
                console.log("5")
            }
        } else {
            this.modalController.dismiss(this.datadocx);
        }
    }

    public async confirmar(estx = false) {
        console.log("confirmar()")
        this.btnSave = true;
        setTimeout(() => {
            this.btnSave = false;
        }, 2000);
        const regex = /^[0-9]*$/;
        console.log("this.nit ", this.nit);
        const onlyNumbers = regex.test(this.nit); // true
        console.log("onlyNumbers ", onlyNumbers)
        //console.log("length ", this.nit.toString().length)
        console.log("onlyNumbers ", onlyNumbers)
        let conarr: any = await this.configService.getSession();

        if (this.nit == null || this.nit == '0') {
            return this.toast.show(`Nit inválido,el campo NIT no puede ser 0 o vacío.`, '4000', 'top').subscribe(toast => {
            });
        }

        if (this.nit.toString().trim() == "" || this.nit == null) {
            return this.toast.show(`Nit inválido,el campo NIT no puede ser 0 o vacío.`, '4000', 'top').subscribe(toast => {
            });
        }

        if (this.nit.toString().length != 0 && this.nit != "0") {
            if(conarr[0].validanitnumerico == 1){
                if (!onlyNumbers) {
                    return this.toast.show(`Nit inválido, ingresa solo números.`, '4000', 'top').subscribe(toast => {
                    });
                }
            }

            if (this.nit.toString().length > 15) {
                return this.toast.show(`Nit debe tener máximo 15 caracteres.`, '4000', 'top').subscribe(toast => {
                });
            }
            if (this.nit.toString().length < 5) {
                return this.toast.show(`Nit debe tener mínimo 5 caracteres.`, '4000', 'top').subscribe(toast => {
                });
            }
        }
        console.log("Razon social",this.razonsocial);

        if(this.razonsocial !== undefined){
            if (this.nit.toString().length >= 5 && this.razonsocial.trim() == "") {
                return this.toast.show(`Ingresa la razón social para el nit ${this.nit}.`, '4000', 'top').subscribe(toast => {
                });
            }
        }else{
            return this.toast.show(`Ingresa la razón social para el nit ${this.nit}.`, '4000', 'top').subscribe(toast => {
            });
        }

        // let validador = this.validatexto(this.razonsocial);
        // console.log("el validador es",validador);
        // if(validador > 0){
        //     return this.toast.show(`Ingrese una Razón Social Valida, no se permiten caracteres especiales ni numeros`, '4000', 'top').subscribe(toast => {});
        // }

        if (this.productodata.DocType == "DFA") {
            let xData: any;
            let dataext: any = {
                "codigo": this.productodata.CardCode
            };

            let cliente = new Clientes();

            // if (this.network.type != 'none') {
            //     xData = await this.dataService.servisReportPost("clientes/consultasaldoclientesap", dataext);
            //     let xJson = JSON.parse(xData.data);
            //     console.log("xJson.respuesta", xJson.respuesta[0]);
            //     await cliente.updatebalancemenossap(xJson.respuesta[0].Balance, this.productodata.CardCode);
            // }

            let cli: any = await cliente.find(this.productodata.CardCode);
            console.log(this.productodata);

            let detalle = new Detalle();
            let totalx: any = await detalle.sumaTotalLocal(GlobalConstants.CabeceraDoc, GlobalConstants.DetalleDoc);

            let total = (totalx.totalNeto - totalx.descuentos).toFixed(2);

            console.log(total);

            if (parseInt(cli[0].CreditLimit) > 0 && parseInt(cli[0].CurrentAccountBalance) >= parseInt(cli[0].CreditLimit) && this.condicion != '-1') {
                this.toast.show(`El cliente supero el límite de crédito.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }

            if (parseInt(cli[0].CreditLimit) > 0 && (parseInt(cli[0].CurrentAccountBalance) + parseInt(total)) >= parseInt(cli[0].CreditLimit) && this.condicion != '-1') {
                this.toast.show(`Al generar este documento el cliente superara el límite de crédito.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }

            if (cli[0].cliente_std9 > 0 && this.condicion != '-1') {
                this.toast.show(`El cliente tiene facturas vencidas.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }
        }
        if (this.productodata.DocumentTotal >= 3000 && this.productodata.DocType == "DFA" && estx == true) {
            if (this.nit == '0' || this.nit == '' || this.razonsocial == '') {
                this.mensajes();
            } else {
                this.registrar();
                console.log("registrar");
            }
        } else {
            this.registrar();
            console.log("registrar");
        }
    }

    public actionChangeMonto(event: any, i: any) {
        (event.target.value != '') ? this.arrinputs[i].Total = parseFloat(event.target.value) : this.arrinputs[i].Total = 0;
        let tox = this.cantidadCuotas - 1;
        let sumador = 0;
        for (let x = 0; x < tox; x++) sumador = sumador + parseFloat(this.arrinputs[x].Total);
        if (sumador < this.productodata.DocTotal) {
            let resp = Calculo.round(this.productodata.DocTotal - sumador);
            this.arrinputs[tox].Total = resp;
        } else {
            this.toast.show(`Valor no permitido supera al monto total.`, '4000', 'top').subscribe(toast => {
            });
        }
    }

    public actionChangeDuoDate(event: any) {
        let documentos = new Documentos();
        let dateFrom: any = documentos.getFechaPicker();
        let dateTo: any = this.ultimoDia;
        let search = event.target.value;
        search = search.substr(0, 10);
        let dateCheck: any = search;
        let d1: any = dateFrom.split("-");
        let d2: any = dateTo.split("-");
        let c: any = dateCheck.split("-");
        let from: any = new Date(d1[0], parseInt(d1[1]) - 1, d1[2]);
        let to: any = new Date(d2[0], parseInt(d2[1]) - 1, d2[2]);
        let check: any = new Date(c[0], parseInt(c[1]) - 1, c[2]);
        if ((check > from && check < to) != true) {
            event.target.value = this.minPiker;
            this.toast.show(`La fecha que seleccionarte no esta permitida.`, '4000', 'top').subscribe(toast => {
            });
        }
    }

    public async cerrar(data: any) {
        if (this.totalDescuento > 0 && localStorage.getItem('esClon') == "NO") {
            const alert = await this.alertController.create({
                cssClass: "my-custom-class",
                header: "Cerrar",
                message: "Si tienes bonificaciones o descuentos se perderán los mismos, puede volver a cargarlos.",
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

                            this.dismissModalAction(data);

                        },
                    },
                ],
            });

            await alert.present();
        } else {
            this.dismissModalAction(data);
        }

    }

    async dismissModalAction(data) {
        if (this.isAddPromocion) {
            this.deletePromocionUsada();
        }
        localStorage.setItem("cancelado", "NO");
        if (localStorage.getItem("esClon") == "NO") {
            let modelPromociones = new promocionaes();

            console.log("DEVD this.dataPromocionesSelected ", this.dataPromocionesSelected);
            let uses: any = await modelPromociones.showAllUsesBycod(this.productodata.cod)
            console.log("DEVD uses ", uses);

            let detalle = new Detalle();
            this.itemsAux = await detalle.findAll(this.productodata.cod);
            console.log("  DEVD this.itemsAux ", this.itemsAux);

            // for await (let item of this.itemsAux) {
            //     if (item.bonificacion == 1) {
            //         await detalle.updateBonificacionLineaReset(item.id);

            //     }
            //     //console.log("DEVD item ", item);

            // }
            if (uses.length > 0) {
                await modelPromociones.deleteUse(this.productodata.cod);

            }
        }


        this.modalController.dismiss(data);
    }

    public actionCantidacuotas(event: any) {
        let total = this.productodata.DocTotal;
        let x = parseInt(event.target.value);
        let cuotas: any = Calculo.round(total / x);
        if (!isNaN(x)) {
            this.cantidadCuotas = x;
            if (this.cantidadCuotas <= 10 && this.cantidadCuotas > 0) {
                let contadorDias: any = (this.diasAdicional / x);
                let contadorAux = 0;
                this.arrinputs = [];
                for (let i = 0; i < this.cantidadCuotas; i++) {
                    this.arrinputs.push({
                        "DueDate": this.sumaDias((contadorAux += contadorDias)),
                        "iddocpedido": this.productodata.cod,
                        "Percentage": 0,
                        "InstallmentId": i,
                        "fecharegistro": this.minPiker,
                        "idcliente": "0",
                        "Total": cuotas
                    });
                }
            }
        } else {
            this.arrinputs = [];
        }
    }

    public sumaDias(addDias: any) {
        let documentos: any = new Documentos();
        let fecha: any = new Date(this.minPiker);
        fecha.setDate(fecha.getDate() + addDias);
        return documentos.timestampdate(fecha);
    }

    public async getOptions(x = true) {

        try {
            let arrxx = [];
            let conarr: any = await this.configService.getSession();
            let condiciones: any = conarr[0].condicionespago;
            console.log("DATOS DEL CLIENTE",this.dataCliente);
            if (conarr[0].ctrl_conPago == 2) {
                this.dataCliente[0].PayTermsGrpCode
                let dias = 0;
                if (condiciones.length > 0) {
                    
                    for (let y of condiciones) {
                        if (y.GroupNumber == this.dataCliente[0].PayTermsGrpCode) {
                            dias = ((parseInt(y.NumberOfAdditionalMonths) * 30) + parseInt(y.NumberOfAdditionalDays));
                        }
                    }

                    console.log("DIAS",dias);
                    for (let x of condiciones) {
                        console.log("dias calculados " + x.GroupNumber.toString() + "-" + dias.toString(),((parseInt(x.NumberOfAdditionalMonths) * 30) + parseInt(x.NumberOfAdditionalDays)));
                        if (((parseInt(x.NumberOfAdditionalMonths) * 30) + parseInt(x.NumberOfAdditionalDays)) <= dias) {
                            arrxx.push({
                                description: x.PaymentTermsGroupName,
                                PaymentTermsGroupName: x.PaymentTermsGroupName,
                                GroupNumber: x.GroupNumber,
                            });
                        }
                    }
                }
            } else {
                if (this.dataCliente[0].cndpago != '') {
                    arrxx.push({
                        description: this.dataCliente[0].cndpagoname,
                        PaymentTermsGroupName: this.dataCliente[0].cndpagoname,
                        GroupNumber: this.dataCliente[0].cndpago
                    });
                }
                if (condiciones.length > 0) {
                    for (let x of condiciones) {
                        arrxx.push({
                            description: x.PaymentTermsGroupName,
                            PaymentTermsGroupName: x.PaymentTermsGroupName,
                            GroupNumber: x.GroupNumber,
                        });
                    }
                }
            }

            console.log("cond pago ", arrxx);
            let condicionesauxtotal = _.sortBy(arrxx, [(o) => o.GroupNumber]);
            let condicionesaux = _.uniqBy(condicionesauxtotal, (o) => o.GroupNumber);
            console.log("XXXXXXXXX ", condicionesaux);
            let arrx = [];
            if (condicionesaux.length > 1 && x == true) {
                for (let x of condicionesaux)
                    arrx.push({ description: x.PaymentTermsGroupName });
                this.selector.show({
                    title: "SELECCIONAR CONDICIÓN DE PAGO",
                    items: [arrx],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR"
                }).then((result: any) => {
                    this.arrinputs = [];
                    let ux: any = condicionesaux[result[0].index];
                    this.condicionName = ux.PaymentTermsGroupName;
                    this.condicion = ux.GroupNumber;
                }, (err: any) => {
                    console.log(err);
                });
            } else {
                this.arrinputs = [];
                let u: any = condicionesaux[0];
                this.condicionName = u.PaymentTermsGroupName;
                this.condicion = u.GroupNumber;
            }
        } catch (e) {
            console.log(e);
        }
    }

    public async getOptionsIdent(x = true) {

        try {
            let arrxx = [];
            let conarr: any = await this.configService.getSession();
            let tipoDocumento: any = conarr[0].fex_tipoDocumento;

            if (tipoDocumento.length > 0) {
                for (let x of tipoDocumento) {
                    arrxx.push({
                        description: x.descripcion,
                        PaymentTermsGroupName: x.descripcion,
                        GroupNumber: x.id,
                    });
                }
            }
            let tipoDocumentoauxtotal = _.sortBy(arrxx, [(o) => o.GroupNumber]);
            let tipoDocumentoaux = _.uniqBy(tipoDocumentoauxtotal, (o) => o.GroupNumber);

            console.log("tipoDocumentoaux ", tipoDocumentoaux);
            let arrx = [];
            if (tipoDocumentoaux.length > 1 && x == true) {
                for (let x of tipoDocumentoaux)
                    arrx.push({ description: x.PaymentTermsGroupName });
                this.selector.show({
                    title: "SELECCIONAR TIPO DE DOCUMENTO",
                    items: [arrx],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR"
                }).then((result: any) => {
                    this.arrinputs = [];
                    let ux: any = tipoDocumentoaux[result[0].index];
                    this.tipoDocumentoName = ux.PaymentTermsGroupName;
                    this.tipoDocu = ux.GroupNumber;
                }, (err: any) => {
                    console.log(err);
                });
            } else {
                this.arrinputs = [];
                let u: any = tipoDocumentoaux[0];
                this.tipoDocumentoName = u.PaymentTermsGroupName;
                this.tipoDocu = u.GroupNumber;
            }
        } catch (e) {
            console.log(e);
        }
    }

    public async selectConsolidador(acc) {

        if (acc == 0) {
            console.log("selectConsolidador()");

            let mcliente: any = { component: ModalclientePage, componentProps: { tipo: this.tipoDocu, uso: '1' } };
            let modalcliente: any = await this.modalController.create(mcliente);
            modalcliente.onDidDismiss().then(async (data: any) => {
                if (data.data != false && typeof data.data != "undefined") {
                    console.log("cliente seleccionado ", data.data);
                    this.carcodeConso = data.data.CardCode;
                    this.datosConsolidador = data.data.CardCode + ' - ' + data.data.CardName;
                    console.log(data.data.CardCode);
                    console.log(data.data.CardName);

                } else {
                    this.navCrl.pop();
                    this.toast.show(`Selecciona un Consolidador para continuar.`, '4000', 'bottom').subscribe(toast => {
                    });
                }
            });
            return await modalcliente.present();
        } else {
            this.carcodeConso = '';
            this.datosConsolidador = '';
            this.toast.show(`Consolidador Eliminado.`, '4000', 'bottom').subscribe(toast => {
            });
        }
    }

    public async selectnit(acc) {

        if (acc == 0) {
            let mnit: any = { component: ModalNitPage};
            let modalnit: any = await this.modalController.create(mnit);
            modalnit.onDidDismiss().then(async (data: any) => {

                if (data.data != false && typeof data.data != "undefined") {
                    console.log("nit seleccionado ", data.data);
                    this.nit = data.data.nit;
                    this.razonsocial = data.data.razon_social;
                    console.log(data.data.nit);
                    console.log(data.data.razon_social);

                } 
            });
            return await modalnit.present();
        } else {
            this.nit = '';
            this.razonsocial = '';
            this.toast.show(`Datos Eliminado.`, '4000', 'bottom').subscribe(toast => {
            });
        }
    }

    /* CAMPOS DINAMICOS DE USUARIO*/
    public async carga_camposusuario(datos) {
        console.log("llaga aqui0");
        let usuariodata: any = await this.configService.getSession();
        let contenedorcampos = '';

        if (usuariodata[0].campodinamicos.length > 0) {

            contenedorcampos = await this.dataService.createcampususer(usuariodata[0].campodinamicos, this.idfrom, datos);
        }

        const div: HTMLDivElement = this.renderer.createElement('div');
        div.className = "col-md-12";
        div.innerHTML = contenedorcampos;
        this.renderer.appendChild(document.getElementById("contenedorcampos"), div);


        for (let i = 0; i < usuariodata[0].campodinamicos.length; i++) {
            if (usuariodata[0].campodinamicos[i].Objeto == this.idfrom) {
                if (usuariodata[0].campodinamicos[i].tipocampo == 1) {
                    if (usuariodata[0].campodinamicos[i].flagrelacion == 1) {
                        let campo = "campousu" + usuariodata[0].campodinamicos[i].Nombre;
                        this.eventoClick = this.renderer.listen(
                            document.getElementById(campo),
                            "ionChange",
                            evt => {
                                this.cargalista_campousuario(evt, usuariodata[0].campodinamicos[i].Id);
                            }
                        );
                    }
                }
            }
        }

        let tipo = '';
        switch (this.productodata.DocType) {
            case "DOF":
                tipo = "1";
            break;
            case "DOP":
                tipo = "2";
            break;
            case "DFA":
                tipo = "3";
            break;
            case "DOE":
                tipo = "4";
            break;
        }


        for (let i = 0; i < usuariodata[0].campodinamicos.length; i++) {
            if (usuariodata[0].campodinamicos[i].Objeto == this.idfrom) {
                if (usuariodata[0].campodinamicos[i].documento != "0") {
                    let campo = "label_campousu" + usuariodata[0].campodinamicos[i].Nombre;
                    let val = usuariodata[0].campodinamicos[i].documento;
                    let objeto = document.getElementById(campo);

                    let aux = 0;
                    for (let i = 0; i < val.length; i++) {
                        const valor = val[i];
                        if(valor != ',' && aux == 0){
                            if(valor == tipo){
                                objeto.style.display = "block"; 
                                aux = 1;
                            }else{
                                objeto.style.display = "none";
                            }
                        }
                    }
                } 
            }
        }
    }

    public async datacamposusuario() {
        let data = [];
        let valor: any;
        let sesion = await this.configService.getSession();
        let camposusuario = sesion[0].campodinamicos;
        for (let i = 0; i < camposusuario.length; i++) {
            if (camposusuario[i].Objeto == this.idfrom) {
                let campo = "campousu" + camposusuario[i].Nombre;
                var variable = document.getElementsByClassName(campo);
                if (camposusuario[i].tipocampo == 1) {
                    for (let i = 0; i < variable[0]["childNodes"].length; i++) {
                        if (variable[0]["childNodes"][i]["className"] == "aux-input") {
                            valor = variable[0]["childNodes"][i]["defaultValue"];
                        }
                    }
                } else {
                    if (camposusuario[i].tipocampo == 0) {
                        valor = variable[0]["childNodes"][0]["childNodes"][0]["defaultValue"];
                    } else {

                        valor = variable[0]["childNodes"][1]["value"];
                    }
                }
                data.push({
                    Objeto: camposusuario[i].Objeto,
                    cmidd: camposusuario[i].cmidd,
                    tabla: camposusuario[i].tabla,
                    campo: campo,
                    valor: valor
                });
            }
        }
        return data;
    }

    public async cargalista_campousuario(val, id) {
        let sesion = await this.configService.getSession();
        let camposusuario = sesion[0].campodinamicos;
        let codigosel = '';
        for (let i = 0; i < camposusuario.length; i++) {
            if (camposusuario[i].tipocampo == 1) {
                if (camposusuario[i].Id == id) {
                    for (let l = 0; l < camposusuario[i].lista.length; l++) {
                        if (camposusuario[i].lista[l].codigo == val.detail.value) {
                            codigosel = camposusuario[i].lista[l].Id;
                        }
                    }
                }
            }
        }


        for (let i = 0; i < camposusuario.length; i++) {
            if (camposusuario[i].Objeto == this.idfrom) {
                if (camposusuario[i].tipocampo == 1) {
                    if (camposusuario[i].relacionado == id) {
                        let campo = ".campousu" + camposusuario[i].Nombre;
                        const objeto = document.querySelector(campo);
                        let contenedorcampos = '';
                        for (let l = 0; l < camposusuario[i].lista.length; l++) {
                            if (camposusuario[i].lista[l].cabecera == id && camposusuario[i].lista[l].detalle == codigosel) {
                                let codigo = camposusuario[i].lista[l].codigo;
                                let nombre = camposusuario[i].lista[l].nombre.replace(/['"]+/g, '');
                                contenedorcampos += "<ion-select-option  value=" + codigo + ">" + nombre + "</ion-select-option>"
                            }
                        }
                        objeto.innerHTML = contenedorcampos;
                    }
                }
            }
        }
    }

    public validatexto(string){
        var out = 0;
        var filtro = " abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ.&'";
        for (var i=0; i<string.length; i++){
            if (filtro.indexOf(string.charAt(i)) == -1){
                console.log(string.charAt(i));
                out ++;
            } 
        }
        return out;
    }
}
