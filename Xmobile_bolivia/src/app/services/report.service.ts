import { Injectable } from '@angular/core';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import { FileTransfer, FileUploadOptions, FileTransferObject } from '@ionic-native/file-transfer/ngx';
import { Pagos } from "../models/pagos";
import { File } from '@ionic-native/file/ngx';
import { FileOpener } from '@ionic-native/file-opener/ngx';
import { Numeroletras } from "../models/numeroletras"
import { Calculo } from "../utilsx/calculo";
import { ConfigService } from "../models/config.service";
import { formatDate, formatNumber } from '@angular/common';
import { Bancos } from "../models/bancos";
import { Documentos } from '../models/documentos'
import { IDataPagoPdf } from '../types/IPagos';

pdfMake.vfs = pdfFonts.pdfMake.vfs;

@Injectable({
    providedIn: 'root'
})
export class ReportService {
    public layout;
    public pdfObj = null;
    public objPDF: any;
    public metadata: any;
    public footerx: any;
    public numeletra: Numeroletras;

    constructor(private file: File, private fileOpener: FileOpener, private transfer: FileTransfer, private configService: ConfigService) {
        this.numeletra = new Numeroletras();
        this.metadata = {
            title: 'Documeto SAP',
            author: 'Exxis - Bolivia',
            subject: 'Bolivia',
            keywords: 'Este documento solo sirve de referencia.',
        };
        this.footerx = {
            columns: [{ text: '(SAP)Exxis - Bolivia', alignment: 'center' }]
        };
        this.layout = {
            exampleLayout: {
                hLineWidth: (i, node) => {
                    if (i === 0 || i === node.table.body.length) {
                        return 0;
                    }
                    return (i === node.table.headerRows) ? 2 : 1;
                },
                vLineWidth: (i) => {
                    return 0;
                },
                hLineColor: (i) => {
                    return i === 1 ? 'black' : '#aaa';
                },
                paddingLeft: (i) => {
                    return i === 0 ? 0 : 8;
                },
                paddingRight: (i, node) => {
                    return (i === node.table.widths.length - 1) ? 0 : 8;
                }
            }
        };

    }

    public async generateEXE(name: string) {
        console.log("PDFMake", this.pdfObj);
        this.pdfObj = pdfMake.createPdf(this.objPDF, this.layout);
        let archivoPDF = `${name}`;
        this.pdfObj.getBuffer(async (buffer) => {
            let blob = new Blob([buffer], { type: 'application/pdf' });
            await this.file.writeFile(this.file.externalApplicationStorageDirectory, archivoPDF, blob, { replace: true });
            this.fileOpener.open(this.file.externalApplicationStorageDirectory + archivoPDF, 'application/pdf');
        });
    }

    public async generarecibo(monto: any, cliente: any, tipo = false, txt: any, datauser: any) {
        window.parent.caches.delete("call");
        console.group();
        console.log("datauser", datauser);
        console.log("monto", monto);
        console.log("cliente", cliente);
        console.log("pagos", txt);
        console.groupEnd();

        let bombrebanco: string = '';
        if (typeof monto.bancoCode != "undefined" && monto.bancoCode != '') {
            let bancos = new Bancos();
            let fx: any = await bancos.findOne();
            let bancosarr = [];
            for (let ii of fx) bancosarr.push({ "code": ii.cuenta, "name": ii.nombre });
            for (let i of datauser[0].gestionbancos) bancosarr.push({ "code": i.BankCode, "name": i.BankName });
            let marx = bancosarr.filter((item) => {
                return item.code == monto.bancoCode;
            });
            bombrebanco = marx[0].name;
        }
        if (!monto.currency) {
            monto.currency = monto.currencyAC;
            console.log("DEV currencyAC. ", monto.currencyAC);
        }
        return new Promise(async (resolve, reject) => {
            let fechahora = formatDate(monto.fecha+' '+monto.hora, 'dd/MM/yyyy HH:mm', 'en-US');
            try {
                let texto = '';
                let aux_texto = '';
                let detallepago = [];
                let auxtc = 0;
                if (monto.dx == "FACTURAS") {

                    texto = '\n Pago de Documento(s). \n';
                    if (txt.length > 0) {
                        let auxcabecera: any = {
                            columns: [{
                                text: ' \n Nro. Fac',
                                style: ['xsmall'],
                                width: '15%'

                            },
                            {
                                text: ' \n Cuota',
                                style: ['xsmall'],
                                width: '15%'

                            },
                            {
                                text: ' \n Total Doc.',
                                style: ['xsmall'],
                                width: '20%'

                            }, {
                                text: ' \n Pago',
                                style: ['xsmall'],
                                width: '20%'

                            }, {
                                text: ' \n Saldo',
                                style: ['xsmall'],
                                width: '20%'

                            }]
                        };
                        detallepago.push(auxcabecera);
                        for (let xpago of txt.facturas) {


                            // let documentosdata = new Documentos();
                            // let rta: any = await documentosdata.findOne(xpago.cod);
                            // console.log(rta);


                            let aux_texto: any = {
                                columns: [{
                                    text: xpago.nroFactura,
                                    style: ['xsmall'],
                                    width: '15%'
                                },
                                {
                                    text: xpago.cuota,
                                    style: ['xsmall'],
                                    alignment: 'right',
                                    width: '15%'
                                },
                                {
                                    text: Calculo.formatMoney(xpago.DocTotal),
                                    style: ['xsmall'],
                                    alignment: 'right',
                                    width: '20%'
                                }, {
                                    text: Calculo.formatMoney(xpago.pagarx),
                                    style: ['xsmall'],
                                    alignment: 'right',
                                    width: '20%'
                                }, {
                                    text: Calculo.formatMoney(xpago.saldo),
                                    style: ['xsmall'],
                                    alignment: 'right',
                                    width: '20%'
                                }]
                            };
                            detallepago.push(aux_texto);
                        }
                    }

                }
                auxtc = txt[0].tipo_cambio;
                if (monto.tipo == "factura") {

                    if (txt.length > 0) {
                        let auxcabecera: any = {
                            columns: [{
                                text: ' \n Documento',
                                style: ['xsmall'],
                                width: '40%'

                            },
                            {
                                text: ' \n Total Doc.',
                                style: ['xsmall'],
                                width: '30%'

                            }, {
                                text: ' \n Pago',
                                style: ['xsmall'],
                                width: '30%'

                            }]
                        };
                        detallepago.push(auxcabecera);

                        let aux_texto: any = {
                            columns: [{
                                text: txt[0].documentoId,
                                style: ['xsmall'],
                                width: '40%'
                            },
                            {
                                text: Calculo.formatMoney(txt[0].monto),
                                style: ['xsmall'],
                                alignment: 'right',
                                width: '30%'
                            }, {
                                text: Calculo.formatMoney(txt[0].monto),
                                style: ['xsmall'],
                                alignment: 'right',
                                width: '30%'
                            }]
                        };
                        detallepago.push(aux_texto);

                    }
                }
                if (monto.tipo == "cuenta") {
                    texto = ' Recepción de anticipo';

                }
                let aux_firmas: any = [
                    {
                        text: '------------------------------------',
                        style: ['small'],
                        alignment: 'center',
                    },
                    {
                        text: 'Recibí conforme',
                        style: ['small'],
                        alignment: 'center',
                    },
                    '\n',
                    {
                        text: '------------------------------------',
                        style: ['small'],
                        alignment: 'center',
                    },
                    {
                        text: 'Entregué conforme',
                        style: ['small'],
                        alignment: 'center',
                    }
                ];
                let copia: object;
                let num_imp = 0;
                if (monto.reimpresion) {
                    num_imp = monto.reimpresion[0]["contador"];
                }
                if (num_imp > 1) {
                    num_imp = num_imp - 1;
                    copia = {
                        text: 'Copia # ' + num_imp + ' de Original',
                        alignment: 'center',
                        style: ['small']
                    };
                }
                let montoDolarExtra2;
                let montoDolarExtra;

                let tipopago = '';
                switch (monto.otpp) {
                    case ('Tarjeta'):
                        montoDolarExtra = '';
                        montoDolarExtra2 = '';
                        tipopago = 'TARJETA \n Baucher: ' + txt[0].baucher;
                        break;
                    case (3):
                        tipopago = 'EFECTIVO';

                        if (txt[0].monedaDolar > 0) {
                            let DocumentTotalPay;
                            if (monto.tipo == "cuenta") {
                                DocumentTotalPay = txt[0].monto;
                            } else {
                                DocumentTotalPay = txt[0].DocumentTotalPay;
                            }

                            console.log("txt[0].cambio ", txt[0].cambio);
                            console.log("txt[0].DocumentTotalPay ", DocumentTotalPay);
                            console.log(Number(txt[0].cambio) + Number(DocumentTotalPay));
                            let resAux = (Number(txt[0].cambio) + Number(DocumentTotalPay)) - Number(txt[0].monedaDolar * txt[0].tipoCambioDolar);
                            console.log("(Number(txt[0].cambio) + Number(DocumentTotalPay))  ", (Number(txt[0].cambio) + Number(DocumentTotalPay)));
                            console.log("-  Number(txt[0].monedaDolar * txt[0].tipoCambioDolar) ", Number(txt[0].monedaDolar * txt[0].tipoCambioDolar))

                            montoDolarExtra = {
                                text: 'Pago en USD : ' + txt[0].monedaDolar + '  (' + Calculo.formatMoney((txt[0].monedaDolar * txt[0].tipoCambioDolar).toFixed(2)) + ' Bs.)',
                                style: ['xsmall']
                            };

                            montoDolarExtra2 = {
                                text: 'Pago en Bs : ' + Calculo.formatMoney(resAux.toFixed(2)) + '',
                                style: ['xsmall']
                            };
                        } else {
                            montoDolarExtra = '';
                            montoDolarExtra2 = '';
                        }
                        break;
                    case ('Cheque'):
                        montoDolarExtra = '';
                        montoDolarExtra2 = '';
                        tipopago = 'CHEQUE \n # Cheque: ' + txt[0].numCheque + ' \n  Banco: (' + txt[0].bancoCode + ') ' + bombrebanco;
                        break;
                    case ('Transferencia'):
                        montoDolarExtra = '';
                        montoDolarExtra2 = '';
                        tipopago = 'TRANSFERENCIA \n # Trans: ' + txt[0].numComprobante + ' \n  Banco Code: (' + txt[0].bancoCode + ') ' + bombrebanco;
                        break;
                }
                let $hoy = new Date();
                let $aux_hoy = formatDate($hoy, 'dd/MM/yyyy HH:mm', 'en-US');
                this.objPDF = {
                    pageSize: { width: 80, height: 'auto' },
                    pageMargins: [5, 5, 5, 10],
                    content: [
                        {
                            text: `${datauser[0].empresa[0].nombre}`,
                            alignment: 'center',
                            style: ['header']
                        },
                        {
                            text: `${datauser[0].empresa[0].ciudad} - ${datauser[0].empresa[0].pais} `,
                            alignment: 'center',
                            style: ['header']
                        },
                        {
                            text: '\n RECIBO DE PAGO',
                            alignment: 'center',
                            style: ['header']
                        },
                        {
                            text: ' Nro:' + txt[0].nro_recibo,
                            alignment: 'center',
                            style: ['header']
                        },

                        {
                            text: `${datauser[0].empresa[0].actividad}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        copia,
                        '\n',
                        {
                            columns: [
                                {
                                    text: 'Recibí de : ',
                                    style: ['small'],
                                },
                                {
                                    text: cliente.CardName,
                                    style: ['small'],
                                    width: '70%',
                                    alignment: 'right',
                                }
                            ]
                        },

                        {
                            columns: [
                                {
                                    text: 'El Monto de  : ',
                                    style: ['small']
                                },
                                {
                                    text: `${Calculo.formatMoney(txt[0].monto)} ${monto.moneda}.`,
                                    style: ['small'],
                                    width: '70%',
                                    alignment: 'right',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'Son : ' + this.numeletra.run(txt[0].monto, monto.moneda) + '',// + datauser[0].config[0].moneda,
                                    style: ['small']
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'Por concepto de : ' + texto,
                                    style: ['small'],

                                }
                            ]
                        },

                        detallepago,
                        '\n',
                        {
                            text: 'Tipo de Pago : ' + tipopago,
                            style: ['xsmall']
                        },
                        {
                            text: 'TC : ' + monto.tipo_cambio,
                            style: ['xsmall']
                        },

                        montoDolarExtra,
                        montoDolarExtra2,
                        {
                            text: 'CAMBIO : ' + Calculo.formatMoney(txt[0].cambio) + ' Bs.',
                            style: ['xsmall']
                        }
                        ,

                        {
                            text: 'COD.VENDEDOR: ' + datauser[0].config[0].codEmpleadoVenta + ' -' + datauser[0].config[0].nombrecliente,
                            style: ['xsmall']
                        },
                        {
                            text: 'USUARIO: ' + +'' + datauser[0].idUsuario + ' - ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona,

                            style: ['xsmall']
                        },
                        {
                            text: 'ASESOR VTA.: ' + datauser[0].config[0].codEmpleadoVenta + ' -' + datauser[0].config[0].nombrecliente,
                            style: ['xsmall'],
                        },
                        {
                            text: 'COD. CLIENTE: ' + cliente.CardCode,
                            style: ['xsmall'],
                        },
                       /* {
                            text: 'ESTADO CTA. : ' + `${Calculo.formatMoney(cliente.CurrentAccountBalance)} ${monto.moneda}.`,
                            style: ['xsmall'],
                        },*/
                        {
                            text: 'Fecha :' + fechahora,
                            style: ['xsmall'],
                        },
                        '\n',
                        '\n',
                        '\n',
                        aux_firmas,
                        '\n',
                        '\n',
                        /* {
                             text: `${datauser[0].empresa[0].direccion}\n` + $aux_hoy,
                             alignment: 'center',
                             style: ['small']
                         },
                         */
                    ],
                    styles: {
                        header: {
                            fontSize: 5,
                            bold: true
                        },
                        number: {
                            fontSize: 4
                        },
                        small: {
                            fontSize: 3
                        },
                        xsmall: {
                            fontSize: 2.5
                        }
                    }
                };
                resolve(true);
            } catch (e) {
                reject(e);
            }
        });
    }

    public async generareciboV2(dataPagoPdf: IDataPagoPdf, datauser: any) {
        window.parent.caches.delete("call");
        console.group();
        console.log("datauser", datauser);
        console.log("dataPago", dataPagoPdf);
        console.log(dataPagoPdf.dataPago.mediosPago[0].bancoCode);
        let bombrebanco: string = '';
        let nombrebanco: string = '';
        let nombre_cliente = "";
        
     
        

        if (dataPagoPdf.dataPago.mediosPago[0].bancoCode != '') {
            if(dataPagoPdf.dataPago.mediosPago[0].formaPago == "PCH"){

                for await (let gestionbancos of datauser[0].gestionbancos) {

                    if(dataPagoPdf.dataPago.mediosPago[0].bancoCode == gestionbancos.BankCode){ 
                        console.log("bancos",gestionbancos.BankName);
                        bombrebanco = gestionbancos.BankName
                    }
                }
            }else{
                let bancos = new Bancos();
                let fx2: any = await bancos.find();
                let fx: any = await bancos.findOnecuenta(dataPagoPdf.dataPago.mediosPago[0].bancoCode);
                if (fx.length > 0) {
                    bombrebanco = fx[0].codigo;
                    nombrebanco = fx[0].nombre;
                }
            }
        }

        console.groupEnd();
        if (!dataPagoPdf.dataPago.moneda) {
            dataPagoPdf.dataPago.moneda = dataPagoPdf.dataPago.moneda;
            console.log("DEV currencyAC. ", dataPagoPdf.dataPago.moneda);
        }
        return new Promise(async (resolve, reject) => {
            //monto.hora

            let fecha = dataPagoPdf.dataPago.fecha.toString() +' '+ dataPagoPdf.dataPago.hora.toString() 
            console.log("la fecha es ", fecha);
            let fechahora = formatDate(fecha , 'dd/MM/yyyy HH:mm', 'en-US');
            try {
                let texto = '';
                let aux_texto = '';
                let detallepago = [];
                let detallevendedor = [];
                let auxtc = 0;
                // dataPagoPdf.dataPago.tipo == "facturas"

                if (dataPagoPdf.dataPago.otpp == 1) {
                    nombre_cliente = dataPagoPdf.dataPago.razon_social;
                }
                if (dataPagoPdf.dataPago.otpp == 2) {

                    if(dataPagoPdf.dataCliente){
                        nombre_cliente = dataPagoPdf.dataCliente.CardName;
                    }else{
                        nombre_cliente = dataPagoPdf.dataPago.razon_social;
                    }

                    console.log("PRUEBA RAFAEL");
                   
                    texto = '\n Pago de Documento(s). \n';
                    if (dataPagoPdf.dataPago.facturaspago.length > 0) {

                        let auxcabecera: any = {
                            columns: [{
                                text: ' \n Nro. Fac',
                                style: ['xsmall'],
                                width: '40%',
                                alignment: 'center',

                            },
                            {
                                text: ' \n Total Doc.',
                                style: ['xsmall'],
                                width: '20%',
                                alignment: 'center',

                            }, {
                                text: ' \n Pago',
                                style: ['xsmall'],
                                width: '20%',
                                alignment: 'center',

                            }, {
                                text: ' \n Saldo',
                                style: ['xsmall'],
                                width: '20%',
                                alignment: 'center'

                            }]
                        };
                        detallepago.push(auxcabecera);

                        for (let xpago of dataPagoPdf.dataPago.facturaspago) {


                            let documentosdata = new Documentos();
                            let rta: any = await documentosdata.findOne(xpago.documentoId);
                            console.log(rta);
                            let numfact = '';
                            if(rta.length > 0 && Number(rta[0].codexternal) > 0){
                                numfact = rta[0].codexternal;
                            }else{
                                numfact = xpago.nroFactura;
                            } 

                            let aux_texto: any = {
                                columns: [{
                                    text: numfact,
                                    style: ['xsmall'],
                                    alignment: 'center',
                                    width: '40%'
                                },
                                {
                                    text: Calculo.formatMoney(xpago.DocTotal),
                                    style: ['xsmall'],
                                    alignment: 'right',
                                    width: '20%'
                                }, {
                                    text: Calculo.formatMoney(xpago.monto),
                                    style: ['xsmall'],
                                    alignment: 'right',
                                    width: '20%'
                                }, {
                                    text: Calculo.formatMoney(xpago.saldo),
                                    // text: Calculo.formatMoney((Number(xpago.DocTotal) - Number(xpago.monto))),
                                    style: ['xsmall'],
                                    alignment: 'right',
                                    width: '20%'
                                }]
                            };

                            let aux_texto2: any = {
                                columns: [{
                                    text: '',
                                    style: ['xsmall'],
                                    alignment: 'center',
                                    width: '40%'
                                },
                                {
                                    text: ' \n Cuota:'+xpago.cuota,
                                    style: ['xsmall'],
                                    alignment: 'letf',
                                    width: '20%'
                                }, {
                                    text: ' \n Vendedor:'+xpago.vendedor,
                                    style: ['xsmall'],
                                    alignment: 'letf',
                                    width: '40%'
                                }]
                            };

                            detallepago.push(aux_texto);
                            detallepago.push(aux_texto2);
                        }
                    }

                }
                if (dataPagoPdf.dataPago.otpp == 3) {
                    if(dataPagoPdf.dataCliente){
                        nombre_cliente = dataPagoPdf.dataCliente.CardName;
                    }else{
                        nombre_cliente = dataPagoPdf.dataPago.razon_social;
                    }
                }

                let usuariodata: any = await this.configService.getSession();

                if (usuariodata[0].campodinamicos.length > 0){
                    let pagoModel = new Pagos();
                    let dataPagoExist: any = await pagoModel.selectAllcabezerapagosByRecibo(dataPagoPdf.dataPago.nro_recibo);  // by idRecibo
                    console.log("datos de la consulta",dataPagoExist);
                    console.log("OTPP",dataPagoPdf.dataPago.otpp);
                    
                    for (let i = 0; i < usuariodata[0].campodinamicos.length; i++) {
                        if (usuariodata[0].campodinamicos[i].Objeto == 6){
                            
                            let documento = usuariodata[0].campodinamicos[i].documento;
                            if(documento.indexOf(dataPagoPdf.dataPago.otpp) >= 0){
                                console.log("datos de los campos",usuariodata[0].campodinamicos[i]); 

                                let titulo = usuariodata[0].campodinamicos[i].Label;
                                let campo = "campousu"+usuariodata[0].campodinamicos[i].Nombre;
                                let detalle = '';

                                if(usuariodata[0].campodinamicos[i].tipocampo == 1){
                                    for await (let lista of usuariodata[0].campodinamicos[i].lista) {
                                        if(lista.codigo == dataPagoExist[0][campo]){
                                            detalle = lista.nombre;
                                        }
                                    }
                                }else{
                                    detalle = dataPagoExist[0][campo];
                                }

                                let aux: any = {
                                    columns: [{
                                        text: titulo+'.: '+ detalle,
                                        style: ['xsmall'],
                                    }]
                                };
                                detallevendedor.push(aux);

                            }
                        }
                    } 
                }


                //auxtc = txt[0].tipoCambioDolar;
                auxtc = dataPagoPdf.dataPago.tipo_cambio;
                if (dataPagoPdf.dataPago.tipo == "factura") {
                    texto = ' Pago de factura';
                    if (dataPagoPdf.dataPago) {
                        let auxcabecera: any = {
                            columns: [{
                                text: ' \n Documento',
                                style: ['xsmall'],
                                width: '40%'

                            },
                            {
                                text: ' \n Total Doc.',
                                style: ['xsmall'],
                                alignment: 'right',
                                width: '30%'

                            }, {
                                text: ' \n Pago',
                                style: ['xsmall'],
                                alignment: 'right',
                                width: '30%'

                            }]
                        };
                        detallepago.push(auxcabecera);

                        let aux_texto: any = {
                            columns: [{
                                text: dataPagoPdf.dataPago.documentoId,
                                style: ['xsmall'],
                                width: '40%'
                            },
                            {
                                text: Calculo.formatMoney(dataPagoPdf.dataPago.monto_total),
                                style: ['xsmall'],
                                alignment: 'right',
                                width: '30%'
                            }, {
                                text: Calculo.formatMoney(dataPagoPdf.dataPago.monto_total),
                                style: ['xsmall'],
                                alignment: 'right',
                                width: '30%'
                            }]
                        };
                        detallepago.push(aux_texto);

                    }
                }

                if (dataPagoPdf.dataPago.tipo == "cuenta") {
                    texto = ' Recepción de anticipo';

                }
                if (dataPagoPdf.dataPago.tipo == "deuda") {
                    texto = 'Pago de facturas';

                }
                let aux_firmas: any = [
                    {
                        text: '------------------------------------',
                        style: ['small'],
                        alignment: 'center',
                    },
                    {
                        text: 'Recibí conforme',
                        style: ['small'],
                        alignment: 'center',
                    },
                    '\n',
                    {
                        text: '------------------------------------',
                        style: ['small'],
                        alignment: 'center',
                    },
                    {
                        text: 'Entregué conforme',
                        style: ['small'],
                        alignment: 'center',
                    }
                ];
                let copia: object;
                let num_imp = 0;
                /* if (monto.reimpresion) {
                    num_imp = monto.reimpresion["contador"];
                } */
                /* if (num_imp > 1) {
                    num_imp = num_imp - 1;
                    copia = {
                        text: 'Copia # ' + num_imp + ' de Original',
                        alignment: 'center',
                        style: ['small']
                    };
                } */
                let montoDolarExtra2;
                let montoDolarExtra;
                let montopagolocal;

                let tipopago = '';
                console.log("data pago", dataPagoPdf.dataPago.mediosPago)
                for await (let mediosPago of dataPagoPdf.dataPago.mediosPago){

                    switch (mediosPago.formaPago) {
                        case ('PCC'):
                            montoDolarExtra = '';
                            montoDolarExtra2 = '';
                            montopagolocal = '';
                            tipopago = tipopago+ 'TARJETA \n Baucher: ' + mediosPago.baucher +'\n\n';
                            break;
                        case ('PEF'):
                            tipopago = tipopago+'EFECTIVO'+'\n\n';

                            if (dataPagoPdf.dataPago.moneda != 'BS') {
                                let DocumentTotalPay;
                                if (dataPagoPdf.dataPago.tipo == "cuenta") {
                                    DocumentTotalPay = dataPagoPdf.dataPago.monto_total;
                                } else {
                                    DocumentTotalPay = dataPagoPdf.dataPago.monto_total;
                                }

                                console.log("txt[0].cambio ", dataPagoPdf.dataPago.tipo_cambio);
                                console.log("txt[0].DocumentTotalPay ", DocumentTotalPay);
                                console.log(Number(dataPagoPdf.dataPago.tipo_cambio) + Number(DocumentTotalPay));
                                let resAux = 0;
                                
                                montoDolarExtra = {
                                    text: 'Pago en USD  0',
                                    style: ['xsmall']
                                };

                                montoDolarExtra2 = {
                                    text: 'Pago en Bs : ' + Calculo.formatMoney(resAux.toFixed(2)) + '',
                                    style: ['xsmall']
                                };
                            } else {
                                if(mediosPago.monedaDolar > 0 ){
                                    montoDolarExtra = {
                                        text: 'Pago en USD  '+mediosPago.monedaDolar+'\n',
                                        style: ['xsmall']
                                    };
                                    montopagolocal = {
                                        text: 'Pago en Bs : '+mediosPago.monedaLocal+'\n',
                                        style: ['xsmall']
                                    };
                                }else{
                                    montoDolarExtra = '';
                                    montoDolarExtra2 = '';
                                }
                                
                            }
                            break;
                        case ('PCH'):
                            montoDolarExtra = '';
                            montoDolarExtra2 = '';
                            tipopago = tipopago+'CHEQUE \n # Cheque: ' + mediosPago.numCheque + ' \n  Banco: (' + mediosPago.bancoCode + ') ' + bombrebanco +'\n\n';
                            break;
                        case ('PBT'):
                            montoDolarExtra = '';
                            montoDolarExtra2 = '';
                            tipopago = tipopago+'TRANSFERENCIA \n # Trans: ' + mediosPago.numComprobante + ' \n  Banco Code: (' + mediosPago.bancoCode + ') ' + bombrebanco + ' \n  Banco: ' + nombrebanco +'\n\n';
                            break;
                    }

                }
                let $hoy = new Date();
                let $aux_hoy = formatDate($hoy, 'dd/MM/yyyy HH:mm', 'en-US');
                this.objPDF = {
                    pageSize: { width: 80, height: 'auto' },
                    pageMargins: [5, 5, 5, 10],
                    content: [
                        {
                            text: `${datauser[0].empresa[0].nombre}`,
                            alignment: 'center',
                            style: ['subheader']
                        },
                        {
                            text: `${datauser[0].empresa[0].ciudad} - ${datauser[0].empresa[0].pais} `,
                            alignment: 'center',
                            style: ['subheader']
                        },
                        {
                            text: '\n RECIBO DE PAGO',
                            alignment: 'center',
                            style: ['subheader']
                        },
                        {
                            text: ' Nro:' + dataPagoPdf.dataPago.nro_recibo,
                            alignment: 'center',
                            style: ['subheader']
                        },

                        /* {
                            text: `${datauser[0].empresa[0].actividad}`,
                            alignment: 'center',
                            style: ['small']
                        }, */
                        //copia,
                        {
                            text: ' ',
                            bold: true,
                            style: ['small'],
                        },
                        {
                            columns: [
                                {
                                    text: 'Recibí de : ',
                                    bold: true,
                                    style: ['small'],
                                },
                                {
                                    text: nombre_cliente,
                                    style: ['small'],
                                    width: '70%',
                                    alignment: 'right',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'Tipo de Pago : ',

                                    bold: true,
                                    style: ['small']
                                },
                                {
                                    text: `${tipopago}.`,
                                    style: ['small'],
                                    //width: '70%',
                                    width: '70%',
                                    alignment: 'right',
                                }
                            ]
                        },


                        {
                            columns: [
                                {
                                    text: 'El Monto de  : ',
                                    bold: true,
                                    style: ['small']
                                },
                                {
                                    text: `${Calculo.formatMoney(dataPagoPdf.dataPago.monto_total)} ${dataPagoPdf.dataPago.moneda}.`,
                                    style: ['small'],
                                    width: '70%',
                                    alignment: 'right',
                                }
                            ]
                        },

                        {
                            columns: [
                                {
                                    text: 'Por concepto de : ',
                                    bold: true,
                                    style: ['small'],

                                },
                                {
                                    text: texto,
                                    style: ['small'],
                                    width: '60%',
                                    alignment: 'right',

                                }
                            ]
                        },

                        detallepago,
                        

                        {
                            text: '\n',// + datauser[0].config[0].moneda,
                            style: ['small']
                        },

                        {
                            columns: [
                                {
                                    text: 'Son : ' + this.numeletra.run(dataPagoPdf.dataPago.monto_total, dataPagoPdf.dataPago.moneda) + '',// + datauser[0].config[0].moneda,
                                    style: ['small']
                                }
                            ]
                        },

                        {
                            text: 'TC : ' + dataPagoPdf.dataPago.tipo_cambio,
                            style: ['xsmall']
                        },

                          montoDolarExtra,
                         montoDolarExtra2, 
                         montopagolocal,
                        {
                            text: 'CAMBIO : ' + Calculo.formatMoney(dataPagoPdf.dataPago.mediosPago[0].cambio) + ' Bs.',
                            style: ['xsmall']
                        } 
                        ,

                        {
                            text: 'COD. CLIENTE: ' + dataPagoPdf.dataPago.cliente_carcode,
                            style: ['xsmall'],
                        },
                        // {
                        //     text: 'ESTADO CTA. : ' + `${Calculo.formatMoney(dataPagoPdf.dataCliente.CurrentAccountBalance)} ${dataPagoPdf.dataPago.moneda}.`,
                        //     style: ['xsmall'],
                        // },
                        {
                            text: 'USUARIO: ' + +'' + datauser[0].idUsuario + ' - ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona,

                            style: ['xsmall']
                        },
                        {
                            text: 'Fecha :' + fechahora,
                            style: ['xsmall'],
                        },
                        {
                            text: '\n',
                            style: ['small']
                        },
                      
                        detallevendedor,

                        '\n',
                        aux_firmas,
                        '\n',
                        '\n',
                        /* {
                             text: `${datauser[0].empresa[0].direccion}\n` + $aux_hoy,
                             alignment: 'center',
                             style: ['small']
                         },
                         */
                    ],
                    styles: {
                        header: {
                            fontSize: 5,
                            bold: true
                        },
                        subheader: {
                            fontSize: 4,
                            bold: true
                        },
                        number: {
                            fontSize: 4
                        },
                        small: {
                            fontSize: 3
                        },
                        xsmall: {
                            fontSize: 2.5
                        }
                    }
                };
                resolve(true);
            } catch (e) {
                reject(e);
            }
        });
    }

    public docFactura(cliente: any, pedido: any, detalles: any, userdata: any, contador: any) {
        window.parent.caches.delete("call");
        console.group("DATA FACTURA XXXXXXXXXXXXXXXXXX");
        console.log("CLIENTE", cliente);
        console.log("PEDIDO", pedido);
        console.log("DETALLES", detalles);
        console.log("userdata", userdata);
        console.log("userdata", userdata[0].docificacion);
        console.groupEnd();
        let U_Leyenda = '';
        let U_NumeroSiguiente = '';
        let U_LB_NumeroFactura = '';
        let U_LB_NumeroAutorizac = '';
        let U_FechaLimiteEmision = '';
        let U_actividad = '';
        let aux_totalesnetos = 0;
        try {
            let docixfix: any = userdata[0].docificacion;
            console.log("docixfix ", docixfix);
            console.log("pedido.U_LB_NumeroAutorizac ", pedido.U_LB_NumeroAutorizac);
            let greaterTenxx = docixfix.filter((dxa) => dxa.U_NumeroAutorizacion == pedido.U_LB_NumeroAutorizac);
            console.log("greaterTenxx ", greaterTenxx);
            if (typeof greaterTenxx !== 'undefined') {
                console.log("SSSSS", greaterTenxx);
                U_Leyenda = greaterTenxx[0].U_Leyenda;
                U_NumeroSiguiente = pedido.U_LB_NumeroFactura;
                U_LB_NumeroFactura = pedido.U_LB_NumeroFactura;
                U_LB_NumeroAutorizac = greaterTenxx[0].U_NumeroAutorizacion;
                U_FechaLimiteEmision = greaterTenxx[0].U_FechaLimiteEmision;
                U_actividad = greaterTenxx[0].U_Actividad;
            } else {
                U_Leyenda = '';
                U_NumeroSiguiente = '';
                U_LB_NumeroFactura = '';
                U_LB_NumeroAutorizac = '';
                U_FechaLimiteEmision = '';
                U_actividad = '';
            }
        } catch (e) {
            console.log("ERROR AL OBTENER NUMERO DOSIFICACION");
            U_Leyenda = '';
            U_NumeroSiguiente = '';
            U_LB_NumeroFactura = '';
            U_LB_NumeroAutorizac = '';
            U_FechaLimiteEmision = '12-12-12';
            U_actividad = '';
        }

        return new Promise((resolve, reject) => {
            let d = pedido.DocDate; // DocDueDate
            let dr = d.split('-');
            //let rqx: string = `${pedido.U_4NIT}|${pedido.U_LB_NumeroFactura}|${pedido.U_LB_NumeroAutorizac}|${dr[2] + '/' + dr[1] + '/' + dr[0]}|${pedido.DocumentTotal}|${pedido.DocumentTotalPay}|${pedido.U_LB_CodigoControl}|${cliente.FederalTaxId}|0|0|0|0|`;
            let rqx: string = '';//`${userdata[0].empresa[0].nit}|${pedido.U_LB_NumeroFactura}|${pedido.U_LB_NumeroAutorizac}|${dr[2] + '/' + dr[1] + '/' + dr[0]}|${pedido.DocumentTotal}|${pedido.DocumentTotalPay}|${pedido.U_LB_CodigoControl}|${pedido.U_4NIT}|0|0|0|0|`;
            let qrx: any;
            let datafactura: object;
            let textfactura: object;
            let copia: object;
            let impuestos: object;
            let sum_imp = 0;
            let impuestosIce: object;
            let sum_impIce = 0;
            let impuestosIcep: object;
            let sum_impIcep = 0;
            let impuesto = 0;
            copia = {};
            impuestos = {};
            let num_imp = 0;
            if(contador.lenght >= 0){
                let num_imp = contador[0]["contador"];
             }else{
                num_imp = 0;
             }

            if (pedido.origen == 'outer') {
                qrx = {};
                textfactura = {};
            } else {
                qrx = {
                    qr: rqx,
                    alignment: 'center',
                    eccLevel: 'Q',
                    fit: '50'
                };
                textfactura = {
                    text: `\n\n"ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAIS. EL USO ILICITO
                    DE ESTA SERA SANCIONADO DE ACUERDO A LEY" \n\n\n\n` + U_Leyenda,
                    style: ['xsmall'],
                    alignment: 'center',
                };
            }
            if (num_imp > 1) {
                num_imp = num_imp - 1;
                copia = {
                    text: 'Copia # ' + num_imp + ' de Original',
                    alignment: 'center',
                    style: ['small']
                };
            }
            let cuerpodetalle = [];
            let aux_descripcion = '';
            let $hoy = new Date();
            let $aux_hoy = formatDate($hoy, 'dd/MM/yyyy HH:mm', 'en-US');
            let totalBonificacion = 0;
            let totalDescLinea = 0;
            let totalDescDocumento = 0;
            for (let detal of detalles) {
                let aux_precio: any = formatNumber(detal.LineTotalPay - detal.U_4DESCUENTO, 'en-US', '1.2-2');

                aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription;

                if (detal.bonificacion == 1) {
                    aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription + ' - ( Bonif. )';
                    totalBonificacion = totalBonificacion + detal.U_4DESCUENTO;//detal.LineTotalPay;
                }
                // if (detal.bonificacion == 2 || detal.bonificacion == 3) {
                //     aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription + ' - ( Desc. )';
                //     totalBonificacion = totalBonificacion + detal.U_4DESCUENTO;

                // }
                if (detal.U_4DESCUENTO > 0 && detal.bonificacion != 1) {
                    totalDescLinea = totalDescLinea + detal.U_4DESCUENTO;
                    //aux_precio = detal.LineTotalPay - detal.U_4DESCUENTO;
                };
                if (detal.productos.lenght > 0) {
                    aux_descripcion = detal.Quantity + ' - ' + detal.Dscription;
                    for (let prodcom of detal.productos) {
                        aux_descripcion += '\n   -- ' + prodcom.ItemName;
                    }
                }
                let aux_total = formatNumber(detal.Price, 'en-US', '1.2-2');
                let aux_totalneto = formatNumber(detal.Price * detal.Quantity, 'en-US', '1.2-2');
                aux_totalesnetos =+ aux_totalneto;

                let aux_descuento = formatNumber(detal.U_4DESCUENTO, 'en-US', '1.2-2');
                let icee = formatNumber(detal.ICEe, 'en-US', '1.2-2');

                let icep = formatNumber(detal.ICEp, 'en-US', '1.2-2');
                sum_imp = Number(sum_imp) + Number(detal.ICEe) + Number(detal.ICEp);
                sum_impIce = sum_impIce + Number(detal.ICEe);
                sum_impIcep = sum_impIcep + Number(detal.ICEp);

                // let detalle_linea = `${aux_descripcion}  \n PRECIO U: ${aux_total} BRUTO: ${aux_totalneto} DESC: ${aux_descuento}  \n NETO: ${aux_precio}`;
                // 2 COMPANEX
                // if (userdata[0].localizacion == "2") {
                //      detalle_linea += ` ICEE: ${Calculo.formatMoney(detal.ICEe)}  ICEP: ${Calculo.formatMoney(detal.ICEp)} ICET: ${Calculo.formatMoney(detal.ICEt)} `;
                //    impuesto += Number(detal.ICEt);
                //   }
                // 3 PARAGUAY
                //// if (userdata[0].localizacion == "3") {
                //      detalle_linea += ` IVA: ${Calculo.formatMoney(detal.ICEe)} `;
                //     impuesto += Number(detal.ICEe);
                // }
                //detalle_linea += '\n\n';
                let header: any;
                if (userdata[0].localizacion == 2) {
                    if (!pedido.DocCur) {
                        pedido.DocCur = "BS";
                    }
                    header = {

                        columns: [
                            {
                                text: `${aux_descripcion} \n PRECIO U: ${aux_total}  \n DESC: ${aux_descuento}  \n ICEE: ${icee} ICEP: ${icep} \n NETO: ${aux_precio}  \n\n`,
                                style: ['small'],
                                width: '70%'
                            }, {
                                text: `\n ${aux_totalneto} ${pedido.DocCur}.`,
                                style: ['small'],
                                alignment: 'right',

                            }
                        ]
                    }
                }
                cuerpodetalle.push(header);
            }

            let importeCF: any = 0;
            let descuentosBonos = 0;
            descuentosBonos = totalBonificacion + totalDescLinea;
            console.log("descuentosBonos ", descuentosBonos);
            let sumIces = Number(sum_impIce + sum_impIcep).toFixed(2);
            importeCF = Number((Number(pedido.DocumentTotalPay) - Number(sum_impIce + sum_impIcep))).toFixed(2);
            console.log("importeCF ", importeCF);
            if (Number(sumIces) > 0) {
                sumIces = sumIces
            }
            else {
                sumIces = "0";
            }
            console.log("convert descuentosBonos 1 ", descuentosBonos)
            descuentosBonos = Number(descuentosBonos.toFixed(2))
            console.log("convert descuentosBonos ", descuentosBonos)
            if (Number(descuentosBonos) > 0) {
                descuentosBonos = descuentosBonos;
            } else {
                descuentosBonos = 0;
            }
            rqx = `${userdata[0].empresa[0].nit}|${pedido.U_LB_NumeroFactura}|${pedido.U_LB_NumeroAutorizac}|${dr[2] + '/' + dr[1] + '/' + dr[0]}|${pedido.DocumentTotal}|${importeCF}|${pedido.U_LB_CodigoControl}|${pedido.U_4NIT}|${sumIces}|0|0|${descuentosBonos}|`;
            console.log("rqx ", rqx);
            if (qrx) {
                qrx.qr = rqx;
            }


            if (userdata[0].localizacion == 2) {


                impuestosIce = {
                    columns: [
                        {
                            text: ' \n TOTAL ICEE ',
                            style: ['small']
                        },
                        {
                            text: '\n  ' + Calculo.formatMoney(sum_impIce) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }
                impuestosIcep = {
                    columns: [
                        {
                            text: 'TOTAL ICEP ',
                            style: ['small']
                        },
                        {
                            text: Calculo.formatMoney(sum_impIcep) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }


                impuestos = {
                    columns: [
                        {
                            text: ' \n CREDITO FISCAL ',
                            style: ['small']
                        },
                        {
                            text: '\n ' + Calculo.formatMoney(0.13 * pedido.DocumentTotalPay) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }

            }
            /****Genera el documento***/
            try {
                this.objPDF = {
                    pageSize: { width: 80, height: 'auto' },
                    pageMargins: [6, 5, 5, 10],
                    content: [
                        {
                            text: `${userdata[0].empresa[0].nombre}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: `${U_LB_NumeroFactura}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: `${userdata[0].empresa[0].direccion}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: `${userdata[0].empresa[0].ciudad} - ${userdata[0].empresa[0].pais}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: 'FACTURA',
                            alignment: 'center',
                            style: ['header']
                        },
                        {
                            text: `ACTIVIDAD ECONÓMICA`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            //text: `${userdata[0].empresa[0].actividad}`,
                            text: `${U_actividad}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        copia,
                        '\n',
                        {
                            text: 'NIT: ' + userdata[0].empresa[0].nit,//solo debe ser nit
                            style: ['small'],
                            alignment: 'center'
                        },
                        {
                            text: 'FACTURA NRO: ' + U_LB_NumeroFactura,
                            style: ['small'],
                            alignment: 'center'
                        },
                        {
                            text: 'AUTORIZACION: ' + U_LB_NumeroAutorizac,
                            style: ['small'],
                            alignment: 'center'
                        },
                        '\n',
                        {
                            columns: [
                                {
                                    text: 'FECHA DOC:',
                                    style: ['small']
                                },
                                {
                                    text: formatDate(pedido.DocDate, 'dd/MM/yyyy', 'en-US'),
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'RAZÓN SOCIAL:',
                                    style: ['small'],
                                    width: '40%'
                                },
                                {
                                    text: pedido.U_4RAZON_SOCIAL,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'NIT/CI:',
                                    style: ['small']
                                },
                                {
                                    text: pedido.U_4NIT,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        },

                        {
                            columns: [
                                {
                                    text: '\n CANTIDAD PRODUCTO',
                                    style: ['small'],
                                    bold: true,
                                    width: '70%'
                                },
                                {
                                    text: '\n SUBTOTAL',
                                    style: ['small'],
                                    alignment: 'right',
                                    bold: true
                                }
                            ]
                        },
                        cuerpodetalle,
                        {
                            columns: [
                                {
                                    text: 'TOTAL BRUTO',
                                    style: ['small']
                                },
                                {
                                    text: Calculo.formatMoney(aux_totalesnetos) + ' ' + pedido.DocCur,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'DESC. BONIFICACION',
                                    style: ['small']
                                },
                                {
                                    text: Calculo.formatMoney(totalBonificacion) + ' ' + pedido.DocCur,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'DESCUENTOS',
                                    style: ['small']
                                },
                                {
                                    text: Calculo.formatMoney(totalDescLinea) + ' ' + pedido.DocCur,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'IMP. CRÉDITO FISCAL',
                                    style: ['small']
                                },
                                {
                                    text: Calculo.formatMoney(Number(pedido.DocumentTotalPay) - Number(sum_impIce + sum_impIcep)) + ' ' + pedido.DocCur,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        },
                        /*  {
                              columns: [
                                  {
                                      text: 'DESC. DOCUMENTO',
                                      style: ['small']
                                  },
                                  {
                                      text: '0.00' + ' ' + pedido.DocCur,
                                      style: ['small'],
                                      alignment: 'right',
                                  }
                              ]
                          },*/
                        //impuestos,
                        impuestosIce,
                        impuestosIcep,
                        {
                            columns: [
                                {
                                    text: 'TOTAL',
                                    style: ['small']
                                },
                                {
                                    text: Calculo.formatMoney(pedido.DocumentTotalPay) + ' ' + pedido.DocCur,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        },
                        {
                            text: '\n  SON: ' + this.numeletra.run(pedido.DocumentTotalPay, pedido.DocCur) + "",// + pedido.DocCur,
                            style: ['xsmall'],
                            alignment: 'left',
                        },
                        {
                            text: '\n CODIGO DE CONTROL: ' + pedido.U_LB_CodigoControl,
                            style: ['small']
                        },
                        {
                            text: 'FECHA LIMITE DE EMISION: ' + formatDate(U_FechaLimiteEmision, 'dd/MM/yyyy', 'en-US'),
                            style: ['small']
                        },
                        '\n',
                        qrx,
                        textfactura,

                        {
                            text: '\n TC.: ' + userdata[0].tipocambioparalelo.tipoCambio,
                            style: ['xsmall']

                        },
                        {
                            text: '\n NRO. DOC.: ' + pedido.cod,
                            style: ['xsmall']

                        },
                        {
                            text: 'OBSERVACIÓN: ' + pedido.comentario,
                            style: ['xsmall']
                        }, {
                            text: 'COD.VENDEDOR: ' + userdata[0].config[0].codEmpleadoVenta + ' -' + userdata[0].config[0].nombrecliente,
                            style: ['xsmall']
                        }, {
                            text: 'USUARIO: ' + userdata[0].idUsuario + ' - ' + userdata[0].nombrePersona + ' ' + userdata[0].apellidoPPersona + ' ' + userdata[0].apellidoMPersona + ' ',
                            style: ['xsmall']
                        }, {
                            text: 'CONDICION DE PAGO: Contado',
                            style: ['xsmall']
                        }, {
                            text: 'FECHA DE ENTREGA: ' + formatDate(pedido.DocDueDate, 'dd/MM/yyyy', 'en-US'),
                            style: ['xsmall']
                        },
                        /*{
                            text: userdata[0].config[0].codEmpleadoVenta + ' -' + userdata[0].config[0].nombrecliente,
                            style: ['xsmall'],
                        },
                        */
                        {
                            text: 'COD. CLIENTE.: ' + cliente.CardCode,
                            style: ['xsmall'],
                        },
                        {
                            text: 'ESTADO CTA. : ' + cliente.CurrentAccountBalance + ' ',
                            style: ['xsmall'],
                        },
                        '\n',
                        {
                            text: '----- GRACIAS POR SU COMPRA -----\n' + $aux_hoy,
                            alignment: 'center',
                            style: ['small']
                        },
                    ],
                    styles: {
                        header: {
                            fontSize: 5,
                            bold: true
                        },
                        number: {
                            fontSize: 4
                        },
                        small: {
                            fontSize: 3
                        },
                        xsmall: {
                            fontSize: 2.5
                        }
                    }
                }
                resolve(true);
            } catch (e) {
                reject(e);
            }
        });
    }

    public async docFacturaV2(cliente: any, pedido: any, detalles: any, userdata: any, contador: any) {
        window.parent.caches.delete("call");
        console.group("DATA FACTURA XXXXXXXXXXXXXXXXXX");
        console.log("CLIENTE", cliente);
        console.log("PEDIDO", pedido);
        console.log("DETALLES", detalles);
        console.log("userdata", userdata);
        console.log("userdata", userdata[0].docificacion);
        console.groupEnd();
        let usa_ices: any;
        if(userdata[0].usa_ices){
            usa_ices = userdata[0].usa_ices;
        }else{
            usa_ices = 0;
        }
        

        let U_Leyenda = '';
        let U_Leyenda2 = '';
        let U_NumeroSiguiente = '';
        let U_LB_NumeroFactura = '';
        let U_LB_NumeroAutorizac = '';
        let U_FechaLimiteEmision = '';
        let U_actividad = '';
        let autorizacion: object;
        let autorizacion2: object;
        let docaux: object;
        let docaux2: object;
        let docaux3: object;
        let titulo = '';
        let ruta2 = '0';
        let leyendasData: any = {};
        let url = userdata[0].fex_url;
        try {

            let docixfix: any = userdata[0].docificacion;
            console.log("docixfix ", docixfix);
            console.log("pedido.U_LB_NumeroAutorizac ", pedido.U_LB_NumeroAutorizac);
            let greaterTenxx = docixfix; //docixfix.filter((dxa) => dxa.U_NumeroAutorizacion == pedido.U_LB_NumeroAutorizac);
            console.log("greaterTenxx ", greaterTenxx);


            if (typeof greaterTenxx !== 'undefined') {



                console.log(greaterTenxx[0].U_Leyenda);
                console.log(pedido.U_LB_NumeroFactura);

                U_Leyenda = greaterTenxx[0].U_Leyenda;
                U_NumeroSiguiente = pedido.U_LB_NumeroFactura;

                console.log(docixfix);

                if (docixfix[0].fex_offline == '0') {

                    U_LB_NumeroFactura = cliente.U_EXX_FENUM;
                } else {
                    console.log("pasa");
                    console.log(docixfix[0].X_NumeroSiguiente);
                    U_LB_NumeroFactura = (Number(docixfix[0].X_NumeroSiguiente) + 1).toString();
                }

                U_LB_NumeroAutorizac = greaterTenxx[0].U_NumeroAutorizacion;
                U_FechaLimiteEmision = greaterTenxx[0].U_FechaLimiteEmision;
                U_actividad = greaterTenxx[0].U_Actividad;

            } else {
                U_Leyenda = '';
                U_NumeroSiguiente = '';
                U_LB_NumeroFactura = '';
                U_LB_NumeroAutorizac = '';
                U_FechaLimiteEmision = '';
                U_actividad = '';
            }

            console.log("userdata[0].uso_fex ", userdata[0].uso_fex);

            if (userdata[0].uso_fex == '1') {
                if (cliente.U_EXX_FE_Cuf != '0') {

                    titulo = 'FACTURA';
                    autorizacion = {
                        text: 'CUF:',
                        bold: true,
                        style: ['small'],
                        alignment: 'center'
                    };
                    autorizacion2 = {
                        text: cliente.U_EXX_FE_Cuf,
                        style: ['small'],
                        alignment: 'center'
                    };
                    docaux = {
                        text: 'FACTURA',
                        alignment: 'center',
                        bold: true,
                        style: ['small']
                    };

                    docaux2 = {
                        text: 'CON DERECHO A CREDITO FISCAL',
                        bold: true,
                        alignment: 'center',
                        style: ['small']
                    };
                    docaux3 = {
                        text: userdata[0].empresa[0].nombre,
                        bold: true,
                        alignment: 'center',
                        style: ['small']
                    };


                    ruta2 = userdata[0].fex_url_siat + 'consulta/QR?nit=' + userdata[0].empresa[0].nit + '&cuf=' + cliente.U_EXX_FE_Cuf + '&numero=' + U_LB_NumeroFactura + '&amp;t=1';
                    
                    U_Leyenda2 = 'Este documento es una impresión de un Documento Digital emitido en una Modalidad de Facturación en Línea';
                    
                    leyendasData = {
                        text: `${userdata[0].almacenes[0].leyendauno} \n \n` + userdata[0].almacenes[0].leyendados + ' \n \n' + U_Leyenda2,
                        alignment: 'center',
                        style: ['small']
                    }

                } else {
                    titulo = 'NOTA DE VENTA';
                    autorizacion = [{
                        text: 'AUTORIZACION',
                        style: ['small'],
                        alignment: 'center'
                    },
                    {
                        text: U_LB_NumeroAutorizac,
                        style: ['small'],
                        alignment: 'center'
                    }
                    ]
                    leyendasData = '';
                }
            } else {
                titulo = 'FACTURA';
                autorizacion = [{
                    text: 'AUTORIZACION',
                    style: ['small'],
                    alignment: 'center'
                },
                {
                    text: U_LB_NumeroAutorizac,
                    style: ['small'],
                    alignment: 'center'
                }
                ]

            }


        } catch (e) {
            console.log("ERROR AL OBTENER NUMERO DOSIFICACION");
            U_Leyenda = '';
            U_NumeroSiguiente = '';
            U_LB_NumeroFactura = pedido.DocNum;
            U_LB_NumeroAutorizac = '';
            U_FechaLimiteEmision = '';
            U_actividad = '';
            titulo = 'NOTA DE VENTA';
            autorizacion = {
                text: 'SIN AUTORIZACION ',
                style: ['small'],
                alignment: 'center'
            };
        }

        return new Promise((resolve, reject) => {
            pedido.DocDate = pedido.DocDate && pedido.DocDate != 'undefined' ? pedido.DocDate : new Date()
            let d = pedido.DocDate && pedido.DocDate != 'undefined' ? pedido.DocDate : new Date(); // DocDueDate
            //let dr = d.split('-');
            //let rqx: string = `${pedido.U_4NIT}|${pedido.U_LB_NumeroFactura}|${pedido.U_LB_NumeroAutorizac}|${dr[2] + '/' + dr[1] + '/' + dr[0]}|${pedido.DocumentTotal}|${pedido.DocumentTotalPay}|${pedido.U_LB_CodigoControl}|${cliente.FederalTaxId}|0|0|0|0|`;
            // let rqx: string = `${userdata[0].empresa[0].nit}|${pedido.U_LB_NumeroFactura}|${pedido.U_LB_NumeroAutorizac}|${dr[2] + '/' + dr[1] + '/' + dr[0]}|${pedido.DocumentTotal}|${pedido.DocumentTotalPay}|${pedido.U_LB_CodigoControl}|${pedido.U_4NIT}|0|0|0|${(pedido.DocumentdescuentoTotal?pedido.DocumentdescuentoTotal:0)}|`;
            //let rqx: string = `${userdata[0].empresa[0].nit}|${pedido.U_LB_NumeroFactura}|${aux}|${dr[2] + '/' + dr[1] + '/' + dr[0]}|${pedido.DocumentTotal}|${pedido.DocumentTotalPay}|${pedido.U_LB_CodigoControl}|${pedido.U_4NIT}|0|0|0|${(pedido.DocumentdescuentoTotal?pedido.DocumentdescuentoTotal:0)}|`;

            //console.log("console log qr", rqx);
            let qrx: object;
            let datafactura: object;
            let textfactura: object;
            let copia: object;
            let impuestos: object;
            let muestraices: object;
            let sum_imp = 0;
            let impuestosIce: object;
            let sum_impIce = 0;
            let impuestosIcep: object;
            let sum_impIcep = 0;
            let impuesto = 0;

            copia = {};
            impuestos = {};
            muestraices = {};
            let num_imp = 0; // let num_imp = contador[0]["contador"];
            let xxx = 0;
            if (pedido.origen == 'outer') {
                qrx = {};
                textfactura = {};
            } else {

                if (ruta2 == '0') {
                    if(xxx == 1){
                        ruta2 = "http://localhost:8082/visorfactura/consulta.php"+userdata[0].fex_url + '?doc=' + pedido.cod + '&nit=' + cliente.FederalTaxId;
                        qrx = {
                            text: '\n\n' + ruta2 + '\n\n',
                            style: ['xsmall'],
                            alignment: 'center',
                        };
                    }else{
                        ruta2 = ''; //userdata[0].fex_url + '?doc=' + pedido.cod + '&nit=' + cliente.FederalTaxId;
                        qrx = {};
                    }
                    
                }else{
                    console.log(ruta2);
                    qrx = {
                        qr: ruta2,
                        alignment: 'center',
                        eccLevel: 'L',
                        fit: '45'
                    };
                }

                textfactura = {
                    text: '\n\n' + userdata[0].fex_leyenda + '\n\n' + ruta2,
                    style: ['xsmall'],
                    alignment: 'center',
                };

            }
            if (num_imp > 1) {
                num_imp = num_imp - 1;
                copia = {
                    text: 'Copia # ' + num_imp + ' de Original',
                    alignment: 'center',
                    style: ['small']
                };
            }
            let cuerpodetalle = [];
            let aux_descripcion = '';
            let $hoy = new Date();
            let $aux_hoy = formatDate($hoy, 'dd/MM/yyyy HH:mm', 'en-US');
            let totalBonificacion = 0;
            let totalDescLinea = 0;
            let totalDescDocumento = 0;
            let netototal = 0;


            for (let detal of detalles) {
                console.log("detalle de factura ", detal);
                let aux_precio: any = formatNumber(detal.LineTotalPay, 'en-US', '1.2-2');

                // aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' + detal.Dscription;
                aux_descripcion = detal.ItemCode + ' - ' + detal.Dscription;

                /* if (detal.bonificacion == 1) {
                    aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' + detal.Dscription + ' - ( Bonif. )';
                    totalBonificacion = totalBonificacion + detal.U_4DESCUENTO;//detal.LineTotalPay;
                }
                 if (detal.bonificacion == 1) {
                        aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' + detal.Dscription + ' - ( Bonif. )';
                        totalBonificacion = totalBonificacion + detal.U_4DESCUENTO;
                    }
                    if (detal.bonificacion == 2 || detal.bonificacion == 3) {
                        aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' + detal.Dscription + ' - ( Desc. )';
                        totalBonificacion = totalBonificacion + detal.U_4DESCUENTO;
    
                    }
                    if (detal.lotesUsados.length > 0) {
                        for (let itemLote of detal.lotesUsados) {
                            aux_descripcion += '\n   - Lote: ' + itemLote.lote + " (Cant. " + itemLote.cant + ")";
                        }
    
                    }
                    if (detal.seriesUsados.length > 0) {
                        for (let itemSerie of detal.seriesUsados) {
                            aux_descripcion += '\n   - Serie: ' + itemSerie.serie;
                        }
                    }
                    if (detal.U_4DESCUENTO > 0 && detal.bonificacion == 0) {
                        totalDescLinea = totalDescLinea + detal.U_4DESCUENTO;
                        //aux_precio = detal.LineTotalPay - detal.U_4DESCUENTO;
                    }; */
                console.log("DEVD detal ", detal);
                console.log("DEVD detal.productos ", detal.productos);
                if (detal.productos.lenght > 0) {
                    //aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' +detal.Dscription;
                    //aux_descripcion = detal.ItemCode +' - ' +detal.Dscription;
                    aux_descripcion = detal.ItemCode + ' - ' + detal.ItemName;

                    for (let prodcom of detal.productos) {
                        aux_descripcion += '\n   -- ' + detal.ItemCode + ' - ' + prodcom.ItemName;
                    }
                }
                let aux_total = formatNumber(detal.Price, 'en-US', '1.2-2');
                let priceUnit = formatNumber(detal.Price, 'en-US', '1.2-2');

                let aux_totalneto = formatNumber(detal.Price * detal.Quantity, 'en-US', '1.2-2');
                let quantity = formatNumber(detal.Quantity, 'en-US', '1.2-2');

                let aux_descuento = formatNumber(detal.U_4DESCUENTO, 'en-US', '1.2-2');
                //let porc_descuento = detal.DiscPrcnt;
                let icee = formatNumber(detal.ICEe, 'en-US', '1.2-2');
                let icep = formatNumber(detal.ICEp, 'en-US', '1.2-2');
                sum_imp = Number(sum_imp) + Number(detal.ICEe) + Number(detal.ICEp);
                sum_impIce = sum_impIce + Number(detal.ICEe);
                sum_impIcep = sum_impIcep + Number(detal.ICEp);


                // let detalle_linea = `${aux_descripcion}  \n PRECIO U: ${aux_total} BRUTO: ${aux_totalneto} DESC: ${aux_descuento}  \n NETO: ${aux_precio}`;
                // 2 COMPANEX
                // if (userdata[0].localizacion == "2") {
                //      detalle_linea += ` ICEE: ${Calculo.formatMoney(detal.ICEe)}  ICEP: ${Calculo.formatMoney(detal.ICEp)} ICET: ${Calculo.formatMoney(detal.ICEt)} `;
                //    impuesto += Number(detal.ICEt);
                //   }
                // 3 PARAGUAY
                //// if (userdata[0].localizacion == "3") {
                //      detalle_linea += ` IVA: ${Calculo.formatMoney(detal.ICEe)} `;
                //     impuesto += Number(detal.ICEe);
                // }
                //detalle_linea += '\n\n';
                let header: any;
                if ((userdata[0].localizacion == 2) || (userdata[0].localizacion == 1)) {
                    if (!pedido.DocCur) {
                        pedido.DocCur = "BS";
                    }

                    header = [
                        {
                            //text: `${aux_descripcion} \n P.U: ${aux_total} DESC: ${aux_descuento} P.B: ${aux_totalneto}  \n\n`,
                            // text: `${aux_descripcion} \n PRECIO U: ${aux_total}   DESC: ${aux_descuento}  ICEE: ${icee} ICEP: ${icep}  NETO: ${aux_precio}  \n\n`,

                            text: `${aux_descripcion}`,
                            bold: true,
                            //style: ['small'],
                            fontSize: 3,
                            width: '75%'
                        },
                        {

                            columns: [
                                {
                                    //text: `${aux_descripcion} \n P.U: ${aux_total} DESC: ${aux_descuento} P.B: ${aux_totalneto}  \n\n`,
                                    // text: `${aux_descripcion} \n PRECIO U: ${aux_total}   DESC: ${aux_descuento}  ICEE: ${icee} ICEP: ${icep}  NETO: ${aux_precio}  \n\n`,

                                    text: ` ${priceUnit} X  ${quantity}  -  ${aux_descuento}  +  ${icep} +  ${icee}  `,

                                    //style: ['small'],
                                    fontSize: 2.5,
                                    width: '75%'
                                }, {
                                    text: ` ${aux_precio} ${pedido.DocCur}.`,
                                    fontSize: 2.5,
                                    style: ['small'],
                                    alignment: 'right',

                                }


                            ]
                        },

                        {
                            //text: `${aux_descripcion} \n P.U: ${aux_total} DESC: ${aux_descuento} P.B: ${aux_totalneto}  \n\n`,
                            // text: `${aux_descripcion} \n PRECIO U: ${aux_total}   DESC: ${aux_descuento}  ICEE: ${icee} ICEP: ${icep}  NETO: ${aux_precio}  \n\n`,

                            text: '\n',
                            //bold: true,
                            //style: ['small'],
                            fontSize: 3,
                            width: '75%'
                        },
                    ]
                }
                netototal = netototal + aux_precio;

                cuerpodetalle.push(header);
            }
            //aplicando descuento de cabecera
            let importeCF: any = 0;
            let descuentosBonos = 0;
            descuentosBonos = totalBonificacion + totalDescLinea;
            console.log("descuentosBonos ", descuentosBonos);
            let sumIces = Number(sum_impIce + sum_impIcep).toFixed(2);
            importeCF = Number((Number(pedido.DocumentTotalPay) - Number(sum_impIce + sum_impIcep))).toFixed(2);
            console.log("importeCF ", importeCF);
            if (Number(sumIces) > 0) {
                sumIces = sumIces
            }
            else {
                sumIces = "0";
            }
            descuentosBonos = Number(descuentosBonos.toFixed(2))
            console.log("convert descuentosBonos ", descuentosBonos)
            if (Number(descuentosBonos) > 0) {
                descuentosBonos = descuentosBonos;
            } else {
                descuentosBonos = 0;
            }

            if (userdata[0].localizacion == 2 && usa_ices == 1) {
                impuestosIce = {
                    columns: [
                        {
                            text: ' \n TOTAL ICEE ',
                            style: ['small']
                        },
                        {
                            text: '\n  ' + Calculo.formatMoney(sum_impIce) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }
                impuestosIcep = {
                    columns: [
                        {
                            text: 'TOTAL ICEP ',
                            style: ['small']
                        },
                        {
                            text: Calculo.formatMoney(sum_impIcep) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }


                impuestos = {
                    columns: [
                        {
                            text: ' \n CREDITO FISCAL ',
                            style: ['small']
                        },
                        {
                            text: '\n ' + Calculo.formatMoney(0.13 * pedido.DocumentTotalPay) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }

            }



            if (userdata[0].localizacion == 2) {


                /*  impuestosIce = {
                     columns: [
                         {
                             text: ' \n TOTAL ICEE ',
                             style: ['small']
                         },
                         {
                             text: '\n  ' + Calculo.formatMoney(sum_impIce) + ' ' + pedido.DocCur,
                             style: ['small'],
                             alignment: 'right',
                         }
                     ]
                 }
                 impuestosIcep = {
                     columns: [
                         {
                             text: 'TOTAL ICEP ',
                             style: ['small']
                         },
                         {
                             text: Calculo.formatMoney(sum_impIcep) + ' ' + pedido.DocCur,
                             style: ['small'],
                             alignment: 'right',
                         }
                     ]
                 } */


                impuestos = {
                    columns: [
                        {
                            text: ' \n CREDITO FISCAL ',
                            style: ['small']
                        },
                        {
                            text: '\n ' + Calculo.formatMoney(0.13 * pedido.DocumentTotalPay) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }

            }

            if(usa_ices == 1){
                muestraices = {
                        columns: [
                            {
                                text: 'Total ICE especifico Bs.',
                                style: ['small'],
                                width: '50%',
                                alignment: 'right',
                            },
                            {
                                text: ' ' + Calculo.formatMoney(sum_impIce) + ' ' + pedido.DocCur,
                                //text: Calculo.formatMoney(totalBonificacion) + ' ' + pedido.DocCur,
                                style: ['small'],
                                alignment: 'right',
                            }
                        ]
                    },
                    {
                        columns: [
                            {
                                text: 'Total ICE porcentual Bs.',
                                style: ['small'],
                                width: '50%',
                                alignment: 'right',
                            },
                            {
                                text: Calculo.formatMoney(sum_impIcep) + ' ' + pedido.DocCur,
                                // text: Calculo.formatMoney(totalBonificacion) + ' ' + pedido.DocCur,
                                style: ['small'],

                                alignment: 'right',
                            }
                        ]
                    }

            }
            



            /****Genera el documento***/
            try {
                this.objPDF = {
                    pageSize: { width: 80, height: 'auto' },
                    pageMargins: [6, 3, 3, 10],
                    content: [
                        /* docaux,
                        docaux2, */
                        {
                            text: '',
                            style: ['small'],
                        },
                        {
                            text: titulo + '',
                            style: ['small'],
                            bold: true,
                            alignment: 'center',
                        },


                        docaux3,
                        {
                            text: `${userdata[0].almacenes[0].empresa}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: userdata[0].docificacion[0].fex_sucursal,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: `${userdata[0].almacenes[0].descripcion}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: 'Punto de Venta ' + userdata[0].docificacion[0].fex_puntoventa,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: `${userdata[0].almacenes[0].direccion}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: 'Tel. ' + userdata[0].almacenes[0].telefonouno,
                            alignment: 'center',
                            style: ['small']
                        },

                        {
                            text: `${userdata[0].almacenes[0].ciudad} - ${userdata[0].almacenes[0].pais}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: '----------------------------------------------------------------------------',
                            alignment: 'center',
                            style: ['small']
                        },
                        /* {
                             text: titulo,
                             alignment: 'center',
                             style: ['header']
                         },
                         {
                             text: `ACTIVIDAD ECONÓMICA`,
                             alignment: 'center',
                             style: ['small']
                         },
                         {
                             //text: `${userdata[0].empresa[0].actividad}`,
                             text: `${U_actividad}`,
                             alignment: 'center',
                             style: ['small']
                         },*/
                        //copia,
                        {
                            text: '',
                            style: ['small'],

                        },
                        {
                            text: 'NIT',
                            style: ['small'],
                            bold: true,
                            alignment: 'center',
                        },
                        {
                            text: userdata[0].empresa[0].nit ? userdata[0].empresa[0].nit : '',
                            style: ['small'],
                            alignment: 'center',

                        },
                        {
                            text: '',
                            style: ['small'],
                        },
                        {
                            text: titulo + '',
                            style: ['small'],
                            bold: true,
                            alignment: 'center',
                        },
                        {
                            text: U_LB_NumeroFactura,
                            style: ['small'],
                            alignment: 'center',
                        }, {
                            text: '',
                            style: ['small'],
                        },

                        {
                            text: '',
                            style: ['small'],
                        },
                        /* {
                            columns: [
                                {
                                    text: '',
                                    style: ['small'],
                                    
                                },
                                {
                                    text: 'NIT:',
                                    style: ['small'],
                                    bold: true,
                                    alignment: 'center',
                                },
                                {
                                    text: userdata[0].empresa[0].nit,
                                    style: ['small'],
                                    alignment: 'center',
    
                                },
                                {
                                    text: '',
                                    style: ['small'],
                                },
                            ] 
                        },*/
                        autorizacion,
                        autorizacion2,
                        {
                            text: '----------------------------------------------------------------------------',
                            alignment: 'center',
                            style: ['small']
                        },


                        {
                            columns: [
                                {
                                    text: 'NOMBRE/RAZÓN SOCIAL:',
                                    style: ['small'],
                                    width: '100%',
                                    bold: true,
                                    alignment: 'center',
                                }
                            ]
                        },
                        {
                            columns: [
                               
                                {
                                    text: pedido.U_4RAZON_SOCIAL,
                                    style: ['small'],
                                    alignment: 'center',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'NIT/CI/CEX  :',
                                    style: ['small'],
                                    bold: true,
                                    alignment: 'right',
                                    width: '40%'
                                },
                                {
                                    text: pedido.U_4NIT,
                                    style: ['small'],
                                    alignment: 'left',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'COD. CLIENTE  :',
                                    bold: true,
                                    style: ['small'],
                                    width: '40%',
                                    alignment: 'right',
                                },
                                {
                                    text: cliente.CardCode,
                                    style: ['small'],
                                    alignment: 'left',
                                }
                            ]
                        },
                        {
                            columns: [
                                {
                                    text: 'FECHA DOC  :',
                                    style: ['small'],
                                    width: '40%',
                                    alignment: 'right',
                                    bold: true,
                                },
                                {
                                    text: formatDate(pedido.DocDate, 'dd/MM/yyyy', 'en-US'),
                                    style: ['small'],
                                    alignment: 'left',
                                }
                            ]
                        },

                        {
                            text: '----------------------------------------------------------------------------',
                            alignment: 'center',
                            style: ['small']
                        },

                        {
                            text: 'DETALLE',
                            alignment: 'center',
                            bold: true,
                            style: ['small']
                        },

                        /* {
                             columns: [
                                 {
                                     text: '\n CANTIDAD PRODUCTO',
                                     style: ['small'],
                                     bold: true,
                                     width: '70%'
                                 },
                                 {
                                     text: '\n SUBTOTAL',
                                     style: ['small'],
                                     alignment: 'right',
                                     bold: true
                                 }
                             ]
                         },*/
                        cuerpodetalle,

                        {
                            text: '............................................................................',
                            alignment: 'center',
                            style: ['small']
                        },

                        muestraices,

                        {
                            columns: [
                                {
                                    text: 'Total a Pagar Bs.',
                                    style: ['small'],
                                    width: '50%',
                                    bold: true,
                                    alignment: 'right',
                                },
                                {
                                    text: Calculo.formatMoney(pedido.DocumentTotalPay) + ' ' + pedido.DocCur,
                                    style: ['small'],
                                    bold: true,
                                    alignment: 'right',
                                }
                            ]
                        },
                        /* {
                            columns: [
                                {
                                    text: 'Importe Base Credito',
                                    style: ['small'], 
                                    width: '50%',
                                    alignment: 'left',
                                },
                                {
                                    text: Calculo.formatMoney(pedido.descuento) + ' ' + pedido.DocCur,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        }, */
                        {
                            columns: [
                                {
                                    text: 'Importe Base Credito',
                                    style: ['small'],
                                    bold: true,
                                    alignment: 'right',
                                }, {
                                    text: Calculo.formatMoney(Number(pedido.DocumentTotalPay) - Number(sum_impIce + sum_impIcep)) + ' ' + pedido.DocCur,
                                    style: ['small'],
                                    bold: true,
                                    alignment: 'right',
                                }
                            ]
                        },
                        /* impuestosIce,
                        impuestosIcep, */
                        /*  {
                             columns: [
                                 {
                                     text: 'TOTAL',
                                     style: ['small']
                                 },
                                 {
                                     text: Calculo.formatMoney(pedido.DocumentTotalPay) + ' ' + pedido.DocCur,
                                     style: ['small'],
                                     alignment: 'right',
                                 }
                             ]
                         }, */
                        {
                            text: '\n  SON: ' + this.numeletra.run(pedido.DocumentTotalPay, pedido.DocCur) + "",// + pedido.DocCur,
                            style: ['xsmall'],
                            alignment: 'left',
                        },
                        /*{
                            text: '\nNombre: ',
                            style: ['small'],
                        },
                        {
                            text: '\nFirma: ______________________________',
                            style: ['small'],
                            alignment: 'center',
                        },
                        {
                            text: 'C.I.: ',
                            style: ['small'],
                        },*/
                        {
                            text: '----------------------------------------------------------------------------',
                            alignment: 'center',
                            style: ['small']
                        },
                        leyendasData,

                        {
                            text: url,
                            alignment: 'center',
                            style: ['small']
                        },
                        
                        /*   {
                              text: `${userdata[0].almacenes[0].leyendauno} \n \n`+userdata[0].almacenes[0].leyendados +' \n \n'+U_Leyenda2,
                              alignment: 'center',
                              style: ['small']
                          }, */


                        /*{
                            text: 'FECHA LIMITE DE EMISION: ' + formatDate(U_FechaLimiteEmision, 'dd/MM/yyyy', 'en-US'),
                            style: ['small']
                        },*/
                        {
                            text: '.',
                            alignment: 'center',
                            style: ['small'],
                            color: '#ffffff'
                        },
                        qrx,
                        textfactura,

                        /* {
                             text: '\n TC.: ' + userdata[0].tipocambioparalelo.tipoCambio,
                             style: ['xsmall']
     
                         },
                         {
                             text: '\n NRO. DOC.: ' + pedido.cod,
                             style: ['xsmall']
     
                         },
                         {
                             text: 'OBSERVACION: ' + pedido.comentario,
                             style: ['xsmall']
                         }, {
                             text: 'COD.VENDEDOR: ' + userdata[0].config[0].codEmpleadoVenta + ' -' + userdata[0].config[0].nombrecliente,
                             style: ['xsmall']
                         }, {
                             text: 'USUARIO: ' + userdata[0].idUsuario + ' - ' + userdata[0].nombrePersona + ' ' + userdata[0].apellidoPPersona + ' ' + userdata[0].apellidoMPersona + ' ',
                             style: ['xsmall']
                         }, {
                             text: 'CONDICION DE PAGO: Contado',
                             style: ['xsmall']
                         }, {
                             text: 'FECHA DE ENTREGA: ' + formatDate(pedido.DocDueDate, 'dd/MM/yyyy', 'en-US'),
                             style: ['xsmall']
                         },
                         /*{
                             text: userdata[0].config[0].codEmpleadoVenta + ' -' + userdata[0].config[0].nombrecliente,
                             style: ['xsmall'],
                         },
                         */
                        /*{
                            text: 'ESTADO CTA. : ' + cliente.CurrentAccountBalance + ' ',
                            style: ['xsmall'],
                        },
                        '\n',
                        {
                            text: '----- GRACIAS POR SU COMPRA -----\n' + $aux_hoy,
                            alignment: 'center',
                            style: ['small']
                        },*/
                    ],
                    styles: {
                        header: {
                            fontSize: 5,
                            bold: true
                        },
                        number: {
                            fontSize: 4
                        },
                        small: {
                            fontSize: 3
                        },
                        xsmall: {
                            fontSize: 2.5
                        }
                    }
                }
                resolve(true);
            } catch (e) {
                reject(e);
            }
        });
    }

    public async docContrato(cliente: any, pedido: any, detalles: any, userdata: any) {
        console.log("CLIENTE", cliente);
        console.log("PEDIDO", pedido);
        console.log("DETALLES", detalles);
        console.log("userdata", userdata);
        console.log("userdata", userdata[0].docificacion);

        /*let fecha = new Date();
        let añoActual = fecha.getFullYear();
        let hoy = fecha.getDate();
        let mesActual = fecha.getMonth() + 1;*/

        let añoActual = pedido.DocDate.substring(0,4);
        let hoy = pedido.DocDate.substring(8,10)
        let mesActual = pedido.DocDate.substring(5,7); 


        let ciudad = userdata[0].sucursalCiudad;


        //let detallefactura = [];
        //let detalle:any; 
        var detallefactura = new Array(detalles.length);
        detallefactura[0] = new Array(7);

        detallefactura[0][0] = {
            text: 'Nº',
            alignment: 'center',
            border: [true, true, false, false],
            color: '#FFFFFF',
            fontSize: 8,
            bold: true
        },
        detallefactura[0][1] = {
            text: 'CODIGO',
            alignment: 'center',
            border: [true, true, false, false],
            color: '#FFFFFF',
            fontSize: 8,
            bold: true
        },
        detallefactura[0][2] = {
            text: 'DESCRIPCION DEL PRODUCTO',
            alignment: 'center',
            border: [true, true, false, false],
            color: '#FFFFFF',
            fontSize: 8,
            bold: true
        },
        detallefactura[0][3] = {
            text: 'CANTIDAD',
            alignment: 'center',
            border: [true, true, false, false],
            color: '#FFFFFF',
            fontSize: 8,
            bold: true
        },
        detallefactura[0][4] = {
            text: 'PRECIO UNITARIO',
            alignment: 'center',
            border: [true, true, false, false],
            color: '#FFFFFF',
            fontSize: 8,
            bold: true
        },
        detallefactura[0][5] = {
            text: 'PRECIO UNIT C/DESCUENTO',
            alignment: 'center',
            border: [true, true, false, false],
            color: '#FFFFFF',
            fontSize: 8,
            bold: true
        },
        detallefactura[0][6] = {
            text: 'TOTAL',
            alignment: 'center',
            border: [true, true, false, false],
            color: '#FFFFFF',
            fontSize: 8,
            bold: true
        };

        let x = 1;

        for (let i = 0; i < detalles.length; i++) {

            detallefactura[x] = new Array(7);

            let descuento = 0;
            if (detalles[i].U_4DESCUENTO > 0) {
                descuento = detalles[i].U_4DESCUENTO
            }

            detallefactura[x][0] = {
                text: Number(i + 1),
                alignment: 'center',
                border: [true, true, true, true],
                fontSize: 8,
                bold: true
            },
                detallefactura[x][1] = {
                    text: detalles[i].ItemCode,
                    alignment: 'center',
                    border: [true, true, true, true],
                    fontSize: 8,
                    bold: true
                },
                detallefactura[x][2] = {
                    text: detalles[i].Dscription,
                    alignment: 'center',
                    border: [true, true, true, true],
                    fontSize: 8,
                    bold: true
                },
                detallefactura[x][3] = {
                    text: Number(detalles[i].Quantity),
                    alignment: 'center',
                    border: [true, true, true, true],
                    fontSize: 8,
                    bold: true
                },
                detallefactura[x][4] = {
                    text: detalles[i].Price,
                    alignment: 'center',
                    border: [true, true, true, true],
                    fontSize: 8,
                    bold: true
                },

                detallefactura[x][5] = {
                    text: (detalles[i].Price - (descuento / Number(detalles[i].Quantity))).toFixed(2),
                    alignment: 'center',
                    border: [true, true, true, true],
                    fontSize: 8,
                    bold: true
                },
                detallefactura[x][6] = {
                    text: (detalles[i].LineTotal - descuento).toFixed(2),
                    alignment: 'center',
                    border: [true, true, true, true],
                    fontSize: 8,
                    bold: true
                }
            x++;
        }

        let condicion;
        let aux = 0;
        let dia = 0;
        let mes = 0;

        for (let i = 0; i < userdata[0].condicionespago.length; i++) {
            if (userdata[0].condicionespago[i].GroupNumber == pedido.PayTermsGrpCode) {
                condicion = userdata[0].condicionespago[i].PaymentTermsGroupName
                dia = parseInt(userdata[0].condicionespago[i].NumberOfAdditionalDays);
                mes = parseInt(userdata[0].condicionespago[i].NumberOfAdditionalMonths);
                aux = 1;
            }
        }
        if(aux == 0){
            condicion = 'CREDITO';
        }

        if(mes > 0){
            let aux = mes*30;
            dia = dia+aux;
        }

        let fecha_ven = new Date(pedido.DocDate);

        console.log(pedido.DocDate);
        console.log(fecha_ven.getDate());

        let dia_firma = pedido.DocDate.substring(8,10);

        let mes_firma = '';
        switch (mesActual) {
            case '01':
                mes_firma = "ENERO";
            break;
            case '02':
                mes_firma = "FEBRERO";
            break;
            case '03':
                mes_firma = "MARZO";
            break;
            case '04':
                mes_firma = "ABRIL";
            break;
            case '05':
                mes_firma = "MAYO";
            break;
            case '06':
                mes_firma = "JUNIO";
            break;
            case '07':
                mes_firma = "JULIO";
            break;
            case '08':
                mes_firma = "AGOSTO";
            break;
            case '09':
                mes_firma = "SEPTIEMBRE";
            break;
            case '10':
                mes_firma = "OCTUBRE";
            break;
            case '11':
                mes_firma = "NOVIEMBRE";
            break;
            case '12':
                mes_firma = "DICIEMBRE";
            break;
        }
        let anio_firma = añoActual;


        let mes_cred = parseInt(mesActual);
        //let mes_cred = 12;
        let dia_contrato = dia+parseInt(hoy);
        //let dia_contrato = 3+parseInt(hoy);
        let anio_contrato = parseInt(añoActual);


        for (let i = 0; i <= 1; i++) {
            
            let limite_mes = 0;

            if(mes_cred == 1 || mes_cred == 3 || mes_cred == 5 || mes_cred == 7 || mes_cred == 8 || mes_cred == 10 || mes_cred == 12){
                limite_mes = 31;
            }else{
                if(mes_cred == 4 || mes_cred == 6 || mes_cred == 9 || mes_cred == 11){
                    limite_mes = 30;
                }else{
                    limite_mes = 28;
                }
            }

            console.log("valida",dia_contrato+'>'+limite_mes);

            if(dia_contrato > limite_mes){
                console.log("paso");
                mes_cred ++;
                if(mes_cred > 12){
                    mes_cred = 1;
                    anio_contrato ++;
                }
                dia_contrato = dia_contrato-limite_mes;
                console.log("dia_contrato",dia_contrato);
                i = 0;
            }
        }
        let dia_contr = dia_contrato.toString();
        if(dia_contrato < 10){
            dia_contr = '0'+dia_contrato;
        }

        let mes_contr = mes_cred.toString();
        if(mes_cred < 10){
            mes_contr = '0'+mes_cred;
        }

        fecha_ven.setDate(fecha_ven.getDate() + dia);
        let fecha_venc = dia_contr + "/" + mes_contr + "/" + anio_contrato;

        window.parent.caches.delete("call");
       
        return new Promise((resolve, reject) => {
            try {
                this.objPDF = {
                    content: [
                        {
                            table: {
                                widths: ['60%', '40%'],
                                heights: [10, 10],
                                headerRows: 1,
                                body: [
                                    [
                                        {
                                            text: 'CSAPEK',
                                            border: [false, false, false, false],
                                            fontSize: 24,
                                            alignment: 'left',
                                            bold: true
                                        },
                                        {
                                            border: [false, false, false, false],
                                            table: {
                                                widths: ['55%', '15%', '15%', '15%'],
                                                heights: [10, 10, 10, 10],
                                                headerRows: 1,
                                                body: [
                                                    [
                                                        {
                                                            text: 'Ciudad',
                                                            alignment: 'center',
                                                            border: [true, true, true, true],
                                                            fontSize: 8,
                                                            bold: true
                                                        },
                                                        {
                                                            text: 'Dia',
                                                            alignment: 'center',
                                                            border: [true, true, true, true],
                                                            fontSize: 8,
                                                            bold: true
                                                        },
                                                        {
                                                            text: 'Mes',
                                                            alignment: 'center',
                                                            border: [true, true, true, true],
                                                            fontSize: 8,
                                                            bold: true
                                                        },
                                                        {
                                                            text: 'Año',
                                                            alignment: 'center',
                                                            border: [true, true, true, true],
                                                            fontSize: 8,
                                                            bold: true
                                                        }
                                                    ],
                                                    [
                                                        {
                                                            text: ciudad,
                                                            alignment: 'center',
                                                            border: [true, true, true, true],
                                                            fontSize: 8,
                                                            bold: false
                                                        },
                                                        {
                                                            text: hoy,
                                                            alignment: 'center',
                                                            border: [true, true, true, true],
                                                            fontSize: 8,
                                                            bold: false
                                                        },
                                                        {
                                                            text: mesActual,
                                                            alignment: 'center',
                                                            border: [true, true, true, true],
                                                            fontSize: 8,
                                                            bold: false
                                                        },
                                                        {
                                                            text: añoActual,
                                                            alignment: 'center',
                                                            border: [true, true, true, true],
                                                            fontSize: 8,
                                                            bold: false
                                                        }
                                                    ]
                                                ]
                                            }
                                        }
                                    ],
                                ]
                            }
                        },
                        '\n',
                        '\n',
                        '\n',
                        {
                            alignment: 'center',
                            text: 'DOCUMENTO PRIVADO DE VENTA AL CRÉDITO',
                            style: 'header',
                            fontSize: 12,
                            bold: true,
                            margin: [0, 10],
                        },
                        {
                            table: {
                                widths: ['33%', '33%', '34%', '0%'],
                                heights: [10, 10, 10, 10],
                                headerRows: 1,
                                body: [
                                    [
                                        {
                                            text: 'DATOS DEL DEUDOR',
                                            border: [true, true, false, false],
                                            colSpan: 3,
                                            fontSize: 11,
                                            bold: true
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                            text: '',
                                            border: [false, true, true, false],
                                        }
                                    ],
                                    [
                                        {
                                            text: 'Nombres y apellidos o razón social: ' + cliente.CardName + '/' + cliente.razonsocial,
                                            border: [true, false, false, false],
                                            colSpan: 3,
                                            fontSize: 9,
                                            bold: true
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                            text: '',
                                            border: [false, false, true, false],
                                        }
                                    ],
                                    [
                                        {
                                            text: 'Nº de NIT o C.I.: ' + cliente.FederalTaxId,
                                            border: [true, false, false, false],
                                            fontSize: 9,
                                            bold: true
                                        },
                                        {
                                            text: 'Teléfono : ' + cliente.PhoneNumber,
                                            border: [false, false, false, false],
                                            fontSize: 9,
                                            bold: true
                                        },
                                        {
                                            text: 'E-mail: ' + cliente.correoelectronico,
                                            border: [false, false, false, false],
                                            fontSize: 9,
                                            bold: true
                                        },
                                        {
                                            text: '',
                                            border: [false, false, true, false],
                                        }
                                    ],
                                    [
                                        {
                                            text: 'Dirección de domicilio legal: ',
                                            border: [true, false, false, false],
                                            colSpan: 3,
                                            bold: true,
                                            fontSize: 9
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                            text: '',
                                            border: [false, false, true, false],
                                        }
                                    ],
                                    [
                                        {
                                            colSpan: 4,
                                            border: [true, false, true, false],
                                            table: {
                                                widths: ['1%', '31%', '1%', '40%', '1%', '26%', '1%'],
                                                heights: [10, 10, 10, 10, 10, 10],
                                                headerRows: 1,
                                                body: [
                                                    [
                                                        {
                                                            text: '',
                                                            border: [true, true, true, true],
                                                        },
                                                        {
                                                            text: 'Régimen General (PJ)',
                                                            border: [false, false, false, false],
                                                            fontSize: 8,
                                                            bold: true

                                                        },
                                                        {
                                                            text: '',
                                                            border: [true, true, true, true],
                                                        },
                                                        {
                                                            text: 'Régimen General (EU)',
                                                            border: [false, false, false, false],
                                                            fontSize: 8,
                                                            bold: true
                                                        },
                                                        {
                                                            text: '',
                                                            border: [true, true, true, true],
                                                        },
                                                        {
                                                            text: 'Persona Natural',
                                                            border: [false, false, false, false],
                                                            fontSize: 8,
                                                            bold: true
                                                        },
                                                        {
                                                            text: '',
                                                            border: [false, false, false, false],
                                                        },

                                                    ],
                                                    [
                                                        {
                                                            text: '',
                                                            border: [true, true, true, true],
                                                        },
                                                        {
                                                            text: 'Sistema Tributario Integrado (STI) ',
                                                            border: [false, false, false, false],
                                                            fontSize: 8,
                                                            bold: true
                                                        },
                                                        {
                                                            text: '',
                                                            border: [true, true, true, true],
                                                        },
                                                        {
                                                            text: 'Régimen Agropecuario Unificado (RAU) ',
                                                            border: [false, false, false, false],
                                                            fontSize: 8,
                                                            bold: true
                                                        },
                                                        {
                                                            text: '',
                                                            border: [true, true, true, true],
                                                        },
                                                        {
                                                            text: 'Régimen Simplificado (RS)',
                                                            border: [false, false, false, false],
                                                            fontSize: 8,
                                                            bold: true
                                                        },
                                                        {
                                                            text: '',
                                                            border: [false, false, false, false],
                                                        },

                                                    ],
                                                ]
                                            }
                                        }
                                    ],
                                    [
                                        {
                                            text: 'Código de cliente: ' + cliente.CardCode,
                                            border: [true, false, false, true],
                                            colSpan: 3,
                                            fontSize: 9,
                                            bold: true
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                            text: '',
                                            border: [false, false, true, true],
                                        }
                                    ]
                                ]
                            }
                        },
                        '\n',
                        {
                            table: {
                                widths: ['33%', '33%', '34%', '0%'],
                                heights: [10, 10, 10, 10],
                                headerRows: 1,
                                body: [
                                    [
                                        {
                                            text: 'DATOS DEL VENDEDOR',
                                            border: [true, true, false, false],
                                            colSpan: 3,
                                            fontSize: 11,
                                            bold: true
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                            text: '',
                                            border: [false, true, true, false],
                                        }
                                    ],
                                    [
                                        {
                                            text: 'Nombres y apellidos: ' + userdata[0].config[0].nombrecliente,
                                            border: [true, false, false, true],
                                            colSpan: 3,
                                            fontSize: 9,
                                            bold: true
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                            text: '',
                                            border: [false, false, true, true],
                                        }
                                    ]
                                ]
                            }
                        },
                        '\n',
                        {
                            style: 'tableExample',
                            layout: {
                                fillColor: function (rowIndex, node, columnIndex) {
                                    return (rowIndex === 0) ? '#000000' : null;
                                }
                            },
                            table: {
                                widths: ['25%', '25%', '25%', '25%'],
                                heights: [10, 10, 10, 10],
                                headerRows: 1,
                                body: [
                                    [
                                        {
                                            text: 'CONDICIONES DE VENTA AL CRÉDITO',
                                            border: [true, true, false, false],
                                            color: '#FFFFFF',
                                            alignment: 'center',
                                            colSpan: 4,
                                            fontSize: 11,
                                            bold: true
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                        }
                                    ],
                                    [
                                        {
                                            text: 'Plazo del crédito: ' + condicion,
                                            border: [true, false, false, false],
                                            colSpan: 2,
                                            fontSize: 9,
                                            bold: true
                                        },
                                        {
                                            text: '',
                                            border: [false, false, false, false],
                                        },
                                        {
                                            text: 'Fecha de vencimiento: ',
                                            fontSize: 9,
                                            alignment: 'right',
                                            border: [false, false, false, false],
                                            bold: true
                                        },
                                        {
                                            text: fecha_venc,
                                            alignment: 'left',
                                            fontSize: 9,
                                            border: [false, false, true, false],
                                        },
                                    ],
                                    [
                                        {
                                            text: 'Precios, cantidad, descuentos y detalle de los productos vendidos al crédito',
                                            border: [true, false, false, false],
                                            fontSize: 8,
                                            colSpan: 3,
                                            bold: true
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                            text: 'Factura Nº : ' + cliente.U_EXX_FENUM,
                                            fontSize: 9,
                                            border: [false, false, true, false],
                                        },
                                    ],
                                    [
                                        {
                                            colSpan: 4,
                                            border: [true, false, true, false],
                                            style: 'tableExample',
                                            layout: {
                                                fillColor: function (rowIndex, node, columnIndex) {
                                                    return (rowIndex === 0) ? '#000000' : null;
                                                }
                                            },
                                            table: {
                                                widths: ['5%', '12%', '35%', '10%', '10%', '15%', '13%'],
                                                heights: [10, 10, 10, 10, 10, 10, 10],
                                                headerRows: 1,
                                                body: detallefactura
                                            }
                                        },
                                        {
                                        },
                                        {
                                        },
                                        {
                                        },
                                    ],
                                    [
                                        {
                                            text: '',
                                            border: [true, false, true, false],
                                            colSpan: 4,
                                        }
                                    ],
                                    [
                                        {
                                            text: '',
                                            border: [true, false, false, true],

                                        },
                                        {
                                            text: '',
                                            border: [false, false, false, true],
                                        },
                                        {
                                            text: '',
                                            border: [false, false, false, true],
                                        },
                                        {
                                            text: 'Total ' + pedido.DocCur + '.        ' + pedido.DocumentTotalDetallePay,
                                            border: [false, false, true, true],
                                            fontSize: 10,
                                            margin: [10, 0, 0, 0],
                                            bold: true,
                                            alignment: 'left',
                                        },

                                    ],
                                ]
                            }
                        },
                        '\n',
                        {
                            table: {
                                widths: ['100%'],
                                headerRows: 1,
                                body: [
                                    [
                                        {
                                            text: 'Conste el presente documento privado de reconocimiento de deuda y compromiso de pago que con el solo reconocimiento de firmas y rúbricas surtirá efectos legales de documento público, declaro en honor a la verdad que la información que antecede es correcta y fidedigna, por lo que de mi libre y espontánea voluntad reconozco haber recibido a mi entera y absoluta conformidad lubricantes de la línea Amalie de la Compañía Importadora de Automotores Mathías Csapek S .A., por lo que sin que medie dolo, error, violencia o vicio de consentimiento alguno, reconozco adeudar a CSAPEK la suma de ' + pedido.DocumentTotalDetallePay + ' ' + pedido.DocCur + ' (' + this.numeletra.run(pedido.DocumentTotalDetallePay, pedido.DocCur) + '), suma de dinero que me comprometo y obligo a cancelar impostergablemente hasta el ' + fecha_venc + ', conforme a las Condiciones de Venta al Crédito establecidas en el presente documento. En caso que no pague la deuda en la fecha establecida, CSAPEK me podrá cobrar una penalidad diaria del 0.1% (diario) a partir del décimo quinto día de la fecha de vencimiento. En caso de incumplimiento en el pago en la fecha de su correspondiente vencimiento, entraré automáticamente en mora por el monto total de la obligación, la misma que se considerará vencida, de suma liquida y exigible sin necesidad de intimación ni de requerimiento judicial o extrajudicial alguno, pudiendo CSAPEK accionar las vías legales para el cobro total de la obligación y de los intereses devengados, quedando mi persona obligada al pago de todos los gastos y demás costos ocasionados a CSAPEK, garantizo el cumplimiento cabal de la presente obligación con la generalidad de mis bienes, muebles e inmuebles, habidos y por haber, presentes y futuros y sin limitación alguna que responderán al pago total de la obligación adeudada u otros que correspondan. El presente documento tiene carácter de Título Ejecutivo conforme a lo establecido en el Art. 379 del Código Procesal Civil, caso para el que me comprometo a reconocer el interés convencional de tres por ciento (3%) mensual sobre la cantidad que adeude al momento de su ejecución, renunciando de manera voluntaria a todos los beneficios que la ley acuerda. La falta de ejercicio por parte de CSAPEK, de cualquiera de los derechos que este documento le otorga como acreedor y tenedor del Título Valor, no implicará la renuncia a estos derechos ni a sus garantías, ni impedirá a CSAPEK ejercer tales derechos u otros en lo sucesivo. En señal de aceptación y conformidad las partes firman a los ' + dia_firma + ' días del mes de ' + mes_firma + ' de ' + anio_firma + '.',
                                            border: [false, false, false, false],
                                            fontSize: 8,
                                            alignment: 'justify',
                                            bold: false
                                        },
                                    ],
                                ]
                            }
                        },
                        '\n',
                        '\n',
                        {
                            table: {
                                widths: ['50%', '50%'],
                                heights: [10, 10],
                                headerRows: 1,
                                body: [
                                    [
                                        {
                                            text: '............................................',
                                            border: [false, false, false, false],
                                            fontSize: 9,
                                            alignment: 'center',
                                            bold: true
                                        },
                                        {
                                            text: '............................................',
                                            border: [false, false, false, false],
                                            fontSize: 9,
                                            alignment: 'center',
                                            bold: true
                                        }
                                    ],
                                    [
                                        {
                                            text: 'FIRMA DEL ACREEDOR',
                                            border: [false, false, false, false],
                                            fontSize: 9,
                                            alignment: 'center',
                                            bold: true
                                        },
                                        {
                                            text: 'FIRMA DEL DEUDOR',
                                            border: [false, false, false, false],
                                            fontSize: 9,
                                            alignment: 'center',
                                            bold: true
                                        }
                                    ],
                                ]
                            }
                        },
                    ],
                    styles: {
                        header: {
                            fontSize: 5,
                            bold: true
                        },
                        number: {
                            fontSize: 4
                        },
                        small: {
                            fontSize: 3
                        },
                        xsmall: {
                            fontSize: 2.5
                        }
                    }
                }
                resolve(true);
            } catch (e) {
                reject(e);
            }
        });
    }


    public docFacturaCollilla(cliente: any, pedido: any, detalles: any, userdata: any, contador: any) {
        window.parent.caches.delete("call");
        console.group("DATA FACTURA XXXXXXXXXXXXXXXXXX");
        console.log("CLIENTE", cliente);
        console.log("PEDIDO", pedido);
        console.log("DETALLES", detalles);
        console.log("userdata", userdata);
        console.log("userdata", userdata[0].docificacion);
        console.groupEnd();
        let U_Leyenda = '';
        let U_NumeroSiguiente = '';
        let U_LB_NumeroFactura = '';
        let U_LB_NumeroAutorizac = '';
        let U_FechaLimiteEmision = '';
        let U_actividad = '';
        try {
            let docixfix: any = userdata[0].docificacion;
            let greaterTenxx = docixfix.filter((dxa) => dxa.U_NumeroAutorizacion == pedido.U_LB_NumeroAutorizac);
            if (typeof greaterTenxx !== 'undefined') {
                console.log("SSSSS", greaterTenxx);
                U_Leyenda = greaterTenxx[0].U_Leyenda;
                U_NumeroSiguiente = pedido.U_LB_NumeroFactura;
                U_LB_NumeroFactura = pedido.U_LB_NumeroFactura;
                U_LB_NumeroAutorizac = greaterTenxx[0].U_NumeroAutorizacion;
                U_FechaLimiteEmision = greaterTenxx[0].U_FechaLimiteEmision;
                U_actividad = greaterTenxx[0].U_Actividad;
            } else {
                U_Leyenda = '';
                U_NumeroSiguiente = '';
                U_LB_NumeroFactura = '';
                U_LB_NumeroAutorizac = '';
                U_FechaLimiteEmision = '';
                U_actividad = '';
            }
        } catch (e) {
        }

        return new Promise((resolve, reject) => {
            let d = pedido.DocDate; // DocDueDate
            let dr = d.split('-');
            //let rqx: string = `${pedido.U_4NIT}|${pedido.U_LB_NumeroFactura}|${pedido.U_LB_NumeroAutorizac}|${dr[2] + '/' + dr[1] + '/' + dr[0]}|${pedido.DocumentTotal}|${pedido.DocumentTotalPay}|${pedido.U_LB_CodigoControl}|${cliente.FederalTaxId}|0|0|0|0|`;
            let rqx: string = `${userdata[0].empresa[0].nit}|${pedido.U_LB_NumeroFactura}|${pedido.U_LB_NumeroAutorizac}|${dr[2] + '/' + dr[1] + '/' + dr[0]}|${pedido.DocumentTotal}|${pedido.DocumentTotalPay}|${pedido.U_LB_CodigoControl}|${pedido.U_4NIT}|0|0|0|0|`;
            let qrx: object;
            let datafactura: object;
            let textfactura: object;
            let copia: object;
            let impuestos: object;
            let sum_imp = 0;
            let impuestosIce: object;
            let sum_impIce = 0;
            let impuestosIcep: object;
            let sum_impIcep = 0;
            let impuesto = 0;
            copia = {};
            impuestos = {};
            let num_imp = contador[0]["contador"];
            if (pedido.origen == 'outer') {
                qrx = {};
                textfactura = {};
            } else {
                qrx = {
                    qr: rqx,
                    alignment: 'center',
                    eccLevel: 'Q',
                    fit: '50'
                };
                textfactura = {
                    text: `\n\n"ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAIS. EL USO ILICITO
                    DE ESTA SERA SANCIONADO DE ACUERDO A LEY" \n\n\n\n` + U_Leyenda,
                    style: ['xsmall'],
                    alignment: 'center',
                };
            }
            if (num_imp > 1) {
                num_imp = num_imp - 1;
                copia = {
                    text: 'Copia # ' + num_imp + ' de Original',
                    alignment: 'center',
                    style: ['small']
                };
            }
            let cuerpodetalle = [];
            let aux_descripcion = '';
            let $hoy = new Date();
            let $aux_hoy = formatDate($hoy, 'dd/MM/yyyy HH:mm', 'en-US');
            let totalBonificacion = 0;
            let totalDescLinea = 0;
            let totalDescDocumento = 0;
            for (let detal of detalles) {
                let aux_precio: any = formatNumber(detal.LineTotalPay - detal.U_4DESCUENTO, 'en-US', '1.2-2');

                aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription;

                if (detal.bonificacion == 1) {
                    aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription + ' - ( Bonif. )';
                    totalBonificacion = totalBonificacion + detal.U_4DESCUENTO;
                }
                if (detal.bonificacion == 2 || detal.bonificacion == 3) {
                    aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription + ' - ( Desc. )';
                    totalBonificacion = totalBonificacion + detal.U_4DESCUENTO;

                }
                if (detal.U_4DESCUENTO > 0 && detal.bonificacion == 0) {
                    totalDescLinea = totalDescLinea + detal.U_4DESCUENTO;
                    //aux_precio = detal.LineTotalPay - detal.U_4DESCUENTO;
                };

                if (detal.productos.lenght > 0) {
                    aux_descripcion = detal.Quantity + ' - ' + detal.Dscription;
                    for (let prodcom of detal.productos) {
                        aux_descripcion += '\n   -- ' + prodcom.ItemName;
                    }
                }
                let aux_total = formatNumber(detal.Price, 'en-US', '1.2-2');
                let aux_totalneto = formatNumber(detal.Price * detal.Quantity, 'en-US', '1.2-2');
                let aux_descuento = formatNumber(detal.U_4DESCUENTO, 'en-US', '1.2-2');
                let icee = formatNumber(detal.ICEe, 'en-US', '1.2-2');

                let icep = formatNumber(detal.ICEp, 'en-US', '1.2-2');
                sum_imp = Number(sum_imp) + Number(detal.ICEe) + Number(detal.ICEp);
                sum_impIce = sum_impIce + Number(detal.ICEe);
                sum_impIcep = sum_impIcep + Number(detal.ICEp);

                let header: any;
                if ((userdata[0].localizacion == 2) || (userdata[0].localizacion == 1)) {
                    if (!pedido.DocCur) {
                        pedido.DocCur = "BS";
                    }
                    header = {

                        columns: [
                            {
                                text: `${aux_descripcion} \n PRECIO U: ${aux_total}  \n DESC: ${aux_descuento}   \n NETO: ${aux_precio}  \n\n`,
                                //text: `${aux_descripcion} \n PRECIO U: ${aux_total}  \n DESC: ${aux_descuento}  \n ICEE: ${icee} ICEP: ${icep} \n NETO: ${aux_precio}  \n\n`,
                                style: ['small'],
                                width: '70%'
                            }, {
                                text: `\n ${aux_totalneto} ${pedido.DocCur}.`,
                                style: ['small'],
                                alignment: 'right',

                            }
                        ]
                    }
                }
                cuerpodetalle.push(header);
            }
            //aplicando descuento de cabecera
            if (Number(pedido.descuento) && Number(pedido.descuento) > 0) {
                totalDescLinea += Number(pedido.descuento);
            }
            if (userdata[0].localizacion == 2) {


                impuestosIce = {
                    columns: [
                        {
                            text: ' \n TOTAL ICEE ',
                            style: ['small']
                        },
                        {
                            text: '\n  ' + Calculo.formatMoney(sum_impIce) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }
                impuestosIcep = {
                    columns: [
                        {
                            text: 'TOTAL ICEP ',
                            style: ['small']
                        },
                        {
                            text: Calculo.formatMoney(sum_impIcep) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }


                impuestos = {
                    columns: [
                        {
                            text: ' \n CREDITO FISCAL ',
                            style: ['small']
                        },
                        {
                            text: '\n ' + Calculo.formatMoney(0.13 * pedido.DocumentTotalPay) + ' ' + pedido.DocCur,
                            style: ['small'],
                            alignment: 'right',
                        }
                    ]
                }

            }
            /****Genera el documento***/
            try {
                this.objPDF = {
                    pageSize: { width: 80, height: 'auto' },
                    pageMargins: [6, 5, 5, 10],
                    content: [
                        {
                            text: `${userdata[0].empresa[0].nombre}`,
                            alignment: 'center',
                            style: ['small']
                        },

                        {
                            text: `${userdata[0].empresa[0].direccion}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: `Telf:${userdata[0].empresa[0].telefono1}`,
                            alignment: 'center',
                            style: ['small']
                        },
                        {
                            text: `${userdata[0].empresa[0].ciudad} `,
                            alignment: 'center',
                            style: ['small']
                        },
                        '\n',
                        {
                            text: 'ORDEN DE ENTREGA ',
                            style: ['small'],
                            alignment: 'center'
                        },


                        '\n',
                        {
                            text: 'FECHA: ' + formatDate(pedido.DocDate, 'dd/MM/yyyy', 'en-US'),
                            style: ['small'],
                            alignment: 'left'
                        },
                        {
                            text: 'NIT: ' + userdata[0].empresa[0].nit,//solo debe ser nit
                            style: ['small'],
                            alignment: 'left'
                        },

                        {
                            text: 'SR(ES): ' + cliente.CardName,
                            style: ['small'],
                            alignment: 'left'
                        },
                        '\n',
                        {
                            text: 'DATOS DE ENTREGA: ',
                            style: ['small'],
                            alignment: 'left'
                        },
                        {
                            text: "\n " + pedido.comentario,
                            style: ['small'],
                            alignment: 'left'

                        },

                        '\n',
                        {
                            // text: 'USUARIO: ' + userdata[0].idUsuario + ' - ' + userdata[0].nombrePersona + ' ' + userdata[0].apellidoPPersona + ' ' + userdata[0].apellidoMPersona + ' ',
                            text: $aux_hoy + '\n EJ. VENTA:' + userdata[0].config[0].nombrecliente,
                            alignment: 'left',

                            style: ['small']
                        },
                    ],
                    styles: {
                        header: {
                            fontSize: 5,
                            bold: true
                        },
                        number: {
                            fontSize: 4
                        },
                        small: {
                            fontSize: 3
                        },
                        xsmall: {
                            fontSize: 2.5
                        }
                    }
                }
                resolve(true);
            } catch (e) {
                reject(e);
            }
        });
    }

    public async factura(cliente: any, pedido: any, detalles: any, userdata: any, contador: any, acc: string) {
        await window.parent.caches.delete("call");
        console.log("FACTURASSSSS");
        console.log("CLIENTE", cliente);
        console.log("PEDIDO", pedido);
        console.log("DETALLES", detalles);
        console.log("USER DATA", userdata);

        let usa_ices: any;
        if(userdata[0].usa_ices){
            usa_ices = userdata[0].usa_ices;
        }else{
            usa_ices = 0;
        }

        let vercontrato = userdata[0].ctrl_contrato;
        if(vercontrato == "0"){
            return new Promise((resolve, reject) => {
                let qrx: object;
                let titulodoc = '';
                let piedoc = '';
                let datafactura: object;
                let textfactura: object;
                let aux_firmas: any;
                let copia: object;
                let impuestos: object;
                let impuestosicee: object;
                let impuestosicep: object;
                let sum_imp = 0;
                let sum_icep = 0;
                let sum_icee = 0;
                let aux_totalesnetos = 0;
                copia = {};
                impuestos = {};
                impuestosicee = {};
                impuestosicep = {};
                // let num_imp = contador[0]["contador"]?contador[0]["contador"]:0;
                let num_imp = 0;

                let impuesto = 0;
                let aux_descuento: any = 0;

                

                /****Variables del documento***/
                aux_firmas = [
                    {
                        text: '------------------------------------',
                        style: ['small'],
                        alignment: 'center',
                    },
                    {
                        text: 'Recibí conforme',
                        style: ['small'],
                        alignment: 'center',
                    },
                    '\n',
                    '\n',
                    {
                        text: '------------------------------------',
                        style: ['small'],
                        alignment: 'center',
                    },
                    {
                        text: 'Entregué conforme',
                        style: ['small'],
                        alignment: 'center',
                    }
                ];
                let $hoy = new Date();
                let $aux_hoy = formatDate($hoy, 'dd/MM/yyyy HH:mm', 'en-US');
                let anul = ''
                if(pedido.canceled == '3'){
                    anul = '(ANULADO)'
                }


                switch (pedido.DocType) {
                    case ('DOF'):
                        titulodoc = 'DOCUMENTO DE OFERTA \n COTIZACIÓN / PROFORMA \n'+ anul;
                        piedoc = '----- GRACIAS POR SU INTERÉS ----- \n' + $aux_hoy;
                        break;
                    case ('DOP'):
                        titulodoc = 'DOCUMENTO DE PEDIDO \n' + anul;
                        piedoc = '----- GRACIAS POR SU PEDIDO -----\n' + $aux_hoy;
                        break;
                    case ('DFA'):
                        qrx = {
                            qr: `|${cliente.FederalTaxId}|0|0|${pedido.DocDueDate}|${pedido.DocTotal}|${pedido.DocTotal}|${pedido.U_LB_CodigoControl}|${cliente.FederalTaxId}|0|0|0|0|`,
                            alignment: 'center',
                            eccLevel: 'H', fit: '70'
                        };
                        datafactura = {
                            text: 'CODIGO DE CONTROL: ' + pedido.U_LB_CodigoControl + '\n ' +
                                'AUTORIZACION: ' + userdata[0].docificacion[0].U_NumeroAutorizacion + '\n ' +
                                'NUMERO:: ' + userdata[0].docificacion[0].U_NumeroSiguiente,
                            style: ['small']
                        };
                        textfactura = {
                            text: userdata[0].docificacion[0].U_Leyenda,
                            style: ['small'],
                            alignment: 'center',
                        };
                        titulodoc = 'FACTURA \n'+ anul;;
                        piedoc = '----- GRACIAS POR SU COMPRA -----\n' + $aux_hoy;
                        break;
                    case ('DOE'):
                        titulodoc = 'DOCUMENTO DE ENTREGA \n' + anul;;
                        piedoc = '----- GRACIAS POR SU COMPRA -----\n' + $aux_hoy;
                        break;
                }
                // let total = 0;
                // let descuento = 0;
                let cuerpodetalle = [];
                let aux_descripcion = '';
                let totaldescuento = 0;
                for (let detal of detalles) {

                    let aux_precio = formatNumber((Number(detal.LineTotalPay)), 'en-US', '1.2-2');
                    aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription;
                    if (detal.bonificacion == 1) {
                        aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription + '( Bonif. )';
                    }
                    if (detal.bonificacion == 2) {
                        aux_descripcion = detal.Quantity + ' - ' + detal.unidadid + ' - ' + detal.Dscription + '( Desc. )';
                    }
                    if (detal.productos.lenght > 0) {
                        aux_descripcion = detal.Quantity + ' - ' + detal.Dscription;
                        for (let prodcom of detal.productos) {
                            aux_descripcion += '\n   -- ' + prodcom.ItemName;
                        }
                    }
                    let aux_total = formatNumber(detal.Price, 'en-US', '1.2-2');
                    //let aux_totalneto = formatNumber((detal.Price * detal.Quantity)-, 'en-US', '1.2-2');
                    console.log("EACH detal.U_4DESCUENTO ", detal.U_4DESCUENTO);

                    aux_descuento += detal.U_4DESCUENTO;
                    console.log("EACH aux_descuento ", aux_descuento);
                    let icee = formatNumber(detal.ICEe, 'en-US', '1.2-2');
                    let icep = formatNumber(detal.ICEp, 'en-US', '1.2-2');
                    sum_imp = Number(sum_imp) + Number(detal.ICEe) + Number(detal.ICEp);
                    sum_icee = sum_icee + Number(detal.ICEe);
                    sum_icep = sum_icep + Number(detal.ICEp);

                    console.log("es false",detal.bonificacion);
                    if(detal.bonificacion == 'false'){
                        console.log("es false");
                        if(detal.U_4DESCUENTO == 0){
                            console.log("U_4DESCUENTO == 0");
                            if(detal.DiscTotalPrcnt > 0){
                                let aux = detal.DiscTotalPrcnt/100;
                                let valor = detal.LineTotal*aux;
                                totaldescuento = (totaldescuento+valor);
                            }else{
                                totaldescuento = (totaldescuento+detal.DiscTotalMonetary);
                            }
                        }else{
                            totaldescuento = totaldescuento+detal.U_4DESCUENTO;
                        }
                    }


                    let header: any;

                    if (userdata[0].localizacion == 2) {
                        
                        if(usa_ices == 1){

                            aux_totalesnetos = aux_totalesnetos+(detal.xneto);
                            header = {
                                columns: [
                                    {
                                        text: ` ${aux_descripcion} \n PRECIO U: ${aux_total} BRUTO: ${formatNumber(detal.LineTotal, 'en-US', '1.2-2')} DESC: ${formatNumber(detal.U_4DESCUENTO, 'en-US', '1.2-2')} ICEE: ${icee} ICEP: ${icep}  \n\n`,
                                        style: ['small'],
                                        width: '70%'
                                    }, {
                                        text: `\n ${formatNumber(detal.xneto, 'en-US', '1.2-2')}  ${pedido.DocCur}.`,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            }
                        }else{
                            aux_totalesnetos = aux_totalesnetos+(detal.xneto);
                            header = {
                                columns: [
                                    {
                                        text: ` ${aux_descripcion} \n PRECIO U: ${aux_total} BRUTO: ${formatNumber(detal.LineTotal, 'en-US', '1.2-2')} DESC: ${formatNumber(detal.U_4DESCUENTO, 'en-US', '1.2-2')} \n\n`,
                                        style: ['small'],
                                        width: '70%'
                                    }, {
                                        text: `\n ${formatNumber(detal.xneto, 'en-US', '1.2-2')}  ${pedido.DocCur}.`,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            }
                        }
                    }
                    ;

                    cuerpodetalle.push(header);
                }

                if (num_imp > 1) {
                    num_imp = num_imp - 1;
                    copia = {
                        text: 'Copia # ' + num_imp + ' de Original',
                        alignment: 'center',
                        style: ['small']
                    };
                }
                if (pedido.estadosend == "6" || pedido.estadosend == "7") {
                    copia = {
                        text: 'ANULADO',
                        alignment: 'center',
                        style: ['small']
                    };
                }

                if (userdata[0].localizacion == 2 && usa_ices == 1) {
                    impuestos = {
                        columns: [
                            {
                                text: 'TOTAL ICE ',
                                style: ['small']
                            },
                            {
                                text: Calculo.formatMoney(sum_imp) + ' ' + pedido.DocCur,
                                style: ['small'],
                                alignment: 'right',
                            }

                        ]
                    };


                    impuestosicep = {
                        columns: [
                            {
                                text: 'TOTAL ICEP ',
                                style: ['small']
                            },
                            {
                                text: Calculo.formatMoney(sum_icep) + ' ' + pedido.DocCur,
                                style: ['small'],
                                alignment: 'right',
                            }
                        ]
                    };
                    impuestosicee = {
                        columns: [
                            {
                                text: 'TOTAL ICEE ',
                                style: ['small']
                            },
                            {
                                text: Calculo.formatMoney(sum_icee) + ' ' + pedido.DocCur,
                                style: ['small'],
                                alignment: 'right',
                            }
                        ]
                    };
                }
                /****Genera el documento***/
                try {
                    this.objPDF = {
                        pageSize: { width: 80, height: 'auto' },
                        pageMargins: [6, 5, 5, 10],
                        content: [
                            {
                                text: `${userdata[0].empresa[0].nombre}`,
                                alignment: 'center',
                                style: ['small']
                            },
                            {
                                text: `${userdata[0].empresa[0].direccion}`,
                                alignment: 'center',
                                style: ['small']
                            },
                            {
                                text: `${userdata[0].empresa[0].ciudad} - ${userdata[0].empresa[0].pais}`,
                                alignment: 'center',
                                style: ['small']
                            },
                            {
                                text: titulodoc,
                                alignment: 'center',
                                style: ['number']
                            },
                            copia,
                            '\n',
                            {
                                columns: [
                                    {
                                        text: 'FECHA DOC:',
                                        style: ['small']
                                    },
                                    {
                                        text: formatDate(pedido.DocDate, 'dd/MM/yyyy', 'en-US'),
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            }, {
                                columns: [
                                    {
                                        text: 'RAZÓN SOCIAL:',
                                        style: ['small'],
                                        width: '40%'
                                    },
                                    {
                                        text: pedido.U_4RAZON_SOCIAL,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            }, {
                                columns: [
                                    {
                                        text: 'NIT/CI: ',
                                        style: ['small']
                                    },
                                    {
                                        text: pedido.U_4NIT,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            }, {
                                columns: [
                                    {
                                        text: '\n CANTIDAD PRODUCTO',
                                        style: ['small'],
                                        bold: true,
                                        width: '70%'
                                    },
                                    {
                                        text: '\n SUBTOTAL',
                                        style: ['small'],
                                        alignment: 'right',
                                        bold: true
                                    }
                                ]
                            },
                            cuerpodetalle,
                            {
                                columns: [
                                    {
                                        text: 'TOTAL BRUTO ',
                                        style: ['small']
                                    },
                                    {
                                        text: Calculo.formatMoney(aux_totalesnetos) + ' ' + pedido.DocCur,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            },
                            {
                                columns: [
                                    {
                                        text: 'DESCUENTO ',
                                        style: ['small']
                                    },
                                    {
                                        text: Calculo.formatMoney(totaldescuento) + ' ' + pedido.DocCur,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            },
                            // detalleImpuesto,
                            //impuestos,
                            impuestosicee,
                            impuestosicep,
                            {
                                columns: [
                                    {
                                        text: 'TOTAL ',
                                        style: ['small']
                                    },
                                    {
                                        text: Calculo.formatMoney(pedido.DocumentTotalPay) + ' ' + pedido.DocCur,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            },
                            {
                                columns: [
                                    {
                                        text: '\n SON: ' + this.numeletra.run(pedido.DocumentTotalPay, pedido.DocCur) + " ",//+ pedido.DocCur,
                                        style: ['xsmall'],
                                        alignment: 'left',
                                    }
                                ]
                            },

                            {
                                text: '\n NRO. DOC.: ' + pedido.cod,
                                style: ['xsmall']

                            },
                            {
                                text: 'OBSERVACIÓN: ' + pedido.comentario,
                                style: ['xsmall']
                            },
                            {
                                text: 'COD.VENDEDOR: ' + userdata[0].config[0].codEmpleadoVenta + ' -' + userdata[0].config[0].nombrecliente,
                                style: ['xsmall']
                            },
                            {
                                text: 'USUARIO: ' + userdata[0].idUsuario + ' - ' + userdata[0].nombrePersona + ' ' + userdata[0].apellidoPPersona + ' ' + userdata[0].apellidoMPersona + ' ',
                                style: ['xsmall']
                            },
                            {
                                text: 'CONDICIÓN PAGO: ' + pedido.displayCondicion,
                                style: ['xsmall']
                            },
                            {
                                text: 'VÁLIDO HASTA:' + formatDate(pedido.DocDueDate, 'dd/MM/yyyy', 'en-US'),
                                style: ['xsmall']
                            },
                            {
                                text: 'ASESOR VTA.: ' + userdata[0].config[0].codEmpleadoVenta + ' -' + userdata[0].config[0].nombrecliente,
                                style: ['xsmall'],
                            },
                            {
                                text: 'COD.CLIENTE.: ' + cliente.CardCode,
                                style: ['xsmall'],
                            },
                            // {
                            //     text: 'ESTADO CTA. : ' + Calculo.formatMoney(cliente.CurrentAccountBalance) + ' ' + pedido.DocCur,
                            //     style: ['xsmall'],
                            // },

                            datafactura,

                            {
                                text: ` \nEl cliente acepta y reconoce que los precios y cantidades son las pactadas; por lo tanto, no se aceptan cambios ni devoluciones`,
                                alignment: 'center',
                                style: ['small']
                            },
                            '\n',
                            {
                                text: piedoc,
                                alignment: 'center',
                                style: ['small']
                            },
                            '\n',
                            '\n',
                            aux_firmas

                        ],
                        styles: {
                            header: {
                                fontSize: 5,
                                bold: true
                            },
                            number: {
                                fontSize: 4
                            },
                            small: {
                                fontSize: 3
                            },
                            xsmall: {
                                fontSize: 2.5
                            }
                        }
                    };
                    resolve(true);
                } catch (e) {
                    reject(e);
                }
            });
        }else{
            return new Promise((resolve, reject) => {
           
                let qrx: object;
                let titulodoc = '';
                let piedoc = '';
                let datafactura: object;
                let textfactura: object;
                let aux_firmas: any;
                let copia: object;
                let ciudad: object;
                let impuestos: object;
                let impuestosicee: object;
                let impuestosicep: object;
                let sum_imp = 0;
                let sum_icep = 0;
                let sum_icee = 0;
                copia = {};
                ciudad = {};
                impuestos = {};
                impuestosicee = {};
                impuestosicep = {};
                console.log("llega");
                //let num_imp = contador[0]["contador"];
                let num_imp = 0;
                let impuesto = 0;
                let aux_descuento: any = 0;
                /****Variables del documento***/
                aux_firmas = [
                    {
                        columns: [
                            {
                                text: '-------------------------------------- ',
                                style: ['small'],
                                alignment: 'center',
                            },
                            {
                                text: '-------------------------------------- ',
                                style: ['small'],
                                alignment: 'center',
                            },
                        ]
                    },
                    {
                        columns: [
                            {
                                text: 'Recibi',
                                style: ['small'],
                                alignment: 'center',
                            },
                            {
                                text: 'Entregue',
                                style: ['small'],
                                alignment: 'center',
                            },
                        ]
                    },
                ];
    
                
                let $hoy = new Date();
                
                let $aux_hoy = formatDate($hoy, 'dd/MM/yyyy HH:mm', 'en-US');
                switch (pedido.DocType) {
                    case ('DOF'):
                        titulodoc = 'DOCUMENTO DE OFERTA \n COTIZACION / PROFORMA';
                        piedoc = '----- GRACIAS POR SU INTERES ----- \n' + $aux_hoy;
                        break;
                    case ('DOP'):
                        titulodoc = 'DOCUMENTO DE PEDIDO';
                        piedoc = '----- GRACIAS POR SU PEDIDO -----\n' + $aux_hoy;
                        break;
                    case ('DFA'):
                        qrx = {
                            qr: `|${cliente.FederalTaxId}|0|0|${pedido.DocDueDate}|${pedido.DocTotal}|${pedido.DocTotal}|${pedido.U_LB_CodigoControl}|${cliente.FederalTaxId}|0|0|0|0|`,
                            alignment: 'center',
                            eccLevel: 'H', fit: '70'
                        };
                        datafactura = {
                            text: 'CODIGO DE CONTROL: ' + pedido.U_LB_CodigoControl + '\n ' +
                                'AUTORIZACION: ' + userdata[0].docificacion[0].U_NumeroAutorizacion + '\n ' +
                                'NUMERO:: ' + userdata[0].docificacion[0].U_NumeroSiguiente,
                            style: ['small']
                        };
                        textfactura = {
                            text: userdata[0].docificacion[0].U_Leyenda,
                            style: ['small'],
                            alignment: 'center',
                        };
    
                        if(acc == '0'){
                            titulodoc = 'FACTURA';
                        }else{
                            titulodoc = 'NOTA DE ENTREGA';
                        }
    
    
                        piedoc = '----- GRACIAS POR SU COMPRA -----\n' + $aux_hoy;
                        break;
    
    
                    case ('DOE'):
                        titulodoc = 'DOCUMENTO DE ENTREGA';
                        piedoc = '----- GRACIAS POR SU COMPRA -----\n' + $aux_hoy;
                        break;
                }
                // let total = 0;
                // let descuento = 0;
                let cuerpodetalle = [];
                let aux_descripcion = '';
                for (let detal of detalles) {
    
    
    
    
                    let aux_precio = formatNumber((Number(detal.LineTotalPay)), 'en-US', '1.2-2');
                    aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' +detal.Dscription;
                    if (detal.bonificacion == 1) {
                        aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' +detal.Dscription + '( Bonif. )';
                    }
                    if (detal.bonificacion == 2) {
                        aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' +detal.unidadid + ' - ' + detal.Dscription + '( Desc. )';
                    }
                    if (detal.productos.lenght > 0) {
                        aux_descripcion = detal.Quantity + ' - ' + detal.ItemCode +' \n ' + detal.Dscription;
                        for (let prodcom of detal.productos) {
                            aux_descripcion += '\n   -- ' + prodcom.ItemName;
                        }
                    }
                    
    
                    if(detal.U_4DESCUENTO == 'undefined' || detal.U_4DESCUENTO == 'NULL' || detal.U_4DESCUENTO == 'NaN'){
                        detal.U_4DESCUENTO = 0; 
                    }
                    console.log("Rafael Documento");
                    let aux_total = formatNumber(detal.Price, 'en-US', '1.2-2');
                    let aux_des_u = Number(detal.U_4DESCUENTO)/Number(detal.Quantity);
                    let porc = ((Number(aux_des_u)*100)/Number(detal.Price));
                    
                    console.log(porc);
    
                    let aux_total_u = Number(detal.Price)-Number(aux_des_u);
                    let totallin = 0;
    
    
    
    
    
                    if(detal.xneto > 0 ){
                        totallin = detal.xneto;
                    }else{
                        totallin  = detal.LineTotalPay
                    }
    
                    //   let aux_totalneto = formatNumber((detal.Price * detal.Quantity)-, 'en-US', '1.2-2');
                    console.log("EACH detal.U_4DESCUENTO ", detal.U_4DESCUENTO);
    
                    aux_descuento += detal.U_4DESCUENTO;
                    console.log("EACH aux_descuento ", aux_descuento);
                    let icee = formatNumber(detal.ICEe, 'en-US', '1.2-2');
                    let icep = formatNumber(detal.ICEp, 'en-US', '1.2-2');
                    sum_imp = Number(sum_imp) + Number(detal.ICEe) + Number(detal.ICEp);
                    sum_icee = sum_icee + Number(detal.ICEe);
                    sum_icep = sum_icep + Number(detal.ICEp);
    
                    //let detalle_linea = `\n ** ${aux_descripcion}  \n PRECIO U: ${aux_total} BRUTO: ${aux_totalneto} DESC: ${aux_descuento}  \n NETO: ${aux_precio}`;
                    // 2 COMPANEX
                    /* if (userdata[0].localizacion == "2") {
                            console.log('entra a localizacion == 2');
                            detalle_linea += ` ICEE: ${Calculo.formatMoney(detal.ICEe)}  ICEP: ${Calculo.formatMoney(detal.ICEp)} ICET: ${Calculo.formatMoney(detal.ICEt)} `;
                            impuesto += Number(detal.ICEt);
                        }*/
                    let header: any;
                    /*
                               detalle_linea += '\n\n';
                               let header: any = {
                                   columns: [
                                       {
                                           text: `${aux_descripcion} \n PRECIO U: ${aux_total} BRUTO: ${aux_totalneto} DESC: ${aux_descuento} \n\n`,
                                           style: ['small'],
                                           width: '70%'
                                       }, {
                                           text: `\n ${aux_totalneto} ${pedido.DocCur}.`,
                                           style: ['small'],
                                           alignment: 'right',
                                       }
                                   ],
                               };
                               */
    
    
                    if (userdata[0].localizacion == 2 && usa_ices == 1) {
                        header = {
                            columns: [
                                {
                                    text: ` ${aux_descripcion} \n PRECIO U: ${aux_total}  DESC U: ${formatNumber(aux_des_u, 'en-US', '1.2-3')} (${Math.round(porc)}%)  \n PRE U - DESC U: ${formatNumber(aux_total_u, 'en-US', '1.2-3')}   DESC: ${formatNumber(detal.U_4DESCUENTO, 'en-US', '1.2-2')} (${Math.round(porc)}%) ICEE: ${icee} ICEP: ${icep}  \n\n`,
                                    style: ['xsmall'],
                                    width: '70%'
                                },{
                                    text: `\n ${formatNumber(totallin, 'en-US', '1.2-2')}  ${pedido.DocCur}.`,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        }
                    } else {
                        header = {
                            columns: [
                                {
                                    text: ` ${aux_descripcion} \n PRECIO U: ${aux_total}  DESC U: ${formatNumber(aux_des_u, 'en-US', '1.2-3')} (${Math.round(porc)}%)  \n PRE U - DESC U: ${formatNumber(aux_total_u, 'en-US', '1.2-3')}   DESC: ${formatNumber(detal.U_4DESCUENTO, 'en-US', '1.2-2')} (${Math.round(porc)}%)   \n\n`,
                                    style: ['xsmall'],
                                    width: '70%'
                                }, {
                                    text: `\n ${formatNumber(totallin, 'en-US', '1.2-2')}  ${pedido.DocCur}.`,
                                    style: ['small'],
                                    alignment: 'right',
                                }
                            ]
                        }
                    }
                    ;
    
                    cuerpodetalle.push(header);
                }
                // adicionando descuencuento de cabecera
                if (Number(pedido.descuento) && Number(pedido.descuento) > 0) {
                    aux_descuento += Number(pedido.descuento);
                }
                if(acc == '0'){
                    if (num_imp > 1) {
                        num_imp = num_imp - 1;
                        copia = {
                            text: 'Copia # ' + num_imp + ' de Original',
                            alignment: 'center',
                            style: ['small']
                        };
                    }
                    if (pedido.estadosend == "6" || pedido.estadosend == "7") {
                        copia = {
                            text: 'ANULADO',
                            alignment: 'center',
                            style: ['small']
                        };
                    }
    
                    ciudad ={
                        text: `${userdata[0].empresa[0].ciudad} - ${userdata[0].empresa[0].pais}`,
                        alignment: 'center',
                        style: ['small']
                    }
    
                }else{
                    copia = {};
                    ciudad ={};
                }
                if (userdata[0].localizacion == 2 && usa_ices == 1) {
                    impuestos = {
                        columns: [
                            {
                                text: 'TOTAL ICE ',
                                style: ['small']
                            },
                            {
                                text: Calculo.formatMoney(sum_imp) + ' ' + pedido.DocCur,
                                style: ['small'],
                                alignment: 'right',
                            }
    
                        ]
                    };
    
    
                    impuestosicep = {
                        columns: [
                            {
                                text: 'TOTAL ICEP ',
                                style: ['small']
                            },
                            {
                                text: Calculo.formatMoney(sum_icep) + ' ' + pedido.DocCur,
                                style: ['small'],
                                alignment: 'right',
                            }
                        ]
                    };
                    impuestosicee = {
                        columns: [
                            {
                                text: 'TOTAL ICEE ',
                                style: ['small']
                            },
                            {
                                text: Calculo.formatMoney(sum_icee) + ' ' + pedido.DocCur,
                                style: ['small'],
                                alignment: 'right',
                            }
                        ]
                    };
                } else {
                    impuestosicep = {
                        columns: [
                            {
                                text: 'TOTAL IMPUESTOS ',
                                style: ['small']
                            },
                            {
                                text: Calculo.formatMoney(sum_icep) + ' ' + pedido.DocCur,
                                style: ['small'],
                                alignment: 'right',
                            }
                        ]
                    };
                }
                console.log("llega2");
                /****Genera el documento***/
                try {
                    this.objPDF = {
                        pageSize: { width: 80, height: 'auto' },
                        pageMargins: [6, 5, 5, 10],
                        content: [

                            {
                                text: `${userdata[0].empresa[0].nombre}`,
                                bold: true,
                                alignment: 'center',
                                style: ['small']
                            },

                           /* {
                                text: `${userdata[0].empresa[0].nombre}`,
                                alignment: 'center',
                                style: ['small']
                            },*/
                            {
                                text: `${userdata[0].empresa[0].direccion}`,
                                alignment: 'center',
                                style: ['small']
                            },
                            ciudad,
                            {
                                text: titulodoc,
                                alignment: 'center',
                                style: ['number']
                            },
                            copia,
                            {
                                text: '.',
                                alignment: 'center',
                                style: ['number'],
                                color: '#ffffff'
                            },
                            {
                                text: '.',
                                alignment: 'center',
                                style: ['number'],
                                color: '#ffffff'
                            },
                            
                            {
                                columns: [
                                    {
                                        text: 'FECHA DOC:',
                                        style: ['small']
                                    },
                                    {
                                        text: formatDate(pedido.DocDate, 'dd/MM/yyyy', 'en-US'),
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            }, {
                                columns: [
                                    {
                                        text: 'RAZON SOCIAL:',
                                        style: ['small'],
                                        width: '40%'
                                    },
                                    {
                                        text: pedido.U_4RAZON_SOCIAL,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            }, {
                                columns: [
                                    {
                                        text: 'NIT/CI: ',
                                        style: ['small']
                                    },
                                    {
                                        text: pedido.U_4NIT,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            }, {
                                columns: [
                                    {
                                        text: '\n DETALLE',
                                        style: ['small'],
                                        bold: true,
                                        width: '100%',
                                        alignment: 'center',
                                    },
                                   /* {
                                        text: '\n SUBTOTAL',
                                        style: ['small'],
                                        alignment: 'right',
                                        bold: true
                                    }*/
                                ]
                            },
                            cuerpodetalle,
                            {
                                columns: [
                                    {
                                        text: 'SUBTOTAL ',
                                        style: ['small']
                                    },
                                    {
                                        text: Calculo.formatMoney(Number(pedido.DocumentTotalPay) + Number(pedido.descuento)) + ' ' + pedido.DocCur,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            },
                            {
                                columns: [
                                    {
                                        text: 'IMPUESTO ',
                                        style: ['small']
                                    },
                                    {
                                        text: Calculo.formatMoney(Number(pedido.DocumentTotalPay) * 0.18) + ' ' + pedido.DocCur,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            },
                            {
                                columns: [
                                    {
                                        text: 'DESCUENTO ',
                                        style: ['small']
                                    },
                                    {
                                        text: Calculo.formatMoney(pedido.descuento) + ' ' + pedido.DocCur,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            },
                            // detalleImpuesto,
                            //impuestos,
                           // impuestosicee,
                           // impuestosicep,
                            {
                                columns: [
                                    {
                                        text: 'TOTAL ',
                                        style: ['small']
                                    },
                                    {
                                        text: Calculo.formatMoney(Number(pedido.DocumentTotalPay) * 1.18) + ' ' + pedido.DocCur,
                                        style: ['small'],
                                        alignment: 'right',
                                    }
                                ]
                            },
                            {
                                columns: [
                                    {
                                        text: '\n SON: ' + this.numeletra.run((Number(pedido.DocumentTotalPay) * 1.18), pedido.DocCur) + " ",//+ pedido.DocCur,
                                        style: ['xxsmall'],
                                        alignment: 'left',
                                    }
                                ]
                            },
    
                            {
                                text: '\n NRO. DOC.: ' + pedido.cod,
                                style: ['xsmall']
    
                            },
                            {
                                text: 'OBSERVACION: ' + pedido.comentario,
                                style: ['xsmall']
                            },
                            /*{
                                text: 'COD.VENDEDOR: ' + userdata[0].config[0].codEmpleadoVenta + ' -' + userdata[0].config[0].nombrecliente,
                                style: ['xsmall']
                            },*/
                            {
                                text: 'USUARIO: ' + userdata[0].idUsuario + ' - ' + userdata[0].nombrePersona + ' ' + userdata[0].apellidoPPersona + ' ' + userdata[0].apellidoMPersona + ' ',
                                style: ['xsmall']
                            },
                            /*{
                                text: 'CONDICIÓN PAGO: ' + pedido.displayCondicion,
                                style: ['xsmall']
                            },*/
                            {
                                text: 'VALIDO HASTA:' + formatDate(pedido.DocDueDate, 'dd/MM/yyyy', 'en-US'),
                                style: ['xsmall']
                            },
                            /*{
                                text: 'ASESOR VTA.: ' + userdata[0].config[0].codEmpleadoVenta + ' -' + userdata[0].config[0].nombrecliente,
                                style: ['xsmall'],
                            },*/
                            {
                                text: 'COD.CLIENTE.: ' + cliente.CardCode,
                                style: ['xsmall'],
                            },
                            // {
                            //     text: 'ESTADO CTA. : ' + Calculo.formatMoney(cliente.CurrentAccountBalance) + ' ' + pedido.DocCur,
                            //     style: ['xsmall'],
                            // },
    
                            //datafactura,
    
                            {
                                text: ` \nEl cliente acepta y reconoce que los precios y cantidades son las pactadas; por lo tanto, no se aceptan cambios ni devoluciones`,
                                alignment: 'center',
                                style: ['xsmall']
                            },
                            {
                                text: '.',
                                alignment: 'center',
                                style: ['number'],
                                color: '#ffffff'
                            },
                            {
                                text: piedoc,
                                alignment: 'center',
                                style: ['small']
                            },
                            '\n',
                            {
                                text: '.',
                                alignment: 'center',
                                style: ['number'],
                                color: '#ffffff'
                            },
                            aux_firmas
    
                        ],
                        styles: {
                            header: {
                                fontSize: 5,
                                bold: true
                            },
                            number: {
                                fontSize: 4
                            },
                            small: {
                                fontSize: 3
                            },
                            xsmall: {
                                fontSize: 2.5
                            },
                            xxsmall: {
                                fontSize: 1.5
                            }
                        }
                    };
                    resolve(true);
                } catch (e) {
                    reject(e);
                }
            });
        }
    }

    public async generaPDF() {
        window.parent.caches.delete("call");
        let bodyarr = [];
        bodyarr.push(['Producto', 'Cant.', 'P/U', 'Desc.', 'total']);
        bodyarr.push([{ text: 'TOTAL', bold: true }, '', '', '', 400]);
        let doc: any = {
            watermark: '',
            pageSize: 'A7',
            footer: {
                columns: ['', { text: 'Xmobile - Exxis', alignment: 'center' }]
            },
            content: [
                { text: 'N°' + 1, alignment: 'right', style: 'header' },
                { text: 'Xmobile', alignment: 'right', style: 'header' },
                { text: 'COMPROBANTE DE PEDIDO', alignment: 'center', style: 'header' },
                { text: ' ', alignment: 'center', style: 'header' },
                { text: ' ', alignment: 'center', style: 'header' },
                { text: 'miguel' },
                { text: 'Direccion:' },
                { text: '' },
                { text: ' ', alignment: 'center' },
                { text: ' ', alignment: 'center' },
                {
                    layout: 'lightHorizontalLines',
                    table: {
                        headerRows: 1,
                        widths: ['*', 'auto', 100, '*', '*'],
                        body: bodyarr
                    }
                }
            ],
            styles: {
                header: {
                    fontSize: 6,
                    bold: true,
                },
                subheader: {
                    fontSize: 6,
                    bold: true,
                    margin: [0, 5, 0, 0]
                },
                story: {
                    fontSize: 6,
                    italic: true,
                    alignment: 'center'
                }
            }
        };
    }



}
