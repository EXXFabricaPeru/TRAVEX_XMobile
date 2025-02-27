import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
import { Clientes } from "./clientes";
import { FacturasPagos } from "./facturasPagos";
import { Documentos } from "./documentos";
import { environment } from "../../environments/environment"



export class Pagos extends Databaseconf {
    public configService: ConfigService;

    public async uniqueBoucher(boucher: any) {
        let sql = `SELECT COUNT(*) AS tx FROM pagos  WHERE baucher = '${boucher}'`;
        let rrx = await this.queryAll(sql);
        return rrx[0].tx;
    }

    public async uniqueTranferencia(code: any) {
        let sql = `SELECT COUNT(*) AS tx FROM pagos  WHERE numTarjeta = '${code}'`;
        let rrx = await this.queryAll(sql);
        return rrx[0].tx;
    }

    public async uniqueCheque(code: any) {
        let sql = `SELECT COUNT(*) AS tx FROM pagos  WHERE numCheque = '${code}'`;
        let rrx = await this.queryAll(sql);
        return rrx[0].tx;
    }

    public async pagosInforme(fecha: any) {
        let sql = `SELECT * FROM pagos p WHERE p.fecha = '${fecha}'`;
        return await this.queryAll(sql);
    }

    public async find(id: any) {
        let sql = `SELECT * FROM pagos WHERE documentoPagoId = '${id}'`;
        return await this.queryAll(sql);
    }

    public async findDoc(id: any) {
        let sql = ` SELECT * FROM documentopago dp INNER JOIN pagos p ON dp.cod = p.documentoPagoId WHERE p.documentoId = '${id}' AND p.anulado = 0`;
        console.log("sql ", sql);

        return await this.queryAll(sql);
    }

    public async findDocAnulacion(id: any) {
        let sql = ` SELECT * FROM documentopago dp INNER JOIN pagos p ON dp.cod = p.documentoPagoId WHERE dp.cod = '${id}' `; //AND p.estado = 0
        console.log("find anulacion sql ", sql);

        return await this.queryAll(sql);
    }

    public async findAll() {
        let sql = `SELECT * FROM pagos`;
        //let sql = `SELECT p.*, p.documentoPagoId as recibo FROM pagos p WHERE `;
        return await this.queryAll(sql);
    }

    /* public async findAllPagos(code: string) {
        let sql = `SELECT p.*, v.DocumentTotalDetallePay, v.DocumentTotal, v.DocumentdescuentoTotal, 
                               v.DocumentTotalPay, v.pago, v.saldox
                   FROM pagos p INNER JOIN v_documentoview9 v ON v.cod = p.documentoId 
                   WHERE p.documentoPagoId = '${code}'; `;
        return await this.queryAll(sql);
    } */
    public async findAllPagos(code: string) {
        let sql = `SELECT  p.*,fp.coddoc, fp.pagarx, v.DocumentTotalDetallePay, v.DocumentTotal, v.DocumentdescuentoTotal, 
                            v.DocumentTotalPay, v.pago, v.saldox
                    FROM pagos p 
                    INNER JOIN facturasPagos fp ON fp.recibo = p.documentoPagoId
                    INNER JOIN v_documentoview9 v ON v.cod = fp.cod 
                   WHERE p.documentoPagoId = '${code}'; `;
        console.log("sql data findallpagos", sql);
        return await this.queryAll(sql);
    }

    public async findAllVISTA(code: string) {
        let sql = `SELECT  p.*,fp.coddoc, fp.pagarx, v.DocumentTotalDetallePay, v.DocumentTotal, v.DocumentdescuentoTotal, 
                            v.DocumentTotalPay, v.pago, v.saldox
                    FROM pagos p 
                    INNER JOIN facturasPagos fp ON fp.recibo = p.documentoPagoId
                    INNER JOIN v_documentoview9 v ON v.cod = fp.cod 
                   WHERE p.documentoPagoId = '${code}'; `;
        console.log("sql data findallpagos", sql);
        return await this.queryAll(sql);
    }


    public async findAllPagosexxis(fecha: string, fechamax: string) {
        let sql = `SELECT p.*, v.DocumentTotalDetallePay, v.DocumentTotal, v.DocumentdescuentoTotal, 
                               v.DocumentTotalPay, v.pago, v.saldox
                   FROM pagos p INNER JOIN v_documentoview9 v ON v.cod = p.documentoId 
                   WHERE p.fecha BETWEEN '${fecha}' AND '${fechamax}' AND CAST(p.monto AS decimal) < CAST(v.DocumentTotalPay AS decimal);`;
        return await this.queryAll(sql);
    }

    public async pagoscontadoexxis(fecha: string, fechamax: string) {
        let sql = `SELECT IFNULL(SUM(p.monto),0) AS contado, IFNULL(COUNT(*),0) AS cantidad  FROM pagos p INNER JOIN v_documentoview9 v ON v.cod = p.documentoId 
                   WHERE p.fecha BETWEEN '${fecha}' AND '${fechamax}' AND v.PayTermsGrpCode = '-1';`;
        return await this.queryAll(sql);
    }

    public async pagoscreaditoexxis(fecha: string, fechamax: string) {
        let sql = `SELECT IFNULL(SUM(v.DocumentTotalPay),0) AS total, IFNULL(COUNT(*),0) AS cantidad
                   FROM v_documentoview9 v WHERE v.CreateDate BETWEEN '${fecha}' AND '${fechamax}' AND v.origen = 'inner' AND v.DocType = 'DFA' AND v.PayTermsGrpCode != '-1';`;
        return await this.queryAll(sql);
    }
    public async getNumeracion() {
        let sql = `select max(correlativo) as numeracion from xmf_cabezera_pagos`;
        return await this.queryAll(sql);
    }

    public async pagoscuentaexxis(fecha: string, fechamax: string) {
        let sql = `SELECT IFNULL(SUM(monto),0) AS total, IFNULL(COUNT(*),0) AS contador FROM pagos WHERE fecha BETWEEN '${fecha}' AND '${fechamax}' AND documentoId = '0';`;
        return await this.queryAll(sql);
    }

    public async pagosCieerediario(fecha: string, fechamax: string) {
        let sqlx = `SELECT currency, formaPago, IFNULL(SUM(monto),0) AS monto, monedaDolar FROM pagos p INNER JOIN v_documentoview9 v ON v.cod = p.documentoId 
                   WHERE p.fecha BETWEEN '${fecha}' AND '${fechamax}' AND monedaDolar = 0 GROUP BY formaPago`;
        let nodolar: any = await this.queryAll(sqlx);
        let sql = `SELECT currency, 'PEFX' AS formaPago, IFNULL(SUM(monto),0) AS monto, IFNULL(SUM(monedaDolar), 0) AS monedaDolar FROM pagos p INNER JOIN v_documentoview9 v ON v.cod = p.documentoId 
                   WHERE p.fecha BETWEEN '${fecha}' AND '${fechamax}' AND monedaDolar != 0`;
        let dolar = await this.queryAll(sql);
        return nodolar.concat(dolar);
    }

    public async pagosPagosrealizados(fecha: string, fechamax: string) {
        let sql = `SELECT r.*, SUM(r.monto) AS total FROM pagosRealizados r 
                   WHERE r.fecha BETWEEN '${fecha}' AND '${fechamax}' AND (formaPago = 'Cheque' OR formaPago = 'Transferencia' OR formaPago = 'Tarjeta') GROUP BY r.codigo;`;
        return await this.queryAll(sql);
    }

    public async findAllPagosCuenta(code: string) {
        let sql = `SELECT p.* FROM pagos p WHERE p.documentoPagoId = '${code}'; `;
        return await this.queryAll(sql);
    }
    public async findAllPagosUNO() {
        let sql = `SELECT * FROM documentopago; `;
        return await this.queryAll(sql);
    }

    public async updatePagos(xid: number, id: number, estado: any, anulado: any, control: any) {
        let sql = `UPDATE pagos SET estado = ${estado}, anulado = ${anulado} WHERE id = ${id}`; //SET estado = ${xid}
        console.log("updatePagos sql ", sql);


        let rx = await this.executeSQL(sql);
        let sql1 = `UPDATE xmf_cabezera_pagos SET estado='${estado}' WHERE id = '${id}' `;

        let rx1 = await this.executeSQL(sql1);

        if (anulado == 6) {
            let xsql = `UPDATE documentopago SET estado = 1 WHERE cod = ${control};`;
            let sql = `UPDATE pagos SET estado = 1 WHERE id = ${id}`;
            console.log("xsql ", xsql);
            await this.executeSQL(xsql);
        }

        return rx;
    }


    
    public async actualizaiddocumento(cod: any,cod_and: any) {
        console.log("DEVD actualizaiddocumento()");
        let sql = `UPDATE pagos SET documentoId='${cod}' WHERE documentoId = '${cod_and}' `;

        let sql1 = `UPDATE xmf_cabezera_pagos SET documentoId='${cod}' WHERE documentoId = '${cod_and}' `;

        let sql2 = `UPDATE xmf_medios_pagos SET documentoId='${cod}' WHERE documentoId = '${cod_and}' `;

        let sql3 = `UPDATE xmf_facturas_pagos SET documentoId='${cod}' WHERE documentoId = '${cod_and}' `;

        console.log("sql update actualizacion", sql);
        await this.executeSQL(sql);

        console.log("sql update actualizacion", sql1);
        await this.executeSQL(sql1);

        console.log("sql update actualizacion", sql2);
        await this.executeSQL(sql2);

        console.log("sql update actualizacion", sql3);
        return await this.executeSQL(sql3);
    }
    


    public async updateCorrelativo(nuevoCodigo: string, codigoAnterior, correlativo: number) {
        let sql1 = `UPDATE pagos SET correlativo = ${Number(correlativo)}, documentoPagoId = '${nuevoCodigo}' WHERE documentoPagoId = '${codigoAnterior}'`; //SET estado = ${xid}
        console.log("updatePagos sql ", sql1);

        let sql2 = `UPDATE documentopago SET cod = ${nuevoCodigo} WHERE cod = '${codigoAnterior}'`; //SET estado = ${xid}
        console.log("updatePagos sql ", sql2);


        // let sql = `UPDATE pagos SET estado = ${xid}, anulado = 1 WHERE id = ${id}`;
        let rx1 = await this.executeSQL(sql1);

        let rx2 = await this.executeSQL(sql2);


        return rx1;
    }


    public selectIdPago(idDocPedido: any) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT p.*, p.documentoPagoId as recibo
             FROM pagos p WHERE p.estado = 0 and p.documentoId='${idDocPedido}'`;
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++) {
                    let arx: any = data.rows.item(i);

                    arr.push(arx);
                }
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public async selectPagosExport(user: any, equipo: any, empleado: any, codeDocument = 0) {
        console.log("selectPagosExport()")
        return new Promise((resolve, reject) => {
            let sql = "";
            if (codeDocument == 0) {
                sql = `SELECT p.*, p.documentoPagoId as recibo, ${equipo} AS equipoId, ${empleado} AS DbtCode FROM pagos p 
                INNER JOIN documentopago pg ON pg.cod = p.documentoPagoId WHERE p.estado = 0 AND p.otpp<>1 LIMIT 1;`;//OR pg.estado = 1  //, pg.estado as anulado  //p.otpp<>1
            } else {
                sql = `SELECT p.*, p.documentoPagoId as recibo, ${equipo} AS equipoId, ${empleado} AS DbtCode FROM pagos p 
                INNER JOIN documentopago pg ON pg.cod = p.documentoPagoId WHERE p.estado = 0 AND  p.documentoId='${codeDocument}' AND p.otpp<>1 LIMIT 1;`;//OR pg.estado = 1  //, pg.estado as anulado 
            }
            console.log("sql selectPagosExport", sql);
            this.executeSQL(sql).then(async (data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++) {
                    let arx: any = data.rows.item(i);
                    console.log("each -> ", arx)
                    let dataCadenaSQL = `SELECT * from pagos where documentoPagoId='${arx.recibo}'; `;
                    let dataCadena = await this.queryAll(dataCadenaSQL);
                    console.log("dataCadenaSQL ", dataCadenaSQL);

                    arx.usuario = user;
                    arx.cardCreditNumber = "0";
                    arx.cuota = arx.ncuota;
                    arx.cadenaPago = JSON.stringify(dataCadena);
                    arx.version = environment.version;
                    arr.push(arx);
                }
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async selectPagosExportrecibo(user: any, equipo: any, empleado: any, codeDocument: any) {
        console.log("selectPagosExport()")
        return new Promise((resolve, reject) => {
            let sql = `SELECT p.*, p.documentoPagoId as recibo, ${equipo} AS equipoId, ${empleado} AS DbtCode FROM pagos p 
            INNER JOIN documentopago pg ON pg.cod = p.documentoPagoId WHERE p.estado = 0 AND  p.documentoPagoId='${codeDocument}' AND p.otpp<>1;`;//OR pg.estado = 1  //, pg.estado as anulado 

            console.log("sql selectPagosExport", sql);
            this.executeSQL(sql).then(async (data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++) {
                    let arx: any = data.rows.item(i);
                    console.log("each -> ", arx)
                    let dataCadenaSQL = `SELECT * from pagos where documentoPagoId='${arx.recibo}'; `;
                    //let sql_totalbonif = `SELECT SUM(Quantity * Price) as total from  detalle where idDocumento='${id}' and bonificacion=0`;
                    let dataCadena = await this.queryAll(dataCadenaSQL);
                    console.log("dataCadenaSQL ", dataCadenaSQL);

                    arx.usuario = user;
                    arx.cardCreditNumber = "0";
                    arx.cuota = arx.ncuota;
                    arx.cadenaPago = JSON.stringify(dataCadena);
                    arx.version = environment.version;
                    arr.push(arx);
                }
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public async selectPagosExportCancela(user: any, equipo: any, empleado: any, codeDocument = 0) {
        console.log("selectPagosExport()")
        return new Promise((resolve, reject) => {
            let sql = "";
            if (codeDocument == 0) {
                sql = `SELECT p.*, p.documentoPagoId as recibo, ${equipo} AS equipoId, ${empleado} AS DbtCode FROM pagos p 
                INNER JOIN documentopago pg ON pg.cod = p.documentoPagoId WHERE p.estado = 0;`;//OR pg.estado = 1  //, pg.estado as anulado  //p.otpp<>1
            } else {
                sql = `SELECT p.*, p.documentoPagoId as recibo, ${equipo} AS equipoId, ${empleado} AS DbtCode FROM pagos p 
                INNER JOIN documentopago pg ON pg.cod = p.documentoPagoId WHERE p.estado = 0 AND  p.documentoId='${codeDocument}';`;//OR pg.estado = 1  //, pg.estado as anulado 
            }
            console.log("sql selectPagosExport", sql);
            this.executeSQL(sql).then(async (data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++) {
                    let arx: any = data.rows.item(i);
                    let dataCadenaSQL = `SELECT * from pagos where documentoPagoId='${arx.recibo}'; `;
                    let dataCadena = await this.queryAll(dataCadenaSQL);
                    console.log("dataCadenaSQL ", dataCadenaSQL);

                    arx.usuario = user;
                    arx.cardCreditNumber = "0";
                    arx.cuota = arx.ncuota;
                    arx.cadenaPago = JSON.stringify(dataCadena);
                    arx.version = environment.version;
                    arr.push(arx);
                }
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }




    public selectPagosExportFactura(user: any, equipo: any, empleado: any, codeDocument = 0) {
        console.log()
        return new Promise((resolve, reject) => {
            let sql = "";
            if (codeDocument == 0) {
                sql = `SELECT p.*, p.documentoPagoId as recibo, ${equipo} AS equipoId, ${empleado} AS DbtCode FROM pagos p 
                INNER JOIN documentopago pg ON pg.cod = p.documentoPagoId WHERE p.estado = 0 ;`;//OR pg.estado = 1  //, pg.estado as anulado 
            } else {
                sql = `SELECT p.*, p.documentoPagoId as recibo, ${equipo} AS equipoId, ${empleado} AS DbtCode FROM pagos p 
                INNER JOIN documentopago pg ON pg.cod = p.documentoPagoId WHERE p.estado = 0 AND  p.documentoId='${codeDocument}' AND p.otpp=1;`;//OR pg.estado = 1  //, pg.estado as anulado 
            }
            console.log("sql selectPagosExport", sql);
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++) {
                    let arx: any = data.rows.item(i);
                    arx.usuario = user;
                    arx.cardCreditNumber = "0";
                    arx.cuota = arx.ncuota;
                    arr.push(arx);
                }
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public selectPagosExportByIdPago(user: any, equipo: any, empleado: any, idDocPedido: any = '') {
        return new Promise((resolve, reject) => {
            let sql = `SELECT p.*, p.documentoPagoId as recibo, ${equipo} AS equipoId, ${empleado} AS DbtCode, 
           ifnull(p.checkdate, '1900-01-01') as checkdate, 
           ifnull(p.transferencedate, '1900-01-01') as transferencedate  FROM pagos p WHERE p.estado = 0 and p.documentoId='${idDocPedido}'`;

           console.log(sql);

            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++) {
                    let arx: any = data.rows.item(i);
                    arx.usuario = user;
                    arx.cardCreditNumber = "0";
                    arx.cuota = arx.ncuota;
                    arr.push(arx);
                }
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async selectPagosExportByIdPago2(user: any, equipo: any, empleado: any, idDocPedido: any = '') {
 
        let sql = `SELECT * FROM xmf_cabezera_pagos where estado = 0 and documentoId='${idDocPedido}'`; //LIMIT 1 
        console.log("sql ", sql);

       let Query: any = await this.queryAll(sql);
       let dataResponse: any = [];
       for await (let itm of Query) {
           console.log("DEVD EACH SELECT ITEMS itm ", itm);
           let mediosQuery = await this.selectAllMediosPagoByRecibo(itm.nro_recibo);
           itm.mediosPago = mediosQuery;
           let facturasQuery: any = await this.selectAllFacturasPago(itm.nro_recibo);
           itm.facturaspago = facturasQuery;
           dataResponse.push(itm);
           itm.usuario = user;
           itm.cardCreditNumber = "0";

       }
       return dataResponse;
   }

   
   public async selectPagosExportByIdPago3() {
 
    let sql = `SELECT * FROM xmf_cabezera_pagos where estado = 0`; //LIMIT 1 
    console.log("sql ", sql);
    let Query: any = await this.queryAll(sql);
   
        return Query;
    }


   public async selectAllMediosPagoByRecibo(recibo) {
        let sql = `SELECT * FROM xmf_medios_pagos WHERE nro_recibo = '${recibo}' order by id desc; `;
        console.log("sql ", sql);

        return await this.queryAll(sql);
    }

    public async selectAllcabezerapagosByRecibo(recibo) {
        let sql = `SELECT * FROM xmf_cabezera_pagos WHERE nro_recibo = '${recibo}' order by id desc; `;
        console.log("sql ", sql);

        return await this.queryAll(sql);
    }

public async selectAllFacturasPago(recibo) {
    let sql = `SELECT * FROM xmf_facturas_pagos  WHERE nro_recibo = '${recibo}' order by id desc; `;
    console.log("sql ", sql);

    return await this.queryAll(sql);
}
    

    public async insert(data: any, tick = true) {

        console.log("insert data ", data);
        let tiempo = moment().format('YYYY-MM-DD');
        let hora = moment().format('h:mm:ss');
        let tp = 0;
        (typeof data.tipoCambioDolar == "undefined") ? tp = 0 : tp = data.tipoCambioDolar;
        if (isNaN(tp)) {
            tp = 0;
        }

        //let sqlvalid = `SELECT * FROM pagos  WHERE fecha = '${tiempo}' AND documentoPagoId = '${data.documentoPagoId}' `; //SET estado = ${xid}

        let sqlvalid = `SELECT * FROM pagos  WHERE documentoPagoId = '${data.documentoPagoId}' `; //SET estado = ${xid}

        console.log("DEVD sqlvalid sql ", sqlvalid);
        // let sql = `UPDATE pagos SET estado = ${xid}, anulado = 1 WHERE id = ${id}`;
        let Validexist: any = await this.queryAll(sqlvalid);
        console.log("DEVD Validexist ", Validexist);
        /*  if(data.dx=="cuenta"){
              Validexist=[];
          }*/
        let sql: any;
        if (Validexist.length == 0) {
            sql = `INSERT INTO pagos VALUES(NULL,
        '${data.documentoId}', 
        '${data.clienteId}', 
        '${data.formaPago}',
        '${tp}', 
        '${data.moneda}', 
        ${data.monto},
           '${data.numCheque}', 
           '${data.numComprobante}',
          '${data.numTarjeta}',
           '${data.numAhorro}', 
           '${data.numAutorizacion}',
            '${data.bancoCode}', 
            '${data.ci}',
          '${tiempo}', 
          '${hora}',
           ${data.cambio}, 
           ${data.monedaDolar}, 
           ${data.monedaLocal}, 
           0, 
           ${data.tipo},
           '${data.documentoPagoId}',
          '${data.dx}',
          ${data.otpp},
          '${data.centro}',
          '${data.baucher}', 
          ${data.ncuota}, 
          '${data.checkdate}', 
          '${data.transferencedate}', 
          0, 
          ${localStorage.getItem("idSession")},
          '${data.CreditCard}',
          ${data.correlativo}
          );`;

            console.log("sql insert pago  ", sql);
            if (tick == false) {
                let clientes = new Clientes();
                await clientes.updateAddAnticipos(data.monto, data.clienteId);
            }
            return await this.executeRaw(sql);
        } else {
            console.log("DUPLICADO PAGO ", data);
            sql = true;
            return sql;
        }


    }
    public async insertDetailFacturasAux(facturas) {
        let facturasPagosModel: FacturasPagos = new FacturasPagos();
        let modelDocumentos = new Documentos();

        console.log("listo para el seteo ", facturas);
        if (typeof facturas != "undefined") {
            console.log("seteo de facturas ", facturas);
            for (var value of facturas) {
                //auxArray.push(value);
                let documentoAux = await modelDocumentos.findOneDocEntry(value.cod, value.docentry, value.cuota);
                console.log("data insert value", value);
                console.log("data insert documentoAux", documentoAux);
                facturasPagosModel.insert(value, documentoAux);
                try {
                    await modelDocumentos.updateSaldoFacturasSap(value);
                } catch (error) {
                    console.log("error al restar el saldo", error);
                }

            }

        }

    }

    public async anularpago(cod: string, tick: any, CardCode: any, monto: any) {
        let sql = `UPDATE pagos SET anulado = 1, estado=0  WHERE documentoPagoId = '${cod}'`;
        if (tick == true) {
            let clientes = new Clientes();
            await clientes.updateRmAnticipos(monto, CardCode);
        }
        console.log("sql ", sql);
        return await this.executeSQL(sql)
    }


    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS pagos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public async selectAll() {
        let sql = `SELECT * FROM pagos order by id desc; `;
        return await this.queryAll(sql);
    }

    public async selectAllF() {
        let sql = `SELECT * FROM facturasPagos order by id desc; `;
        return await this.queryAll(sql);
    }
    public async selectOne(cod) {
        let sql = `SELECT * FROM facturasPagos where recibo = '${cod}' `;
        console.log("sql facturasPagos ", sql)
        return await this.queryAll(sql);
    }

    public async selectAllDocs() {
        let sql = `SELECT * FROM documentopago order by id desc; `;
        return await this.queryAll(sql);
    }



    public async selectlogs() {
        let sql = `    SELECT execquery.last_execution_time AS [Date Time], execsql.text AS [Script],DB_Name(execsql.dbid) as BD
        FROM sys.dm_exec_query_stats AS execquery
        CROSS APPLY sys.dm_exec_sql_text(execquery.sql_handle) AS execsql
       
        ORDER BY execquery.last_execution_time DESC; `;
        return await this.queryAll(sql);// WHERE execsql.dbid = DB_ID('NombreDeMiBaseDatos') --nombre de la bd que se quiere monitorear
    }
    /// funciones reportes nueva clase pagos

    // mau reportes
    public async resumenCaja(date){
        let $sql="  Select Sum(monto_total) as total,count(*) as cantidad,otpp,estado,cancelado,moneda,fecha from xmf_cabezera_pagos where fecha='"+date+"' and estado=3 and  cancelado=0 group by otpp,estado,cancelado,moneda,fecha ";
        return await this.queryAll($sql);
    }
    public async resumenCajamedios(date){
        let $sql="  Select Sum(M.monto) as total,M.formaPago,count(*) as cantidad,P.estado,P.cancelado,P.moneda from xmf_medios_pagos M left join xmf_cabezera_pagos P  on M.idcabecera=P.id where P.fecha='"+date+"' and  P.estado=3 and P.cancelado=0  group by M.formaPago,P.moneda ";
        //let $sql="  Select * from xmf_medios_pagos ";
        console.log("sql :", $sql);
        return await this.queryAll($sql);
    }
    public async resumenCajatotal(date){
        let $sql="  Select Sum(monto_total) as total,count(*) as cantidad,estado,cancelado,moneda,fecha from xmf_cabezera_pagos where fecha='"+date+"' and estado=3 and  cancelado=0 ";
        console.log("sqlfile -->",$sql);
        return await this.queryAll($sql);
    }
    public async detalleCajaFacturas(date){
        let $sql = "select * from xmf_cabezera_pagos where otpp=1 and fecha='"+date+"'";
        let $dataRelacion = [{
            table: "xmf_medios_pagos",
            relationshipFieldPrin: "id",
            relationshipFieldSeg: "idcabecera"
        }
        ];
        return await this.queryAllByDetalle($sql, $dataRelacion);
    }
    public async detalleCajaDeuda(date){
        let $sql = "select * from xmf_cabezera_pagos where otpp=2 and fecha='"+date+"'";
        let $dataRelacion = [{
            table: "xmf_medios_pagos",
            relationshipFieldPrin: "id",
            relationshipFieldSeg: "idcabecera"
        },
        {
            table: "xmf_facturas_pagos",
            relationshipFieldPrin: "id",
            relationshipFieldSeg: "idcabecera"
        }
        ];
        return await this.queryAllByDetalle($sql, $dataRelacion);
    }
    public headerDetail = async() =>{ 
        let $sql=" select * from xmf_medios_pagos GROUP BY formaPago";
        //let $sql="  Select * from xmf_medios_pagos ";
        console.log("sql :", $sql);
        return await this.queryAll($sql);
    }

    public paymentMethodDetail = async(methodPayment, date) =>{ 
        let $sql=`select b.cliente_carcode,b.otpp,IFNULL(c.documentoId,'') as docId,b.estado, IFNULL(b.documentoId,'') as codDocumento,b.cancelado, sum(a.monto) as subTotal, a.* from xmf_medios_pagos a
        inner join xmf_cabezera_pagos b on b.nro_recibo=a.nro_recibo
        left join xmf_facturas_pagos c on c.nro_recibo=a.nro_recibo
        where a.formaPago='${methodPayment}'
        and a.fecha='${date}' and b.estado = 3 and b.cancelado = 0
        GROUP BY a.nro_recibo, a.formaPago`;
        //let $sql="  Select * from xmf_medios_pagos ";
        console.log("sql :", $sql);
        return await this.queryAll($sql);
    }

    public paymentMethodDetailFall = async(methodPayment, date) =>{ 
        let $sql=`select b.cliente_carcode,b.otpp,IFNULL(c.documentoId,'') as docId, b.estado,IFNULL(b.documentoId,'') as codDocumento,b.cancelado, sum(a.monto) as subTotal, a.* from xmf_medios_pagos a
        inner join xmf_cabezera_pagos b on b.nro_recibo=a.nro_recibo
        left join xmf_facturas_pagos c on c.nro_recibo=a.nro_recibo
        where a.formaPago='${methodPayment}'
        and a.fecha='${date}' and b.estado = 2 and b.cancelado = 0
        GROUP BY a.nro_recibo, a.formaPago`;
        //let $sql="  Select * from xmf_medios_pagos ";
        console.log("sql :", $sql);
        return await this.queryAll($sql);
    }
    /**
     * 
     * @param methodPayment PBT|PCC|PCH|PEF
     * @param date YYY-MM-DD
     * @returns 
     */
    public paymentMethodDetailUsd = async(methodPayment,date) =>{ 
        let $sql=`select b.cliente_carcode, b.otpp,IFNULL(c.documentoId,'') as docId,b.estado, IFNULL(b.documentoId,'') as codDocumento,b.cancelado, sum(a.monto) as subTotal, a.* from xmf_medios_pagos a
        inner join xmf_cabezera_pagos b on b.nro_recibo=a.nro_recibo
        left join xmf_facturas_pagos c on c.nro_recibo=a.nro_recibo
        where a.formaPago='${methodPayment}' and  a.monedaDolar<>0 and a.monedaDolar is not  null
        and a.fecha='${date}' and b.estado = 3 and b.cancelado = 0
        GROUP BY a.nro_recibo, a.formaPago`;
        //let $sql="  Select * from xmf_medios_pagos ";
        console.log("sql :", $sql);
        return await this.queryAll($sql);
    }

    public paymentMethodDetailUsdfall = async(methodPayment,date) =>{ 
        let $sql=`select b.cliente_carcode, b.otpp,IFNULL(c.documentoId,'') as docId,b.estado, IFNULL(b.documentoId,'') as codDocumento,b.cancelado, sum(a.monto) as subTotal, a.* from xmf_medios_pagos a
        inner join xmf_cabezera_pagos b on b.nro_recibo=a.nro_recibo
        left join xmf_facturas_pagos c on c.nro_recibo=a.nro_recibo
        where a.formaPago='${methodPayment}' and  a.monedaDolar<>0 and a.monedaDolar is not  null
        and a.fecha='${date}' and b.estado = 2 and b.cancelado = 0
        GROUP BY a.nro_recibo, a.formaPago`;
        //let $sql="  Select * from xmf_medios_pagos ";
        console.log("sql :", $sql);
        return await this.queryAll($sql);
    }
    

    public async detalleCajaAnticipo(date){
        let $sql = "select * from xmf_cabezera_pagos where otpp=3 and fecha='"+date+"'";
        let $dataRelacion = [{
            table: "xmf_medios_pagos",
            relationshipFieldPrin: "id",
            relationshipFieldSeg: "idcabecera"
        }
        ];
        return await this.queryAllByDetalle($sql, $dataRelacion);
    }

    public async uniquetrasferencia(code: any,bancoCode: any) {
        let sql = `SELECT COUNT(*) AS tx FROM xmf_medios_pagos  WHERE numComprobante = '${code}' and bancoCode='${bancoCode}'`;
        let rrx = await this.queryAll(sql);
        return rrx[0].tx;
    }
    //
}
