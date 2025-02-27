import { Component, OnInit,Renderer2 } from '@angular/core';
import { ModalController, NavParams } from "@ionic/angular";
import { WheelSelector } from "@ionic-native/wheel-selector/ngx";
import { Toast } from "@ionic-native/toast/ngx";
import { Documentos } from "../../models/documentos";
import { ConfigService } from "../../models/config.service";
import { Calculo } from "../../utilsx/calculo";
import { Clientes } from "../../models/clientes";
import { Pagos } from "../../models/pagos";
import { DataService } from "../../services/data.service";
import { Bancos } from "../../models/bancos";
import { Centrocostos } from "../../models/centrocostos";
import { Documentopago } from "../../models/documentopago";
import { Tiempo } from "../../models/tiempo";
import * as moment from 'moment';
import { of } from 'rxjs';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { IPagos, IMediosPagos, httpResponse, IFacturasPagos } from '../../types/IPagos';
import { PagosService } from '../../services/pagos.service';
import { GlobalConstants } from "../../../global";
import { AlertController } from "@ionic/angular";
import { ModalModopagoPage } from '../modal-modopago/modal-modopago.page';


@Component({
    selector: 'app-frompagos',
    templateUrl: './frompagos.page.html',
    styleUrls: ['./frompagos.page.scss'],
})
export class FrompagosPage implements OnInit {
    public idfrom = 6;
    public tipo: number;
    public data: any;
    public cod: any;
    public monto: number;
    public montoAuxGlobal: number;
    public montoaux: number;
    public montodocx: number;
    public montopagar: number;
    public tc: number;
    public documento: any;
    public userdata: any;
    public monedaUser: any;
    public arrcambio: any;
    public formapago: any;
    public documentosdata: Documentos;
    public clientedata: Clientes;
    public boucher: number;
    public cambio: number;
    public chequeCheque: any;
    public dateExpires: any;
    public bancosgestion: any;
    public tranferenciaBanco: any;
    public tranferenciaBancoName: any;
    public tranferenciaComprobante: any;
    public currency: any;
    public tipodocument: any;
    public tipopago: any;
    public otppex: any;
    public centroName: any;
    public centroCode: any;
    public documentoId: any;
    public limitFecha: string;
    public nrecibo: number;
    public pagosArray: any[] = [];
    public totalAmount: Number = 0;
    public totalSaldo: Number = 0;

    enviando: boolean = false;
    numberformat;
    toggleuSD: boolean = false;
    estadoEnviar: boolean = true;
    estadoagregar: boolean = false;
    montoDolar: any = 0;
    montoDolarAbs: any = 0;
    faltaCompletar: any = 0;
    CreditCardArray: any = [];
    CreditCardCode: any = "";
    CreditCardName: any = "";
    montoEnvio = 0
    montoDolarEnvio = 0
    auxmontoEnvio = 0
    auxmontoDolarEnvio = 0
    eventoClick = null;

    constructor(public modalController: ModalController, public navParams: NavParams, public dataservis: DataService, private spinnerDialog: SpinnerDialog, public pagosService: PagosService,public alertController: AlertController,
        private toast: Toast, private selector: WheelSelector, private configService: ConfigService,private dataService: DataService,private renderer: Renderer2) {
        this.documentosdata = new Documentos();
        this.clientedata = new Clientes();
        this.data = this.navParams.data;
        this.tipo = 0;
        this.tipopago = 0;
        this.montopagar = 1;
        this.arrcambio = [];
        this.bancosgestion = [];
        this.formapago = '';
        this.tipodocument = '';
        this.chequeCheque = 0;
        this.tranferenciaBanco = '';
        this.tranferenciaBancoName = '';
        this.tranferenciaComprobante = '';
        this.dateExpires = '';
        this.boucher = 0;
        this.cambio = 0;
        this.otppex = 0;
        this.montodocx = 0;
        this.centroName = '';
        this.centroCode = '';
        this.limitFecha = moment().format('YYYY-MM-DD');
        console.log("this.limitFecha ", this.limitFecha)

    }

    public async ngOnInit() {
        // tiene los datos del documento 
        let documento = GlobalConstants.CabeceraDoc;
        this.estadoEnviar = true;
        await this.carga_camposusuario('','contenedorcampos');

        console.log("DEVD recibido this.data ", this.data);
        console.log("this.data.dataCliente ", this.data.dataCliente);

        this.userdata = await this.configService.getSession();

        console.log("this.userdata[0] ", this.userdata)
        if (!this.userdata[0].tipocambioparalelo) {
            this.toast.show(`TIPO DE CAMBIO NO ENCONTRADO. `, '8000', 'center').subscribe(toast => {
            });
            this.estadoEnviar = true;
            this.userdata[0].tipocambioparalelo.tipoCambio = 0;
        }
        console.log("this.userdata[0].tipocambioparalelo.tipoCambio ", this.userdata[0].tipocambioparalelo.tipoCambio)

        this.tipo = this.data.tipo;

        console.log('  this.tipo ', this.tipo);
        this.monto = this.data.monto;
        this.totalSaldo = this.data.monto;

        this.montoAuxGlobal = this.data.monto;
        this.montoaux = 0;
        this.cod = this.data.cod;

        if (!this.userdata[0].tipoTarjeta) {
            this.CreditCardArray.push({
                CreditCard: 1,
                CardName: "Sin Tarjetas"
            });
        } else {
            this.CreditCardArray = this.userdata[0].tipoTarjeta;
            console.log("this.CreditCardArray ", this.CreditCardArray);

        }

        this.monedaUser = this.userdata[0].config[0].moneda;
        await this.selectcambio();
        console.log("this.tipo", this.tipo);        
        if (this.tipo == 1) {
            this.currency = 'USD';
        } else {
            for await (let moned of  this.userdata[0].monedas) {
                console.log("moned.Type", moned.Type);                
                if(moned.Type == "L"){
                    this.currency = moned.Code;
                }
            }
        }
        // this.currency = this.moneda
       
        let sumadocx: any;
        switch (this.data.modo) {
            case ('FACTURAS'):
                this.otppex = 2;
                this.tipodocument = 'factura';
                sumadocx = this.data.documento.reduce((sum, value) => (sum + value.pagarx), 0);
                switch (this.tipo) {
                    case (1)://dolares
                        this.montodocx = Calculo.round(sumadocx / this.tc);
                        break;
                    case (2): //efectivo
                        this.montodocx = Calculo.round(sumadocx);
                        break;
                }
                this.tipomonedaexe();
                break;
            case ('FACTURA'):
                this.otppex = 1;
                // this.documento = await this.documentosdata.findexe(this.data.documento[0].cod);

                console.log("DEVD factura a pagar   this.documento  ", documento);
                this.documento = documento[0];
                switch (this.documento.DocType) {
                    case ('DOP'):
                        this.tipodocument = 'pedido';
                        break;
                    case ('DFA'):
                        this.tipodocument = 'factura';
                        break;
                    case ('DOF'):
                        this.tipodocument = 'oferta';
                        break;
                }
                sumadocx = this.montoAuxGlobal//Number(this.documento.saldox).toFixed(2);
                switch (this.tipo) {
                    case (1)://dolares
                        this.montodocx = Calculo.round(sumadocx / this.tc);
                        break;
                    case (2): //efectivo
                        this.montodocx = Calculo.round(sumadocx);
                        break;
                }
                this.tipomonedaexe();
                break;
            case ('CLIENTE'):
                this.tipodocument = 'cuenta';
                this.otppex = 3;
                this.documentoId = 0;
                this.tipomonedaexe();
                break;
        }
    }

    public async pagar() {


        let pagoModel = new Pagos();
        let dataPagoExist: any = await pagoModel.find(this.data.cod)  // by idRecibo
        if (dataPagoExist && dataPagoExist.length > 0) {
            let inix: any = await this.pagosService.getNumeracionpago();
            let numerox = (inix + 1);
            let codPago = await Calculo.generaCodeRecibo(this.userdata[0].idUsuario.toString(), numerox.toString(), '1');
            this.data.cod = codPago;
            this.cod = this.data.cod;
            console.log("codigo ", this.cod);
        }
        let dataAux = await this.dataPayFormat(this.data.monto);
        console.log("dataAux ", dataAux);

    }

    public async dataPayFormat(monto) {

        let num: any = await this.pagosService.getNumeracionpago();
        num = num + 1;
        console.log("NUEVO NUMERO STORAGE ", num);
        console.log("this.data ", this.data);

        console.log("cod ", this.cod);

        let tiempo = moment().format('YYYY-MM-DD');
        let hora = moment().format('h:mm:ss');

        this.idfrom = 6;
        let camposusuario = await this.datacamposusuario();


        let modo;
        let tipoDoc;
        let factura_cod = null;

        if (this.data.modo == "CLIENTE") {
            modo = 3;
            tipoDoc = "cuenta";
        }
        if (this.data.modo == "FACTURAS") {
            modo = 2;
            tipoDoc = "deuda";
        }
        if (this.data.modo == "FACTURA") {
            modo = 1;
            tipoDoc = "factura";
            if (this.data.documento.length > 0) {
                factura_cod = this.data.documento[0].cod;
            }
        }
        console.log(" this.tipodocument ", this.tipodocument);
        let ubi: any = {
            lat: 0,
            lng: 0
        }

        try {
            ubi = await this.configService.getUbicacion();

        } catch (error) {

        }
        let dataSend = {
            nro_recibo: this.cod,
            correlativo: num,
            usuario: Number(this.userdata[0].idUsuario),
            documentoId: factura_cod,
            fecha: tiempo,
            hora: hora,
            monto_total: monto,
            tipo: tipoDoc,
            otpp: modo,
            tipo_cambio: this.tc,
            moneda: this.currency,
            cliente_carcode: this.data.dataCliente.CardCode,
            razon_social: this.data.dataCliente.razonsocial,
            nit: this.data.dataCliente.FederalTaxId,
            equipo: this.userdata[0].equipoId,
            latitud: ubi.lat,
            longitud: ubi.lng,
            estado: 0,
            cancelado: 0,
            mediosPago: this.pagosArray,
            facturaspago: [],
            camposusuario: camposusuario,
        }

        console.log("this.data.dataCliente",this.data.dataCliente);
        console.log(dataSend);

       
        //const rta =  await dataSend.mediosPago = this.pagosArray;

        let facturaspago: any = [];

        console.log("this.data.modo",this.data.modo);

        if (this.data.modo == 'FACTURAS') {
            for await (let documx of this.data.documento) {
                console.log("each documx ", documx);

                facturaspago.push(
                    {
                        nro_recibo: this.cod,
                        clienteId: this.data.dataCliente.CardCode,
                        documentoId: documx.cod,
                        docentry: documx.DocEntry,
                        monto: documx.pagarx,
                        CardName: this.data.dataCliente.razonsocial,
                        saldo: documx.saldo,
                        nroFactura: documx.cod,
                        DocTotal: documx.DocTotal,
                        cuota: documx.cuota
                    }
                );
               
            }
            console.log("facturaspago ", facturaspago);
            dataSend.facturaspago = facturaspago;


        }

        if (this.data.modo != "FACTURA") {
            this.pagosService.payCreate(dataSend).then((data: httpResponse) => {
                console.log("pago exitoso ", data);

                this.toast.show(data.mensaje, '8000', 'top').subscribe(toast => {
                });

                this.cerrar(dataSend);

            }).catch((e) => {
                console.log("ERROR en el pago ", e);
                this.toast.show(e.mensaje, '8000', 'top').subscribe(toast => {
                });
            })

        } else {
            // TODO : SI ES FACTURA DIRECTA SE DEBE DE MANDAR EL OBJETO AL DOCUMENTO 
            this.cerrar(dataSend);
        }

        console.log("dataSend ", dataSend);
        //return dataSend;
    }

    public datapago(...data) {
        let md = 0;
        let ml = 0;
        /*if (this.tipo == 1) {
            md = data[2];
        } else {
            md = 0;
        }
        */
        if (this.montoDolar > 0 || this.tipopago == 'PEF') {

            console.log("montoDolar ", this.montoDolar);
            data[1] = this.montoAuxGlobal;
            md = Number(this.auxmontoDolarEnvio);
            ml = Number(this.auxmontoEnvio);

        } else { md = 0; }
        return {
            documentoId: this.documentoId,
            clienteId: this.data.cliente,
            formaPago: this.formapago,
            tipoCambioDolar: this.tc,
            moneda: this.currency,
            monto: data[1],
            ci: this.userdata[0].idUsuario,
            cambio: this.cambio,
            monedaDolar: md,
            monedaLocal: ml,
            tipo: this.tipopago,
            documentoPagoId: this.cod,
            dx: this.tipodocument,
            otpp: data[0],
            numCheque: this.chequeCheque,
            transferencedate: this.dateExpires,
            baucher: this.boucher,
            bancoCode: this.tranferenciaBanco,
            checkdate: this.dateExpires,
            numTarjeta: this.tranferenciaComprobante,
            centro: this.centroCode,
            numComprobante: this.tranferenciaComprobante,
            numAhorro: 0,
            numAutorizacion: 0,
            ncuota: 0,
            CreditCard: this.CreditCardCode
        }
    }

    public async tipomonedaexe() {

        this.formapago = 'PEF';
        console.log(this.tipo);
        console.log("this.data.dataCliente ", this.data.dataCliente);
        console.log(
            { ...this.data.dataCliente }
        )
        this.userdata = await this.configService.getSession();

        let aux = 0;
        for await (let pagos of this.pagosArray){
            if(pagos.formaPago == 'PEF'){
                aux ++;
            }
        }

        switch (this.data.tipo) {
            case (2):
                this.tipopago = 2;
                let PCC: any, PBT: any, PCH: any;
                let descriptions: any = [];
                if(aux == 0){
                    let PEF = { description: 'Efectivo', cod: 'PEF', tipo: 2 };
                    descriptions.push(PEF);
                }

                console.log("accion creada",this.userdata[0].ctrl_formaPago);

                if(this.userdata[0].ctrl_formaPago == '0'){
                    PBT = { description: 'Transferencia', cod: 'PBT', tipo: 5 };
                    descriptions.push(PBT);
                    PCH = { description: 'Cheque', cod: 'PCH', tipo: 4 };
                    descriptions.push(PCH);
                    PCC = { description: 'Tarjeta', cod: 'PCC', tipo: 3 };
                    descriptions.push(PCC);
                }else{
                    if (this.data.dataCliente.cliente_std6 == 'Y') {
                        PBT = { description: 'Transferencia', cod: 'PBT', tipo: 5 };
                        descriptions.push(PBT);
                    } else { PBT = "" }
                    if (this.data.dataCliente.cliente_std7 == 'Y') {
                        PCH = { description: 'Cheque', cod: 'PCH', tipo: 4 };
                        descriptions.push(PCH);
                    } else {
                        PCH = "";
                    }
                    if (this.data.dataCliente.cliente_std8 == 'Y') {
                        PCC = { description: 'Tarjeta', cod: 'PCC', tipo: 3 };
                        descriptions.push(PCC);
                    } else {
                        PCC = "";
                    }

                }

                this.selector.show({
                    title: "SELECCIONAR TIPO DE PAGO.",
                    items: [descriptions],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR"
                }).then((result: any) => {
                    let ux: any = descriptions[result[0].index];
                    this.tipo = ux.tipo;
                    this.formapago = ux.cod;
                    this.Agregarnewpago();
                }, (err: any) => {
                    //this.cerrar(1);
                });
                break;
            case (1):
                this.tipopago = 1;
                this.formapago = 'PEF';
                this.Agregarnewpago();
                break;
       
        }
        
    }
    

    public async insertDocumentoPago() {
        return new Promise(async (resolve, reject) => {
            let texto: string = this.tipodocument.toUpperCase() + '_' + Tiempo.fecha();
            let documentoPagoData = { cod: this.cod, closa: texto, tipo: this.tipodocument };
            let documentospago = new Documentopago();
            let resp: any = await documentospago.insert(documentoPagoData);
            let docPago: any = await documentospago.find(resp);
            return resolve(docPago[0]);
        });
    }

   
    public selectcambio(exe = true) {
        let ux: any;
        this.arrcambio = [];
        let tiposcambio = [];
        try {
            tiposcambio = this.userdata[0].tiposcambio.filter((n) => {
                return n.ExchangeRate > 0;
            });
            for (let cambio of tiposcambio) {
                this.arrcambio.push({
                    ExchangeRate: cambio.ExchangeRate,
                    ExchangeRateDate: cambio.ExchangeRateDate,
                    ExchangeRateFrom: String(cambio.ExchangeRateFrom),
                    ExchangeRateTo: String(cambio.ExchangeRateTo)
                });
            }
            if (typeof this.userdata[0].tipocambioparalelo != "undefined") {
                this.tc = parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio);
                this.arrcambio.push({
                    ExchangeRate: parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio),
                    ExchangeRateDate: this.userdata[0].tipocambioparalelo.fecha,
                    ExchangeRateFrom: this.userdata[0].tipocambioparalelo.from,
                    ExchangeRateTo: this.userdata[0].tipocambioparalelo.to
                });
            }
        } catch (e) {
            this.modalController.dismiss(0);
            this.toast.show(`Existen problemas con el cambio de dólar cierra tu sesión y vuelve a ingresar.`, '4000', 'top').subscribe(toast => {
            });
        }
        if (!exe) {
            if (this.arrcambio.length > 0) {
                this.selector.show({
                    title: "SELECCIONAR EL TIPO DE CAMBIO A USAR.",
                    items: [this.arrcambio],
                    positiveButtonText: "SELECCIONAR",
                    negativeButtonText: "CANCELAR",
                    displayKey: 'ExchangeRate'
                }).then((result: any) => {
                    ux = this.arrcambio[result[0].index];
                    this.tc = ux.ExchangeRate;
                    this.montopagar = Calculo.round(this.monto * this.tc);
                    //this.operacionmprimaria();
                }, (err: any) => {
                });
            }
        }
    }


    public cerrar(data: any) {
        this.modalController.dismiss(data);
    }


    /* CAMPOS DINAMICOS DE USUARIO*/

    public async carga_camposusuario(datos,id) {
        console.log("llaga aqui0");
        let usuariodata: any = await this.configService.getSession();
        let contenedorcampos = '';

        if (usuariodata[0].campodinamicos.length > 0) {

            contenedorcampos = await this.dataService.createcampususer(usuariodata[0].campodinamicos, this.idfrom, datos);
        }

        const div: HTMLDivElement = this.renderer.createElement('div');
        div.className = "col-md-12";
        div.innerHTML = contenedorcampos;
        this.renderer.appendChild(document.getElementById(id), div);

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

    public async Agregarnewpago(){
        try {
            let datapagos = [];
            
            datapagos.push({
                Tiposel: this.tipo,
                datos: this.data,
                formapago:this.formapago,
                monto: this.totalSaldo,
                pagosrealizados: this.pagosArray
            });

            let modalpagos: any = { component: ModalModopagoPage,componentProps: datapagos};
            let modalmodopagos: any = await this.modalController.create(modalpagos);

            modalmodopagos.onDidDismiss().then(async (data: any) => {
                console.log("datos retornados de la firma",data.data);
                let datos = data.data;
                let tipo_pago = '';
                switch (datos.formaPago) {
                    case 'PEF':
                        tipo_pago = 'Efectivo';
                    break;
                    case 'PBT':
                        tipo_pago = 'Transferencia';
                    break;
                    case 'PCH':
                        tipo_pago = 'Cheque';
                    break;
                    case 'PCC':
                        tipo_pago = 'Tarjeta';
                    break;
                }
                this.pagosArray.push({
                    CreditCard: datos.CreditCard,
                    bancoCode:datos.bancoCode,
                    baucher: datos.baucher,
                    cambio:datos.cambio,
                    camposusuario: datos.camposusuario,
                    centro: datos.centro,
                    checkdate: datos.checkdate,
                    fecha: datos.fecha,
                    formaPago: datos.formaPago,
                    monedaDolar: datos.monedaDolar,
                    monedaLocal: datos.monedaLocal,
                    monto: datos.monto,
                    nro_recibo: datos.nro_recibo,
                    numCheque: datos.numCheque,
                    numComprobante: datos.numComprobante,
                    numTarjeta: datos.numTarjeta,
                    transferencedate: datos.transferencedate,
                    type:tipo_pago,
                    amount: datos.monto,
                    NumeroTarjeta: datos.NumeroTarjeta,
                    NumeroID: datos.NumeroID,
                    emitidoPor:datos.emitidoPor,
                    tipoCheque: datos.tipoCheque,
                    dateEmision:datos.dateEmision
                });

                this.totalAmount = Number(this.totalAmount) + Number(datos.monto);
                this.totalSaldo = Number(this.totalSaldo) - Number(datos.monto);

                this.totalSaldo = Calculo.round(this.totalSaldo);

                console.log("TOTAL PAGADO",this.totalSaldo);

                if(this.totalSaldo == 0){
                    console.log("entra aqui");
                    this.estadoagregar = true;
                    this.estadoEnviar = false
                }else{
                    this.estadoagregar = false;
                    this.estadoEnviar = true
                }

                console.log("MEDIOS PAGOS",this.pagosArray);

            });
            return await modalmodopagos.present();
        } catch (e) {
            console.log("error");
            console.log(e);
        }
    }



    async ItemAmountDetail(item) {
        console.log(item);
        
        let messageHTML = "";
        switch (item.type) {
          case "DÓLARES":
            break;
          case "Efectivo":
            console.log("EFECTIVO");
            //messageHTML = "<strong>montodocx </strong><br>" + item.montodocx + "";
            messageHTML =
              messageHTML + "<strong>cambio </strong>" + item.cambio.toFixed(2) + "";
            // centroName:this.centroName
            break;
          case "Cheque":
            console.log("CHEQUE");
            //messageHTML = "<strong>montodocx </strong><br>" + item.montodocx + "";
            messageHTML =
              messageHTML +
              "<strong>Banco: </strong>" +
              item.tranferenciaBancoName +
              "";
            messageHTML =
              messageHTML +
              "<br><strong>Nro. Cheque: </strong>" +
              item.chequeCheque +
              "";
            messageHTML =
              messageHTML + "<br><strong>Tipo </strong>" + item.typeChek + "";
            messageHTML =
              messageHTML +
              "<br><strong>Fecha Venc.: </strong>" +
              item.dateExpires +
              "";
            // centroName:this.centroName
            break;
          case "Transferencia":
            console.log("TRANSFERENCIA");
            messageHTML =
              messageHTML +
              "<strong>Banco: </strong>" +
              item.tranferenciaBancoName +
              "";
            messageHTML =
              messageHTML +
              "<br><strong>Comprobante: </strong>" +
              item.tranferenciaComprobante +
              "";
            messageHTML =
              messageHTML +
              "<br><strong>Fecha Venc.: </strong>" +
              item.dateExpires +
              "";
            // centroName:this.centroName
            break;
          case "Tarjeta":
            messageHTML =
              messageHTML + "<strong>Boucher: </strong>" + item.boucher + "";
            messageHTML =
              //messageHTML + "<strong>tranferenciaComprobante </strong><br>" + item.tranferenciaComprobante + "";
            messageHTML =
              messageHTML +
              "<br><strong>Tarjeta: </strong>" +
              item.codigoTarjeta +
              "";
            // centroName:this.centroName
            break;
    
          default:
            console.log("default");
        }
    
        const alert = await this.alertController.create({
          cssClass: "my-custom-class",
          header: item.type,
          subHeader: this.currency + " " + item.amount,
          message: messageHTML,
          buttons: ["OK"],
        });
    
        await alert.present();
    }



    async deleteItemAmount(item) {
        const alert = await this.alertController.create({
          message: "Eliminar registro?",
          buttons: [
            {
              text: "Ok",
              handler: () => {
                this.pagosArray = this.pagosArray.filter((obj) => obj !== item);
                this.totalAmount = Number(this.totalAmount) - Number(item.amount);
                this.totalSaldo = Number(this.totalSaldo) + Number(item.amount);
                if(this.totalSaldo == 0){
                    
                    this.estadoagregar = true;
                    this.estadoEnviar = false;
                }else{
                    this.estadoEnviar = true;
                    this.estadoagregar = false;
                }

                /*if (this.pagosArray.length == 0) {
                  this.disabledSelectmoney = false;
                }*/
              },
            },
            {
              text: "Cancelar",
              role: "cancel",
            },
          ],
        });
        await alert.present();
    }

    /*public operacionmprimaria() {
        if (this.monto > this.montodocx) {

            //this.cambio = Calculo.round(this.monto - this.montodocx);
        }
    }*/

    /*transformAmount(element) {
        this.numberformat = Calculo.formatMoney(this.monto);
        // Remove or comment this line if you dont want 
        // to show the formatted amount in the textbox.
        element.target.value = this.numberformat;
    }*/

     /*public async centroCosto() {
        let arrx = [];
        let centro: any = new Centrocostos();
        let centroarr: any = await centro.findAll();
        if (centroarr.length > 0) {
            for (let x of centroarr)
                arrx.push({ description: x.PrcName });
            this.selector.show({
                title: "SELECCIONAR CENTRO DE COSTO",
                items: [arrx],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR"
            }).then((result: any) => {
                let dataCentro: any = centroarr[result[0].index];
                this.centroName = dataCentro.PrcName;
                this.centroCode = dataCentro.PrcCode;
            }, (err: any) => {
                console.log(err);
            });
        }
    }*/
}
