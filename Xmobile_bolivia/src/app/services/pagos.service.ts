import { Injectable } from '@angular/core';
import { HTTP } from '@ionic-native/http/ngx';
import { ConfigService } from "../models/config.service";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { Documentos } from "../models/documentos";
import { Detalle } from "../models/detalle";
import { Clientes } from "../models/clientes";
import { Pagos } from "../models/V2/pagos";
import * as moment from 'moment';
import { FacturasPagos } from '../models/facturasPagos';
import { IPagos, httpResponse } from '../types/IPagos';
import { Network } from '@ionic-native/network/ngx';
import { Calculo } from "../../app/utilsx/calculo";
import { AlertController } from '@ionic/angular';



@Injectable({
    providedIn: 'root'
})
export class PagosService {
    pagosModel: Pagos;
    public path: any;
    public arraux: any;
    public idfrom = 6;

    constructor(private http: HTTP, private configService: ConfigService,
        private network: Network, private spinnerDialog: SpinnerDialog,

    ) {

    }

    /**
     * CREACION DE PAGO EN EL MOVIL EN BASE A LA RESPUESTE DE MIDLEWARE 
     */

    async payCreate2(dataPago: IPagos, respuesta: any) {
        const modelPagos = new Pagos();
        console.log("data payayCreate ", dataPago);
        let statusPay = respuesta.respuesta.estadoPago;
        modelPagos.insert(dataPago, statusPay, dataPago.nro_recibo);
    }

    async payCreate(dataPago): Promise<httpResponse> {
        const modelPagos = new Pagos();
        console.log("data payayCreate ", dataPago);
        let statusPay = 0;
        this.spinnerDialog.show(undefined, undefined, true);
        let responseData: httpResponse = {
            mensaje: "Datos incorrectos, debe anular el pago",
            codigo: 0,
            estado: 0,
        };

        if (!this.validDataPay(dataPago)) {
            return responseData;
        }

        try {
            // RESPUESTA DEL SERVICIO 
            let sendMid = await this.exportPagosRefact(dataPago);
            console.log("EXITOSO REGISTER LOCAL", sendMid);

            // CODIGO DE RECIBO REPETIDO -> GENERAR NUEVO CONDIGO EN BASE A LA NUMERACION RETORNADA
            if (sendMid.data.estado == 0) {
                console.log('ERROR EN EL FORMATE DE MID ');
                const userdata: any = await this.configService.getSession();
                dataPago.correlativo = Number(sendMid.data.numeracion) + 1;
                let nuevoCodigo = Calculo.generaCodeRecibo(userdata[0].idUsuario.toString(), String(dataPago.correlativo), '1');
                dataPago.nro_recibo = nuevoCodigo;
                modelPagos.insert(dataPago, statusPay, nuevoCodigo);
                let response: httpResponse = {
                    mensaje: "Pago con recibo duplicado, debe enviar manualmente desde PAGOS RECIBIDOS.",
                    codigo: 200,
                    estado: 0,
                }
                responseData = response;
            } else {
                if (sendMid.data.estado == 2) {
                    let response: httpResponse = {
                        mensaje: sendMid.data.mensaje,
                        codigo: 200,
                        estado: 0,
                    }
                    responseData = response;
                }
                if (sendMid.data.estado == 3) {
                    statusPay = sendMid.data.estado;
                    // sendMid.data.mensaje;
                    // sendMid.data.numeracion;
                    // sendMid.data.recibo; //registro
                    modelPagos.insert(dataPago, statusPay, dataPago.nro_recibo);
                    responseData = sendMid;
                }
            }
            this.spinnerDialog.hide();
        } catch (error) {
            // NO SE PUDO CONECTAR CON EL SERVIDOR GUARDAR PAGO EN EL MOVIL CON ESTADO CERO
            statusPay = 0;
            console.log("error try ", error);
            modelPagos.insert(dataPago, statusPay, dataPago.nro_recibo);
            responseData = error;
            this.spinnerDialog.hide();
        }
        return responseData;
    }

    /**
     * EXPORTAR PAGO 1 A 1 
     */

    public async exportPagosRefact(data: IPagos): Promise<any> {
        console.log("exportPagosRefact() ", data)
        return new Promise(async (resolve, reject) => {
            try {
                let id: any = await this.configService.getSession();

                if (this.network.type != 'none') {
                    console.log("con conexion");

                } else {
                    console.log("sin conexion")

                }

                // let exporExit: any = this.http.setDataSerializer('json');
                this.path = await this.configService.getIp();
                console.log("pat2", data);
                this.http.post(this.path + 'v2/pagosmoviles', data, {}).then((data: any) => {
                    console.log("pago enviado a mid ", data);
                    let responseMid = JSON.parse(data.data);
                    console.log("responseMid ", responseMid);

                    let response: httpResponse = {
                        mensaje: "Registro exitoso.",
                        codigo: 200,
                        estado: 1,
                        data: responseMid.respuesta ? responseMid.respuesta : responseMid.mensaje
                    };
                    resolve(response);
                }).catch((error: any) => {
                    console.log("error al enviar a mid ", error);
                    let response: httpResponse = {
                        mensaje: "1. Ocurrió un error al intentar comunicarse con el servidor.",
                        codigo: 500,
                        estado: 0,
                    };
                    reject(response);
                });

            } catch (e) {
                let response: httpResponse = {
                    mensaje: "2. Ocurrió un error al intentar comunicarse con el servidor.",
                    codigo: 500,
                    estado: 0,
                }
                reject(response);
            }
        })
    }

    /**
     * REENVIAR PAGO QUE SOLO SE REGISTRO EN EL MOVIL ESTADO CERO
     * Params dataPago
     */
    async resendPayService(dataPago: IPagos): Promise<httpResponse> {
        const modelPagos = new Pagos();
        let objeto: any = await this.datacamposusuario(dataPago);
        dataPago.camposusuario = objeto;
        console.log("data resend ", dataPago);
        let statusPay = 0;
        this.spinnerDialog.show(undefined, undefined, true);

        let responseData: httpResponse = {
            mensaje: "",
            codigo: 0,
            estado: 0,
        };
        try {
            let sendMid = await this.exportPagosRefact(dataPago);
            console.log("EXITOSO REGISTER LOCAL", sendMid);

            if (sendMid.data.estado == 0) {

                console.log('ERROR EN EL FORMATE DE MID ');
                const userdata: any = await this.configService.getSession();

                dataPago.correlativo = Number(sendMid.data.numeracion) + 1;

                let nuevoCodigo = Calculo.generaCodeRecibo(userdata[0].idUsuario.toString(), String(dataPago.correlativo), '1');
                dataPago.nro_recibo = nuevoCodigo;

                modelPagos.insert(dataPago, statusPay, nuevoCodigo);
                let response: httpResponse = {
                    mensaje: "Pago con recibo duplicado, debe enviar manualmente desde PAGOS RECIBIDOS.",
                    codigo: 200,
                    estado: 0,
                }
                responseData = response;


            } else {
                statusPay = sendMid.data.estado;
                // sendMid.data.mensaje;
                // sendMid.data.numeracion;
                // sendMid.data.recibo; //registro

                modelPagos.updateEstadoPagoByRecibo(statusPay, dataPago.nro_recibo);
                let response: httpResponse = {
                    mensaje: "Pago enviado correctamente.",
                    codigo: 200,
                    estado: 3,
                }
                responseData = response;

            }
            this.spinnerDialog.hide();
        } catch (error) {
            statusPay = 0;
            // console.log("error try ", error);
            // modelPagos.insert(dataPago, statusPay, dataPago.nro_recibo);

            let response: httpResponse = {
                mensaje: "Error en el servidor.",
                codigo: 400,
                estado: 0,
            }
            responseData = response;
            this.spinnerDialog.hide();
        }
        return responseData;
    }

    /**
     * VALIDACIONES DE DATA COMPLETA ANTES DE ENVIAR A MIDLEWARE
     */

    validDataPay = (data: IPagos): boolean => {
        console.log("data a validar antes de enviar a mid ", data);
        if (data.mediosPago.length === 0) {
            return false;
        }
        if (data.otpp == 2) {
            if (data.facturaspago!.length === 0) {
                return false;
            }
        }
        if (data.nro_recibo != '' && data.nro_recibo != '0') {
            console.log("CODIGO DE RECIBO NO ENCONTRADO ");
            //return false;
            return true;
        }
        return true;
    }

    /**
 * SOLICITUD DE AUTORIZACION PARA ANULAR PAGOS CON FECHA POSTERIOR A LA DE HOY
 */
    cancelPayAuthorizationService = async (data: IPagos): Promise<boolean> => {
        return this.http.post(this.path + 'v2/AuthorizationCancel', data, {}).then((data: any) => {
            console.log("pago enviado a mid ", data);
            let responseMid = JSON.parse(data.data);
            console.log("responseMid ", responseMid);

            // let response: httpResponse = {
            //     mensaje: "Operación exitosa.",
            //     codigo: 200,
            //     estado: 1

            // };
            return true;
        }).catch((error: any) => {
            console.log("error al enviar a mid ", error);
            // let response: httpResponse = {
            //     mensaje: "1. Ocurrió un error al intentar comunicarse con el servidor.",
            //     codigo: 500,
            //     estado: 0,
            // };
            return false;
        });
    }

    /**
     * ANULACIONES DE PAGO 1 A 1
     */
    /* cancelPayService = async (data: IPagos): Promise<boolean> =>
    {
         

        this.path = await this.configService.getIp();
        
        console.log("ruta path ", this.path + 'v2/cancelarpago'); 
        console.log("data services ", data);
        console.log("data services "+JSON.stringify(data));
        
        
        return this.http.post(this.path + 'v2/cancelarpago', data, {}).then((data: any) => {
            console.log("pago enviado a mid ", data);
            let responseMid = JSON.parse(data.data);
            console.log("responseMid ", responseMid);

            // let response: httpResponse = {
            //     mensaje: "Operación exitosa.",
            //     codigo: 200,
            //     estado: 1

            // };
            return responseMid;
        }).catch((error: any) => {
            console.log("error al enviar a mid ", error);
            // let response: httpResponse = {
            //     mensaje: "1. Ocurrió un error al intentar comunicarse con el servidor.",
            //     codigo: 500,
            //     estado: 0,
            // };
            return false;
        });

        

    } */

    cancelPayService = async (data: IPagos) => {


        this.path = await this.configService.getIp();

        console.log("ruta path ", this.path + 'v2/cancelarpago');
        console.log("data services ", data);
        console.log("data services " + JSON.stringify(data));


        return this.http.post(this.path + 'v2/cancelarpago', data, {}).then((data: any) => {
            console.log("pago enviado a mid ", data);
            let responseMid = JSON.parse(data.data);
            console.log("responseMid ", responseMid);

            return responseMid;
        }).catch((error: any) => {
            console.log("error al enviar a mid ", error);

            return error;
        });



    }


    /*******
     * EXPORTAR PAGOS EN ESTADO CER
     */

    async exportPagosPendientes(dataPago: IPagos[]): Promise<httpResponse> {
        console.log("dataPago ", dataPago);
        let arrayResponses: any = [];
        for await (const item of dataPago) {

            let objeto: any = await this.datacamposusuario(item)
            item.camposusuario = objeto;

            console.log("each ", item);
            const modelPagos = new Pagos();
            let statusPay = 0;

            let sendMid = await this.exportPagosRefact(item);

            console.log("EXITOSO REGISTER LOCAL CON ESTADO DE MID ", sendMid);
            //{"estado":200,"respuesta":{"id":"105","estado":2,"anulado":0,"recibo":"101000300050","numeracion":0,"codigo":201,"registro":false,"mensaje":"\"Cannot add payment ....
            await modelPagos.updateEstadoPagoByRecibo(sendMid.data.estado, item.nro_recibo);
            arrayResponses.push(item.nro_recibo);

            if(sendMid.data.estado == 3){
                console.log("actualiza pago en documento",item);
                await modelPagos.updateSaldoFacturas2(item);
            }

        }

        let response: httpResponse = {
            mensaje: "Pago enviado correctamente.",
            codigo: 200,
            estado: 0,
            data: arrayResponses
        }

        return response;
    }

    public async getNumeracionpago() {
        this.pagosModel = new Pagos();
        return new Promise(async (resolve, reject) => {
            try {
                let dataNumeracion: any = await this.pagosModel.getNumeracion();
                console.log("dataNumeracion ", dataNumeracion);
                let numeracion = 0;
                if (dataNumeracion[0].numeracion > 0) {
                    numeracion = dataNumeracion[0].numeracion;
                } else {
                    dataNumeracion = 0;

                }
                resolve(numeracion);
            } catch (error) {
                console.log("error en momento de ", error);
                reject(0)
            }
        });
    }

    public async datacamposusuario(datos) {
        let data = [];
        let valor: any;
        let sesion = await this.configService.getSession();
        let camposusuario = sesion[0].campodinamicos;
        for (let i = 0; i < camposusuario.length; i++) {
            if (camposusuario[i].Objeto == this.idfrom) {

                let campo = "campousu" + camposusuario[i].Nombre;
                data.push({
                    Objeto: camposusuario[i].Objeto,
                    cmidd: camposusuario[i].cmidd,
                    tabla: camposusuario[i].tabla,
                    campo: campo,
                    valor: datos[campo]
                });
            }
        }
        return data;
    }
}