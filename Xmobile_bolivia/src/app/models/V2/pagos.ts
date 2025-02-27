import { Databaseconf } from "../databaseconf";
import { ConfigService } from "../config.service";
import { IPagos } from '../../types/IPagos';
import {Camposusuario} from "../../services/camposusuario.service";


export class Pagos extends Databaseconf {
    public configService: ConfigService;
    public Camposusuario: Camposusuario;

    /**
     * INSERT DE PAGOS MEDIOS DE PAGO Y PAGOS FACTURAS
     */
    public async insert(data: IPagos, status: number, nro_recibo: string = '0') {
        console.log("insert data ", data);
        console.log("insert status ", status);
        try {

            let campos = new Camposusuario();
            let sql2 = await campos.camposusuario(data,6);
            let sqlCabezera: any;
            /*sqlCabezera = `insert into xmf_cabezera_pagos 
            (
                    nro_recibo, 
                    correlativo, 
                    usuario,
                    documentoId,
                    fecha, 
                    hora, 
                    monto_total, 
                    tipo, 
                    otpp, 
                    tipo_cambio, 
                    moneda, 
                    cliente_carcode,
                    razon_social,
                    nit,
                    equipo,
                    latitud,
                    longitud,
                    estado,
                    cancelado
                )
                VALUES
                (
                '${nro_recibo}',
                '${data.correlativo}', 
                '${data.usuario}', 
                '${data.documentoId}', 
                '${data.fecha}', 
                '${data.hora}', 
                '${data.monto_total}',
                '${data.tipo}', 
                '${data.otpp}',
                '${data.tipo_cambio}',
                '${data.moneda}', 
                '${data.cliente_carcode}', 
                '${data.razon_social}',
                '${data.nit}', 
                '${data.equipo}', 
                '${data.latitud}', 
                '${data.longitud}', 
                '${status}',
                ${data.cancelado?data.cancelado:0}
            );`;*/

            sqlCabezera = `insert into xmf_cabezera_pagos VALUES
            (   NULL, 
                '${nro_recibo}',
                '${data.correlativo}', 
                '${data.usuario}', 
                '${data.documentoId}', 
                '${data.fecha}', 
                '${data.hora}', 
                '${data.monto_total}',
                '${data.tipo}', 
                '${data.otpp}',
                '${data.tipo_cambio}',
                '${data.moneda}', 
                '${data.cliente_carcode}', 
                '${data.razon_social}',
                '${data.nit}', 
                '${status}',
                '${data.equipo}', 
                '${data.latitud}', 
                '${data.longitud}', 
                '${data.cancelado?data.cancelado:0}'`+sql2+`);`
            console.log("sqlCabezera ", sqlCabezera);

            await this.executeRaw(sqlCabezera);
            let $sql_ultid = "SELECT id from xmf_cabezera_pagos order by id DESC limit 1";
            let idcabecera: any = await this.queryAll($sql_ultid);

            let sqlMedios: any;

            for await (let mediosPago of data.mediosPago){

                let sql21 = await campos.camposusuario(mediosPago,7);
                let sql22 = await campos.camposusuario(mediosPago,8);
                let sql23 = await campos.camposusuario(mediosPago,9);
                let sql24 = await campos.camposusuario(mediosPago,10);
                let sql25 = await campos.camposusuario(mediosPago,11);

                console.log("")

                sqlMedios = `insert into xmf_medios_pagos VALUES(NULL,
                    '${nro_recibo}',
                    '',
                    '${mediosPago.formaPago}', 
                    '${mediosPago.monto}', 
                    '${mediosPago.numCheque}', 
                    '${mediosPago.numComprobante}',
                    '${mediosPago.numTarjeta}',
                    '${mediosPago.bancoCode}', 
                    '${mediosPago.fecha}',
                    '${mediosPago.cambio}',
                    '${mediosPago.monedaDolar}', 
                    '${mediosPago.monedaLocal}', 
                    '${mediosPago.centro}', 
                    '${mediosPago.baucher}', 
                    '${mediosPago.checkdate}', 
                    '${mediosPago.transferencedate}', 
                    '${mediosPago.CreditCard}',
                    '${idcabecera[0].id}',
                    '${mediosPago.NumeroTarjeta}',
                    '${mediosPago.NumeroID}',
                    '${mediosPago.emitidoPor}',
                    '${mediosPago.tipoCheque}',
                    '${mediosPago.dateEmision}'`+sql21+``+sql22+``+sql23+``+sql24+``+sql25+`
                );`;

                console.log("sqlMedios ", sqlMedios);

                await this.executeRaw(sqlMedios);
            }
            console.log("para el insert facturas pagos ", data.facturaspago);

            if (data.facturaspago) {
                for (var value of data.facturaspago) {
                    //auxArray.push(value);
                    console.log("each fp", value);

                    await this.insertFacturasPagos(value, idcabecera[0].id, nro_recibo);
                    try {
                        if(data.estado == 3){
                            
                            await this.updateSaldoFacturasSap(value);
                        }
                    } catch (error) {
                        console.log("error al restar el saldo", error);
                    }

                }
            }

            return true;
        } catch (error) {
            console.log(error);
            return false;
        }
    }

    /**
     * INSERT DE FACTURAS PAGADAS OTPP 2
     */
    public async insertFacturasPagos(documentoAux, idCabezera, nro_recibo = '0') {
        console.log("documentoAux insert sql  ", documentoAux);
        let sql = `INSERT INTO xmf_facturas_pagos 
            VALUES 
            (NULL,'${documentoAux.clienteId}',
            '${nro_recibo}',
            '${documentoAux.documentoId}',
            '${documentoAux.docentry}', 
            ${Number(documentoAux.monto)},
            '${documentoAux.CardName}', 
            '${(Number(documentoAux.saldo) - Number(documentoAux.monto)).toFixed(2)}', 
            '${documentoAux.nroFactura}', 
            '${documentoAux.DocTotal}',
            '${documentoAux.cuota}',
            '${idCabezera}',
            '${documentoAux.vendedor}'
            );`;
        // let sqlx = sql.slice(0, -1);
        console.log("sql insert pagaos", sql);
        return await this.executeSQL(sql);
    }

    //ACTUALIZAR PAGO A ANULADO (PUEDE SER OTRO ESTADO)
    public async updateEstadoPagoByRecibo(status: number = 0, recibo: string) {
        let sql = "";
        sql = `UPDATE xmf_cabezera_pagos SET estado = '${Number(status)}' WHERE nro_recibo = '${recibo}' `;
        console.log("sql update pago factura", sql);
        return await this.executeSQL(sql);
    }

    /**
     * UPDATE SALDO DE FACTURAS OUTER TPP 2
     */
    public async updateSaldoFacturasSap(data: any) {
        let sql = "";
        sql = `UPDATE documentos SET saldo = saldo-${Number(data.monto)} WHERE cod = '${data.documentoId}' AND cuota =  '${data.cuota}' `;
        console.log("sql update pago factura", sql);
        return await this.executeSQL(sql);
    }
    

    public async updateSaldoFacturas2(data: any) {

        let $sql = "select * from xmf_facturas_pagos where nro_recibo='" + data.nro_recibo + "'";
        let resp: any = await this.queryAll($sql);
        console.log(resp);
        let sql = "";
        sql = `UPDATE documentos SET saldo = saldo-${Number(resp[0].monto)} WHERE cod = '${resp[0].documentoId}'`;
        console.log("sql update pago factura", sql);
        return await this.executeSQL(sql);
    }

    public async updateSaldoFacturasAnula(data: any) {

        let $sql = "select * from xmf_facturas_pagos where nro_recibo='" + data.nro_recibo + "'";
        let resp: any = await this.queryAll($sql);
        let sql = "";
        sql = `UPDATE documentos SET saldo = saldo+${Number(resp[0].monto)} WHERE cod = '${resp[0].documentoId}'`;
        console.log("sql update pago factura", sql);
        return await this.executeSQL(sql);
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

    public async getNumeracion() {
        let sql = `select max(correlativo) as numeracion from xmf_cabezera_pagos`;
        return await this.queryAll(sql);
    }
    
    /**
     * LISTADO DE PAGO CON MEDIOS DE PAGO Y FACTURAS PAGADAS
     */
    public async selectAllCabezera(search: string = '', status = '') {
        console.log("selectAllCabezera status ", status);

        let text = '';
        let and = '';
        if (search != '') {
            text = ` WHERE nro_recibo LIKE '%${search}%' 
            OR cliente_carcode LIKE '%${search}%' 
            OR razon_social LIKE '%${search}%'
            OR fecha LIKE '%${search}%'
             `;
        }
        if (status !== '') {
            if (search !== '') {
                and = ' AND ';
            } else {
                and = ' WHERE  '
            }
            and += ' estado = ' + status + ' ';
        }
        let sql = `SELECT * FROM xmf_cabezera_pagos ${text} ${and} order by id desc ; `; //LIMIT 1 
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

        }
        return dataResponse;

    }


    public async selectAllpagos(search: string = '', status = '') {
        console.log("selectAllpagos status ", status);

 
        let sql = `SELECT * FROM xmf_cabezera_pagos where otpp <> '1'  and estado = '0'  order by id desc ; `; //LIMIT 1 
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

        }
        return dataResponse;

    }

    public async selectAllMediosPagoByRecibo(recibo) {
        let sql = `SELECT * FROM xmf_medios_pagos WHERE nro_recibo = '${recibo}' order by id desc; `;
        console.log("sql ", sql);

        return await this.queryAll(sql);
    }

    public async selectAllMediosPago() {
        let sql = `SELECT * FROM xmf_medios_pagos W order by id desc; `;
        console.log("sql ", sql);

        return await this.queryAll(sql);
    }

    public async selectAllFacturasPago(recibo) {
        let sql = `SELECT * FROM xmf_facturas_pagos  WHERE nro_recibo = '${recibo}' order by id desc; `;
        console.log("sql ", sql);

        return await this.queryAll(sql);
    }

    /// Mau insertar historial pagos
    public async insertAllHistorial(data) {
        await this.deleteAllmedios();
        await this.deleteAllfacturas();
        await this.deleteAllcabecera();
        await this.insertAllcabecera(data);
    }


    public async insertAllcabecera(data) {
        let valores = "";
        let cadena = "";
        let campos = new Camposusuario();
        let session = await campos.consultasesion();

        let $sql = "insert into xmf_cabezera_pagos VALUES";
        /*$sql += `(
            nro_recibo, 
            correlativo, 
            usuario,
            documentoId,   
            fecha, 
            hora, 
            monto_total, 
            tipo, 
            otpp, 
            tipo_cambio, 
            moneda, 
            cliente_carcode,
            razon_social,
            nit,
            estado,
            equipo,
            latitud,
            longitud,
            cancelado             
            
            
            
        )`;
        $sql += " Values ";
        console.log(data);
        console.log(data.length);*/
        for (let i = 0; i < data.length; i++) {
            let elemento = data[i];
            let element = elemento.pagos;

            let sql2 = await campos.camposusuariosinc2(element.camposusuario,6,session);
            console.log("RAFAEL",sql2);

            let $sql_ultid = "";
            let idcabecera: any;
            if (element.cancelado == "null") {
                element.cancelado = 0;
            }
            valores = `(NULL ,
               '${element.nro_recibo}',
               '${element.correlativo}', 
               '${elemento.usuario}', 
               '${element.documentoId}', 
               '${element.fecha}', 
               '${element.hora}', 
               '${element.monto_total}',
               '${element.tipo}', 
               '${element.otpp}',
               '${element.tipo_cambio}',
               '${element.moneda}', 
               '${element.cliente_carcode}', 
               '${element.razon_social}',
               '${element.nit}', 
               '${element.estado}',
               '${element.equipo ? element.equipo : ""}',
               '${element.latitud ? element.latitud : ""}',
               '${element.longitud ? element.longitud : ""}',
               '${element.cancelado ? element.cancelado : 0}'
               `+sql2+`
           )`;
            cadena = $sql + " " + valores;

            console.log(cadena);
            await this.executeSQL(cadena);

            $sql_ultid = "SELECT id from xmf_cabezera_pagos order by id DESC limit 1";
            idcabecera = await this.queryAll($sql_ultid);
            idcabecera = idcabecera[0].id;
            this.insertAllmedios(element.mediosPago, idcabecera);
            if (element.otpp == 2) {
                this.insertAllfacturas(element.facturaspago, idcabecera);
            }

        }

    }

    public async insertAllmedios(data, id) {
        let valores = "";
        let cadena = "";
        let $sql = "insert into xmf_medios_pagos";
        $sql += `(
                formaPago,
                monto,
                numCheque,
                numComprobante,
                numTarjeta,
                bancoCode,
                fecha,
                cambio,
                monedaDolar,
                monedaLocal,
                nro_recibo,
                centro,
                baucher,
                checkdate,
                transferencedate,
                CreditCard,
                idcabecera             
        )`;
        $sql += " Values ";
        console.log(data);
        for (let i = 0; i < data.length; i++) {
            let element = data[i];
            valores += `(
                '${element.formaPago}',
                '${element.monto}', 
                '${element.numCheque}', 
                '${element.numComprobante}', 
                '${element.numTarjeta}', 
                '${element.bancoCode}', 
                '${element.fecha}',
                '${element.cambio}', 
                '${element.monedaDolar}',
                '${element.monedaLocal}',
                '${element.nro_recibo}', 
                '${element.centro}', 
                '${element.baucher}',
                '${element.checkdate}', 
                '${element.transferencedate}',
                '${element.CreditCard}',
                '${id}'
            ),`;
            // exxis sonia 
            // essix marca
        };
        valores = valores.slice(0, -1);
        cadena = $sql + " " + valores;

        console.log("cadena historial pagos medios");
        console.log(cadena);
        return await this.executeSQL(cadena);
    }

    public async insertAllfacturas(data, id) {
        let valores = "";
        let cadena = "";
        let $sql = "insert into xmf_facturas_pagos";
        $sql += `(
            clienteId, 
            nro_recibo, 
            documentoId,
            docentry,   
            monto, 
            CardName, 
            saldo, 
            nroFactura, 
            DocTotal, 
            cuota,
            idcabecera,
            vendedor 
                          
        )`;
        $sql += " Values ";
        console.log(data);
        for (let i = 0; i < data.length; i++) {
            let element = data[i];

            valores += `(
                '${element.clienteId}',
                '${element.nro_recibo}', 
                '${element.documentoId}', 
                '${element.docentry}', 
                '${element.monto}', 
                '${element.CardName}', 
                '${element.saldo}',
                '${element.nroFactura}', 
                '${element.DocTotal}',
                '${element.cuota}',
                '${id}',
                '${element.vendedor}'                
            ),`;

        };
        valores = valores.slice(0, -1);
        cadena = $sql + " " + valores;

        console.log("cadena historial pagos facturas");
        console.log(cadena);
        return await this.executeSQL(cadena);
    }
    public async deleteAllcabecera() {
        console.log("Borrando cabecera pagos");
        let $sql = "delete from xmf_cabezera_pagos where estado=3";
        return await this.executeSQL($sql);
    }
    public async deleteAllmedios() {
        console.log("Borrando pagos medios");
        let $sql = "delete  from xmf_medios_pagos  where nro_recibo in(select nro_recibo from xmf_cabezera_pagos where estado=3);";
        return await this.executeSQL($sql);
    }
    public async deleteAllfacturas() {
        console.log("Borrando pagos facturas");
        let $sql = "delete  from xmf_facturas_pagos where nro_recibo in(select nro_recibo from xmf_cabezera_pagos where estado=3);";
        return await this.executeSQL($sql);
    }
    //fin mau insertar historial pagos
    // Mau ver detalle de pago
    public async consultapago(id) {
        let $sql = "select * from xmf_cabezera_pagos where id='" + id + "'";
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
    public async consultacabecera(recibo) {
        let $sql = "select * from xmf_cabezera_pagos where nro_recibo='" + recibo + "'";
        return await this.executeSQL($sql);
    }
    public async consultaMedios(recibo) {
        let $sql = "select * from xmf_medios_pagos where nro_recibo='" + recibo + "'";
        return await this.executeSQL($sql);
    }
    public async consultaFacturas(recibo) {
        let $sql = "select * from xmf_facturas_pagos where nro_recibo='" + recibo + "'";
        return await this.executeSQL($sql);
    }

    //fin mau ver detalle de pago
    // mau reportes
    public async resumenCaja() {
        let $sql = " Select monto_total,otpp,estado,cancelado,moneda from xmf_cabezera_pagos";
        return await this.executeSQL($sql);
    }
    //
    /**
     * OBTENER LOS PAGOS CON ESTADO CERO(REGISTROS SOLO MOVIL) PARA ENVIARLOS A MIDLEWARE 
     */
    public async pagosPendientesEnvio(status: number = 0) {
        let $sql = "select * from xmf_facturas_pagos where estado='" + status + "'";

        return await this.executeSQL($sql);
    }
    /**
     * Actualiza el estado del pago a eliminado
     */
    public async updatePayCanceled(codRecibo: string) {
        let queryUpdate = `update xmf_cabezera_pagos set cancelado=3 where nro_recibo='${codRecibo}'  `;
        console.log("sql anulacion", queryUpdate);
        return await this.executeSQL(queryUpdate);
    }

    public async uniqueCheque(code: any,bancoCode: any) {
        let sql = `SELECT COUNT(*) AS tx FROM xmf_medios_pagos  WHERE numCheque = '${code}' and bancoCode='${bancoCode}'`;
        let rrx = await this.queryAll(sql);
        return rrx[0].tx;
    }
    
    public async uniquetrasferencia(code: any,bancoCode: any) {
        let sql = `SELECT COUNT(*) AS tx FROM xmf_medios_pagos  WHERE numComprobante = '${code}' and bancoCode='${bancoCode}'`;
        let rrx = await this.queryAll(sql);
        return rrx[0].tx;
    }
}
