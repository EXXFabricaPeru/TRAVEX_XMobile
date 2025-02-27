import { Injectable } from '@angular/core';
import { HTTP } from '@ionic-native/http/ngx';
import { ConfigService } from "../models/config.service";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { Documentos } from "../models/documentos";
import { Detalle } from "../models/detalle";
import { Clientes } from "../models/clientes";
import { Contactos } from "../models/contactos";
import { Pagos } from "../models/pagos";
import { Visitas } from "../models/visitas";
import * as moment from 'moment';
import { FacturasPagos } from '../models/facturasPagos';
import { environment } from '../../environments/environment';
import { Databaseconf } from "../models/databaseconf";
import { Network } from "@ionic-native/network/ngx";
import { Calculo } from "../../app/utilsx/calculo";
import { IPagos } from './../types/IPagos';
import { GlobalConstants } from "../../global";
import { PagosService } from '../services/pagos.service'
import { Almacenes } from '../models/almacenes';



@Injectable({
    providedIn: 'root'
})
export class DataService {
    public path: any;
    public arraux: any;

    constructor(private http: HTTP, private network: Network, private configService: ConfigService, private spinnerDialog: SpinnerDialog, private pagosservice: PagosService) {
    }

    public async exportNumeracionSync() {
        console.log("DEVD exportNumeracionSync() ");
        return new Promise(async (resolve, reject) => {
            try {
                let dataresp: any = await this.configuraciones();
                console.log("DEVD dataresp ", dataresp);
                let inix: any = dataresp.data;
                let ini: any = JSON.parse(inix);
                console.log("DEVD ini ", ini)
                await this.configService.setNumeracion(ini);
                resolve(ini);
            } catch (e) {
                reject(false);
            }
        });
    }

    public async exportDocumentosAsinc(iddoc): Promise<any> {

        console.log("exportDocumentosAsinc() ");
        let datauser: any = await this.configService.getSession();
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        this.arraux = [];
        let documentos = new Documentos();
        let exportdata2: any = await documentos.dataExport2(id[0]);
        console.log("exportdata2 ", exportdata2);
        let exportdata: any = await documentos.dataExport(id[0]);
        console.log("exportdata ", exportdata);
        let aux = exportdata.length;
        let aux2 = 0;
        let DocImportados = [];

        for await (let item of exportdata) {
            aux2 = aux2 + 1;
            console.log("AUX2", aux2);

            let detalle = new Detalle();
            let detalledoc: any;
            console.log("idSucursal ", item.idSucursalMobile);

            item.idSucursalMobile = await documentos.selectDocumentSucursal(item.CardCode, item.idSucursalMobile);
            detalledoc = await detalle.find(item.cod);
            console.log("detalledoc ", detalledoc);

            let camposusuariodetalle = [];
            let idfrom = 2
            let camposu = datauser[0].campodinamicos;
            for (let i = 0; i < camposu.length; i++) {
                if (camposu[i].Objeto == idfrom) {
                    let campo = "campousu" + camposu[i].Nombre;
                    camposusuariodetalle.push({
                        Objeto: camposu[i].Objeto,
                        cmidd: camposu[i].cmidd,
                        tabla: camposu[i].tabla,
                        campo: campo,
                        valor: item[campo]
                    });
                }
            }

            detalledoc[0].camposusuario = camposusuariodetalle;

            //et conificacion: any = await detalle.sumaBonificacion(item.id);

            let clonado = 0;
            let documentosx = new Documentos();
            let clone: any = await documentosx.findexe(item.clone);
            console.log("clone ", clone);
            if (typeof clone !== 'undefined') {
                clonado = clone.cod;
                if (clone.origen !== "inner") {
                    clonado = 0;
                }
            }
            item.idDocPedido = item.cod;
            item.DocTotal = item.DocumentTotalPay;
            item.DocTotalPay = item.DocumentTotalPay;
            item.TotalDiscMonetary = item.descuento;
            item.TotalDiscPrcnt = item.tipodescuento;
            item.origenclone = clonado;
            item.U_NumeroAutorizacion = id[0].docificacion.length ? id[0].docificacion[0].U_NumeroAutorizacion : 0;

            if (item.tipoestado == 'anulado') {
                item.estado = 6;

                console.log(" item anulado frontend ", item);
            }


            let camposusuario = [];
            idfrom = 1
            camposu = datauser[0].campodinamicos;
            for (let i = 0; i < camposu.length; i++) {
                if (camposu[i].Objeto == idfrom) {
                    let campo = "campousu" + camposu[i].Nombre;
                    camposusuario.push({
                        Objeto: camposu[i].Objeto,
                        cmidd: camposu[i].cmidd,
                        tabla: camposu[i].tabla,
                        campo: campo,
                        valor: item[campo]
                    });
                }
            }

            item.camposusuario = camposusuario;
 
            const pagos = await this.exportPagosFindPagoIdAsyncOnlySelect(item.cod);
            const cadenaCabezera = await documentosx.findOne(item.cod);
            // const cadenaDetalle = await documentosx.findOneDetalles(item.cod);
            // console.log("cadenaDetalle ", cadenaDetalle);
            if(pagos.length > 0){
                console.log("ROMERO 4.1",pagos);

                let camposusuariopago = [];
                idfrom = 6
                camposu = datauser[0].campodinamicos;
                for (let i = 0; i < camposu.length; i++) {
                    if (camposu[i].Objeto == idfrom) {
                        let campo = "campousu" + camposu[i].Nombre;
                        camposusuariopago.push({
                            Objeto: camposu[i].Objeto,
                            cmidd: camposu[i].cmidd,
                            tabla: camposu[i].tabla,
                            campo: campo,
                            valor: item[campo]
                        });
                    }
                }

                pagos[0].camposusuario = camposusuariopago;
            }



            const cadenaPago = await this.findOnePago(item.cod);


            let d = {
                "usuariodataid": id[0].idUsuario,
                "catidadDetalle": detalledoc.length,
                "header": item,
                "detalles": detalledoc,
                "pagos": pagos[0],
                "version": environment.version,
                "cadenaPago": JSON.stringify(cadenaPago),
                "cadenaCabezera": JSON.stringify(cadenaCabezera),
                "cadenaDetalle": JSON.stringify(detalledoc)
            };


            if (item.origen == "inner" && aux2 <= aux) {
                let accion = 0;
                if (this.arraux.length > 0) {
                    for (let i = 0; i < this.arraux.length; i++) {
                        if (this.arraux[i].header.idDocPedido == d.header.idDocPedido) {
                            accion = 1;
                        }
                    }
                }
                if (accion == 0) {
                    this.arraux.push(d);
                }
            } else {
                DocImportados.push(d);
            }
        }
        let documentosx = new Documentos();
        console.log("EXPORTA DATA ", this.arraux);
        


        if (this.arraux.length > 0 || DocImportados.length > 0) {
            let xm: Pagos = new Pagos();
            console.log("data factura con pago", this.arraux);

            for await (let dat of this.arraux) {
                if (iddoc == dat.header.cod) {
                    let datoslocal = JSON.stringify(dat)
                    console.log("exportando: ", datoslocal);
                    let local = new Databaseconf();
                    let aux1 = await local.writedblocal(datoslocal, 'doc');
                    console.log("datos retornados ");
                    console.log(aux1);
                }
            }
            // try {
            let repuestax: any;
            let repuestaxOuter: any;
            let aux = 0;
             let envio = [];
            if (this.arraux.length > 0) {
                for await (let dat of this.arraux) {

                    if(aux == 0){
                        let datoslocal = JSON.stringify(dat)
                        console.log("exportando: ", datoslocal);

                        envio.push(dat);
                        
                        console.log(envio);

                        let pagos = new Pagos();

                        let auxxx = await pagos.selectPagosExportByIdPago3();
                        console.log(auxxx);


                        console.log("ENVIO",envio);

                        repuestax = await this.pedidosAddx(envio);
                        console.log("data return repuestax ", repuestax);
                        if(repuestax.respuesta.estadoDoc == '3') {
                            try {
                                await documentosx.actualizarEnviados(dat.header.id,dat.header.key,repuestax.respuesta.estadoDoc);

                                if (dat.pagos && dat.pagos.length > 0) {
                                    if(repuestax.respuesta.estadoPago == '3') {
                                        for await (let ui of dat.pagos) {
                                            try {
                                                await xm.updatePagos(ui.xid, ui.id, 3, 0, ui.control);
                                            } catch (error) {
                                                console.log("error al actualizar el pago", error)
                                            }
                                        }
                                    }
                                }
                            } catch (e) {
                                console.log("ERROE EN SQL:", e);
                            }
                        }else{

                            if (repuestax.respuesta.estadoDoc == 0 && repuestax.respuesta.numeracionDoc > 0) {


                                console.log("la numeracion entrante es", repuestax.respuesta.numeracionDoc);
                                let idUser = id[0].idUsuario;
                                let numero = repuestax.respuesta.numeracionDoc
                                let serial = parseInt(envio[0].header.cod.substr(-5));
                                console.log("serial",serial);
                                let aumento  = numero-serial;
                                console.log("aumento",aumento);

                                let documentosdata = new Documentos();
                                let detalledata = new Detalle();
                                let pagos = new Pagos();
                                let almacenes = new Almacenes();

                                let lista = await documentosdata.findAllPedidosMayor(envio[0].header.DocType,envio[0].header.id);
                                console.log("nueva documentid", lista);

                                for await (let list of lista) {
                                    
                                    console.log("list",list);
                                    let serie = parseInt(list.cod.substr(-5));
                                    console.log("serie",serie);
                                    serie = serie+aumento;
                                    console.log("serie",serie);

                                    let documentid = await documentosdata.generaCod(list.DocType, idUser, serie);
                                    console.log("nueva documentid", documentid);
    
                                    let resp1 = await detalledata.actualizaiddocumento(documentid, list.cod);
                                    console.log(resp1);
    
                                    let resp2 = await almacenes.actualizaiddocumento(documentid, list.cod);
                                    console.log(resp2);
    
                                    let resp3 = await pagos.actualizaiddocumento(documentid, list.cod);
                                    console.log(resp3);

                                    let resp = await documentosdata.actualizaiddocumento(documentid, list.cod);
                                    console.log(resp);

                                }
                                aux = 1;
                            }

                            if(repuestax.respuesta.mensajeDoc == "Error! El registro ya existe"){
                                await documentosx.actualizarEnviados(dat.header.id,dat.header.key,3);
                            }
                        }

                        envio = [];
                    }

                }
            }

            if(aux == 0){

                console.log("EXPORTA OUTER ", DocImportados);
                for await (let ind of DocImportados) {
                    console.log("each --a. ", ind.header.cod);
                    await documentosx.actualizarEnviadosOuter(ind.header.cod);

                }

                if (DocImportados.length > 0) {
                    repuestaxOuter = await this.pedidosAddxOuter(DocImportados);
                }

                if (repuestaxOuter.respuesta) {
                    console.log("hay respuesta importados ");
                    try {

                        console.log("each importado return ", repuestaxOuter);
                        //await documentosx.actualizarEnviados(ind.idPedidoUsr, ind.idPedidoServicio, ind.estado);
                    } catch (e) {
                        console.log("ERROE EN SQL:", e);
                    }
                }
                await this.exportNumeracionSync();

                return true;
                // } catch (e) {
                //     return false;
                // }
            }else {
                console.log("LLAMA");
                this.exportDocumentosAsinc(iddoc);
            } 
        } else {
            return true;
        }


    }

    public async exportDocumentoObjeto() {
        console.log("exportDocumentoObjeto() ");
        console.log("CABECERA",JSON.stringify(GlobalConstants.CabeceraDoc[0]));
        console.log("DETALLE",JSON.stringify(GlobalConstants.DetalleDoc[0]));

        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        this.arraux = [];

        let documentos = new Documentos();
        let item: any;
        item = GlobalConstants.CabeceraDoc[0];

        console.log("romerooooo",item);

        let aux2 = 0;
        let DocImportados = [];

        let detalledoc: any;
        item.idSucursalMobile = await documentos.selectDocumentSucursal(item.CardCode, item.idSucursalMobile);

        detalledoc = GlobalConstants.DetalleDoc;
        console.log("detalledoc ", detalledoc);

        item.idDocPedido = item.cod;
        item.DocTotal = item.DocumentTotalPay;
        item.DocTotalPay = item.DocumentTotalPay;
        item.TotalDiscMonetary = item.descuento;
        item.TotalDiscPrcnt = item.tipodescuento;
        item.origenclone = 0;
        item.U_NumeroAutorizacion = id[0].docificacion.length ? id[0].docificacion[0].U_NumeroAutorizacion : 0;

        if (item.tipoestado == 'anulado') {
            item.estado = 6;
            console.log(" item anulado frontend ", item);
        }

        //const pagos = await this.exportPagosFindPagoIdAsyncOnlySelect(item.cod);

        const pagos = GlobalConstants.CabeceraDoc[0].pago;
        const cadenaPago = await this.findOnePago(item.cod);
        // mau modifica "pagos":'' por pagos:item.pagos
        let d = {
            "usuariodataid": id[0].idUsuario,
            "catidadDetalle": detalledoc.length,
            "header": item,
            "detalles": detalledoc,
            "pagos": item.pagos,
            "version": environment.version,
            "cadenaPago": JSON.stringify(cadenaPago),
            "cadenaCabezera": JSON.stringify(item),
            "cadenaDetalle": JSON.stringify(detalledoc)
        };
        console.log(d);
        this.arraux.push(d);

        let documentosx = new Documentos();
        let datoslocal = JSON.stringify(this.arraux)
        console.log("exportando: ", datoslocal);


        let repuestax: any;
        if (this.arraux.length > 0) {

            if (this.network.type != 'none') {
                repuestax = await this.pedidosAddx(this.arraux);
            } else {


                console.log("data factura con pago", this.arraux);
                for await (let dat of this.arraux) {
                    let datoslocal = JSON.stringify(dat)
                    console.log("exportando: ", datoslocal);
                    let local = new Databaseconf();
                    let aux1 = await local.writedblocal(datoslocal, 'doc');
                    console.log("datos retornados ");
                    console.log(aux1);
                }

                console.log("sin conexion");
                repuestax = {
                    respuesta: {
                        anuladoDoc: 0,
                        anuladoPago: 0,
                        codigoDoc: "",
                        codigoPago: "",
                        estadoDoc: 0,
                        estadoPago: 0,
                        mensajeDoc: "",
                        mensajePago: "",
                        numeracionDoc: 0,
                        numeracionPago: 0,
                    }
                };
            }
            /*repuestax = {
                respuesta: {
                    anuladoDoc: 0,
                    anuladoPago: 0,
                    codigoDoc: "DFA1000900054",
                    codigoPago: "101000900030",
                    estadoDoc: 2,
                    estadoPago: 0,
                    mensajeDoc: "El documento no paso a sap - revisar pago",
                    mensajePago: "Recibo duplicado, enviar nievamente el pago",
                    numeracionDoc: 0,
                    numeracionPago: 24,
                }
            };*/
        }
        return repuestax;
    }


    public async exportDocumentoslocal(iddoc): Promise<any> {


        console.log("exportDocumentosAsinc() ");
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        this.arraux = [];
        let documentos = new Documentos();
        let exportdata2: any = await documentos.dataExport2(id[0]);
        console.log("exportdata2 ", exportdata2);
        let exportdata: any = await documentos.dataExport(id[0]);
        console.log("exportdata ", exportdata);
        let aux = exportdata.length;
        let aux2 = 0;
        let DocImportados = [];


        for await (let item of exportdata) {
            aux2 = aux2 + 1;
            console.log("AUX2", aux2);

            let detalle = new Detalle();
            let detalledoc: any;
            console.log("idSucursal ", item.idSucursalMobile);

            item.idSucursalMobile = await documentos.selectDocumentSucursal(item.CardCode, item.idSucursalMobile);
            detalledoc = await detalle.find(item.cod);
            console.log("detalledoc ", detalledoc);
            //et conificacion: any = await detalle.sumaBonificacion(item.id);
            /**Verificando si esta clonado **/
            let clonado = 0;
            let documentosx = new Documentos();
            let clone: any = await documentosx.findexe(item.clone);
            console.log("clone ", clone);
            if (typeof clone !== 'undefined') {
                clonado = clone.cod;
                if (clone.origen !== "inner") {
                    clonado = 0;
                }
            }
            item.idDocPedido = item.cod;
            item.DocTotal = item.DocumentTotalPay;
            item.DocTotalPay = item.DocumentTotalPay;
            item.TotalDiscMonetary = item.descuento;
            item.TotalDiscPrcnt = item.tipodescuento;
            item.origenclone = clonado;
            item.U_NumeroAutorizacion = id[0].docificacion.length ? id[0].docificacion[0].U_NumeroAutorizacion : 0;

            if (item.tipoestado == 'anulado') {
                item.estado = 6;

                console.log(" item anulado frontend ", item);
            }
            const pagos = await this.exportPagosFindPagoIdAsyncOnlySelect(item.cod);
            const cadenaCabezera = await documentosx.findOne(item.cod);
            const cadenaDetalle = await documentosx.findOneDetalles(item.cod);
            // console.log("cadenaDetalle ", cadenaDetalle);

            const cadenaPago = await this.findOnePago(item.cod);


            let d = {
                "usuariodataid": id[0].idUsuario,
                "catidadDetalle": detalledoc.length,
                "header": item,
                "detalles": detalledoc,
                "pagos": pagos,
                "version": environment.version,
                "cadenaPago": JSON.stringify(cadenaPago),
                "cadenaCabezera": JSON.stringify(cadenaCabezera),
                "cadenaDetalle": JSON.stringify(cadenaDetalle)
            };


            if (item.origen == "inner" && aux2 <= aux) {
                let accion = 0;
                if (this.arraux.length > 0) {
                    for (let i = 0; i < this.arraux.length; i++) {
                        if (this.arraux[i].header.idDocPedido == d.header.idDocPedido) {
                            accion = 1;
                        }
                    }
                }
                if (accion == 0) {
                    this.arraux.push(d);
                }
            } else {
                DocImportados.push(d);
            }
        }


        let documentosx = new Documentos();
        console.log("EXPORTA DATA ", this.arraux);
        console.log("EXPORTA OUTER ", DocImportados);
        for await (let ind of DocImportados) {
            console.log("each --a. ", ind.header.cod);
            await documentosx.actualizarEnviadosOuter(ind.header.cod);

        }

        if (this.arraux.length > 0 || DocImportados.length > 0) {
            console.log("data factura con pago", this.arraux);
            for await (let dat of this.arraux) {
                if (iddoc == dat.header.cod) {
                    let datoslocal = JSON.stringify(dat)
                    console.log("exportando: ", datoslocal);
                    let local = new Databaseconf();
                    let aux1 = await local.writedblocal(datoslocal, 'doc');
                    console.log("datos retornados ");
                    console.log(aux1);
                }
            }
            return true;
        } else {
            return true;
        }
    }

    public async exportanulacionlocal(data: any, iddoc, tipo, iduser): Promise<any> {

        let d = {
            "idDocPedido": iddoc,
            "U_4MOTIVOCANCELADOCABEZERA": data.code,
            "DocType": tipo,
            "U_4MOTIVOCANCELADO": data.conceptoAnulacion,
            "idUsuario": iduser
        };

        this.path = await this.configService.getIp();
        try {
            this.http.setDataSerializer('json');
            let datax: any = await this.http.post(this.path + 'v2/cancelardocumento', d, {});
            return (JSON.parse(datax.data));
        } catch (e) {
            return (e);
        }
    }

    public async exportPagosFindPagoIdAsyncOnlySelect(idocpedido): Promise<any> {
        console.log("exportPagosFindPagoIdAsyncOnlySelect()")
        return new Promise(async (resolve, reject) => {
            try {
                let id: any = await this.configService.getSession();
                let inix: any = 0;
                try {
                    inix = await this.pagosservice.getNumeracionpago();
                } catch (e) {
                }
                let pagos = new Pagos();

                let resp: any = await pagos.selectPagosExportByIdPago2(id[0].idUsuario, id[0].equipoId, id[0].config[0].codEmpleadoVenta, idocpedido);

                if (resp.length > 0) {
                    // let exporExit: any = await this.exportPagosById(resp, inix);
                    resolve(resp);
                } else {
                    resolve([]);
                }

            } catch (e) {
                console.log(e);
                reject(false);
            }
        })
    }


    public async exportPagosFindPagoIdAsync(idocpedido): Promise<any> {
        console.log("exportPagosFindPagoIdAsync()")
        return new Promise(async (resolve, reject) => {
            try {
                let id: any = await this.configService.getSession();
                let inix: any = 0;
                try {
                    inix = await this.pagosservice.getNumeracionpago();
                } catch (e) {
                }
                let pagos = new Pagos();
                let resp: any = await pagos.selectPagosExportByIdPago(id[0].idUsuario, id[0].equipoId, id[0].config[0].codEmpleadoVenta, idocpedido);
                console.log(" exportPagosAsync > selectPagosExport  ", resp);
                if (resp.length > 0) {
                    let exporExit: any = await this.exportPagosById(resp, inix);
                    resolve(exporExit);
                } else {
                    resolve([]);
                }

            } catch (e) {
                console.log(e);
                reject(false);
            }
        })
    }



    public async findOnePago(idocpedido) {
        console.log("findOnePago ")
        return new Promise(async (resolve, reject) => {
            try {
                let id: any = await this.configService.getSession();
                let inix: any = 0;
                try {
                    inix = await this.pagosservice.getNumeracionpago();
                } catch (e) {
                }
                let pagos = new Pagos();
                let resp: any = await pagos.selectIdPago(idocpedido);
                // console.log(" exportPagosAsync > cadena  ", resp);
                if (resp.length > 0) {

                    resolve(resp);
                } else {
                    resolve([]);
                }

            } catch (e) {
                console.log(e);
                reject(false);
            }
        })
    }

    public async exporcliensync(): Promise<any> {
        console.log("exporcliensync(): ");

        try {
            let clientes = new Clientes();
            let cxx: any = await clientes.exportAll();
 
            for await (let inde of cxx) {
                let contactos = new Contactos();
                let perContactArr: any = await contactos.selectCarCode(inde.CardCode);
                let perSucursalesArr: any = await contactos.selectSucursales(inde.CardCode);
                let PersonaContacto = [];
                let datauser: any = await this.configService.getSession();
                let dataSucursales = [];

                PersonaContacto.push({
                    nombrePersonaContacto: perContactArr[0].nombre,
                    fonoPersonaContacto: perContactArr[0].telefono,
                    comentarioPersonaContacto: perContactArr[0].comentario,
                    tituloPersonaContacto :perContactArr[0].titulo,
                    correoPersonaContacto: perContactArr[0].correo,
                    cardCode: perContactArr[0].cardCode,
                    internalcode : perContactArr[0].internalcode
                });

                 let camposusuario = [];
                 let idfrom = 3
                 let camposu = datauser[0].campodinamicos;
                 for (let i = 0; i < camposu.length; i++) {
                     if (camposu[i].Objeto == idfrom) {
                         let campo = "campousu" + camposu[i].Nombre;
                         camposusuario.push({
                             Objeto: camposu[i].Objeto,
                             cmidd: camposu[i].cmidd,
                             tabla: camposu[i].tabla,
                             campo: campo,
                             valor: inde[campo]
                         });
                     }
                 }
 
                 let sucursalescampos = [];
                 idfrom = 4;
                 for (let i = 0; i < camposu.length; i++) {
                    if (camposu[i].Objeto == idfrom) {
                        let campo = "campousu" + camposu[i].Nombre;
                        sucursalescampos.push({
                            Objeto: camposu[i].Objeto,
                            cmidd: camposu[i].cmidd,
                            tabla: camposu[i].tabla,
                            campo: campo,
                            valor: inde[campo]
                        });
                    }
                };


                dataSucursales.push({
                    idUser: datauser[0].idUsuario,
                    AddresName: perSucursalesArr[0].AddresName,
                    Street: perSucursalesArr[0].Street,
                    LineNum: perSucursalesArr[0].LineNum,
                    State: 0,
                    FederalTaxId: 0,
                    CreditLimit: 0,
                    CardCode: perSucursalesArr[0].CardCode, //"901000001",
                    User: perSucursalesArr[0].idUser,
                    Status: 1, //"1",
                    DateUpdate: "",
                    idDocumento: 0,
                    TaxCode: "",//"IVA_10", NO 
                    AdresType: perSucursalesArr[0].AdresType,  //"S", // select (ENTREGA S  FACTURACION B)
                    u_zona: "",// NO */
                    u_lat: perSucursalesArr[0].u_lat,
                    u_lon: perSucursalesArr[0].u_lon,
                    u_territorio: perSucursalesArr[0].u_territorio, //null, // SERVICE
                    u_vendedor: datauser[0].config[0].codEmpleadoVenta,// null (USER LOGEO)
                    labelTerritorio: "",
                    camposusuario: sucursalescampos
                });

                if (inde.CardCode) {
                    inde.ContactPerson = perContactArr;
                    inde.SucursalPerson = perSucursalesArr;
                    //arrx.push(inde);
                }


                let data = {
                    idUser: datauser[0].idUsuario,
                    actualizado: inde.actualizado,
                    CardCode: inde.CardCode,
                    CardName: inde.CardName,
                    CardType: '0',
                    Address: inde.Address,
                    CreditLimit: "0",
                    MaxCommitment: "0",
                    DiscountPercent: "0",
                    PriceListNum: datauser[0].config[0].listaPrecios,
                    SalesPersonCode: datauser[0].config[0].codEmpleadoVenta,
                    Currency: datauser[0].config[0].moneda,
                    County: "0",
                    Country: "0",
                    CurrentAccountBalance: "0",
                    NoDiscounts: "0",
                    PriceMode: "0",
                    FederalTaxId: inde.FederalTaxId,
                    PhoneNumber: inde.PhoneNumber,
                    ContactPerson: PersonaContacto,
                    SucursalesCliente: dataSucursales,
                    PayTermsGrpCode: "0",
                    Latitude: inde.Latitude,
                    Longitude: inde.Longitude,
                    GroupCode: inde.GroupCode,
                    User: datauser[0].idUsuario,
                    territorio: inde.territorio,
                    Status: "1000",
                    DateUpdate: inde.DateUpdate,
                    idDocumento: "0",
                    imagen: inde.imagen,
                    export: 0,
                    celular: inde.celular,
                    pesonacontactocelular: inde.pesonacontactocelular,
                    correoelectronico: inde.correoelectronico,
                    rutaterritorisap: inde.rutaterritorisap,
                    rutaterritorisaptext: "0",
                    diavisita: inde.diavisita,
                    diavisitatext: '',
                    comentario: inde.comentario,
                    creadopor: datauser[0].config[0].idUser,
                    xcodigocliente: inde.xcodigocliente,
                    fechaset: inde.fechaset,
                    fechaupdate: inde.fechaupdate,
                    razonsocial: inde.razonsocial,
                    idEmpresa: inde.id,
                    codeCanal: inde.codeCanal,
                    codeSubCanal: inde.codeSubCanal,
                    codeTipoTienda: inde.codeTipoTienda,
                    cadena: inde.cadena,
                    codeCadenaConsolidador: inde.codeCadenaConsolidador,
                    img: inde.img,
                    cliente_std1: inde.cliente_std1,
                    cliente_std2: inde.cliente_std2,
                    cliente_std3: inde.cliente_std3,
                    cliente_std4: inde.cliente_std4,
                    cliente_std5: inde.cliente_std5,
                    cliente_std6: inde.cliente_std6,
                    cliente_std7: inde.cliente_std7,
                    cliente_std8: inde.cliente_std8,
                    cliente_std9: inde.cliente_std9,
                    cliente_std10: inde.cliente_std10,
                    Fex_tipodocumento: inde.Fex_tipodocumento,
                    Fex_complemento: inde.Fex_complemento,
                    Fex_codigoexcepcion: inde.Fex_codigoexcepcion,
                    camposusuario: camposusuario
                };

                let rx: any = await this.exportClientes(data);
                console.log("return export cliente ", rx);
                if(rx.estado == '3'){
                    try {
                        let clientes = new Clientes();
                        await clientes.updateImport(data.CardCode);
                    } catch (e) {
                        console.log(e);
                    }
                }

            }
            
            await this.exportNumeracionSync();
        } catch (e) {
            console.log(e);
        }
        return true;
    }

    public async exporclienlocal(data: any, acc): Promise<any> {

        console.log("exporclienlocal(): ");
        console.log("data export ", JSON.stringify(data));
        let dir = '';
        if (acc == 1) {
            dir = 'v2/clientes/create';
        } else {
            dir = 'v2/clientes/update';
        }

        this.path = await this.configService.getIp();
        console.log("this.path", this.path + dir)
        return new Promise((resolve, reject) => {
            this.http.setDataSerializer('json');
            this.http.post(this.path + dir, data, {}).then(async (data: any) => {
                console.log("data", data)
                let ux = JSON.parse(data.data);
                resolve(ux);
            }).catch((error: any) => {
                reject(error);
            });
        });

    }

    public async exportPagosAsync(recibo): Promise<any> {
        console.log("exportPagosAsync() ")
        return new Promise(async (resolve, reject) => {
            try {
                let id: any = await this.configService.getSession();
                let inix: any = 0;
                try {
                    inix = await this.pagosservice.getNumeracionpago();

                } catch (e) {
                }

                let pagos = new Pagos();

                let resp: any = await pagos.selectPagosExport(id[0].idUsuario, id[0].equipoId, id[0].config[0].codEmpleadoVenta);
                let conexion = 0;

                if (this.network.type != 'none') {
                    console.log("con conexion");
                    conexion = 1;
                } else {
                    console.log("sin conexion")
                    conexion = 0;
                }

                console.log("select export resp ", resp);
                if (resp.length > 0) {
                    let xm = new Pagos();

                    for (let i = 0; i < resp.length; i++) {

                        console.log("datos que va a mandar", resp[i]);
                        if (conexion == 1) {
                            let exporExit: any = await this.exportPagos(resp[i], inix, recibo);
                            console.log("----> export return pagos PAGO ", exporExit);


                            if (exporExit[i].error && exporExit[i].error == 'Duplicado') {

                                // await this.configService.setNumeracionpago(Number(resp[i].numero));
                                console.log("INTENTAR NUEVAMENTE");
                                localStorage.setItem("Duplicado_pago", "" + Number(Number(exporExit[i].numero) + 1));
                                localStorage.setItem("Duplicado_pago_codigo", "" + exporExit[i].control);


                                const nuevoCorrelativo: number = Number(localStorage.getItem("Duplicado_pago"));

                                let num: any = await this.pagosservice.getNumeracionpago();

                                console.log("NUEVO NUMERO STORAGE ", num);
                                const userdata: any = await this.configService.getSession();

                                let nuevoCodigo = Calculo.generaCodeRecibo(userdata[0].idUsuario.toString(), localStorage.getItem("Duplicado_pago"), '1');
                                console.log("NUEVO COD PAGO ", nuevoCodigo);
                                console.log("NUEVO COD nuevoCorrelativo ", nuevoCorrelativo);
                                // localStorage.setItem("Duplicado_pago", "0");
                                let pagos = new Pagos();
                                await pagos.updateCorrelativo(nuevoCodigo, localStorage.getItem("Duplicado_pago_codigo"), nuevoCorrelativo);



                            } else {
                                localStorage.setItem("Duplicado_pago", "0");
                                await xm.updatePagos(exporExit[0].xid, resp[i].id, exporExit[0].estado, exporExit[0].anulado, exporExit[0].control);
                            }

                        } else {
                            console.log("manda a guardar sin conexion")
                            let exporExit: any = await this.exportPagoslocal(resp[i], inix, recibo);
                            console.log("----> export return pagos Local", exporExit);
                            await xm.updatePagos(exporExit.xid, exporExit.id, exporExit.estado, exporExit.anulado, exporExit.id);
                        }
                    }




                }
                resolve(true);
            } catch (e) {
                reject(false);
            }
        })
    }


    public async exporevidencia(evidencia: any): Promise<any> {

        /*console.log("exporevidencia(): ");
        console.log("data export ", JSON.stringify(evidencia));*/

        console.log(evidencia);

        let datos = {
            cabecera: evidencia[0].cabecera,
            detalle: evidencia[0].dealle,
        };

        console.log("data export ", JSON.stringify(datos));


        let dir = 'v2/evidencias/create';

        this.path = await this.configService.getIp();
        console.log("this.path", this.path + dir);
        return new Promise((resolve, reject) => {
            this.http.setDataSerializer('json');
            this.http.post(this.path + dir, datos, {}).then(async (data: any) => {
                console.log("data", data)
                let ux = JSON.parse(data.data);
                resolve(ux);
            }).catch((error: any) => {
                reject(error);
            });
        });

    }


    public async exportPagosAsyncCancela(): Promise<any> {
        console.log("exportPagosAexportPagosAsyncCancelasync() ")
        return new Promise(async (resolve, reject) => {
            try {
                let id: any = await this.configService.getSession();
                let inix: any = 0;
                try {
                    inix = await this.pagosservice.getNumeracionpago();

                } catch (e) {
                }

                let pagos = new Pagos();

                let resp: any = await pagos.selectPagosExportCancela(id[0].idUsuario, id[0].equipoId, id[0].config[0].codEmpleadoVenta);
                console.log("select export resp ", resp);
                if (resp.length > 0) {
                    for (let i = 0; i < resp.length; i++) {
                        console.log("datos que va a mandar", resp[i]);
                        let exporExit: any = await this.exportPagos(resp[i], inix);
                        console.log("----> export return pagos ", exporExit);
                        //for await (let ui of exporExit) {
                        console.log("a actualizar", exporExit);
                        let xm = new Pagos();
                        await xm.updatePagos(exporExit[0].xid, resp[i].id, exporExit[0].estado, exporExit[0].anulado, exporExit[0].control);
                        //}
                    }
                }
                resolve(true);
            } catch (e) {
                reject(false);
            }
        })
    }


    public async exportReimpreciones(data: any) {
        this.path = await this.configService.getIp();
        let arr: any = await this.__postOnline('reimpresion', data);
        return JSON.parse(arr.data);
    }

    public async exportVistas() {
        console.log("exportVistas()");

        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        let visitas = new Visitas();
        let resp: any = await visitas.exportedata();
        console.log("data no visitas ", resp);
        let data = [];
        resp.forEach(element => {
            data.push({
                CardCode: element.CartCode,
                CardName: element.CartName,
                fecha: element.fecha,
                hora: element.hora,
                //horafin: element.horafin,
                lat: element.lat,
                lng: element.lng,
                //  foto: element.foto,
                usuario: id[0].idUsuario,
                estadoEnviado: element.id,
                motivoCode: element.motivoCode,
                motivoRazon: element.motivoRazon,
                motivoName: element.motivoName,
                descripcionTxt: element.descripcionTxt,
                img: element.img
            });
        });
        console.log("new data data ", data);
        let respuestaserver: any = await this.__postOnline('visitas', data);
        let respuestaJson = JSON.parse(respuestaserver.data);
        let visitasActualizadas = respuestaJson.respuesta;
        if (visitasActualizadas && visitasActualizadas.length > 0) {
            visitasActualizadas.forEach(visita => {
                visitas.exportUpdate(visita.estadoEnviado, visita.id);
            });
        }
    }

    /**************Consulta ONline*************/
    public async getClientesAction(textosearch: string, page = 1) {
        if (this.network.type != 'none') {
            this.path = await this.configService.getIp();
            let id: any = await this.configService.getSession();
            let ux: string = 'clientesearchmovil/search?usuario=' + id[0].idUsuario + '&name=' + textosearch;
            console.log("ux ", ux);

            let arr: any = await this.__getDow(ux);
            console.log(arr.data);
            return JSON.parse(arr.data);
        } else {
            return 0;
        }
    }

    public async NumeracionAction(data: any) {
        this.path = await this.configService.getIp();
        console.log("this.path", this.path + "numeracion/actualizanum");
        console.log("data ", data);
        return await this.__postOnline('numeracion/actualizanum', data);
    }

    public async getTerritorios() {
        this.path = await this.configService.getIp();
        let ux: string = 'territorios';
        let arr: any = await this.__getDow(ux);
        return JSON.parse(arr.data);
    }

    public async getTerritoriosFilter() {
        // this.path = await this.configService.getIp();
        // let ux: string = 'territorios';
        // let arr: any = await this.__getDow(ux);
        // return JSON.parse(arr.data);
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        let arr: any = await this.__postOnline('territorios', {
            "iduser": id[0].idUsuario
        });
        return JSON.parse(arr.data);
    }



    public async getClientesDocument(code: string) {
        this.path = await this.configService.getIp();
        let ux: string = 'facturasappmovil/search?cardcode=' + code;
        let arr: any = await this.__getDow(ux);
        return JSON.parse(arr.data);
    }

    public async getClientesOnline(search: string) {
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        let arr: any = await this.__postOnline('clientes', {
            "usuario": id[0].idUsuario,
            "texto": search
        });
        return JSON.parse(arr.data);
    }

    public async getProductosOnline(search: string) {
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        let arr: any = await this.__postOnline('items', {
            "usuario": id[0].idUsuario,
            "texto": search
        });
        return JSON.parse(arr.data);
    }

    private __postOnline(url: string, data: any) {

        return new Promise((resolve, reject) => {
            console.log("this.path + url ", this.path + url);
            console.log("this.path + url ", data);
            this.http.setDataSerializer('json');
            this.http.setRequestTimeout(0);
            this.http.post(this.path + url, data, {}).then((data: any) => {

                resolve(data);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }

    public async configuraciones() {
        console.log("configuraciones() ");
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        return await this.__getView('numeracion', id[0].idUsuario);
    }

    /*************DONWLOAD DATA **************/
    public async servisDownloadPedidos(servis: string) {
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        return await this.__postDow(servis, {
            "usuario": id[0].idUsuario,
            "tipo": 2,
            "texto": "0"
        });
    }

    public async servisDownloadPost(servis: string) {
        //console.log("servisDownloadPost ", servis);
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        let dataext: any = {
            "usuario": id[0].idUsuario,
            "sucursal": id[0].sucursalxId,
            "equipo": id[0].uuid
        };
        return await this.__postDow(servis, dataext);
    }
    public async servisDownloadPostPaginate(servis: string, pagina: number) {
        //console.log("servisDownloadPost ", servis);

        let id: any = await this.configService.getSession();
        //console.log("session info ", id);
        let dataext: any = {
            "usuario": id[0].idUsuario,
            "sucursal": id[0].sucursalxId,
            "equipo": id[0].uuid,
            "pagina": pagina
        };
        return await this.__postDow(servis, dataext);
    }


    public async servisDownloadGet(servis: string) {
        this.path = await this.configService.getIp();
        return await this.__getDow(servis);
    }

    /*************EXPORT DATA **************/
    /* public async exportPagos(data: any, numero: number) {
         this.path = await this.configService.getIp();
         let id: any = await this.configService.getSession();
         let dataext: any = {
             "usuario": id[0].idUsuario,
             "sucursal": id[0].sucursalxId,
             "equipo": id[0].equipoId,
             "numero": numero,
         };
         console.log("export data ", data);
         let returnedTarget = Object.assign(dataext, data);
         return new Promise((resolve, reject) => {
             this.http.setDataSerializer('json');
             this.http.post(this.path + 'pagosmoviles', returnedTarget, {}).then((data: any) => {
                 resolve(JSON.parse(data.data));
             }).catch((error: any) => {
                 reject(error);
             });
         });
     }*/

    public async exportPagos(data: any, numero: number, recibo = 0) {
        let facturasPagosModel: FacturasPagos = new FacturasPagos();
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        let dataext: any = {
            "usuario": id[0].idUsuario,
            "sucursal": id[0].sucursalxId,
            "equipo": id[0].equipoId,
            "numero": numero,
        };

        console.log("returnedTarget ", data);
        console.log("facturas---> ", JSON.parse(localStorage.getItem('facturas')));

        let allItems: any = await facturasPagosModel.all();
        console.log("allItems", allItems);
        //filtrado las facturas por cod recibo
        //console.log("recibo",data[0].documentoPagoId);
        //let itemFacturas: any = await facturasPagosModel.findByRecibo(data[0].documentoPagoId);
        console.log("recibo", data.documentoPagoId);
        let itemFacturas: any = await facturasPagosModel.findByRecibo(data.documentoPagoId);
        console.log("facturas encontradas del pago ", itemFacturas);

        /*  for await (let value of itemFacturas) {
             let documentos = new Documentos();
             try {
                 await documentos.updateSaldoFacturasSap(value);
             } catch (error) {
                 console.log("error al restar el saldo", error);
             }
         } */
        console.log("itemFacturas filtrado ", itemFacturas);
        console.log("each data pagos ", data);
        // otpp: 1 facturas del movil, 2 facturas desde sap, 3 es pagos anticipaos
        if (data.dx && data.dx.toLowerCase() === "facturas") {
            console.log("validar si este data es de tipo facturas para el insert ", data);
            data.facturas = itemFacturas;
            data.cadenaFacturas = JSON.stringify(itemFacturas);
        }

        /* for await (let value of data) {
            console.log("each data pagos ", data);
            // otpp: 1 facturas del movil, 2 facturas desde sap, 3 es pagos anticipaos
            if (value.dx && value.dx.toLowerCase() === "facturas") {
                console.log("validar si este data es de tipo facturas para el insert ", data);
                //  console.log("***************  forEach ", value.dataInsert);  

                value.facturas = itemFacturas;
                value.cadenaFacturas = JSON.stringify(itemFacturas);
            }

        }*/


        //} else {
        //  console.log("-----  sin ");
        //}

        let returnedTarget = Object.assign(dataext, data);
        console.log("exportando data: ", returnedTarget);
        let datoslocal = JSON.stringify(returnedTarget)
        console.log("exportando data 2: ", datoslocal);

        console.log("aqui inicia recibo", recibo);
        if (recibo != 0) {
            console.log("aqui inicia recibo", returnedTarget.documentoPagoId);
            console.log("aqui inicia recibo", recibo);
            if (returnedTarget.documentoPagoId == recibo) {
                console.log("el recibo a guardar es ");
                let local = new Databaseconf();
                let aux1 = await local.writedblocal(datoslocal, 'pagos');
                console.log("datos retornados ");
                console.log(aux1);
            }
        }



        return new Promise((resolve, reject) => {
            this.http.setDataSerializer('json');
            this.http.post(this.path + 'pagosmoviles', returnedTarget, {}).then((data: any) => {
                resolve(JSON.parse(data.data));
            }).catch((error: any) => {
                reject(error);
            });

        });



    }

    public async exportPagoslocal(data: any, numero: number, recibo = 0) {
        console.log("exportPagoslocal");
        let facturasPagosModel: FacturasPagos = new FacturasPagos();
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        let dataext: any = {
            "usuario": id[0].idUsuario,
            "sucursal": id[0].sucursalxId,
            "equipo": id[0].equipoId,
            "numero": numero,
        };

        console.log("returnedTarget ", data);
        console.log("facturas---> ", JSON.parse(localStorage.getItem('facturas')));

        let allItems: any = await facturasPagosModel.all();
        console.log("allItems", allItems);
        console.log("recibo", data.documentoPagoId);
        let itemFacturas: any = await facturasPagosModel.findByRecibo(data.documentoPagoId);
        console.log("facturas encontradas del pago ", itemFacturas);
        console.log("itemFacturas filtrado ", itemFacturas);

        console.log("each data pagos ", data);
        if (data.dx && data.dx.toLowerCase() === "facturas") {
            console.log("validar si este data es de tipo facturas para el insert ", data);
            data.facturas = itemFacturas;
            data.cadenaFacturas = JSON.stringify(itemFacturas);
        }
        let returnedTarget = Object.assign(dataext, data);
        console.log("exportando data: ", returnedTarget);
        let datoslocal = JSON.stringify(returnedTarget)
        console.log("exportando data 2: ", datoslocal);

        console.log("aqui inicia recibo", recibo);
        if (recibo != 0) {
            console.log("aqui inicia recibo", returnedTarget.documentoPagoId);
            console.log("aqui inicia recibo", recibo);
            if (returnedTarget.documentoPagoId == recibo) {
                console.log("el recibo a guardar es ");
                let local = new Databaseconf();
                let aux1 = await local.writedblocal(datoslocal, 'pagos');
                console.log("datos retornados ");
                console.log(aux1);
            }
        }

        console.log("datos a retorna", data);

        return new Promise((resolve, reject) => {
            resolve(data);
        });
    }

    public async exportContactos(data: any) {
        this.path = await this.configService.getIp();
        return new Promise((resolve, reject) => {
            this.http.setDataSerializer('json');
            this.http.post(this.path + 'clientesimport', data, {}).then((data: any) => {
                let ux = JSON.parse(data.data);
                resolve(ux);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }
    public async exportPagosById(data: any, numero: number) {
        console.log("exportPagosById() ")
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        let dataext: any = {
            "usuario": id[0].idUsuario,
            "sucursal": id[0].sucursalxId,
            "equipo": id[0].equipoId,
            "numero": numero,
        };

        console.log("returnedTarget ", data);
        if (localStorage.getItem('facturas') && JSON.parse(localStorage.getItem('facturas')).length > 0) {

            console.log("------  items fac");
            let itemFacturas = JSON.parse(localStorage.getItem('facturas'));
            console.log("itemFacturas  ", itemFacturas);


            itemFacturas = itemFacturas.filter((item) => {
                console.log("", item.cod, " == ", data[0].documentoPagoId);
                return item.cod == data[0].documentoPagoId;
            });

            for await (let value of itemFacturas) {
                let documentos: Documentos = new Documentos();
                await documentos.updateSaldoFacturasSap(value);
            }
            console.log("itemFacturas filtrado ", itemFacturas);
            for await (let value of data) {
                console.log("each data pagos ", value);
                // otpp: 1 facturas del movil, 2 facturas desde sap, 3 es pagos anticipaos
                if (value.dx === "facturas") {
                    console.log("validar si este data es de tipo facturas para el insert ", data);
                    //  console.log("***************  forEach ", value.dataInsert);  

                    value.facturas = itemFacturas
                }

            }
        } else {
            console.log("-----  sin ");
        }

        let returnedTarget = Object.assign(dataext, data);
        // console.log("exportando data: ", returnedTarget);
        // console.log("exportando data 2: ", JSON.stringify(returnedTarget));
        // console.log("url---> ", this.path + 'pagosmoviles');

        return new Promise((resolve, reject) => {
            resolve(returnedTarget);
        });
    }
    public async ubicacionesExport(data: any) {
        return new Promise((resolve, reject) => {
            this.http.setDataSerializer('json');
            this.http.post(this.path + 'geolocalizacion', data, {}).then((data: any) => {
                let ux = JSON.parse(data.data);
                resolve(ux);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }

    public async exportClientes(data: any) {

        let datos = {
            idUser: data.idUser,
            CardCode: data.CardCode,
            CardName: data.CardName,
            CardType: '0',
            Address: data.Address,
            CreditLimit: "0",
            MaxCommitment: "0",
            DiscountPercent: "0",
            PriceListNum: data.PriceListNum,
            SalesPersonCode: data.SalesPersonCode,
            Currency: data.Currency,
            County: data.County,
            Country: data.Country,
            CurrentAccountBalance: data.CurrentAccountBalance,
            NoDiscounts: "0",
            PriceMode: data.PriceMode,
            FederalTaxId: data.FederalTaxId,
            PhoneNumber: data.PhoneNumber,
            ContactPerson: data.ContactPerson,
            SucursalesCliente: data.SucursalesCliente,
            PayTermsGrpCode: data.PayTermsGrpCode,
            Latitude: data.Latitude,
            Longitude: data.Longitude,
            GroupCode: data.GroupCode,
            User: data.User,
            territorio: data.territorio,
            Status: data.Status,
            DateUpdate: data.DateUpdate,
            idDocumento: data.idDocumento,
            imagen: data.imagen,
            export: data.export,
            celular: data.celular,
            pesonacontactocelular: data.pesonacontactocelular,
            correoelectronico: data.correoelectronico,
            rutaterritorisap: data.rutaterritorisap,
            rutaterritorisaptext: data.rutaterritorisaptext,
            diavisita: data.diavisita,
            diavisitatext: '',
            comentario: data.comentario,
            creadopor: data.creadopor,
            xcodigocliente: data.xcodigocliente,
            fechaset: data.fechaset,
            fechaupdate: data.fechaupdate,
            razonsocial: data.razonsocial,
            idEmpresa: data.idEmpresa,
            codeCanal: data.codeCanal,
            codeSubCanal: data.codeSubCanal,
            codeTipoTienda: data.codeTipoTienda,
            cadena: data.cadena,
            codeCadenaConsolidador: data.codeCadenaConsolidador,
            img: data.img,
            cliente_std1: data.cliente_std1,
            cliente_std2: data.cliente_std2,
            cliente_std3: data.cliente_std3,
            cliente_std4: data.cliente_std4,
            cliente_std5: data.cliente_std5,
            cliente_std6: data.cliente_std6,
            cliente_std7: data.cliente_std7,
            cliente_std8: data.cliente_std8,
            cliente_std9: data.cliente_std9,
            cliente_std10: data.cliente_std10,
            Fex_tipodocumento: data.Fex_tipodocumento,
            Fex_complemento: data.Fex_complemento,
            Fex_codigoexcepcion: data.Fex_codigoexcepcion,
            camposusuario: data.camposusuario
        };
  
        console.log("data export ", JSON.stringify(datos));
        this.path = await this.configService.getIp();
        
        let dir = '';
        if (data.actualizado == 'Y') {
            dir = 'v2/clientes/update';
        } else {
            dir = 'v2/clientes/create';
            
        }
        console.log("this.path + 'clientesmovil' ", this.path + dir);

        return new Promise((resolve, reject) => {
            this.http.setDataSerializer('json');
            this.http.post(this.path + dir, datos, {}).then(async (data: any) => {
                console.log("data", data)
                let ux = JSON.parse(data.data);
                resolve(ux);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }

    public async pedidosAddx(data: any): Promise<any> {
        this.path = await this.configService.getIp();
        console.log("datos a enviar",data);
        try {
            this.http.setDataSerializer('json');
            this.http.setRequestTimeout(0);
            let datax: any = await this.http.post(this.path + 'v2/pedidosmovil', data, {});
            return (JSON.parse(datax.data));
        } catch (e) {
            return (e);
        }
    }

    public async pedidosAddxOuter(data: any): Promise<any> {
        console.log("pedidosAddxOuter () ");
        this.path = await this.configService.getIp();
        console.log("this.path + 'anulaciondocmovil' ", this.path + 'anulaciondocmovil');
        try {

            this.http.setDataSerializer('json');

            let datax: any = await this.http.post(this.path + 'anulaciondocmovil', data, {});
            return (JSON.parse(datax.data));
        } catch (e) {
            console.log("error export ", e);
            return (e);
        }
    }
    public async divisasAddx(data: any) {
        this.path = await this.configService.getIp();
        return new Promise((resolve, reject) => {
            this.http.setDataSerializer('json');
            this.http.post(this.path + 'cambiodivisasmovil', data, {}).then((data: any) => {
                resolve(JSON.parse(data.data));
            }).catch((error: any) => {
                reject(error);
            });
        });
    }

    public async getLbcc() {
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        return await this.__post('lbcc', { "usuario": id[0].idUsuario });
    }

    async getCI(data: any) {
        this.path = await this.configService.getIp();
        return await this.__getView('solicitudregistro', data);
    }

    async solicitaRegistro(data: any) {
        this.path = await this.configService.getIp();
        return await this.__post('solicitudregistro', data);
    }

    public async registroEquipo(data: any) {

        this.path = await this.configService.getIp();
        console.log("this.path ", this.path + "registerequipo");
        console.log("data ", data);
        return await this.__post('registerequipo', data);
    }

    async numeracionAction(data: any) {
        this.path = await this.configService.getIp();
        return await this.__post('numeracion', data);
    }

    public async actionPoligono(data: any) {
        this.path = await this.configService.getIp();
        console.log("    this.path  ", this.path);
        console.log("data ", data);
        return await this.__post('v2/poligono', data);
    }

    public async actionNoVisita(data: any) {
        this.path = await this.configService.getIp();
        console.log("    this.path  ", this.path);
        console.log("data ", data);
        return await this.__post('motivonoventa', data);
    }



    public async login(data: any) {
        this.path = await this.configService.getIp();
        console.log("this.path", this.path + "login");
        console.log("data ", data);
        return await this.__postLogin('login', data);
    }

    public async resepassword(id: any, data: any) {
        this.path = await this.configService.getIp();
        return await this.__put('resetear/' + id, data);
    }

    public __getinit(url: string) {
        console.log("?url ", url);
        return new Promise((resolve, reject) => {
            this.http.get(url, {}, {}).then((data: any) => {
                console.log("data ", data);

                resolve(data);
            }).catch((error: any) => {
                reject(error.error);
            });
        });
    }

    private __postLogin(url: string, data: any) {
        return new Promise((resolve, reject) => {
            console.log("ruta: ", this.path + url);
            this.http.setDataSerializer('json');
            this.http.post(this.path + url, data, {}).then((data: any) => {
                console.log(data);
                resolve(data);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }

    public __get(url: string) {
        console.log("get ", url)
        return new Promise((resolve, reject) => {
            this.http.get(this.path + url, {}, {}).then((data: any) => {
                resolve(data);
            }).catch((error: any) => {
                reject(error.error);
            });
        });
    }

    private __delete(url: string, data: any) {
        this.spinnerDialog.show(null, 'Loading...', true);
        return new Promise((resolve, reject) => {
            this.http.post(this.path + url, data, {}).then((data: any) => {
                this.spinnerDialog.hide();
                resolve(data);
            }).catch((error: any) => {
                this.spinnerDialog.hide();
                reject(error.error);
            });
        });
    }

    public __put(url: string, updata: any) {
        return new Promise((resolve, reject) => {
            this.http.put(this.path + url, updata, {}).then((data: any) => {
                resolve(data);
            }).catch((error: any) => {
                reject(error.error);
            });
        });
    }

    private async __postDow(url: string, data: any, tiempo = 0) {
        this.path = await this.configService.getIp();
        return new Promise((resolve, reject) => {
            this.http.setRequestTimeout(tiempo);
            this.http.setRequestTimeout(0);
            console.log("SINC -> ", this.path + url);
            console.log("data : ", data);
            this.http.post(this.path + url, data, {}).then((data: any) => {
                //console.log("RETURN POST ", data);
                resolve(data);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }

    private __getDow(url: string) {
        console.log("this.path + url, ", this.path + url);
        return new Promise((resolve, reject) => {
            this.http.get(this.path + url, {}, {}).then((data: any) => {
                resolve(data);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }

    public __post(url: string, data: any) {
        return new Promise((resolve, reject) => {
            console.log("this.path + url ", this.path + url);
            this.http.post(this.path + url, data, {}).then((data: any) => {
                resolve(data);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }


    private __getView(url: string, data: any) {
        console.log(this.path + url + '/' + data);
        return new Promise((resolve, reject) => {
            this.http.get(this.path + url + '/' + data, {}, {}).then((data: any) => {
                resolve(data);
            }).catch((error: any) => {
                console.log("error numeracion ");
                reject(error.error);
            });
        });
    }
    public async serviceAutorization(data: any) {
        this.path = await this.configService.getIp();
        console.log("this.path", this.path + "autorizaciondoc");
        console.log("data ", data);
        return await this.__post("autorizaciondoc", data);
    }

    /**CONSULTAS DIRECTAS A SAP */


    public async Consultasaldoclientesap(data: any) {

        this.path = await this.configService.getIp();

        console.log("data ", data);
        return await this.__post("clientes/consultasaldoclientesap", data);
    }

    public async Consultasaldodocumento(data: any) {

        this.path = await this.configService.getIp();
        console.log("data ", data);
        return await this.__post("documentos/consultaestadodocumento", data);
    }


    public async Consultasucursalsap(data: any) {

        this.path = await this.configService.getIp();

        console.log("data ", data);
        return await this.__post("clientes/consultasucursalsap", data);
    }

    public async Cosultacontactossap(data: any) {

        this.path = await this.configService.getIp();

        console.log("data ", data);
        return await this.__post("clientes/consultacontactosap", data);
    }


    public async Cosultafacturasap(data: any) {

        this.path = await this.configService.getIp();

        console.log("data ", data);
        return await this.__post("clientes/consultacontactosap", data);
    }

    public async servisReportValidaStockPost(data, tiempo = 0) {
        //console.log("servisDownloadPost ", servis);
        this.path = await this.configService.getIp();
        return await this.__postDowTimeOut("productosalmacenes/productosalmacensap", data, tiempo);
    }

    public async servisReportConsultaCufPost(data, tiempo = 0) {
        console.log("facturasapp/consultacufsap", data);
        this.path = await this.configService.getIp();
        return await this.__postDowTimeOut("facturasapp/consultacufsap", data, tiempo);
    }

    public async servisReportConsultaestadofactura(data, tiempo = 0) {
        console.log("facturasapp/consultaestadofact", data);
        this.path = await this.configService.getIp();
        return await this.__postDowTimeOut("facturasapp/consultaestadofact", data, tiempo);
    }

    public async servisReportConsultaNumfacPost(id, tiempo = 0) {
        console.log("facturasapp/consultanumfacsap", id);
        this.path = await this.configService.getIp();
        return await this.__postDowTimeOut("facturasapp/consultanumfacsap", id, tiempo);
    }

    private async __postDowTimeOut(url: string, data: any, tiempo = 0) {
        this.path = await this.configService.getIp();
        console.log("tiempo : ", tiempo);
        return new Promise((resolve, reject) => {
            this.http.setRequestTimeout(tiempo);
            console.log("SINC -> ", this.path + url);
            console.log("data : ", data);
            this.http.post(this.path + url, data, {}).then((data: any) => {
                resolve(data);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }
    public async servisDownloadDocsSap(CardCode) {
        let id: any = await this.configService.getSession();
        let dataext: any = {
            "codigo": CardCode,
        };
        return await this.__postDow(`documentosmovilsap/doccliente`, dataext);
    }
    public async servisLogDeleteDB(data) {
        console.log("data ", data);
        let id: any;
        let idSend = 0;
        try {
            id = await this.configService.getSession();
            console.log("session info ", id);
            idSend = id[0].idUsuario;
        } catch (error) {
            console.log("error al obtener el objeto del usuario ", error)
            idSend = 0;
        }

        let dataext: any = {
            "usuario": idSend,
            "fecha": moment().format('YYYY-MM-DD'),
            "version": environment.version,
            "equipo": data.uuid
        };
        console.log("dataext ", dataext)
        return await this.__postDow(`versionequipo`, dataext);
    }



    public async serviseMigratesMovil() {
        return await this.__postDow(`migratesmovil`, {});
    }


    public async servisReportPost(servis, data, tiempo = 0) {
        //console.log("servisDownloadPost ", servis);
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        return await this.__postDow(servis, data, tiempo);
    }
    public async servisStatesPayments() {
        //console.log("servisDownloadPost ", servis);

        let id: any = await this.configService.getSession();
        //console.log("session info ", id);
        let dataext: any = {
            "usuario": id[0].idUsuario,
        };
        return await this.__postDow(`estadorecibo`, dataext);
    }



    checkDataStatusMid = async () => {
        try {
            const dataFromMid: any = await this.servisStatesPayments()
            const responseData: any = JSON.parse(dataFromMid.data)
            console.table("dataFromMid ", responseData.respuesta)
            return responseData.respuesta
        } catch (error) {
            return []

        }
    }

    public async servisStatesDocuments() {
        //console.log("servisDownloadPost ", servis);

        let id: any = await this.configService.getSession();
        //console.log("session info ", id);
        let dataext: any = {
            "usuario": id[0].idUsuario,
        };
        const responseData: any = await this.__postDow(`estadodocumento`, dataext);
        // console.log("documentos status ", responseData);

        const responseDataJson = JSON.parse(responseData.data)
        // console.table("dataFromMid ", responseDataJson.respuesta)
        return responseDataJson.respuesta
    }


    /*******DATA*********/
    public async menu() {
        return new Promise((resolve, reject) => {
            let arr = [
                {
                    est: false,
                    title: 'Inicio',
                    url: '/home',
                    icon: 'home-outline'
                },
                {
                    est: false,
                    title: 'mk',
                    url: '/home',
                    icon: 'list-box'
                },
                {
                    est: false,
                    title: 'Cotización',
                    url: '/pedidos/DOF',
                    icon: 'pricetags-outline'
                },
                {
                    est: false,
                    title: 'Pedidos',
                    url: '/pedidos/DOP',
                    icon: 'document-outline'
                },
                {
                    est: false,
                    title: 'Factura',
                    url: '/pedidos/DFA',
                    icon: 'card-outline'
                },
                {
                    est: false,
                    title: 'Entregas',
                    url: '/pedidos/DOE',
                    icon: 'checkbox-outline'
                },
                {
                    est: false,
                    title: 'mk',
                    url: '/home',
                    icon: 'list-box'
                },
                {
                    est: false,
                    title: 'Clientes',
                    url: '/clientes/all',
                    icon: 'people-outline'
                },
                {
                    est: false,  //(cantidad.debe > 0) ? true : false,
                    title: 'Pagos recibidos',
                    url: '/pagos',
                    icon: 'briefcase-outline'
                },
                {
                    est: false,
                    title: 'Informes diarios',
                    url: '/informes',
                    icon: 'clipboard-outline'
                },
                {
                    est: false,
                    title: 'Productos',
                    url: '/productos',
                    icon: 'layers-outline'
                },
                {
                    est: false,
                    title: 'mk',
                    url: '/home',
                    icon: 'list'
                },
                {
                    est: false,
                    title: 'Rutas',
                    url: '/ruta',
                    icon: 'locate-outline'
                },
                {
                    est: false,
                    title: 'Perfil',
                    url: '/perfil',
                    icon: 'person-outline'
                },
                {
                    est: false,
                    title: 'Agenda',
                    url: '/agendas',
                    icon: 'calendar-outline'
                }
            ];
            resolve(arr);
        });
    }

    public menuHome() {
        return [
            {
                est: false,
                title: 'COTIZACION',
                url: '/pedidos/DOF',
                icon: 'pricetag'
            },
            {
                est: false,
                title: 'Pedidos',
                url: '/pedidos/DOP',
                icon: 'document'
            },
            {
                est: false,
                title: 'Factura',
                url: '/pedidos/DFA',
                icon: 'card'
            },
            {
                est: false,
                title: 'Entregas',
                url: '/pedidos/DOE',
                icon: 'checkbox'
            },
            {
                est: false,
                title: 'Clientes',
                url: '/clientes/all',
                icon: 'people'
            },
            {
                est: false,
                title: 'Pagos recibidos',
                url: '/pagos',
                icon: 'wallet'
            },
            {
                est: false,
                title: 'Informes',
                url: '/informes',
                icon: 'clipboard'
            },
            {
                est: false,
                title: 'Productos',
                url: '/productos',
                icon: 'layers'
            },
            {
                est: false,
                title: 'Rutas',
                url: '/ruta',
                icon: 'locate-sharp'
            },
            {
                est: false,
                title: 'Agenda',
                url: '/agendas',
                icon: 'calendar-sharp'
            }
        ];
    }

    public async dataDocumet(pedido: any, dataexport: any, latitude, longitude) {
        console.log("estructure dataDocumet ", pedido);
        let id: any = await this.configService.getSession();
        return {
            DocType: pedido.dataproducto.dataexport.tipoDoc,
            DocEntry: '',
            creadopor: id[0].idUsuario,
            DocNum: '',
            CardCode: pedido.dataproducto.dataexport.cliente.CardCode,
            CardName: pedido.dataproducto.dataexport.cliente.CardName,
            PriceListNum: pedido.unidadID.IdListaPrecios,
            DocCur: dataexport.moneda,
            Series: pedido.dataproducto.Series,
            Address: pedido.dataproducto.dataexport.cliente.Address,
            estadosend: 1,
            fechaentrega: moment().format('YYYY-MM-DD'),
            canceled: '',
            Printed: '',
            DocStatus: '',
            NumAtCard: '',
            DiscPrcnt: '',
            DiscSum: '',
            DocRate: '',
            DocTotal: 0,
            PaidToDate: '',
            Ref1: '',
            Ref2: '',
            Comments: '',
            GroupNum: '',
            SlpCode: '',
            LicTradNum: '',
            UserSign: '',
            UserSign2: '',
            UpdateDate: '',
            U_4MOTIVOCANCELADO: '',
            U_4NIT: pedido.dataproducto.dataexport.cliente.FederalTaxId,
            U_4RAZON_SOCIAL: pedido.dataproducto.dataexport.cliente.razonsocial,
            U_LATITUD: latitude,
            U_LONGITUD: longitude,
            U_4SUBTOTAL: '',
            JrnlMemo: '',
            U_4DOCUMENTOORIGEN: '',
            U_4MIGRADOCONCEPTO: '',
            U_4MIGRADO: '',
            estado: '1',
            gestion: '',
            mes: '',
            correlativo: '',
            rowNum: '',
            descuento: 0,
            tipocambio: 1,
            currency: dataexport.moneda,
            clone: 0,
            tipodescuento: 0,
            tipotransaccion: 'AX',
            tipoestado: 'new',
            fechasend: pedido.dataproducto.dataexport.fechaentrega,
            codigoControl: "NULL",
            grupoproductoscode: dataexport.grupoproductoscode,
            idSucursal: dataexport.sucursal.id
        };

    }

    public dataDetalle(pedido: any, dataexport: any) {
        console.log("DEVD dataDetalle()")
        console.log("DEVD pedido ", pedido);
        console.log("DEVD dataexport ", dataexport);
        let codeMidBoni = 0;
        if (pedido.bonificacion == 1) {//ES BONIFICACION SETEAR CODE MID CABEZERA
            codeMidBoni = dataexport.databoni[0].codeMid;
            console.log("codigo de bonificacion mid ", codeMidBoni);
        }
        return {
            ItemCode: pedido.dataproducto.ItemCode,
            ItemName: pedido.dataproducto.ItemName,
            cantidad: pedido.cantidad,
            price: pedido.presio,
            Currency: dataexport.currency,
            LineTotal: parseFloat((pedido.presio * pedido.cantidad) + pedido.ICEe + pedido.ICEp),
            WhsCode: pedido.WhsCode,
            unidadID: pedido.unidadID.Code,
            descuento: pedido.descuentototal,
            porcentajedata: pedido.descuentoporsentaje,
            DocEntry: '',
            DocNum: '',
            LineNum: '',
            BaseType: '',
            BaseEntry: '',
            BaseLine: '',
            LineStatus: '',
            GrossBase: '',
            OpenQty: '',
            DiscPrcnt: pedido.descuentoporsentaje,
            CodeBars: '',
            PriceAfVAT: '',
            TaxCode: '',
            U_4LOTE: '',
            tc: '',
            idProductoPrecio: '',
            ProductoPrecio: '',
            LineTotalPay: parseFloat(((pedido.presio * pedido.cantidad) + pedido.ICEe + pedido.ICEp)) - pedido.descuento,
            DiscTotalPrcnt: pedido.descuentoporsentaje,
            DiscTotalMonetary: pedido.descuentototal,
            icetp: pedido.icetp,
            icete: pedido.icete,
            icett: pedido.icett,
            ICEt: pedido.ICEt,
            ICEp: pedido.ICEp,
            ICEe: pedido.ICEe,
            bonificacion: pedido.bonificacion,
            combos: pedido.combos,
            BaseId: pedido.BaseId,
            IdBonfAut: pedido.IdBonfAut,
            GroupName: pedido.GroupName,
            bonificacionesUsadas: pedido.bonificacionesUsadas,
            codeMid: codeMidBoni,
            BaseQty: pedido.unidadID.BaseQty,
            lotes:pedido.lotesarr
        };
    }

    public async createcampususer(camposusuario: any, form: any, valor: any) {

        console.log("datos que llegan", camposusuario);
        console.log("datos que llegan2", valor);

        let acc = 1;
        let value = '';
        let contenedorcampos = '';
        let aux = 0;

        for (let i = 0; i < camposusuario.length; i++) {
            if (camposusuario[i].Objeto == form) {

                aux = 1;
                if (camposusuario[i].tipocampo == 1) {

                    value = '';
                    if (valor["campousu" + camposusuario[i].Nombre]) {
                        value = valor["campousu" + camposusuario[i].Nombre];
                        acc = 2;
                    } else {
                        acc = 1;
                    };

                    contenedorcampos += "<ion-list id=\"label_campousu" + camposusuario[i].Nombre + "\">";
                    contenedorcampos += "<ion-item >";
                    contenedorcampos += "<ion-label position=\"stacked\">" + camposusuario[i].Label + "</ion-label>";
                    if (acc == 1) {
                        contenedorcampos += "<ion-select idxmobile=\"" + camposusuario[i].Id + "\" class=\"campousu" + camposusuario[i].Nombre + "\" id=\"campousu" + camposusuario[i].Nombre + "\" placeholder=\"...\">";

                        if (camposusuario[i].relacionado == null) {
                            for (let l = 0; l < camposusuario[i].lista.length; l++) {
                                let codigo = camposusuario[i].lista[l].codigo;
                                let nombre = camposusuario[i].lista[l].nombre.replace(/['"]+/g, '');
                                contenedorcampos += "<ion-select-option  value=" + codigo + ">" + nombre + "</ion-select-option>"
                            }
                        }

                    } else {
                        contenedorcampos += "<ion-select  value=" + value + "  class=\"campousu" + camposusuario[i].Nombre + "\" id=\"campousu" + camposusuario[i].Nombre + "\" placeholder=\"...\">";

                        if (camposusuario[i].relacionado == null) {
                            for (let l = 0; l < camposusuario[i].lista.length; l++) {
                                let codigo = camposusuario[i].lista[l].codigo;
                                let nombre = camposusuario[i].lista[l].nombre.replace(/['"]+/g, '');
                                contenedorcampos += "<ion-select-option  value=" + codigo + ">" + nombre + "</ion-select-option>"
                            }
                        } else {

                            let val = ''
                            let id = ''
                            for (let j = 0; j < camposusuario.length; j++) {
                                if (camposusuario[j].Objeto == form) {
                                    if (camposusuario[j].tipocampo == 1) {
                                        if (camposusuario[j].Id == camposusuario[i].relacionado) {
                                            if (valor["campousu" + camposusuario[j].Nombre]) {
                                                val = valor["campousu" + camposusuario[j].Nombre];

                                                for (let l = 0; l < camposusuario[j].lista.length; l++) {
                                                    if (camposusuario[j].lista[l].codigo == val) {
                                                        id = camposusuario[j].lista[l].Id;
                                                    }

                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            for (let x = 0; x < camposusuario.length; x++) {
                                if (camposusuario[x].Objeto == form) {
                                    if (camposusuario[x].tipocampo == 1) {
                                        if (camposusuario[x].relacionado != null) {
                                            for (let l = 0; l < camposusuario[x].lista.length; l++) {
                                                if (camposusuario[x].lista[l].cabecera == camposusuario[i].relacionado && camposusuario[x].lista[l].detalle == id) {

                                                    let codigo = camposusuario[x].lista[l].codigo;
                                                    let nombre = camposusuario[x].lista[l].nombre.replace(/['"]+/g, '');
                                                    contenedorcampos += "<ion-select-option  value=" + codigo + ">" + nombre + "</ion-select-option>"

                                                }
                                            }
                                        }
                                    }
                                }
                            }

                        }
                    }

                    contenedorcampos += "</ion-select>"
                    contenedorcampos += "</ion-item>"
                    contenedorcampos += "</ion-list>"
                } else {
                    if (camposusuario[i].tipocampo == 0) {

                        if (valor["campousu" + camposusuario[i].Nombre]) {
                            console.log(valor["campousu" + camposusuario[i].Nombre]);
                            value = valor["campousu" + camposusuario[i].Nombre];
                            acc = 2;
                        } else {
                            acc = 1;
                        };

                        contenedorcampos += "<ion-item id=\"label_campousu" + camposusuario[i].Nombre + "\">"
                        contenedorcampos += "<ion-label position=\"stacked\">" + camposusuario[i].Label + "</ion-label>";
                        if (acc == 1) {
                            contenedorcampos += "<ion-textarea class=\"campousu" + camposusuario[i].Nombre + "\" maxlength=\"" + camposusuario[i].longitud + "\" id=\"campousu" + camposusuario[i].Nombre + "\" placeholder=\"...\"></ion-textarea>";
                        } else {
                            contenedorcampos += "<ion-textarea value=\"" + value + "\" class=\"campousu" + camposusuario[i].Nombre + "\" maxlength=\"" + camposusuario[i].longitud + "\" id=\"campousu" + camposusuario[i].Nombre + "\" placeholder=\"...\"></ion-textarea>";
                        }
                        contenedorcampos += "</ion-item>";

                    } else {

                        if (valor["campousu" + camposusuario[i].Nombre]) {
                            console.log(valor["campousu" + camposusuario[i].Nombre]);
                            value = valor["campousu" + camposusuario[i].Nombre];
                            acc = 2;
                        } else {
                            acc = 1;
                        };

                        contenedorcampos += "<ion-item id=\"label_campousu" + camposusuario[i].Nombre + "\">"
                        contenedorcampos += "<ion-label position=\"stacked\">" + camposusuario[i].Label + "</ion-label>";
                        if (acc == 1) {
                            contenedorcampos += "<ion-input type=\"number\" class=\"campousu" + camposusuario[i].Nombre + "\" max=\"" + camposusuario[i].longitud + "\" id=\"campousu" + camposusuario[i].Nombre + "\" placeholder=\"...\"></<ion-input>";
                        } else {
                            contenedorcampos += "<ion-input value=\"" + value + "\" type=\"number\" class=\"campousu" + camposusuario[i].Nombre + "\" max=\"" + camposusuario[i].longitud + "\" id=\"campousu" + camposusuario[i].Nombre + "\" placeholder=\"...\"></<ion-input>";
                        }
                        contenedorcampos += "</ion-item>";
                    }
                }
            }


        }

        if (aux == 1) {
            contenedorcampos = '<ion-card name="camposUsuarios"><ion-list-header color="medium"> <ion-label>Campos de Usuarios</ion-label></ion-list-header>' + contenedorcampos + '</ion-card>'
        }

        return contenedorcampos;
    }

    public async createcampususerlabel(camposusuario: any, form: any, valor: any) {

        console.log("el valor es", valor);
        console.log("camposusuario", camposusuario);
        let contenedorcampos = '';
        contenedorcampos += '<ion-card>';
        contenedorcampos += '<ion-list-header color="medium">';
        contenedorcampos += '<ion-label>Campos de Usuarios</ion-label>';
        contenedorcampos += '</ion-list-header>';
        contenedorcampos += '<ion-card-content>';

        campousuCode: "501"
        campousuPrcName: "01"

        let aux = 0;
        for (let i = 0; i < camposusuario.length; i++) {
            let value = ''
            if (camposusuario[i].Objeto == form) {
                aux = 1;
                if (valor["campousu" + camposusuario[i].Nombre]) {
                    if (camposusuario[i].tipocampo == "1") {
                        for (let x = 0; x < camposusuario[i].lista.length; x++) {
                            if (camposusuario[i].lista[x].codigo == valor["campousu" + camposusuario[i].Nombre]) {
                                value = camposusuario[i].lista[x].nombre;
                            }
                        }
                    } else {
                        value = valor["campousu" + camposusuario[i].Nombre];
                    }
                }
                contenedorcampos += '<p><b>' + camposusuario[i].Label + ': </b>' + value;
            }

        }
        contenedorcampos += '</ion-card-content></ion-card>';
        return contenedorcampos;
    }
    
    public async stocklistaitems(data: any) {

        this.path = await this.configService.getIp();
        console.log(this.path + 'v2/productosalmacenes/listaproductosalmacensap');
        console.log("envio",JSON.stringify(data));
        return new Promise((resolve, reject) => {
            this.http.setDataSerializer('json');
            this.http.setRequestTimeout(0);
            this.http.post(this.path + 'v2/productosalmacenes/listaproductosalmacensap', data, {}).then((data: any) => {
                let ux = JSON.parse(data.data);
                resolve(ux);
            }).catch((error: any) => {
                reject(error);
            });
        });
    }

    public async servisDescuentosapPost(servis,data,tiempo=0) {
        this.path = await this.configService.getIp();
        let id: any = await this.configService.getSession();
        return await this.__postDow(servis, data,tiempo);
    } 
    
}
