import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import { Detalle } from "./detalle";
import * as moment from 'moment';
import 'lodash';
import { Bolivia } from "../utilsx/bolivia";
import { Companex } from "../utilsx/companex";
import { Calculo } from "../utilsx/calculo";
import { Pagos } from "../models/V2/pagos";
import { GlobalConstants } from "../../global";
import {Camposusuario} from "../services/camposusuario.service"


declare var _: any;

export class Documentos extends Databaseconf {
    public configService: ConfigService;
    public localizacion: any;
    public Camposusuario: Camposusuario;
    //  public configService: ConfigService;
    //   public configService: ConfigService;

    public async createView() {
        let sql = `
                CREATE VIEW IF NOT EXISTS viewuno AS SELECT d.*, printf("%.2f",d.DocTotal,2) AS DocTotalx, 
                printf("%.2f", ROUND((SELECT (SUM(icett) - d.descuento) FROM detalle WHERE idDocumento = d.id),2)) AS total,
                printf("%.2f", (SELECT ROUND(SUM(monto),2) FROM pagos p WHERE p.documentoId = d.cod)) AS pagado,
                strftime("%d-%m-%Y", d.fecharegistro) AS fechareg,
                ((SELECT round(SUM(monto),2) FROM pagos p WHERE p.documentoId = d.id) <= (SELECT (SUM(icett) - d.descuento) FROM detalle WHERE idDocumento = d.id)) AS estx
                FROM documentos AS d`;
        return await this.executeSQL(sql);
    }

    /*************/
    // public async listadodocumentos() {


    //     // let pagox = `(SELECT SUM(p.monto) FROM documentopago dp INNER JOIN pagos p ON dp.cod = p.documentoPagoId WHERE p.documentoId = d.cod AND dp.estado = 0)`;
    //     let pagox = `(SELECT SUM(p.monto) FROM pagos p WHERE p.documentoId = d.cod)`
    //     let bonifix = `(SELECT IFNULL(SUM(dx.Price * dx.Quantity),0) FROM detalle dx WHERE dx.bonificacion = 1 AND dx.idDocumento = d.cod)`;
    //     let innerx = `ROUND(printf("%.2f", (((SUM(l.Price * l.Quantity) - SUM(l.U_4DESCUENTO) + SUM(ICEe) + SUM(ICEp) ) - d.descuento)) ,2),2)- IFNULL(${pagox},'0') `;
    //     //let pagox = `(SELECT SUM(p.monto) FROM pagos p WHERE p.documentoId = d.cod)`;
    //     let totalx = `ROUND(printf("%.2f", (((SUM(l.Price * l.Quantity) - SUM(l.U_4DESCUENTO) + SUM(ICEe) + SUM(ICEp) ) - d.descuento)) ,2),2)`;

    //     let sql = `CREATE VIEW IF NOT EXISTS v_documentoview9 AS SELECT d.*, COUNT(l.id) AS cantidadDetalle,
    //                     printf("%.2f",SUM(l.Price * l.Quantity),2) AS DocumentTotal,
    //                     printf("%.2f",(SUM(l.Price * l.Quantity) - SUM(l.U_4DESCUENTO)+SUM(ICEe)+SUM(ICEp)),2) AS DocumentTotalDetallePay,
    //                     printf("%.2f", ((SUM(l.U_4DESCUENTO) + d.descuento) ) ,2) AS DocumentdescuentoTotal,
    //                     (CASE (d.origen) WHEN 'inner' THEN ${totalx} ELSE ROUND(d.DocTotal,2) END) AS DocumentTotalPay,
    //                     (CASE (d.origen) WHEN 'inner' THEN ${pagox} ELSE (d.PaidtoDate + IFNULL(${pagox},'0')) END) AS pago,
    //                     (CASE (d.origen) WHEN 'inner' THEN ${innerx} ELSE d.DocTotal - (d.PaidtoDate +  IFNULL(${pagox},'0'))END) AS saldox 
    //                FROM documentos d LEFT JOIN detalle l ON d.cod = l.idDocumento 
    //                GROUP BY d.cod ORDER BY d.id DESC`;
    //     console.log("sql v_documentoview9 ", sql)
    //     return await this.queryAll(sql);
    // }

    public async listadodocumentos() {

        let sql1 = `DROP VIEW IF EXISTS v_documentoview9;`;
        await this.queryAll(sql1);

        let pagox = `(SELECT SUM(p.monto_total) as monto FROM xmf_cabezera_pagos p WHERE p.documentoId = d.cod and estado = 3 and p.cancelado <> 3)`

        let bonifix = `(SELECT IFNULL(SUM(dx.Price * dx.Quantity),0) FROM detalle dx WHERE dx.bonificacion = 1 AND dx.idDocumento = d.cod)`;
        //let innerx = `ROUND(printf("%.2f", (((SUM(l.Price * l.Quantity) - SUM(l.U_4DESCUENTO) + SUM(ICEe) + SUM(ICEp) ) - d.descuento)) ,2),1)- IFNULL(${pagox},'0') `;
        
        let innerx = `printf("%.2f",ROUND(printf("%.2f", (((SUM(l.Price * l.Quantity) - SUM(l.U_4DESCUENTO) + SUM(ICEe) + SUM(ICEp) ) -0)) ,2),2)- IFNULL((SELECT SUM(p.monto_total)  FROM xmf_cabezera_pagos p WHERE p.documentoId = d.cod and p.estado = 3  and p.cancelado <> 3),'0') ,2)`;

        //let pagox = `(SELECT SUM(p.monto) FROM pagos p WHERE p.documentoId = d.cod)`;
        let totalx = `ROUND(printf("%.2f", (((SUM(l.Price * l.Quantity) - SUM(l.U_4DESCUENTO) + SUM(ICEe) + SUM(ICEp) ) + 0)) ,2),2)`;

        let sql = `CREATE VIEW IF NOT EXISTS v_documentoview9 AS SELECT d.*, COUNT(l.id) AS cantidadDetalle,
                        printf("%.2f",SUM(l.Price * l.Quantity),2) AS DocumentTotal,
                        printf("%.2f",(SUM(l.Price * l.Quantity) - SUM(l.U_4DESCUENTO)+SUM(ICEe)+SUM(ICEp)),2) AS DocumentTotalDetallePay,
                        printf("%.2f", ((SUM(l.U_4DESCUENTO) + 0) ) ,2) AS DocumentdescuentoTotal,
                        (CASE (d.origen) WHEN 'inner' THEN ${totalx} ELSE d.DocTotal END) AS DocumentTotalPay,
                        (CASE (d.origen) WHEN 'inner' THEN ${pagox} ELSE (d.PaidtoDate + IFNULL(${pagox},'0')) END) AS pago,
                        (CASE (d.origen) WHEN 'inner' THEN ${innerx} ELSE d.saldo END) AS saldox
                        ,${innerx} AS prueba
                   FROM documentos d LEFT JOIN detalle l ON d.cod = l.idDocumento 
                   GROUP BY d.cod, d.cuota ORDER BY d.id DESC`; //ROUND  d.DocTotal 
        console.log("create view v9-->", sql);
        return await this.queryAll(sql);
    }

    public async documentos(search = '', tipo: string, origen: string) {
        let like = ``;
        let reverseinvis = ``;
        (origen == 'outer' && tipo == 'DFA') ? reverseinvis = ` AND ReserveInvoice = 'Y' AND (cantidadDetalle > 0) ` : reverseinvis = ``;
        
        (tipo == 'DOE') ? reverseinvis = ` AND EnvioEvidencia = '0' ` : reverseinvis = ``;

        if (search != '') like = ` AND (CardName LIKE '%${search}%' OR TaxDate LIKE '%${search}%' OR DocDueDate LIKE '%${search}%' OR federalTaxId LIKE '%${search}%' OR CardCode LIKE '%${search}%' OR cod LIKE '%${search}%')`;
        //let sql = `SELECT * FROM v_documentoview9 WHERE origen = '${origen}' AND DocType = '${tipo}' AND idUser=${localStorage.getItem("idSession")} ${reverseinvis}  ${like} LIMIT 30`;
        
        let sql = `SELECT * FROM v_documentoview9 WHERE origen = '${origen}' AND DocType = '${tipo}' ${reverseinvis}  ${like} LIMIT 30`;
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public async documentospen() {
        let sql = `SELECT * FROM v_documentoview9 WHERE origen = 'inner' AND (tipoestado = 'null' OR tipoestado = 'new') AND idUser=${localStorage.getItem("idSession")} ; `;
        console.log("sql ", sql);

        return await this.queryAll(sql);
    }

    public async cantidadDocx(fecha: string, fechafin: string) {
        let sql = `SELECT DocType, COUNT(*) AS cantx, currency, tipoestado, SUM(DocumentTotalPay) AS total  
                   FROM v_documentoview9 
                   WHERE origen = 'inner' AND CreateDate AND idUser=${localStorage.getItem("idSession")} BETWEEN '${fecha}' AND '${fechafin}' GROUP BY DocType, tipoestado  ORDER BY DocType DESC`;
        return await this.queryAll(sql);
    }

    public async docxAnulados(fecha: string, fechafin: string) {
        let sql = `SELECT DocType, currency, tipoestado, SUM(DocumentTotalPay) AS total  
                   FROM v_documentoview9 
                   WHERE origen = 'inner' AND CreateDate BETWEEN '${fecha}' AND '${fechafin}' AND idUser=${localStorage.getItem("idSession")} AND tipoestado = 'anulado' GROUP BY DocType, tipoestado ORDER BY DocType DESC`;
        return await this.queryAll(sql);
    }

    public async findexe(code: string) {
        let sql = `SELECT * FROM v_documentoview9 WHERE cod = '${code}' LIMIT 1`;
        let resp: any = await this.queryAll(sql);
        return resp[0];
    }

    public async copiados(code: string) {
        let sql = `SELECT * FROM v_documentoview9 WHERE clone = '${code}' AND canceled != '3' AND idUser=${localStorage.getItem("idSession")};`;
        return await this.queryAll(sql);
    }

    public async find(id: number) {
        let sql = `SELECT * FROM v_documentoview9 WHERE id = ${id} LIMIT 1`;
        console.log(sql);
        let resp: any = await this.queryAll(sql);
        return resp[0];
    }

    public async dataExport(id: any) {
        let sqlx = `SELECT d.*, '${id.sucursalxId}' AS sucursalId, '${id.equipoId}' AS equipoId, '1' AS papelId 
                    FROM v_documentoview9 d WHERE (d.estadosend = 1 OR d.estadosend = 7)  AND (d.tipoestado = "cerrado"  OR  d.tipoestado = "anulado") AND idUser=${localStorage.getItem("idSession")} ORDER BY id ASC LIMIT 20`;
        console.log("sqlx ", sqlx);
        return await this.queryAll(sqlx);
    }

    public async dataExport2(id: any) {
        let sqlx = `SELECT d.* FROM v_documentoview9 d ORDER BY id ASC `;
        console.log("sqlx ", sqlx);
        return await this.queryAll(sqlx);
    }

    public async deudasCliente(cardCode: string, search = '') {

        let txtserach = ' ';
        (search != '') ? txtserach = `  AND ((v.centrocosto LIKE '%${search}%' OR v.unidadnegocio LIKE '%${search}%' OR v.cod LIKE '%${search}%' OR v.codexternal LIKE '%${search}%') OR v.DocDueDate LIKE '%${search}%') ` : txtserach = '';
        let sql = ` SELECT v.*, saldox AS pagarx FROM v_documentoview9 v WHERE v.CardCode = '${cardCode}' AND v.DocType = 'DFA' AND v.origen = 'outer' AND v.saldox > 0 AND v.cuota > 0 ${txtserach} ORDER BY v.id DESC;`; //AND v.idUser=${localStorage.getItem("idSession")} 
        console.log("sql deudas cliente", sql);
        return await this.queryAll(sql);
    }

    public async deudasClienteAll() {
        //let sql = ` SELECT v.*, saldox AS pagarx FROM v_documentoview9 v WHERE v.CardCode IN (select CardCode from clientes) AND v.origen = 'outer' AND v.saldox > 0 ORDER BY v.id DESC;`; // AND v.idUser=${localStorage.getItem("idSession")} 
        let sql = ` SELECT v.* FROM documentos v WHERE v.CardCode IN (select CardCode from clientes) AND v.origen = 'outer' ORDER BY v.id DESC;`; // AND v.idUser=${localStorage.getItem("idSession")} 
        console.log("sql deudas cliente all ", sql);
        return await this.queryAll(sql);
    }

    public async ClienteTodos() {
        let sql = ` SELECT * FROM documentos v
          ORDER BY v.id DESC;`;
        return await this.queryAll(sql);
    }

    public findAllCloneUltimo(doc: string, origen = 'outer') {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM viewuno WHERE tipoestado != 'null' AND DocType = '${doc}' ORDER BY id DESC LIMIT 1`;
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++)
                    arr.push(data.rows.item(i));
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async existDocument(idcod: string) {
        let sql = `SELECT COUNT(*) AS xd FROM documentos WHERE codexternal = '${idcod}'`;
        let resp: any = await this.queryAll(sql);
        return resp[0];
    }

    /*************/

    public async copia(clone: any, DocType: any, reserve: number) {
        if (DocType == 'DOE' && reserve == 1) {
            let detalle = new Detalle();
            let resp: any = await detalle.docDetalle(clone);
            let cantidadtotal: number = resp[0].Quantity;
            let sql = `SELECT GROUP_CONCAT(dx.id) as documentoscopy FROM documentos dx WHERE dx.clone = '${clone}' AND dx.DocType = '${DocType}' AND dx.idUser=${localStorage.getItem("idSession")}`;
            let rs: any = await this.queryAll(sql);
            let arrdetalles: any = await detalle.docDetalle(rs[0].documentoscopy);
            let cantidadtotalentregado: number = arrdetalles[0].Quantity;
            if (cantidadtotalentregado >= cantidadtotal) {
                return [{ "copia": 1 }];
            } else {
                return [{ "copia": 0 }];
            }
        } else {
            let sql = `SELECT COUNT(dx.id) AS copia FROM documentos dx WHERE dx.clone = '${clone}' AND dx.DocType = '${DocType}' AND dx.idUser=${localStorage.getItem("idSession")}`;
            return await this.queryAll(sql);
        }
    }

    public contador(tipo: string, id: number) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT COUNT(*) as total FROM documentos WHERE CardCode = "' + id + '" AND DocType = "' + tipo + '" AND tipoestado != "anulado" AND idUser=' + localStorage.getItem("idSession") + '';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    private subquery(fecha: string, tipo: string): string {
        return `(SELECT (CASE WHEN SUM(DocTotal) IS NULL THEN 0 ELSE printf("%.2f",SUM(DocTotal),2) END) FROM documentos WHERE strftime("%Y-%m-%d", fecharegistro) = '${fecha}' AND DocType = '${tipo}') AND idUser=${localStorage.getItem("idSession")}`;
    }

    public async reportVentaDiaria(fechax = '') {
        let fecha = null;
        (fechax == '') ? fecha = this.getFechaPicker() : fecha = fechax;
        let oferta = this.subquery(fecha, 'DOF');
        let pedidos = this.subquery(fecha, 'DOP');
        let facturas = this.subquery(fecha, 'DFA');
        let sql = `SELECT ${oferta} AS ofertas, ${pedidos} AS pedidos, ${facturas} AS facturas, (SELECT printf("%.2f",SUM(monto),2) FROM pagos WHERE fecha = '${fecha}') AS pagado`;
        return await this.queryAll(sql);
    }

    public findAllPedidos(code: string) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT t.*, printf("%.2f", CAST(t.total as decimal) - CAST(t.pagado as decimal)) deudax FROM viewuno t WHERE t.CardCode = "' + code + '" AND t.DocType = "DFA" AND t.tipoestado != "null" AND CAST(t.pagado as decimal) < CAST(t.total as decimal)  ORDER BY t.id DESC';
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++)
                    arr.push(data.rows.item(i));
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public findAllTotal(code: string) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT printf("%.2f", SUM(CAST(t.total as decimal) - CAST(t.pagado as decimal))) totalx FROM viewuno t WHERE t.CardCode = "' + code + '" AND t.DocType = "DFA" AND t.tipoestado != "null" AND CAST(t.pagado as decimal) <= CAST(t.total as decimal) ORDER BY t.id DESC';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async anulacionDocumento(data, cod: any) {
        console.log("DEVD anulacionDocumento()");

        let dateAnular = moment().format('YYYY-MM-DD');

       // let sql = `UPDATE documentos SET UpdateDate='${dateAnular}', U_4MOTIVOCANCELADO = '${data.conceptoAnulacion}', U_4MOTIVOCANCELADOCABEZERA = '${data.opcionAnular}',  estadosend = 7, tipoestado = 'anulado' WHERE cod = '${cod}' `;
        let sql = `UPDATE documentos SET UpdateDate='${dateAnular}', U_4MOTIVOCANCELADO = '${data.conceptoAnulacion}', U_4MOTIVOCANCELADOCABEZERA = '${data.opcionAnular}',  canceled = 3 WHERE cod = '${cod}' `;
        
        console.log("sql update anulacion", sql);
        return await this.executeSQL(sql);
    }

    public async actualizaiddocumento(cod: any,cod_and: any) {
        return new Promise((resolve, reject) => {
            console.log("DEVD actualizaiddocumento()");
            let sql = `UPDATE documentos SET cod ='${cod}' WHERE cod = '${cod_and}' `;
            console.log("sql update actualizacion", sql);

            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                console.log(e);
                reject(e);
            });
        });
    }

    public async findAllPedidosMayor(tipo: string, id: number) {
        let sql = 'SELECT d.* FROM v_documentoview9 d WHERE  DocType = "' + tipo + '" AND  (d.estadosend = 1 OR d.estadosend = 7)  AND (d.tipoestado = "cerrado"  OR  d.tipoestado = "anulado") order by id desc';

        console.log("sqlx ", sql);
   
        let resp: any = await this.queryAll(sql);
        return resp;        
    }

    // activo, cerrado, anulado, null
    public async updateEstado(estado: any, idpedido: number, codControl: string, U_NumeroSiguiente: any, U_LB_NumeroAutorizac: any, Reserve: any, razon: string, nit: string) {
        let sql = `UPDATE documentos SET tipoestado = '${estado}', U_LB_CodigoControl = '${codControl}', U_LB_NumeroFactura = '${U_NumeroSiguiente}', 
                   U_LB_NumeroAutorizac = '${U_LB_NumeroAutorizac}', Reserve = '${Reserve}', U_4RAZON_SOCIAL = '${razon}', U_4NIT = '${nit}'  WHERE cod = '${idpedido}'`;
        return new Promise((resolve, reject) => {
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async generaCod(tipoDoc: any, idUser: any, numnext: any) {
        console.log({ tipoDoc }, { idUser }, { numnext });

        return new Promise((resolve, reject) => {
            let numx = "00000".slice(0, -parseInt(numnext.toString().length));
            let numxuser = "0000".slice(0, -parseInt(idUser.toString().length));
            let codigo = (tipoDoc + "1" + (numxuser + "" + idUser) + "" + (numx + "" + numnext));
            resolve(codigo.toString());
        });
    }

    public async clonar(id: string, idUser: number, tipoDocumento: string, x: boolean, cod: any, reserve = 0) {
        let pedido: any = await this.findexe(id);
        pedido.fechasend = moment().format('YYYY-MM-DD');
        pedido.estado = '';
        pedido.estadosend = '1';
        pedido.clone = id;
        pedido.cod = cod;
        pedido.tipoestado = 'new';
        pedido.DocType = tipoDocumento;
        pedido.cloneaux = 1;

        console.log("clonar() ", pedido);

        console.log("clonar() ",pedido.idSucursalMobile);
        if ((x == true) && ((tipoDocumento == 'DOF') || (tipoDocumento == 'DOP'))) pedido.clone = 0;
            console.log("return pedido ", pedido);
            let aux = await this.insertLocal(pedido, idUser, cod, reserve);
        if (aux == '0') {
            return '0';
        } else {
            return aux;
        }
    }

    public async insert(data: any, idx: number, codigox: any, reserve = 0) {
        let tipoDocumento = codigox;
        let fecha = moment().format('YYYY-MM-DD');
        let hora = moment().format('h:mm:ss');
        console.log('insert data cabezera ', data);
        console.log('insert data idx ', idx);

        let campos = new Camposusuario();
        let session = await campos.consultasesion();

        if (!idx || idx == undefined) {
            idx = Number(localStorage.getItem("idSession"));
        }

        //data.idSucursalMobile

        if (data.idSucursal == undefined || data.idSucursal == "undefined") {
            data.idSucursal = '0';
        }

        if (data.idSucursalMobile == undefined || data.idSucursalMobile == "undefined") {
            data.idSucursalMobile = data.idSucursal;
        }

        let sql = `SELECT * FROM clientessucursales where id = ${data.idSucursalMobile} AND CardCode = '${data.CardCode}'  ; `;
        console.log(" sql ", sql);
        let sucursalInfo: any = await this.queryAll(sql);
        console.log('sucursalInfo ', sucursalInfo);
        if (sucursalInfo.length == 0) {
            console.log("volver a buscar por linea");
            let sql = `SELECT * FROM clientessucursales where LineNum = ${data.idSucursalMobile} AND CardCode = '${data.CardCode}'  ; `;
            console.log(" sql ", sql);
            let sucursalInfoLineNum: any = await this.queryAll(sql);
            console.log('sucursalInfoLineNum 2 ', sucursalInfoLineNum);
            if (sucursalInfoLineNum.length > 0) {
                //data.idSucursal = "0";
                console.log("sucursalInfoLineNum[0].LineNum ", sucursalInfoLineNum[0].LineNum)
                sucursalInfo = sucursalInfoLineNum;
                data.idSucursal = sucursalInfoLineNum[0].id;
            } else {

                let sql = `SELECT * FROM clientessucursales where LineNum = '0' AND CardCode = '${data.CardCode}'  ; `;
                console.log(" sql ", sql);
                let sucursalInfoLineNum2: any = await this.queryAll(sql);
                console.log('sucursalInfoLineNum 3 ', sucursalInfoLineNum2);

                if (sucursalInfoLineNum2.length > 0) {
                    console.log("sucursalInfoLineNum[0].LineNum ", sucursalInfoLineNum2[0].rowNum)
                    sucursalInfo = sucursalInfoLineNum2;
                    data.idSucursal = sucursalInfoLineNum2[0].id;
                } else {
                    return '0';
                }

            }
            //   data.idSucursal = "0";

        } else {
            data.idSucursal = sucursalInfo[0].LineNum;
        }
        
        let sql2 = await campos.camposusuariosinc(data,1,session);
        console.log("RETORNO 2",sql2);

        console.log("sucursalInfo[0].LineNum 2 ", sucursalInfo[0].LineNum)
        let sqlinsert = `INSERT INTO documentos VALUES (NULL, '${data.cod}', '${data.DocEntry}', '${data.DocNum}', '${data.DocType}', '${data.canceled}', '${data.Printed}', 
            '${data.DocStatus}', '${fecha}', '${fecha}', '${data.CardCode}', '${data.CardName}', '${data.NumAtCard}', '${data.DiscPrcnt}', 
            '${data.DiscSum}', '${data.DocCur}', '${data.DocRate}', ${data.DocTotal}, '${data.PaidToDate}', '${data.Ref1}', '${data.Ref2}', '${data.Comments}', '${data.JrnlMemo}', 
            '${data.GroupNum}', '${data.SlpCode}', '${data.Series}', '${fecha}', '${data.LicTradNum}', '${data.Address}', '${data.UserSign}', '${fecha}', 
            '${data.UserSign2}', '${data.UpdateDate}', '${data.U_4MOTIVOCANCELADO}', '${data.U_4NIT}', '${data.U_4RAZON_SOCIAL}', '${data.U_LATITUD}', '${data.U_LONGITUD}', '${data.U_4SUBTOTAL}',
            '${data.U_4DOCUMENTOORIGEN}', '${data.U_4MIGRADOCONCEPTO}', '${data.U_4MIGRADO}', '${data.PriceListNum}', '${data.estadosend}', '${fecha} ${hora}',
            '${fecha}', '${data.fechasend}', '2', '${idx}', '${data.estado}', '${data.gestion}', '${data.mes}', '${data.correlativo}', '${data.LineNum}',
            ${(data.descuento == "") ? 0 : data.descuento},${(data.tipocambio == "") ? 0 : data.tipocambio},'${data.currency}','${data.clone}',${(data.tipodescuento == "") ? 0 : data.tipodescuento},'','','${data.PayTermsGrpCode}','${data.tipotransaccion}','${data.tipoestado}','','','inner'
            ,'N',0,0,'null','null','null','${data.codigoControl}','null','${data.U_4RAZON_SOCIAL}','null', ${reserve},'','','0','','${data.grupoproductoscode}',
          '0','0','0', '', '' , '${data.idSucursal}','','0','${data.codeConsolidador}','${data.cndpagoname}', 0,'',0,''`+sql2+`);`;
        //   ,'0','0','0' );`;
        console.log("sqlinsert DOCUMENTO ", sqlinsert);
        let idxx: any = await this.executeRaw(sqlinsert);
        let sqlx = `UPDATE documentos SET cod = '${tipoDocumento}', correlativo = ${(idxx + 1)}, DocNum = ${idxx} WHERE id= ${idxx};`;
        await this.executeRaw(sqlx);
        return tipoDocumento;
    }

    public async insertDoc(data: any) {
        console.log("codigo de sucursal",data);
        let idsucursal = 1;
        if (data.idSucursal == undefined || data.idSucursal == "undefined") {
            idsucursal = 1;
        }else{
            idsucursal = data.idSucursalMobile[0].id;
        }

        let campos = new Camposusuario();
        let sql2 = await campos.camposusuario(data,1);

        let sqlinsert = `INSERT INTO documentos VALUES (
            NULL,
            '${data.cod}',
            '${data.DocEntry}',
            '${data.DocNum}',
            '${data.DocType}',
            '0',
            '${data.Printed}',
            '${data.DocStatus}',
            '${data.DocDate}',
            '${data.DocDueDate}',
            '${data.CardCode}',
            '${data.CardName}',
            '${data.NumAtCard}',
            '${data.DiscPrcnt}',
            '${data.DiscSum}',
            '${data.DocCur}',
            '${data.DocRate}',
            '${data.DocTotal}',
            '${data.PaidToDate}',
            '${data.Ref1}',
            '${data.Ref2}',
            '${data.Comments}',
            '${data.JrnlMemo}',
            '${data.GroupNum}',
            '${data.SlpCode}',
            '${data.Series}',
            '${data.TaxDate}',
            '${data.LicTradNum}',
            '${data.Address}',
            '${data.UserSign}',
            '${data.CreateDate}',
            '${data.UserSign2}',
            '${data.UpdateDate}',
            '${data.U_4MOTIVOCANCELADO}',
            '${data.U_4NIT}',
            '${data.U_4RAZON_SOCIAL}',
            '${data.U_LATITUD}',
            '${data.U_LONGITUD}',
            '${data.U_4SUBTOTAL}',
            '${data.U_4DOCUMENTOORIGEN}',
            '${data.U_4MIGRADOCONCEPTO}',
            '${data.U_4MIGRADO}',
            '${data.PriceListNum}',
            '${data.estadosend}',
            '${data.fecharegistro}',
            '${data.fechaupdate}',
            '${data.fechasend}',
            '${data.key}',
            '${data.idUser}',
            '${data.estado}',
            '${data.gestion}',
            '${data.mes}',
            '${data.correlativo}',
            '${data.rowNum}',
            '${data.descuento}',
            '${data.tipocambio}',
            '${data.currency}',
            '${data.clone}',
            '${data.tipodescuento}',
            '${data.federalTaxId}',
            '${data.cardNameAux}',
            '${data.PayTermsGrpCode}',
            '${data.tipotransaccion}',
            '${data.tipoestado}',
            '${data.comentario}',
            '${data.cuenta}',
            '${data.origen}',
            '${data.ReserveInvoice}',
            '${data.saldo}',
            '${data.Pendiente}',
            '${data.U_LB_NumeroFactura}',
            '${data.U_LB_NumeroAutorizac}',
            '${data.U_LB_FechaLimiteEmis}',
            '${data.U_LB_CodigoControl}',
            '${data.U_LB_EstadoFactura}',
            '${data.U_LB_RazonSocial}',
            '${data.U_LB_TipoFactura}',
            '${data.Reserve}',
            '${data.centrocosto}',
            '${data.unidadnegocio}',
            '${data.reimpresiones}',
            '${data.codexternal}',
            '${data.grupoproductoscode}',
            '${data.U_CodigoCampania}',
            '${data.U_Saldo}',
            '${data.U_ValorSaldo}',
            '${data.U_4MOTIVOCANCELADOCABEZERA}',
            '${data.U_DOCENTRY}',
            '${idsucursal}',
            '${data.Fex_documento}',
            '${data.Fex_tipodocumento}',
            '${data.codeConsolidador}',
            '${data.cndpagoname}',
            '${data.cuota}',
            '${data.TipoEnvioDoc}',
            '${data.EnvioEvidencia}',
            ''
            `+sql2+`);`;

        let idxx: any = await this.executeRaw(sqlinsert);
        console.log("sqlinsert ", sqlinsert);
        let sqlx = `UPDATE documentos SET correlativo = ${(idxx + 1)}, DocNum = ${idxx} WHERE id= ${idxx};`;
        await this.executeRaw(sqlx);
    }

    public async insertLocal(data: any, idx: number, codigox: any, reserve = 0) {
        console.log("CONSOLA: INICIA insertLocal 568");

        let tipoDocumento = codigox;
        let fecha = moment().format('YYYY-MM-DD');
        let hora = moment().format('h:mm:ss');

        if(!data.cloneaux){
            data.cloneaux = '';
        }

        if (!idx || idx == undefined) {
            idx = Number(localStorage.getItem("idSession"));
        }

        if (data.idSucursal == undefined || data.idSucursal == "undefined") {
            data.idSucursal = '0';
        }

        if (data.idSucursalMobile == undefined || data.idSucursalMobile == "undefined") {
            data.idSucursalMobile = data.idSucursal;
        }

        let sql = `SELECT * FROM clientessucursales where id= ${data.idSucursalMobile} AND CardCode = '${data.CardCode}'  ; `;
        console.log(" sql ", sql);
        let sucursalInfo: any = await this.queryAll(sql);
        console.log('sucursalInfo ', sucursalInfo);
        if (sucursalInfo.length == 0) {

            let sql = `SELECT * FROM clientessucursales where LineNum = ${data.idSucursalMobile} AND CardCode = '${data.CardCode}'  ; `;
            console.log(" sql ", sql);
            let sucursalInfoLineNum: any = await this.queryAll(sql);

            if (sucursalInfoLineNum.length > 0) {
                sucursalInfo = sucursalInfoLineNum;
                data.idSucursal = sucursalInfoLineNum[0].LineNum;
            } else {

                let sql = `SELECT * FROM clientessucursales where LineNum = '0' AND CardCode = '${data.CardCode}'  ; `;
                console.log(" sql ", sql);
                let sucursalInfoLineNum2: any = await this.queryAll(sql);
                console.log('sucursalInfoLineNum 3 ', sucursalInfoLineNum2);

                if (sucursalInfoLineNum2.length > 0) {
                    sucursalInfo = sucursalInfoLineNum2;
                    data.idSucursal = sucursalInfoLineNum2[0].id;
                } else {
                    sucursalInfo =[{
                                    AddresName: "",
                                    AdresType: "",
                                    CardCode: data.CardCode,
                                    CreditLimit: "0",
                                    DateUpdate: "",
                                    FederalTaxId: "",
                                    LineNum: 0,
                                    State: "",
                                    Status: "1",
                                    Street: "Sin Datos",
                                    Tax: "IVA",
                                    TaxCode: "IVA",
                                    User: "1",
                                    campousudescript: "",
                                    export: 1,
                                    id: 0,
                                    idDocumento: 0,
                                    idUser: "",
                                    u_lat: "",
                                    u_lon: "",
                                    u_territorio: "",
                                    u_vendedor: "",
                                    u_zona: ""

                    }];
                    data.idSucursal =0;
                }
            }
        } else {
            data.idSucursal = sucursalInfo[0].LineNum;
        }

        console.log("CONSOLA: DATOS ANTES DE INSERTAR CABECERA 651",data);

        GlobalConstants.CabeceraDoc = [];
        GlobalConstants.CabeceraDoc.push({
            id: '',
            cod: tipoDocumento,
            DocEntry: data.DocEntry,
            DocNum: data.DocNum,
            DocType: data.DocType,
            canceled: data.canceled,
            Printed: data.Printed,
            DocStatus: data.DocStatus,
            DocDate: fecha,
            DocDueDate: data.DocDueDate,
            CardCode: data.CardCode,
            CardName: data.CardName,
            NumAtCard: data.NumAtCard,
            DiscPrcnt: data.DiscPrcnt,
            DiscSum: data.DiscSum,
            DocCur: data.DocCur,
            DocRate: data.DocRate,
            DocTotal: data.DocTotal,
            PaidToDate: data.PaidToDate,
            Ref1: data.Ref1,
            Ref2: data.Ref2,
            Comments: data.Comments,
            JrnlMemo: data.JrnlMemo,
            GroupNum: data.GroupNum,
            SlpCode: data.SlpCode,
            Series: data.Series,
            TaxDate: fecha,
            LicTradNum: data.LicTradNum,
            Address: data.Address,
            UserSign: data.UserSign,
            CreateDate: fecha,
            UserSign2: data.UserSign2,
            UpdateDate: data.UpdateDate,
            U_4MOTIVOCANCELADO: data.U_4MOTIVOCANCELADO,
            U_4NIT: data.U_4NIT,
            U_4RAZON_SOCIAL: data.U_4RAZON_SOCIAL,
            U_LATITUD:  GlobalConstants.latitud,// data.U_LATITUD,
            U_LONGITUD: GlobalConstants.Longitud,//; data.U_LONGITUD,
            U_4SUBTOTAL: data.U_4SUBTOTA,
            U_4DOCUMENTOORIGEN: data.U_4DOCUMENTOORIGEN,
            U_4MIGRADOCONCEPTO: data.U_4MIGRADOCONCEPTO,
            U_4MIGRADO: data.U_4MIGRADO,
            PriceListNum: data.PriceListNum,
            estadosend: data.estadosend,
            fecharegistro: fecha + " " + hora,
            fechaupdate: fecha,
            fechasend: (data.cloneaux == "") ? data.fechasend  : data.DocDueDate,
            key: 2,
            idUser: idx,
            estado: data.estado,
            gestion: data.gestion,
            mes: data.mes,
            correlativo: data.correlativo,
            rowNum: data.LineNum,
            descuento: (data.descuento == "") ? 0 : data.descuento,
            tipocambio: (data.tipocambio == "") ? 0 : data.tipocambio,
            currency: data.currency,
            clone: data.clone,
            cloneaux: (data.cloneaux == "") ? 0 : data.cloneaux,
            tipodescuento: (data.tipodescuento == "") ? 0 : data.tipodescuento,
            federalTaxId: '',
            cardNameAux: data.cardNameAux,
            PayTermsGrpCode: data.PayTermsGrpCode,
            tipotransaccion: data.tipotransaccion,
            tipoestado: data.tipoestado,
            comentario: (data.comentario == "undefined") ? '' : data.comentario,
            cuenta: '',
            origen: 'inner',
            ReserveInvoice: 'N',
            saldo: 0,
            Pendiente: 0,
            U_LB_NumeroFactura: 'null',
            U_LB_NumeroAutorizac: 'null',
            U_LB_FechaLimiteEmis: 'null',
            U_LB_CodigoControl: data.codigoControl,
            U_LB_EstadoFactura: 'null',
            U_LB_RazonSocial: data.U_4RAZON_SOCIAL,
            U_LB_TipoFactura: 'null',
            Reserve: reserve,
            centrocosto: '',
            unidadnegocio: '',
            reimpresiones: '0',
            codexternal: '',
            grupoproductoscode: data.grupoproductoscode,
            U_CodigoCampania: '0',
            U_Saldo: '0',
            U_ValorSaldo: '0',
            U_4MOTIVOCANCELADOCABEZERA: '',
            U_DOCENTRY: '',
            idSucursalMobile: data.idSucursal,
            Fex_documento: '',
            Fex_tipodocumento: '0',
            codeConsolidador: data.codeConsolidador,
            cndpagoname: data.cndpagoname,
            cuota: 0,
            DocumentTotalPay: 0,
            U_MontoCampania:0,
            TipoEnvioDoc:'',
            EnvioEvidencia:0,
            camposusuario:''
        });

        console.log("CONSOLA: DATOS GUARDADOS DE CABECERA 651",JSON.stringify(GlobalConstants.CabeceraDoc));

        return tipoDocumento;
    }

    updatePromocionesDocReset(cod) {
        return new Promise(async (resolve, reject) => {

            let sql = `UPDATE documentos SET U_CodigoCampania = '0' , U_Saldo = '0', U_ValorSaldo= '0' WHERE cod = '${cod}'`;
            console.log("sql update", sql);
            let sqlx: any = await this.queryAll(sql);
            await this.executeRaw(sqlx);

            resolve(true);
        });
    };

    public async updateEnvioEvidencia(cod:any, estado: any) {

            let sql = `UPDATE documentos SET EnvioEvidencia = '${estado}'  WHERE DocNum = '${cod}'`;
            console.log("sql update", sql);
            return await this.executeSQL(sql);
    };

    public async consultaEnvioEvidencia(cod) {

        let sql = `select * from documentos WHERE cod = '${cod}'`;
        console.log(sql);
        return await this.queryAll(sql);

    };

    public async consultadocimportados(cod) {
        let sql = `select * from documentos`;
        console.log(sql);
        return await this.queryAll(sql);

    }
    
    public async existeDocumento(cod: any) {
        //let sql = `SELECT COUNT(*) AS exits FROM documentos WHERE DocEntry = '${cod}' AND clone = '0' AND idUser=${localStorage.getItem("idSession")}`;
        let sql = `SELECT COUNT(*) AS exits FROM documentos WHERE DocEntry = '${cod}' AND clone = '0'`;
        return await this.queryAll(sql);
    }

    public async deleteDocumento(cod: any) {
        let sqlSelect = `DELETE FROM  documentos WHERE cod = '${cod}'`;
        console.log("-----> Eliminar sqlSelect ", sqlSelect)
        await this.exe(sqlSelect);
    }

    public async Documentosel(cod: any) {
        let sql = `SELECT *  FROM documentos WHERE cod = '${cod}'`;
        console.log(sql);
        return await this.queryAll(sql);
    }

    public async numeraldoc(tipoDoc: string, idUser: any, numnext: any) {
        let numx = "00000".slice(0, -parseInt(numnext.toString().length));
        let numxuser = "0000".slice(0, -parseInt(idUser.toString().length));
        let codigo = (tipoDoc + "1" + (numxuser + "" + idUser) + "" + (numx + "" + numnext));

        console.log("cosigo generado es :", codigo);

        let sql = `SELECT count(*) as Cantidad FROM documentos where DocType = '${tipoDoc}' and cod = '${codigo}'`;
        console.log(sql);
        let aux: any = await this.queryAll(sql);
        console.log("la cantidad conseguida es ", aux[0].Cantidad);
        if (aux[0].Cantidad > 0) {
            let cont = 1
            let i = numnext;
            for (i; i < numnext + cont; i++) {
                let numx = "00000".slice(0, -parseInt(i.toString().length));
                let numxuser = "0000".slice(0, -parseInt(idUser.toString().length));
                let codigo = (tipoDoc + "1" + (numxuser + "" + idUser) + "" + (numx + "" + i));
                console.log("1 cosigo generado es :", codigo);

                let sql = `SELECT count(*) as Cantidad FROM documentos where DocType = '${tipoDoc}' and cod = '${codigo}'`;
                console.log(sql);
                let aux1: any = await this.queryAll(sql);
                if (aux1[0].Cantidad > 0) {
                    console.log("numero ya asignado se intenta con: ", i + 1);
                    cont++;
                } else {

                    console.log("1 el nuevo numero es: ", i);
                    numnext = i;
                    break;
                }
            }
        } else {
            console.log("el nuevo numero es: ", numnext);
        }
        console.log("retorna: ", numnext);
        return numnext;
    }

    insertAllLocales = async (data: any, id: any, contador = 0) => {
        console.log("datos sincronizados", data);
        /*if (contador == 0) {
            // let sql = 'DELETE FROM documentos;';
            await this.clearLocales();
            // await this.exe(sql);
            let sqlSelectH = `DELETE FROM  documentopago WHERE cod  IN ( SELECT documentoPagoId FROM pagos WHERE otpp = 1 )`;
            await this.exe(sqlSelectH);
            let sqlSelect = `DELETE FROM  pagos where otpp = 1`;
            // console.log("-----> Eliminar sqlSelect ", sqlSelect)
            await this.exe(sqlSelect);

        }*/

        let datax = JSON.parse(data.data);
        let auxlocal = new Databaseconf();
        let aux2: any = await auxlocal.loadenddblocal('doc');
        console.log("datos retornadossss ");
        let datoslocales;
        if (aux2 != 0) {
            let aux = await auxlocal.limpiardblocal('doc');
            console.log("tabla limpia");
            console.log(aux);
            aux2 = '[' + aux2 + ']';
            datoslocales = JSON.parse(aux2);
            console.log("datos locales", datoslocales);
            let accion = 0;
            for (let da of datoslocales) {
                let existe = 0;
                for (let d of datax.respuesta) {
                    console.log("datos a comparar");
                    console.log(d.header[0].cod);
                    console.log(da.header.cod);

                    if (d.header[0].cod == da.header.cod) {
                        existe = 1;
                        console.log("son iguales");
                    }
                }
                if (existe == 0) {
                    console.log("el documento no existe en la sincronizacion");
                    console.log(da);
                    let datoaguardar = JSON.stringify(da)
                    console.log("datoaguardar", datoaguardar);
                    let aux1 = await auxlocal.writedblocal(datoaguardar, 'doc');
                    console.log("datos retornados ");

                    datax.respuesta.push({ cantidadDetalle: 0, detalles: da.detalles, header: JSON.parse(da.cadenaCabezera), pagos: da.pagos, usuariodataid: da.usuariodataid });
                    console.log("datos agregados", datax.respuesta);
                }
            }
        }

        let sql = 'INSERT INTO documentos VALUES ';
        let fecha;
        let idx = id;
        let hora = moment().format('h:mm:ss'); // ojo
        console.log("1 datax.respuesta ", datax.respuesta);

        for (let d of datax.respuesta) {
            console.log("d.header[0] ", d.header)
            let data: any = d.header;

            let sql1 = "select count(*) as CANTIDAD from  documentos where cod = '" + data.cod + "'";
            console.log("la consulta es", sql1);
            let cantidad = await this.queryAll(sql1);
            console.log("la cantidad es", cantidad[0].CANTIDAD);

            if (cantidad[0].CANTIDAD == 0) {
                fecha = d.header.fechasend;
                if (d.header.idDocPedido == "DOP1005111376") {
                    console.log("---> HEADER DOP1005111376 ")
                    console.table(d);
                }
                if (d.pagos) {
                    if (d.pagos.length > 0) {
                        await this.insertAllLocalPagoFactura(d.pagos[0])
                    }
                }
                let reserve = 0;
                // }
                // d.origen = 'inner';
                // d.clone = '0';
                // d.cod = d.DocNum;
                // console.log("d.header[0].estado ", d.header[0].estado)
                // console.log("d.header[0].estadosend ", d.header[0].estadosend)
                // if (d.header[0].estado == 4 || d.header[0].estado == 1 || d.header[0].estado == 3) {
                if (d.header.estado == 4 || d.header.estado == 3) {
                    data.estado = 3;
                    data.estadosend = 3;
                } else {
                    if (d.header.estado != 1) {
                        data.estado = 6;
                        data.estadosend = 6;
                        data.tipoestado = "anulado";
                    }
                }

                let idSucursalMobile;
                if(data.idSucursalMobile.length){
                    idSucursalMobile = data.idSucursalMobile[0].LineNum;
                }else{
                    idSucursalMobile = '';
                }

                let campos = new Camposusuario();
                let session = await campos.consultasesion();
                let sql2 = await campos.camposusuariosinc(data,1,session);
                console.log("RETORNO 4",sql2);

                sql += `(NULL, '${data.cod}', '${data.DocEntry}', '${data.DocNum}', '${data.DocType}', '${data.canceled}', '${data.Printed}', 
                    '${data.DocStatus}', '${fecha}', '${fecha}', '${data.CardCode}', '${data.CardName}', '${data.NumAtCard}', '${data.DiscPrcnt}', 
                    '${data.DiscSum}', '${data.DocCur}', '${data.DocRate}', ${data.DocTotal}, '${Number(data.PaidToDate).toFixed(2)}', '${data.Ref1}', '${data.Ref2}', '${data.Comments}', '${data.JrnlMemo}', 
                    '${data.GroupNum}', '${data.SlpCode}', '${data.Series}', '${fecha}', '${data.LicTradNum}', '${data.Address}', '${data.UserSign}', '${fecha}', 
                    '${data.UserSign2}', '${data.UpdateDate}', '${data.U_4MOTIVOCANCELADO}', '${data.U_4NIT}', '${data.U_4RAZON_SOCIAL}', '${data.U_LATITUD}', '${data.U_LONGITUD}', '${data.U_4SUBTOTAL}',
                    '${data.U_4DOCUMENTOORIGEN}', '${data.U_4MIGRADOCONCEPTO}', '${data.U_4MIGRADO}', '${data.PriceListNum}', '${data.estadosend}', '${fecha} ${hora}',
                    '${fecha}', '${data.fechasend}', '2', '${idx}', '${data.estado}', '${data.gestion}', '${data.mes}', '${data.correlativo}', '${data.rowNum}',
                    ${data.descuento},${data.tipocambio},'${data.currency}','${data.clone}',${data.tipodescuento},'','','${data.PayTermsGrpCode}','${data.tipotransaccion}','${data.tipoestado}','','','inner'
                    ,'N',0,0,'${data.U_LB_NumeroFactura}','null','null','${data.codigoControl}','null','${data.U_4RAZON_SOCIAL}','null', ${reserve},'','','0','','${data.grupoproductoscode}',
                    '0','0','0', '', '' , '${idSucursalMobile}','','0','','${data.cndpagoname}', 0,'',0,''`+sql2+`),`;

                // console.log("sql ", sql)
                let id = d.header.cod;

                console.log(d.header);

                if (contador == 0) {
                    // let sql = 'DELETE FROM documentos;';
                    // await this.exe(sql);
                    let sqlSelect = `DELETE FROM  detalle WHERE idDocumento = '${id}'`;
                    // console.log("-----> Eliminar sqlSelect ", sqlSelect)
                    await this.exe(sqlSelect);
                }
                // if (id == "DOP1005111376") {
                //     console.log("d.detalles ", d.detalles);
                // }

                for (let detalle of d.detalles) {
                    //params 
                    console.log("detalle ", detalle);

                    let dataI: any = detalle;
                    console.log("Number(dataI.Price) ", Number(dataI.Price));

                    // let unidad = '';
                    // if (dataI.combos == 0 || dataI.combos == "0") unidad = dataI.unidadID;
                    // let xd: number;

                    // if (!dataI.XMPORCENTAJECABEZERA) {
                    //     dataI.XMPORCENTAJECABEZERA = 0;
                    // }
                    // if (!dataI.XMPROMOCIONCABEZERA) {
                    //     dataI.XMPROMOCIONCABEZERA = 0;
                    // }
                    // if (!dataI.BaseQty) {
                    //     dataI.BaseQty = 0;
                    // "[{"id":45,"DocEntry":"","DocNum":"","LineNum":"0","BaseType":"","BaseEntry":"","BaseLine":"",
                    // "LineStatus":"","ItemCode":"10CAL0005","Dscription":"Ron Caldas Carta de Oro 8 Años 750mlx12u",
                    // "Quantity":20,"OpenQty":"","Price":"54.60","Currency":"undefined","DiscPrcnt":0,"LineTotal":1182.1,
                    // "WhsCode":"LP-VIG","CodeBars":"","PriceAfVAT":"","TaxCode":"","U_4DESCUENTO":54.6,"XMPORCENTAJE":0,
                    // "U_4LOTE":"","GrossBase":"","idDocumento":"DOF1005011105","fechaAdd":"2022-4-28","unidadid":"UNI","tc":"",
                    // "idCabecera":"DOF1005011105","idProductoPrecio":"","ProductoPrecio":"","LineTotalPay":1182.1,
                    // "DiscTotalPrcnt":0,"DiscTotalMonetary":0,"icett":"1092.00","icete":"2.722500","icetp":"10.000000",
                    // "ICEt":"A","ICEe":"54.45","ICEp":"90.25","bonificacion":0,"combos":"0","PriceAfterVAT":"","Rate":"",
                    // "TaxTotal":"","User":"","Status":"","DateUpdate":"","Entregado":"","Serie":"","BaseId":1,"IdBonfAut":0,
                    // "GroupName":"Ron","codeBonificacionUse":"0","XMPORCENTAJECABEZERA":5,"XMPROMOCIONCABEZERA":0,"BaseQty":1,
                    // "grupoproductodocificacion":0,"totalPagar":1182.1,"lotes":[],"series":[]}]"
                    // // }
                    // let fecha = this.timeStamp();
                    //  console.log("guardar Number(detalle.LineTotalPay).toFixed(2) ", Number(dataI.LineTotalPay).toFixed(2));
                    let sqlD = `INSERT INTO detalle VALUES (NULL, '${dataI.DocEntry}', '${dataI.DocNum}', '${dataI.LineNum}', '${dataI.BaseType}', '${dataI.BaseEntry}', 
                                '${dataI.BaseLine}', '${dataI.LineStatus}', '${dataI.ItemCode}', '${dataI.Dscription}', '${dataI.Quantity}', '${dataI.OpenQty}', '${Number(dataI.Price)}', '${dataI.Currency}',
                                ${dataI.DiscPrcnt}, ${dataI.LineTotal}, '${dataI.WhsCode}', 
                                '${dataI.CodeBars}', '${dataI.PriceAfVAT}', '${dataI.TaxCode}', '${dataI.U_4DESCUENTO}','${dataI.XMPORCENTAJE}', '${dataI.U_4LOTE}', '${dataI.GrossBase}', '${id}',
                                '${dataI.fechaAdd}', '${dataI.unidadid}', '${dataI.tc}', '${dataI.idDocumento}', '${dataI.idProductoPrecio}', '${dataI.ProductoPrecio}', ${Number(dataI.LineTotalPay).toFixed(2)}, ${dataI.DiscTotalPrcnt}, 
                                ${dataI.DiscTotalMonetary}, '${dataI.icett}', '${dataI.icete}', '${dataI.icetp}', '${dataI.ICEt}', '${dataI.ICEe}', '${dataI.ICEp}', 
                                ${dataI.bonificacion},'${dataI.combos}','','','','','','','','','${dataI.BaseId}','${dataI.IdBonfAut}', '${dataI.GroupName}', '${dataI.codeMid}', ${dataI.XMPORCENTAJECABEZERA}, ${dataI.XMPROMOCIONCABEZERA},'${dataI.BaseQty}', 0,'','');`;
                     console.log("sqlD dataI ", sqlD);

                    let r = await this.executeRaw(sqlD);
                }
            }
        }
        if (Number(datax.respuesta.length) > 0) {
            let sqlx = sql.slice(0, -1);
            let f = sqlx + ';';
            try {
                await this.executeSQL(f);
                console.log("SI EJECUTADO");
            } catch (error) {
                console.log("NO EJECUTADO ", error);
            }
            return true;
        } else {

            return true;
        }
    }

    public clearLocales() {
        console.log("clearLocales")
        return new Promise((resolve, reject) => {
            let sql = `DELETE FROM documentos WHERE origen = 'inner'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    async deletePagosFactura(data) {
        let sqlSelect = `DELETE FROM  pagos WHERE documentoPagoId = '${data.documentoPagoId}'`;
        // console.log("-----> Eliminar sqlSelect ", sqlSelect)
        await this.exe(sqlSelect);
        let sqlSelectH = `DELETE FROM  documentopago WHERE cod = '${data.documentoPagoId}'`;
        // console.log("-----> Eliminar sqlSelect ", sqlSelect)
        await this.exe(sqlSelectH);
    }

    insertAllLocalPagoFactura = async (data: any) => {
        console.log("insertAllLocalPagoFactura() ")
        await this.deletePagosFactura(data);
        console.log("pago : 1 FACTURA insert ", data);
        // if (data.documentoId == "DFA1002511152") {
        //     console.log(" insert pago DFA1002511152 ", data)
        // }
        data.estadoCabezera = 0;
        // if (Number(data.estado) > 0) {
        data.estado = 1;
        // }
        let sqlHeader = `INSERT INTO documentopago VALUES(NULL,'${data.recibo}','${data.fecha}', '${data.hora}','${"FACTURA_" + data.fecha}','factura','${data.estadoCabezera}');`;
        console.log("sql master ", sqlHeader);
        try {
            await this.executeRaw(sqlHeader);
            console.log("PAGO INSERTADO ", data.recibo);

        } catch (error) {
            console.log("PAGO NO INSERTADO ", data.recibo);
        }

        let sqlPago = `
                INSERT INTO pagos VALUES(NULL,
                    '${data.documentoId}', 
                    '${data.clienteId}', 
                    '${data.formaPago}',
                        '${data.tipoCambioDolar}', 
                        '${data.moneda}', 
                        '${data.monto}',

                        '${data.numCheque}', 
                        '${data.numComprobante}',
                        '${data.numTarjeta}',
                        '${data.numAhorro}', 
                        '${data.numAutorizacion}',
                        '${data.bancoCode}', 
                        '${data.usuario}',
                        '${data.fecha}', 
                        '${data.hora}',
                        '${data.cambio}',
                        '${data.monedaDolar}',
                        '${data.monedaLocal}', '${data.estado}', '${data.tipo}','${data.documentoPagoId}',
                        'factura',1 ,'${data.centro}','${data.baucher}', '${data.ncuota}', '${data.checkdate}', 
                        '${data.transferencedate}',  '${data.anulado}', '${localStorage.getItem("idSession")}', '${data.correlativo ? data.correlativo : 0}');
                        `;
        console.log("sql sqlPago ", sqlPago);
        try {
            await this.executeRaw(sqlPago);
            console.log("PAGO DETALLE INSERTADO ", data.recibo);

        } catch (error) {
            console.log("PAGO DETALLE NO INSERTADO ", error);
        }

        // console.table(data.facturas)
        // for (let dataF of data.facturas) {
        //     let sqlpF = ` INSERT INTO facturasPagos VALUES (NULL,'${dataF.clienteId}','${dataF.cod}','${dataF.nroFactura}','${dataF.docentry}', '${dataF.docnum}',${Number(dataF.pagarx)},'${dataF.recibo}', '${dataF.CardName}','${dataF.saldo}','${dataF.nroFactura}','${dataF.DocTotal}');`;
        //     console.log("sqlpF ", sqlpF)
        //     await this.executeRaw(sqlpF);
        // }

    }

    // otpp: 1 facturas del movil, 2 facturas desde sap, 3 es pagos anticipaos
    insertAllLocalesPagos = async (data: any, id: any, contador = 0) => {
        /*if (contador == 0) {
            let sqlHTEST = 'SELECT * FROM pagos WHERE otpp <> 1';
            console.log("a eliminar ", await this.queryAll(sqlHTEST));
            let sqlSelectH = `DELETE FROM  documentopago WHERE cod  IN ( SELECT documentoPagoId FROM pagos WHERE otpp <> 1 )`;
            await this.exe(sqlSelectH);
            let sqlP = 'DELETE FROM pagos WHERE otpp <> 1;';
            await this.exe(sqlP);
            let sqlPF = 'DELETE FROM facturasPagos';
            await this.exe(sqlPF);
        }*/

        let datax = JSON.parse(data.data);
        console.log("datax ", datax);

        let auxlocal = new Databaseconf();
        let aux2: any = await auxlocal.loadenddblocal('pagos');
        console.log("datos retornadossss ");
        let datoslocales;
        let historialpagos = new Pagos;

        await historialpagos.insertAllHistorial(datax.respuesta);

        /*
        if (aux2 != 0) {

            let aux = await auxlocal.limpiardblocal('pagos');
            console.log("tabla limpia");
            console.log(aux);

            aux2 = '[' + aux2 + ']';
            datoslocales = JSON.parse(aux2);
            console.log("datos locales", datoslocales);


            let accion = 0;
            for (let da of datoslocales) {
                let existe = 0;
                for (let d of datax.respuesta) {
                    console.log("datos a comparar");
                    console.log(d.pagos[0].documentoPagoId);
                    console.log(da.documentoPagoId);
                    if (d.pagos[0].documentoPagoId == da.documentoPagoId) {
                        existe = 1;
                    }
                }
                if (existe == 0) {
                    console.log("el pago no existe en la sincronizacion");
                    let datoaguardar = JSON.stringify(da)
                    console.log("datoaguardar", datoaguardar);
                    let aux1 = await auxlocal.writedblocal(datoaguardar, 'pagos');
                    console.log("datos retornados ");
                    datax.respuesta.push({ pagos: JSON.parse(da.cadenaPago), facturas: JSON.parse(da.cadenaFacturas), usuario: da.idUser.toString(), sucursal: da.sucursal });
                    console.log("datos agregados", datax.respuesta);
                }
            }
        }

        for (let d of datax.respuesta) {
            let sql = "SELECT count(*) AS CANTIDAD FROM pagos WHERE documentoPagoId = '" + d.pagos[0].documentoPagoId + "';";
            console.log("la consulta es", sql);
            let cantidad = await this.queryAll(sql);
            console.log("la cantidad es", cantidad[0].CANTIDAD);

            if (cantidad[0].CANTIDAD == 0) {

                console.log("PAGO EACH  insert ", d);
                // if (d.pagos.otpp == 1) {//FACTURA MOBILE
                //     console.log("d : 1 FACTURA insert ", d);

                // }
                if (Number(d.pagos[0].estado) == 0) {
                    d.pagos[0].estado = 1;
                }

                if (Number(d.pagos[0].estado) == 6) {

                    d.pagos[0].anulado = 1;
                }
                if (d.pagos[0].otpp == 2) {//FACTURA PENDIENTE DE PAGO
                    console.log("d : 2 FACTURAS insert ", d);
                    d.pagos[0].estadoCabezera = 0;
                    //sql master  INSERT INTO documentopago VALUES(NULL,'1003100014','2022-02-14', '16:26:28','FACTURA_2022-02-14','factura',0);
                    //let sqlHeader = `INSERT INTO documentopago VALUES(NULL,'${d.pagos.recibo}','${d.pagos.fecha}', '${d.pagos.hora}','${"FACTURA_" + d.pagos.fecha}','factura','${d.pagos.estadoCabezera}');`;
                    if (d.pagos[0].documentoPagoId == '101000200002') {
                        console.log("d.pagos[0] ", d.pagos[0]);

                    }
                    let sqlHeader = `INSERT INTO documentopago VALUES(NULL,'${d.pagos[0].documentoPagoId}','${d.pagos[0].fecha}', '${d.pagos[0].hora}','${"FACTURA_" + d.pagos[0].fecha}','factura','${d.pagos[0].estadoCabezera}');`;
                    console.log("sql master ", sqlHeader);
                    await this.executeRaw(sqlHeader);

                    let sqlPago = `
                        INSERT INTO pagos VALUES(NULL,
                            '0', 
                            '${d.pagos[0].clienteId}', 
                            '${d.pagos[0].formaPago}',
                                '${d.pagos[0].tipoCambioDolar}', 
                                '${d.pagos[0].moneda}', 
                                '${d.pagos[0].monto}',
            
                                '${d.pagos[0].numCheque}', 
                                '${d.pagos[0].numComprobante}',
                            '${d.pagos[0].numTarjeta}',
                                '${d.pagos[0].numAhorro}', 
                                '${d.pagos[0].numAutorizacion}',
                                '${d.pagos[0].bancoCode}', 
                                '${d.usuario}',
                                '${d.pagos[0].fecha}', 
                                '${d.pagos[0].hora}',
                                '${d.pagos[0].cambio}',
                                '${d.pagos[0].monedaDolar}',
                                '${d.pagos[0].monedaLocal}', '${d.pagos[0].estado}', '${d.pagos[0].tipo}','${d.pagos[0].documentoPagoId}',
                                'FACTURAS',2 ,'${d.pagos[0].centro}','${d.pagos[0].baucher}', ${d.pagos[0].ncuota}, '${d.pagos[0].checkdate}', 
                                '${d.pagos[0].transferencedate}',  '${d.pagos[0].anulado}', '${localStorage.getItem("idSession")}','${d.pagos[0].CreditCard}','${d.pagos[0].correlativo ? d.pagos[0].correlativo : 0}'); 
                                `;
                    console.log("sql sqlPago ", sqlPago);
                    await this.executeRaw(sqlPago);
                    console.table("d.pagos.facturas ", d.facturas)
                    for (let dataF of d.facturas) {
                        let sqlpF = ` INSERT INTO facturasPagos VALUES (NULL,'${dataF.clienteId}','${dataF.cod}','${dataF.coddoc}','${dataF.docentry}', '${dataF.docnum}',${Number(dataF.pagarx)},'${dataF.recibo}', '${dataF.CardName}','${dataF.saldo}','${dataF.nroFactura}','${dataF.DocTotal}');`;
                        console.log("sqlpF ", sqlpF)
                        await this.executeRaw(sqlpF);
                    }


                }
                if (d.pagos[0].otpp == 3) {//ANTICIPO
                    console.log("d : 3 CUENTA insert ", d);
                    d.pagos[0].estadoCabezera = 0; //ENVIADO
                    //FALTA ESTADO DOCUMENTOCABEZERA 0 es anulado
                    // let texto: string = this.tipodocument.toUpperCase() '_' Tiempo.fecha();
                    let sqlHeader = `INSERT INTO documentopago VALUES(NULL,'${d.pagos[0].documentoPagoId}','${d.pagos[0].fecha}', '${d.pagos[0].hora}','${"CUENTA_" + d.pagos[0].fecha}','cuenta','${d.pagos[0].estadoCabezera}');`;
                    console.log("sql master ", sqlHeader);
                    await this.executeRaw(sqlHeader);
                    d.pagos[0].estado = 1; //ENVIADO
                    //  d.pagos[0].anulado = 0; //NO ANULADO

                    let sqlPago = `
                            INSERT INTO pagos VALUES(NULL,
                            '0', 
                            '${d.pagos[0].clienteId}', 
                            '${d.pagos[0].formaPago}',
                                '${d.pagos[0].tipoCambioDolar}', 
                                '${d.pagos[0].moneda}', 
                                '${d.pagos[0].monto}',
            
                                '${d.pagos[0].numCheque}', 
                                '${d.pagos[0].numComprobante}',
                                '${d.pagos[0].numTarjeta}',
                                '${d.pagos[0].numAhorro}', 
                                '${d.pagos[0].numAutorizacion}',
                                '${d.pagos[0].bancoCode}', 
                                '${d.pagos[0].usuario}',
                                '${d.pagos[0].fecha}', 
                                '${d.pagos[0].hora}',
                                '${d.pagos[0].cambio}',
                                '${d.pagos[0].monedaDolar}',
                                '${d.pagos[0].monedaLocal}', '${d.pagos[0].estado}', '${d.pagos[0].tipo}','${d.pagos[0].documentoPagoId}',
                                'cuenta',3,'${d.pagos[0].centro}','${d.pagos[0].baucher}', ${d.pagos[0].ncuota}, '${d.pagos[0].checkdate}', 
                                '${d.pagos[0].transferencedate}',  '${d.pagos[0].anulado}', '${localStorage.getItem("idSession")}','${d.pagos[0].CreditCard}','${d.pagos[0].correlativo ? d.pagos[0].correlativo : 0}');
                                `;

                    console.log("sql sqlPago ", sqlPago);
                    await this.executeRaw(sqlPago);

                }
            }

            // sql master  INSERT INTO documentopago VALUES(NULL,'1003100012','2022-02-14', '10:20:31','CUENTA_2022-02-14','cuenta',0);


        }

        */
    }

    public async insertAll(data: any, id: any, contador = 0) {
        if (contador == 0) {
            // let sql = 'DELETE FROM documentos;';
            await this.clear();
            // await this.exe(sql);
        }

        let campos = new Camposusuario();
        let session = await campos.consultasesion();
        let sql2 = await campos.camposusuariosinc(data,1,session);
        console.log("RETORNO 3",sql2);

        let datax = JSON.parse(data.data);
        console.log(datax);

        let sql = 'INSERT INTO documentos VALUES ';

        let idx = Number(localStorage.getItem("idS ession"));

        for (let d of datax.respuesta) {
            //            d.DocNum = "DOP" d.DocNum;
            console.log("d.grupoclientedocificacion ", d.grupoclientedocificacion);
            d.origen = 'outer';
            d.clone = '0';
            d.cod = d.DocNum;
            // if (Number(d.Saldo) > 0) {
            //     console.table(d)
            // }

            d.tipodescuento = 0;
            if (d.descuentos) {
                if (Number(d.descuento) > 0) {
                    d.tipodescuento = Number(d.descuento);
                    d.descuento = Number(Number(d.descuento / 100) * Number(d.GTotal)).toFixed(4);
                } else {
                    d.descuento = 0;
                }
            }
            let idSucursalMobile = 0;
            if(d.idSucursalMobile == undefined){
                idSucursalMobile = 0;
            }else{
                idSucursalMobile = d.idSucursalMobile
            }

            let reimpresiones = 0;
            if(d.reimpresiones == undefined){
                reimpresiones = 0;
            }else{
                reimpresiones = d.reimpresiones
            }
            
            let unidadnegocio = 0;
            if(d.unidadnegocio == undefined){
                unidadnegocio = 0;
            }else{
                unidadnegocio = d.unidadnegocio
            }

            let Saldo = 0;
            if(d.Saldo == undefined){
                Saldo = 0;
            }else{
                Saldo = d.Saldo
            }
            let vendedor= '';
            if(d.vendedor == undefined){
                vendedor = '';
            }else{
                vendedor = d.vendedor
            }

            let reserve;
            if(d.ReserveInvoice == 'N'){
                reserve = false;
            }else{
                reserve = true;
            }

            //QUEMAR IR USUARIO AND idUser=${localStorage.getItem("idSession")}
            let fecha = moment().format('YYYY-MM-DD h:mm:ss');
            sql += ` (NULL,'${d.cod}', '${d.DocEntry}', '${d.DocNum}', '${d.DocType}', '', '', '',
                  '${d.DocDate}', '${d.DocDueDate}','${d.CardCode}', '${d.CardName}', '', '', '', '', '', '${d.DocTotal}',
                  '${d.PaidtoDate}','','','','','','','0','0','', '','','${fecha}','','${d.DateUpdate}',
                  '','','','${d.U_4NIT}','${d.U_4RAZON_SOCIAL}','','','','','${d.PriceListNum}','1','${fecha}','${d.DateUpdate}','','','','','','','','',
                  '${d.descuento}','','${d.DocCurrency}','${d.clone}','${d.tipodescuento}','','','','','','','','${d.origen}','${d.ReserveInvoice}', ${Saldo},
                  ${d.Pendiente},'','','','','','','','${reserve}','${d.centrocosto}','${unidadnegocio}','${reimpresiones}','${d.U_xMOB_Codigo}','${d.grupoproductodocificacion}' 
                  ,'0','0','0', '', '${d.DocEntry}','${idSucursalMobile}','','','','${d.cndpagoname}', 0,'',0,'${d.vendedor}'`+sql2+`),`;

        }
        console.log("sqlx  ", sql);

        if (datax.respuesta.length > 0) {
            let sqlx = sql.slice(0, -1);
            let f = sqlx + ';';
            return await this.executeSQL(f);
        } else {
            return true;
        }
    }

    async deletePagoscuota(CardCode) {
        console.log("deletePagoscuota()")
        let sqld = `delete from documentos where  CardCode ='${CardCode}' and DocType ='DFA' and origen = 'outer'`;
        console.log("deletePagoscuota()",sqld)
        await this.executeSQL(sqld);        
    }

    public async insertAllSap(d: any) {
        console.log("insertAllSap()")

        let campos = new Camposusuario();
        let session = await campos.consultasesion();
        let sql2 = await campos.camposusuariosinc(d,1,session);
        console.log("RETORNO 1",sql2);

        let fecha = moment().format('YYYY-MM-DD h:mm:ss');
        let sql = 'INSERT INTO documentos VALUES ';
        console.log("doc ---> ", d);
        sql += ` (NULL,'${d.cod}', '${d.DocEntry}', '${d.DocNum}', '${d.DocType}', '', '', '',
                    '${d.DocDate}', '${d.DocDueDate}','${d.CardCode}', '${d.CardName}', '', '', '', '', '', '${d.DocTotal}',
                    '${d.PaidtoDate}','','','','','','','0','0','', '','','${fecha}','','${d.DateUpdate}',
                    '','','','${d.U_4NIT}','${d.U_4RAZON_SOCIAL}','','','','','${d.PriceListNum}','1','${fecha}','${d.DateUpdate}','','','${localStorage.getItem("idSession")}','','','','','',
                    '${d.descuento}','','${d.Currency}','${d.clone}','${d.tipodescuento}','','','','','','','','${d.origen}','${d.ReserveInvoice}', ${d.Saldo},
                    ${d.Pendiente},'','','','','','','',true,'${d.centrocosto}','${d.unidadnegocio}','${d.reimpresiones}','${d.U_XMB_AUX1}','${d.grupoproductodocificacion}' 
                    ,'0','0','0', '', '${d.DocEntry}', '','','','','${d.cndpagoname}',${d.Cuota},'',0,'${d.vendedor}'`+sql2+`);`;
        console.log("sqlx  ", sql);
        return await this.executeSQL(sql);
       /* let sql1 = "select count(*) as CANTIDAD from  documentos where cod = '" + d.cod + "'";
        console.log("la consulta es", sql1);
        let cantidad = await this.queryAll(sql1);
        console.log("la cantidad es", cantidad[0].CANTIDAD);

        let fecha = moment().format('YYYY-MM-DD h:mm:ss');

        if (cantidad[0].CANTIDAD > 0) {
            console.log(cantidad);
            if(d.origen == "outer"){
                let sqp = `UPDATE documentos SET 
                cod ='${d.cod}',
                DocEntry ='${d.DocEntry}',
                DocNum ='${d.DocNum}',
                DocType ='${d.DocType}',
                DocDate ='${d.DocDate}',
                DocDueDate ='${d.DocDueDate}',
                CardCode ='${d.CardCode}',
                CardName ='${d.CardName}',
                DocTotal ='${d.DocTotal}',
                PaidToDate ='${d.PaidtoDate}',
                Series ='0',
                TaxDate ='0',
                CreateDate ='${fecha}',
                UpdateDate ='${d.DateUpdate}',
                U_4NIT ='${d.U_4NIT}',
                PriceListNum = '${d.PriceListNum}',
                estadosend = '1',
                fecharegistro = '${fecha}',
                fechaupdate = '${d.DateUpdate}',
                idUser = '${localStorage.getItem("idSession")}',
                descuento = '${d.descuento}',
                currency = 'BS',
                clone = '${d.clone}',
                tipodescuento = '${d.tipodescuento}',
                origen = '${d.origen}',
                ReserveInvoice = '${d.ReserveInvoice}',
                saldo = ${d.Saldo},
                Pendiente = ${d.Pendiente},
                Reserve = true,
                centrocosto = '${d.centrocosto}',
                unidadnegocio = '${d.unidadnegocio}',
                reimpresiones = '${d.reimpresiones}',
                codexternal = '${d.U_XMB_AUX1}',
                grupoproductoscode = '${d.grupoproductodocificacion}',
                U_DOCENTRY = '${d.DocEntry}',
                cndpagoname = '${d.cndpagoname}',
                cuota = ${d.Cuota}
                where  DocNum= '${d.DocNum}'`;

                console.log("sqlx  ", sqp);
                await this.executeSQL(sqp);
            }
        }else{
            let sql = 'INSERT INTO documentos VALUES ';
            console.log("doc ---> ", d);
            sql += ` (NULL,'${d.cod}', '${d.DocEntry}', '${d.DocNum}', '${d.DocType}', '', '', '',
                      '${d.DocDate}', '${d.DocDueDate}','${d.CardCode}', '${d.CardName}', '', '', '', '', '', '${d.DocTotal}',
                      '${d.PaidtoDate}','','','','','','','0','0','', '','','${fecha}','','${d.DateUpdate}',
                      '','','','${d.U_4NIT}','${d.U_4RAZON_SOCIAL}','','','','','${d.PriceListNum}','1','${fecha}','${d.DateUpdate}','','','${localStorage.getItem("idSession")}','','','','','',
                      '${d.descuento}','','BS','${d.clone}','${d.tipodescuento}','','','','','','','','${d.origen}','${d.ReserveInvoice}', ${d.Saldo},
                      ${d.Pendiente},'','','','','','','',true,'${d.centrocosto}','${d.unidadnegocio}','${d.reimpresiones}','${d.U_XMB_AUX1}','${d.grupoproductodocificacion}' 
                      ,'0','0','0', '', '${d.DocEntry}', '','','','','${d.cndpagoname}',${d.Cuota} );`;
            console.log("sqlx  ", sql);
            return await this.executeSQL(sql);
        }*/       
    }

    public async update(data: any, idpedido: number) {
        let sql = '';
        console.log("update()");
        let fecha = moment().format('YYYY-MM-DD h:mm:ss');
        let tiempo = this.tiempo();
        sql = `UPDATE documentos SET 
            DocEntry = '${data.DocEntry}', 
            DocNum = '${data.DocNum}', 
            DocType = '${data.DocType}', 
            canceled = '${data.canceled}', 
            Printed = '${data.Printed}', 
            DocStatus = '${data.DocStatus}', 
            DocDate = '${fecha}', 
            DocDueDate = '${fecha}', 
            CardCode = '${data.CardCode}', 
            CardName = '${data.CardName}', 
            NumAtCard = '${data.NumAtCard}', 
            DiscPrcnt = '${data.DiscPrcnt}', 
            DiscSum = '${data.DiscSum}', 
            DocCur = '${data.DocCur}', 
            DocRate = '${data.DocRate}', 
            DocTotal = '${data.DocTotal}', 
            PaidToDate = '${data.PaidToDate}', 
            Ref1 = '${data.Ref1}', 
            Ref2 = '${data.Ref2}', 
            Comments = '${data.Comments}', 
            JrnlMemo = '${data.JrnlMemo}', 
            GroupNum = '${data.GroupNum}', 
            SlpCode = '${data.SlpCode}', 
            Series = '${data.Series}', 
            TaxDate = '${fecha}', 
            LicTradNum = '${data.LicTradNum}', 
            Address = '${data.Address}', 
            UserSign = '${data.UserSign}', 
            CreateDate = '${fecha}', 
            UserSign2 = '${data.UserSign2}', 
            UpdateDate ='${data.UpdateDate}', 
            U_4MOTIVOCANCELADO = '${data.U_4MOTIVOCANCELADO}', 
            U_4NIT = '${data.U_4NIT}', 
            U_4RAZON_SOCIAL = '${data.U_4RAZON_SOCIAL}', 
            U_LATITUD = '${data.U_LATITUD}', 
            U_LONGITUD = '${data.U_LONGITUD}', 
            U_4SUBTOTAL = '${data.U_4SUBTOTAL}',
            U_4DOCUMENTOORIGEN = '${data.U_4DOCUMENTOORIGEN}', 
            U_4MIGRADOCONCEPTO = '${data.U_4MIGRADOCONCEPTO}', 
            U_4MIGRADO = '${data.U_4MIGRADO}', 
            PriceListNum = '${data.PriceListNum}', 
            estadosend = '${data.estadosend}', 
            fecharegistro = ${tiempo},
            fechaupdate = ${tiempo}, 
            fechasend = '${data.fechasend}', 
            key = '1', 
            estado = '${data.estado}', 
            gestion = '${data.gestion}', 
            mes = '${data.mes}', 
            correlativo = '${data.correlativo}', 
            rowNum = '${data.rowNum}',
            descuento = ${data.descuento}, 
            tipocambio = ${data.tipocambio}, 
            currency = '${data.currency}', 
            clone = ${data.clone}, 
            tipodescuento = ${data.tipodescuento}
            WHERE id = ${idpedido}`;
        console.log("sql ", sql)
        return new Promise((resolve, reject) => {
            this.executeSQL(sql).then((data: any) => {
                resolve(data.insertId);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public controlPagos(cod: any) {

        let sql = 'select * FROM documentos as d WHERE d.cod = "' + cod + '" ';
        let consuta = this.queryAll(sql);
        console.log("consulta 1", sql);
        console.log("datos 1", consuta);

        let sql1 = 'SELECT de.* FROM documentos d inner join detalle de on de.idDocumento = d.cod WHERE d.cod = "' + cod + '" ';
        let consuta1 = this.queryAll(sql1);
        console.log("consulta 2", sql1);
        console.log("datos 2", consuta1)

        let sql3 = 'SELECT * FROM detalle';
        let consuta3 = this.queryAll(sql3);
        console.log("consulta 3", sql3);
        console.log("datos 3", consuta3)

        let sql2 = 'SELECT * from pagos';
        let consuta2 = this.queryAll(sql2);
        console.log("consulta 4", sql2);
        console.log("datos 4", consuta2)



        return new Promise((resolve, reject) => {
            let sql = '' +
                'SELECT ' +
                'printf("%.2f", round(d.descuento, 2)) as descuento,' +
                'printf("%.2f", round(d.tipodescuento, 2)) as  tipodescuento,' +
                'printf("%.2f", round((SELECT (SUM(icett) - d.descuento) FROM detalle WHERE idDocumento = d.id),2)) as total, ' +
                'printf("%.2f", round((SELECT SUM(icett) FROM detalle WHERE idDocumento = d.id),2)) as totalNeto, ' +
                'printf("%.2f",(SELECT SUM(monto) FROM pagos p WHERE p.documentoId = d.id)) as pagado,  ' +
                'printf("%.2f",(SELECT (round((SELECT (SUM(icett) - d.descuento) FROM detalle WHERE idDocumento = d.id),2)) - (SELECT round(SUM(monto),2)) FROM pagos p WHERE p.documentoId = d.id)) as saldo ' +
                'FROM documentos as d WHERE d.cod = "' + cod + '" ';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async controlPagoslocal(cabecera: any, detalle: any) {
        console.log("controlPagoslocal", cabecera);
        console.log("controlPagoslocal", detalle);

        let sql = 'SELECT SUM(monto) as monto FROM pagos p WHERE p.documentoId = "' + cabecera[0].id + '"';
        let pago = await this.queryAll(sql);
        if (pago[0].monto == null) {
            pago[0].monto = 0;
        }
        console.log("pago", pago);

        let sumn_icett = 0;
        for (let items of detalle) {
            sumn_icett = Math.round(sumn_icett) + Math.round(items.icett);
        }
        let descab = 0;
        if(cabecera[0].tipodescuento > 0){
            descab = pago[0].monto*(cabecera[0].tipodescuento/100);
        }

        let datos = {
            descuento: Calculo.round(cabecera[0].descuento),
            tipodescuento: Calculo.round(cabecera[0].tipodescuento),
            total: Calculo.round(sumn_icett - (cabecera[0].descuento+descab)),
            totalNeto: Calculo.round(sumn_icett),
            pagado: Calculo.round(pago[0].monto),
            saldo: Calculo.round((sumn_icett - (cabecera[0].descuento+descab)) - pago[0].monto),
        };
        console.log("datos", datos);
        return datos;
    }

    public async findAll(id: number, doc: string, origen: string, cardCode = '') {
        let xs = ' ';
        if (cardCode != '') xs = ` AND CardCode = '${cardCode}' `;
        let sql = `SELECT * FROM viewuno WHERE tipoestado != 'null' AND DocType = '${doc}' ${xs} ORDER BY id DESC LIMIT 30`;
        return await this.queryAll(sql);
    }

    public async findFacturaFromPedido(codePedido) {
        let sql = `SELECT cod AS codFacturado  FROM documentos WHERE clone = '${codePedido}' AND DocType IN ('DFA', 'DOE') AND idUser=${localStorage.getItem("idSession")} `;
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public async deletedoc(id: number) {
        let sql = `DELETE FROM documentos WHERE id = ${id}`;
        console.log("deletedoc sql ", sql);
        return await this.executeSQL(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS documentos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public clear() {
        return new Promise((resolve, reject) => {
            let sql = `DELETE FROM documentos WHERE origen = 'outer'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public actualizarEnviados(id: any, key: number, estado: any) {
        console.log("actualizarEnviados() ");
        let sqlAux = '';
        return new Promise((resolve, reject) => {
            if (estado == 6 || estado == 7) {
                console.log("DEVD Anulado por el backend ");

                sqlAux = ', tipoestado="anulado"';
            }
            let sql = `UPDATE documentos SET estadosend = '${estado}', estado = '${estado}', key = '${key}'  ${sqlAux} WHERE id = ${id}`;
            console.log("sql ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public actualizarEnviadosOuter(id: any) {
        console.log("actualizarEnviados() ");
        let sqlAux = '';
        return new Promise((resolve, reject) => {

            let sql = `UPDATE documentos SET estadosend = '6' , estado = '6' WHERE cod = '${id}'`;
            console.log("sql ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public actualizarconfirmacion(data: any, id: any, estado: number) {
        console.log("actualizarconfirmacion() ")
        return new Promise((resolve, reject) => {
            let sql = `UPDATE documentos SET fechasend = '${data.plazospago}', DocDueDate = '${data.plazospago}', federalTaxId = '${data.nit}', 
            cardNameAux = '${data.razonsocial}', U_LB_RazonSocial = '${data.razonsocial}', comentario = '${data.comentario}', cuenta = '${data.cuenta}', 
            PayTermsGrpCode= '${data.condicion}', estadosend = '${estado}',codeConsolidador = '${data.carcodeConso}',Fex_documento = '${data.documentofex}', Fex_tipodocumento = '${data.tipodocumento}' WHERE cod = '${id}'`;
            console.log("sql ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public descuentoAdicional(descuentoDelTotal: number, descuentoDelTotalPorcentual: number, id: any) {
        console.log("descuentoDelTotal ", descuentoDelTotal)
        console.log("descuentoDelTotalPorcentual ", descuentoDelTotalPorcentual)
        console.log("id ", id)
        console.log("descuentoDelTotal ", descuentoDelTotal)

        return new Promise((resolve, reject) => {
            let sql = "";
            if (descuentoDelTotalPorcentual > 0) {
                sql = `UPDATE documentos SET descuento =  ${descuentoDelTotal}, tipodescuento = ${descuentoDelTotalPorcentual} WHERE cod = '${id}'`;

            } else {
                sql = `UPDATE documentos SET descuento = ${descuentoDelTotal}, tipodescuento = ${descuentoDelTotalPorcentual} WHERE cod = '${id}'`;

            }
            console.log("sql ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rowsAffected);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public descuentoAdicionalICE(descuentoDelTotal: number, descuentoDelTotalPorcentual: number, id: any, hace: any) {
        console.log("descuentoAdicionalICE()");

        return new Promise(async (resolve, reject) => {
            let sql_total = `SELECT SUM(Quantity * Price) as total  from  detalle where idDocumento='${id}' and bonificacion=0 `;
            //let sql_totalbonif = `SELECT SUM(Quantity * Price) as total from  detalle where idDocumento='${id}' and bonificacion=0`;
            let totaldoc = await this.queryAll(sql_total);
            //  let totaldocbonif = await this.queryAll(sql_totalbonif);
            console.log("totaldoc lineas ", totaldoc);
            // console.log("totaldocbonif lineas ", totaldocbonif);
            let sqlupdate = "";
            if (hace == false) {
                // if (totaldocbonif[0].total === null) totaldocbonif[0].total = 0;
                // sqlupdate = `UPDATE detalle SET U_4DESCUENTO = (DiscTotalMonetary + round((((Quantity * (Price*1.0))/${totaldoc[0].total})*${totaldocbonif[0].total}),2))  where idDocumento='${id}' and bonificacion=0 `;
                //  console.log("sqlupdate ", sqlupdate);
                //  await this.executeSQL(sqlupdate);

                if (descuentoDelTotal > 0) {


                    console.log("descuentoDelTotalPorcentual ", descuentoDelTotalPorcentual);

                    let sqlupdate_0 = "";
                    sqlupdate_0 = `UPDATE detalle SET XMPORCENTAJECABEZERA=${descuentoDelTotalPorcentual},  
                  XMPORCENTAJE =${descuentoDelTotalPorcentual}, 
                  U_4DESCUENTO = (U_4DESCUENTO + round((((Quantity * Price)*${descuentoDelTotalPorcentual})/100),2))  where idDocumento='${id}' and bonificacion=0  `;
                    console.log("sqlupdate_0 ", sqlupdate_0);
                    await this.executeSQL(sqlupdate_0);
                }
            }
            /*
            let sqlupdate1 = `
             UPDATE detalle set BaseId= (Select BaseQty from productosprecios where productosprecios.ItemCode=detalle.ItemCode and productosprecios.Code=detalle.unidadId)  where idDocumento='${id}';
             UPDATE detalle set ICEe=(Quantity * icete * BaseId)  where idDocumento='${id}';
             UPDATE detalle set ICEp=(((Quantity * Price)-U_4DESCUENTO )* 0.87 * icetp /100)  where idDocumento='${id}';
             UPDATE detalle set LineTotalPay=((Quantity * Price)-U_4DESCUENTO + ICEe + ICEp )  where idDocumento='${id}' 
             `;
            await this.executeSQL(sqlupdate1);
            */
            resolve(true);
        });
    }

    public async descuentoAdicionalICEmonto(descuentoDelTotal: number, id: any) {
        return new Promise(async (resolve, reject) => {
            let sql_total = `SELECT SUM(Quantity * Price) as total, COUNT(*) AS countItems from  detalle where idDocumento='${id}' and bonificacion=0 `;
            //let sql_totalbonif = `SELECT SUM(Quantity * Price) as total from  detalle where idDocumento='${id}' and bonificacion=0`;
            let totaldoc = await this.queryAll(sql_total);
            //  let totaldocbonif = await this.queryAll(sql_totalbonif);
            console.log("DEVD totaldoc lineas ", totaldoc);
            // console.log("totaldocbonif lineas ", totaldocbonif);

            if (descuentoDelTotal > 0) {
                let descuentoDividido = (descuentoDelTotal / totaldoc[0].countItems).toFixed(2);
                console.log("DEVD descuentoDividido ", descuentoDividido);
                console.log("DEVD descuentoDelTotal ", descuentoDelTotal);

                let sqlupdate_0 = "";
                sqlupdate_0 = `UPDATE detalle SET XMPROMOCIONCABEZERA=${descuentoDividido},  
              
                  U_4DESCUENTO = U_4DESCUENTO+${descuentoDividido} where idDocumento='${id}' and bonificacion=0  `;
                console.log("DEVD sqlupdate_0 ", sqlupdate_0);
                await this.executeSQL(sqlupdate_0);
            }

            resolve(true);
        });
    }

    /*
        public async updateDescuentoLinea(descuentoDelTotal: number, descuentoDelTotalPorcentual: number, id: any, hace: any) {
    
            let sql_total = `SELECT SUM(Quantity * Price) as total from  detalle where idDocumento='${id}' and bonificacion=0 `;
                let sql_totalbonif = `SELECT SUM(Quantity * Price) as total from  detalle where idDocumento='${id}' and bonificacion=1 `;
                let totaldoc = await this.queryAll(sql_total);
                let totaldocbonif = await this.queryAll(sql_totalbonif);
                let sqlupdate = "";
                if (hace == false) {
                   
                    //  await this.executeSQL(sqlupdate);
                    if (descuentoDelTotal > 0) {
                        let sqlupdate_0 = "";
                        sqlupdate_0 = `UPDATE detalle 
                        SET  U_4DESCUENTO = (U_4DESCUENTO + round((((Quantity * (Price*1.0))/${totaldoc[0].total})*${descuentoDelTotal}),1)) 
                         where idDocumento='${id}' and bonificacion=0  `;
                        console.log("DEVD sqlupdate_0 ", sqlupdate_0);
                        await this.executeSQL(sqlupdate_0);
                    }
                }
    
    
            let sqlDesc = `        UPDATE detalle SET XMPROMOCIONCABEZERA=${descuentoDelTotal}
                    WHERE idDocumento = '${idDocumento}' AND ItemCode='${itemCode}'; 
                    UPDATE detalle set ICEe=(icete * BaseQty)  where idDocumento='${idDocumento}' AND ItemCode='${itemCode}';  `; // Quantity *  ojo 
            console.log("******* sqlDesc ", sqlDesc);
            await this.executeRaw(sqlDesc);
    
    
    
            let sqlupdateIces = `
    
            UPDATE detalle set ICEp=ROUND(((Quantity * Price)-U_4DESCUENTO) * 0.87 * icetp /100, 1)  where idDocumento='${idDocumento}' AND ItemCode='${itemCode}'  ;
            UPDATE detalle set LineTotalPay=((Quantity * Price)-U_4DESCUENTO + ICEe + ICEp )  where idDocumento='${idDocumento}'  AND ItemCode='${itemCode}';
    
            `;
            console.log("DEVD sqlupdateIces ", sqlupdateIces);
    
            let r =await this.executeRaw(sqlupdateIces);
    
    
            return r;
        }
    */

    public descuentoICE(descuentoDelTotal: any, descuentoDelTotalPorcentual: number, id: any, hace: any, bono = 0) {
        return new Promise(async (resolve, reject) => {
            let sql_total = `SELECT SUM((Quantity * Price)-U_4DESCUENTO) as total from  detalle where idDocumento='${id}' and bonificacion<>1 `;
            //let sql_totalbonif = `SELECT SUM((Quantity * Price)-U_4DESCUENTO) as total from  detalle where idDocumento='${id}' and bonificacion=1 `;
            let totaldoc = await this.queryAll(sql_total);
            console.log("totaldoc ", totaldoc);
            console.log("descuentoDelTotal ", descuentoDelTotal);
            // let totaldocbonif = await this.queryAll(sql_totalbonif);
            // let sqlupdate = "";
            descuentoDelTotal = Number(descuentoDelTotal).toFixed(2);
            console.log("DEVD descuento recibido en moneda particionado ", descuentoDelTotal);
            console.log("DEVD descuentoDelTotalPorcentual recibido ", descuentoDelTotalPorcentual);

            if (hace == false) {
                /* if (totaldocbonif[0].total === null) totaldocbonif[0].total = 0;
                 sqlupdate = `UPDATE detalle SET U_4DESCUENTO = (DiscTotalMonetary + round((((Quantity * (Price*1.0))/${totaldoc[0].total})*${totaldocbonif[0].total}),2)) 
                  where idDocumento='${id}' and bonificacion=0 `;
                 console.log("DEVD sqlupdate ", sqlupdate);
                 */
                //  await this.executeSQL(sqlupdate);
                if (descuentoDelTotal > 0) {
                    let sqlupdate_Desc = "";
                    sqlupdate_Desc = `
                    UPDATE detalle 
                    SET  U_4DESCUENTO = (U_4DESCUENTO + round(((((Quantity * (Price*1.0))-U_4DESCUENTO)/${totaldoc[0].total})*${descuentoDelTotal}),2)) 
                     where idDocumento='${id}' and bonificacion<>1;
                  ';
                     `;
                    console.log("DEVD sqlupdate_Desc ", sqlupdate_Desc);
                    await this.executeSQL(sqlupdate_Desc);
                    let sqlupdate_Ice = "";
                    sqlupdate_Ice = `
                   
                     UPDATE detalle set ICEe=ROUND((icete * (Quantity *  BaseQty)),2)  where idDocumento='${id}' AND bonificacion<>1 ; ';
                     `;
                    console.log("DEVD sqlupdate_Ice ", sqlupdate_Ice);
                    await this.executeSQL(sqlupdate_Ice);
                }
            }
            // BaseId ?? BaseQty
            // iceP = ((bruto-descuentoL//inea-DescBono-DescuentoCabezera)*87%)*10%
            if (descuentoDelTotalPorcentual > 0) {

                let sqlupdateCabeceraDesc = `
                UPDATE detalle set XMPORCENTAJECABEZERA=${descuentoDelTotalPorcentual} where idDocumento='${id}' and bonificacion<>1 ;
                `;
                console.log("DEVD sqlupdateCabeceraDesc ", sqlupdateCabeceraDesc);
                await this.executeSQL(sqlupdateCabeceraDesc);

            } else {
                let sqlupdateCabeceraDesc = `
                UPDATE detalle set XMPROMOCIONCABEZERA=${descuentoDelTotal} where idDocumento='${id}' and bonificacion<>1 ;
                `;
                console.log("DEVD sqlupdateCabeceraDesc ", sqlupdateCabeceraDesc);
                await this.executeSQL(sqlupdateCabeceraDesc);
            }
            if (bono != 0) {
                let sqlInfoBono = `UPDATE detalle SET  codeBonificacionUse='${bono}', bonificacion='2' where idDocumento='${id}' and bonificacion<>1 ;`;
                console.log("DEVD sqlInfoBono ", sqlInfoBono);
                await this.executeSQL(sqlInfoBono);
            }
            let sqlupdateIcep = `UPDATE detalle set ICEp=ROUND(((Quantity * Price)-U_4DESCUENTO) * 0.87 * icetp /100, 2)  where idDocumento='${id}'  and bonificacion<>1 ;
             UPDATE detalle set LineTotalPay=((Quantity * Price)-U_4DESCUENTO + ICEe + ICEp )where idDocumento='${id}' and bonificacion<>1  ;
             `;
            console.log("DEVD sqlupdateIcep ", sqlupdateIcep);
            await this.executeSQL(sqlupdateIcep);
            resolve(true);
        });
    }

    /*****************/
    public async selectAll() {
        let sql = `SELECT * FROM documentos where  origen = 'outer' order by id desc`;
        return await this.queryAll(sql);
    }

    public async selectAllVista() {
        let sql = `SELECT * FROM v_documentoview9 order by id desc`;
        return await this.queryAll(sql);
    }

    public async selectAllDetalle() {
        let sql = `SELECT * FROM detalle order by id desc`;
        return await this.queryAll(sql);
    }

    public async selectAllPagos(date) {
        let sql = `SELECT * FROM pagos WHERE fecha='${date}' AND dx='facturas'  order by id desc`; //
        return await this.queryAll(sql);
    }

    public async selectAllDoc(date) {
        let sql = `SELECT * FROM documentos  where origen = 'inner' AND DocDate='${date}'  AND idUser=${localStorage.getItem("idSession")} order by id desc`;// AND fecha='${date}'
        //   console.log("******* ", sql);
        return await this.queryAll(sql);
    }

    public async selectInVisitas(date) {
        let sql = `SELECT * FROM visitas WHERE fecha='${date}'   ORDER BY id DESC;`;
        console.log("sql no visita ", sql);
        return await this.queryAll(sql);
    }

    public async findOne(doc: string) {
        let sql = `SELECT * FROM documentos WHERE cod = '${doc}' `;
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public async findOneDocEntry(doc: string, docentry, cuota) {
        let sql = `SELECT * FROM documentos WHERE cod = '${doc}' AND DocEntry = '${docentry}' AND cuota= '${cuota}'`;
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public async findOneDetalles(cod: any) {
        let sql = `SELECT * FROM detalle WHERE idDocumento = '${cod}';`;
        return await this.queryAll(sql);
    }

    public async selectAllResumenCaja(date: any, esFactura: any) {
        if (esFactura == 'factura') {
            esFactura = "=";
        } else {
            esFactura = "!=";
        }
        let sql = `SELECT PU.id, PU.anulado as newAnulado,  CASE PU.formaPago
        WHEN 'PEF' THEN 'Efectivo'
        WHEN 'PCC' THEN 'Tarjeta'
        WHEN 'PBT' THEN 'Transferencia'
        WHEN 'PCH' THEN 'Cheque'
        END AS formaPagoText, 
         (SELECT  IFNULL(SUM(monto), 0)   FROM pagos p WHERE p.dx ` + esFactura + ` 'factura' AND p.moneda='BS' AND  p.fecha = '${date}' AND PU.formaPago=p.formaPago AND p.anulado=0) AS acumuladoActivo,
         (SELECT IFNULL(SUM(monto), 0)    FROM pagos p WHERE p.dx ` + esFactura + ` 'factura' AND p.moneda='BS' AND  p.fecha = '${date}' AND PU.formaPago=p.formaPago AND p.anulado>0) AS acumuladoInactivo,
         (SELECT IFNULL( COUNT(*), 0)   FROM pagos p WHERE p.dx ` + esFactura + ` 'factura' AND p.moneda='BS' AND  p.fecha = '${date}' AND PU.formaPago=p.formaPago AND p.anulado=0) AS totalDocumentosActivos,
         (SELECT IFNULL( COUNT(*), 0)  FROM pagos p WHERE p.dx ` + esFactura + ` 'factura' AND p.moneda='BS' AND  p.fecha = '${date}' AND PU.formaPago=p.formaPago AND p.anulado>0)  AS totalDocumentosInactivos,
         (SELECT  IFNULL(SUM(PU.monedaDolar), 0)   FROM pagos p WHERE p.dx ` + esFactura + ` 'factura' AND p.moneda='BS' AND  p.fecha = '${date}' AND PU.formaPago=p.formaPago AND p.anulado=0) AS acumuladoActivoUSD,

         COUNT(*) as documentos, SUM(PU.monto) as total, 
        PU.formaPago, PU.tipoCambioDolar, PU.monedaDolar, PU.dx , PU.otpp , PU.moneda
         FROM pagos PU where PU.dx ` + esFactura + ` 'factura' and PU.moneda='BS' AND fecha = '${date}' AND idUser=${localStorage.getItem("idSession")} group by PU.formaPago`;
        return await this.queryAll(sql);
    }

    public async selectAllResumenCajaDetalle(date: any, esFactura: any) {
        if (esFactura == 'factura') {
            esFactura = "=";
        } else {
            esFactura = "!=";
        }
        let sql = ` SELECT  PU.id, PU.documentoId, PU.documentoPagoId, PU.anulado, formaPago, CASE PU.formaPago
        WHEN 'PEF' THEN 'Efectivo'
        WHEN 'PCC' THEN 'Tarjeta'
        WHEN 'PBT' THEN 'Transferencia'
        WHEN 'PCH' THEN 'Cheque'
        END AS formaPagoText, 
        PU.bancoCode,
        PU.numCheque,
        PU.numComprobante,
        PU.numTarjeta,
        PU.fecha,
        PU.monto,  PU.baucher,
         PU.formaPago, PU.tipoCambioDolar, PU.monedaDolar, PU.dx , PU.otpp , PU.moneda
         FROM pagos PU where PU.moneda='BS'  AND PU.anulado=0 AND fecha = '${date}' AND idUser=${localStorage.getItem("idSession")}
         order by id desc`;
        return await this.queryAll(sql);
    }

    /**
     *
     * @param date
     switch (tipo) {
            case('oferta'):
                tipo = 'DOF';
                break;
            case('pedido'):
                tipo = 'DOP';
                break;
            case('factura'):
                tipo = 'DFA';
                break;
            case('entrega'):
                tipo = 'DOE';
                break;
        }
     * @param esFactura
     */
    public async selectAllResumenCajaDetalleAnulados(date: any, esFactura: any) {
        if (esFactura == 'factura') {
            esFactura = "=";
        } else {
            esFactura = "!=";
        }
        let sql = ` SELECT  PU.id, PU.documentoId, PU.documentoPagoId, PU.anulado, formaPago, CASE PU.formaPago
        WHEN 'PEF' THEN 'Efectivo'
        WHEN 'PCC' THEN 'Tarjeta'
        WHEN 'PBT' THEN 'Transferencia'
        WHEN 'PCH' THEN 'Cheque'
        END AS formaPagoText, 
        PU.bancoCode,
        PU.numCheque,
        PU.numComprobante,
        PU.numTarjeta,
        PU.fecha,
        PU.monto,
         PU.formaPago, PU.tipoCambioDolar, PU.monedaDolar, PU.dx , PU.otpp , PU.moneda
         FROM pagos PU where PU.moneda='BS'  AND PU.anulado>0 AND fecha = '${date}' AND idUser=${localStorage.getItem("idSession")}
    
         order by id desc`;
        return await this.queryAll(sql);
    }

    /**
     * RESUMEN VENTAS
     * @param date
     * @param esFactura
     */
    public async selectAllResumenVentas(date: any, esFactura: any) {
        if (esFactura == 'factura') {
            esFactura = "=";
        } else {
            esFactura = "!=";
        }
        //DocumentTotal - DocumentdescuentoTotal deberia ser el total del documento pagado
        let sql = ` SELECT 
                          case when DocType = 'DOF' THEN 'OFERTA'
                          when DocType = 'DOP' THEN 'PEDIDO'
                          when DocType = 'DFA' THEN 'FACTURA'
                          when DocType = 'DO3' THEN 'ENTREGA' END 
                          DocType,
                          DocCur,
                          ( SELECT  COUNT (subV.tipoestado)  FROM v_documentoview9 subV WHERE subV.canceled=3 AND  subV.DocDate = '${date}' AND subV.DocType=v_documentoview9.DocType AND idUser=${localStorage.getItem("idSession")}) AS CantidadAnulado,
                          ( SELECT  SUM (subV.DocumentTotalPay)  FROM v_documentoview9 subV WHERE subV.canceled=3 AND  subV.DocDate = '${date}' AND subV.DocType=v_documentoview9.DocType AND idUser=${localStorage.getItem("idSession")}) AS MontoAnulado,
                          ( SELECT  COUNT (subV.tipoestado)  FROM v_documentoview9 subV WHERE subV.canceled<>3 AND  subV.DocDate = '${date}' AND subV.DocType=v_documentoview9.DocType AND idUser=${localStorage.getItem("idSession")}) AS CantidadActivo,
                          ( SELECT  SUM (subV.DocumentTotalPay)  FROM v_documentoview9 subV WHERE subV.canceled<>3 AND  subV.DocDate = '${date}' AND subV.DocType=v_documentoview9.DocType AND idUser=${localStorage.getItem("idSession")}) AS MontoActivo
                          FROM v_documentoview9
        where  DocDate = '${date}' AND origen = 'inner'  AND idUser=${localStorage.getItem("idSession")}
        group by DocType, DocCur`;
        return await this.queryAll(sql);
    }

    /**
     * RESUMEN VENTAS
     * @param date
     * @param esFactura
     */
    public async selectAllResumenDocOfertas(date: any, tipo: any, anulado: any) {
        switch (tipo) {
            case ('oferta'):
                tipo = 'DOF';
                break;
            case ('pedido'):
                tipo = 'DOP';
                break;
            case ('factura'):
                tipo = 'DFA';
                break;
            case ('entrega'):
                tipo = 'DOE';
                break;
        }
        let sql = ` SELECT 
                          cod,
                          DocType,
                          DocDate,
                          CardCode,
                          CardName,
                          DocCur,
                          printf("%.2f", DocumentTotalPay)  AS monto,
                          estadosend,
                          estado,
                          currency, origen,tipoestado,
                          canceled
                          FROM v_documentoview9
        where  DocDate = '${date}' AND origen = 'inner' AND idUser=${localStorage.getItem("idSession")} order by DocType, DocCur`;
        return await this.queryAll(sql);
    }

    /**
     * INFORME DE BONIFICAIONES Y DESCUENTOS DISPONIBLES 
     */
    public async selectBonificaionesDescuentos(tipo) {
        let sql = `SELECT * FROM bonificacion_ca WHERE cabezera_tipo like '%${tipo}%' GROUP BY code;`;
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public async selectBonificaiones() {
        let sql = `SELECT * FROM bonificacion_ca;`;
        return await this.queryAll(sql);
    }

    public async showAllCompras() {
        let sql = `SELECT * FROM bonificacion_compras;`;
        return await this.queryAll(sql);
    }

    public async showAllRegalos() {
        let sql = `SELECT * FROM bonificacion_regalos`;
        return await this.queryAll(sql);
    }

    public async updateSaldoFacturasSap(data: any) {
        let sql = "";
        console.log("updateSaldoFacturasSap");
        if (data.coddoc.substring(0, 3) == "DFA") {
            sql = `UPDATE documentos SET saldo = saldo-${Number(data.pagarx)} WHERE cod = '${data.coddoc}' AND cuota =  '${data.cuota}' `;
        }
        else {
            sql = `UPDATE documentos SET saldo = saldo+${Number(data.pagarx)} WHERE cod = '${data.coddoc}' `;
        }

        console.log("sql update pago factura", sql);

        return await this.executeSQL(sql);
    }

    public async dataExportAll() {
        let sqlx = `SELECT d.*, '1' AS papelId FROM v_documentoview9 d WHERE (d.estadosend = 1 OR d.estadosend = 7)  AND (d.tipoestado = "cerrado"  OR  d.tipoestado = "anulado") ORDER BY id ASC LIMIT 20`;
        console.log("sqlx ", sqlx);
        return await this.queryAll(sqlx);
    }

    public selectDocumentSucursal(CardCode: string, idSucursal: string) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM clientessucursales WHERE cardCode= '${CardCode}' AND LineNum = '${idSucursal}'`;
            console.log("sql ", sql);
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++)
                    arr.push(data.rows.item(i));
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async camposCabeceraDocumentos(table: string) {
        let $sql = "";
        $sql = " SELECT name FROM pragma_table_info('" + table + "');";
        return await this.queryAll($sql);
    }

    public async alterTableRun(table: string, row: string, type: string = 'char(1)  DEFAULT "Y"') {
        const statement = `ALTER TABLE ${table} ADD COLUMN ${row} ${type};`;
        console.log({ statement });
        return await this.executeSQL(statement);
    }

    public async selectDinamic(table: string) {
        return await this.queryAll("SELECT * FROM " + table);
    }

    public async selectClients() {
        return await this.queryAll(" SELECT * FROM clientes  LIMIT 1;");
    }

    public async docxporfecha(fecha: string) {
        let sql = `SELECT *  
                   FROM v_documentoview9 
                   WHERE origen = 'inner' AND CreateDate = '${fecha}' AND DocType = 'DFA' AND idUser=${localStorage.getItem("idSession")} and tipoestado <> "anulado" ORDER BY cod asc`;
        console.log(sql);         
        return await this.queryAll(sql);
    }

    public async Factporfecha(fecha: string) {
        let sql = `SELECT *  
                   FROM v_documentoview9 
                   WHERE origen = 'inner'  AND DocType = 'DFA'  AND CreateDate = '${fecha}' AND idUser=${localStorage.getItem("idSession")} ORDER BY cod asc`;

        console.log(sql);         
        return await this.queryAll(sql);
    }

}
