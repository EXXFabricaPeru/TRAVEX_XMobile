import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Documentopago extends Databaseconf {
    public configService: ConfigService;

    public async viewPagos() {
        let sqlexe = `
           CREATE VIEW IF NOT EXISTS pagosRealizados AS SELECT 
            pg.id as id,
            pg.cod AS codigo, 
            pg.tipo AS tipo, 
            pg.fecha || " " || pg.hora AS hora, 
            p.fecha AS fecha, 
            p.anulado AS anulado,
            p.monto AS monto,
            p.idUser, 
            p.bancoCode AS bancoCode, 
            p.documentoId AS documentoId,  
            p.tipoCambioDolar AS tipoCambioDolar,  
            p.dx AS dx,  
            (SELECT dx.U_LB_NumeroFactura FROM documentos dx WHERE dx.cod = p.documentoId) AS numeracion,
            (SELECT dx.U_LB_NumeroAutorizac FROM documentos dx WHERE dx.cod = p.documentoId) AS authorizacion,
            (SELECT dx.U_LB_FechaLimiteEmis FROM documentos dx WHERE dx.cod = p.documentoId) AS fechalim,
            (SELECT dx.currency FROM documentos dx WHERE dx.cod = p.documentoId) AS currency,
            p.moneda AS currencyAC,  
            CASE p.formaPago
                 WHEN 'PEF' THEN 'Efectivo'
                 WHEN 'PCC' THEN 'Tarjeta'
                 WHEN 'PBT' THEN 'Transferencia'
                 WHEN 'PCH' THEN 'Cheque'
            END formaPago,
            p.documentoPagoId AS cod,
            p.clienteId AS CardCode,
            p.estado AS estado,
            (SELECT cli.CardName FROM clientes cli WHERE cli.CardCode = p.clienteId) AS CardName
          FROM pagos p INNER JOIN documentopago pg ON pg.cod = p.documentoPagoId;`;
        return await this.executeSQL(sqlexe);
    }

    // public async findPagos(textsearch: string) {
    //     let text = '';
    //     if (textsearch != '')
    //         text = ` AND codigo LIKE '%${textsearch}%' OR CardCode LIKE '%${textsearch}%' OR CardName LIKE '%${textsearch}%' OR hora LIKE '%${textsearch}%'`;
    //     let sql = `SELECT pago.*, (CASE pago.dx 
    //                         WHEN 'cuenta' THEN printf("%.2f",pago.monto,2) 
    //                         WHEN 'factura' THEN printf("%.2f",SUM(pago.monto),2) 
    //                         WHEN 'pedido' THEN printf("%.2f",SUM(pago.monto),2) 
    //                         WHEN 'oferta' THEN printf("%.2f",SUM(pago.monto),2) 
    //                         END) AS total, 
    //                       COUNT(pago.cod) AS num FROM pagosRealizados   pago WHERE pago.idUser=${localStorage.getItem("idSession")}${text}  
    //                       GROUP BY pago.cod ORDER BY pago.id DESC LIMIT 20;`;
    //     console.log("buscar sql ", sql);
    //     return await this.queryAll(sql);
    // }
    public async findPagos(textsearch: string) {
        let text = '';
        if (textsearch != '')
            text = ` WHERE codigo LIKE '%${textsearch}%' OR CardCode LIKE '%${textsearch}%' OR CardName LIKE '%${textsearch}%' OR hora LIKE '%${textsearch}%'`;
        let sql = `SELECT pago.*,(CASE WHEN pagosfacturas.countfact IS NOT NULL
            THEN pagosfacturas.countfact
            ELSE COUNT(pago.cod) end) as num, (CASE pago.dx 
                            WHEN 'cuenta' THEN printf("%.2f",pago.monto,2) 
                            WHEN 'factura' THEN printf("%.2f",SUM(pago.monto),2)
                            WHEN 'FACTURAS' THEN printf("%.2f",SUM(pago.monto),2)
                            WHEN 'facturas' THEN printf("%.2f",SUM(pago.monto),2) 
                            WHEN 'pedido' THEN printf("%.2f",SUM(pago.monto),2) 
                            WHEN 'oferta' THEN printf("%.2f",SUM(pago.monto),2) 
                            END) AS total
                            
                          FROM pagosRealizados pago
                          left join(
                            select count(*) as countfact, recibo from facturasPagos group by recibo
                            ) pagosfacturas on pago.cod=pagosfacturas.recibo	
                          ${text}
                          GROUP BY pago.cod  ORDER BY pago.fecha DESC LIMIT 20;`;
        console.log("buscar sql ", sql);
        return await this.queryAll(sql);
    }

    public async pagosexportCantidad() {
        let sql = `SELECT pago.* FROM pagosRealizados pago WHERE estado = 0 AND tipo<>"factura"`;
        return await this.queryAll(sql);
    }

    /*********************/
    public async insert(data: any) {
        let tiempo = this.getFechaPicker();
        let hora = this.getHoraCurrent();
        let sqlvalid = `SELECT * FROM documentopago  WHERE fecha = '${tiempo}' AND cod = '${data.cod}' `; //SET estado = ${xid}
        console.log("DEVD sqlvalid sql ", sqlvalid);
        // let sql = `UPDATE pagos SET estado = ${xid}, anulado = 1 WHERE id = ${id}`;
        let Validexist: any = await this.queryAll(sqlvalid);
        console.log("DEVD Validexist ", Validexist);
        let sql: any;

        if (Validexist.length == 0) {

            sql = `INSERT INTO documentopago VALUES(NULL,'${data.cod}','${tiempo}', '${hora}','${data.closa}','${data.tipo}',0);`;
            console.log("sql master ", sql);
            return await this.executeRaw(sql);

        } else {
            console.log("DEVD ERROR Pago cabezera duplicado ", data);
            sql = true;
            return sql;
        }


    }

    public async find(id: any) {
        let sql = `SELECT * FROM documentopago WHERE id = ${id} `;
        return await this.queryAll(sql);
    }

    public async findOne(id: any) {
        let sql = `SELECT * FROM documentopago WHERE cod = ${id}`;
        console.log("sql documentopago ", sql)
        return await this.queryAll(sql);
    }

    public async anulardocumento(cod: string, fecha: string) {
        let sql = `UPDATE documentopago SET estado = 0 WHERE cod = '${cod}' AND fecha = '${fecha}'`;
        console.log
            (" anulardocumento sql ", sql);
        return await this.executeSQL(sql);
    }

    public async getPgoAnuladoDoc(cod: string, fecha: string) {
        let sql = `SELECT * FROM documentopago  WHERE cod = '${cod}' AND fecha = '${fecha}'  AND estado = 0  `;
        return await this.queryAll(sql);
    }

    public async drop() {
        let sql = `DROP TABLE IF EXISTS documentopago`;
        return await this.executeSQL(sql);
    }
    public async alldocPagos() {

        let sql = `SELECT * FROM documentopago  order by id desc `;
        return await this.queryAll(sql);
    }
    public async allPagos() {
        let sql = `SELECT * FROM pagos order by id desc `;
        return await this.queryAll(sql);
    }
    public async selectlogs() {
        let sql = `    SELECT execquery.last_execution_time AS [Date Time], execsql.text AS [Script],DB_Name(execsql.dbid) as BD
        FROM sys.dm_exec_query_stats AS execquery
        CROSS APPLY sys.dm_exec_sql_text(execquery.sql_handle) AS execsql
       
        ORDER BY execquery.last_execution_time DESC; `;
        return await this.queryAll(sql);// WHERE execsql.dbid = DB_ID('NombreDeMiBaseDatos') --nombre de la bd que se quiere monitorear
    }

}
