import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class FacturasPagos extends Databaseconf {
    public configService: ConfigService;

    public async insertAll(objeto: any) {
        return new Promise((resolve, reject) => {
            let sql = 'INSERT INTO facturasPagos VALUES ';
            for (let o of objeto)
                sql += `(NULL,'${o.clienteId}','${o.cod}',${o.coddoc},'${o.docentry}', '${o.docnum}','${o.pagarx}','${o.recibo}','${o.cuota}' ),`;
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public async insert(o: any, documentoAux) {
        return new Promise((resolve, reject) => {
            let sql = 'INSERT INTO facturasPagos VALUES ';
            console.log("sql object", o);
            console.log("sql object documentoAux ", documentoAux);
            sql += `(NULL,'${o.clienteId}','${o.cod}','${o.coddoc}','${o.docentry}', '${o.docnum}',${Number(o.pagarx)},'${o.recibo}', '${documentoAux[0].CardName}', '${(Number(documentoAux[0].saldo) - Number(o.pagarx)).toFixed(2)}', '${documentoAux[0].codexternal}', '${documentoAux[0].DocTotal}','${documentoAux[0].cuota}' );`;
            // let sqlx = sql.slice(0, -1);
            console.log("sql insert pagaos", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findByRecibo(recibo: string) {
        let sql = `SELECT * FROM facturasPagos WHERE recibo  = '${recibo}';`;
        console.log(sql);
        return await this.queryAll(sql);
    }

    public async findByReciboDoc(recibo: string, cod: string) {
        let sql = `SELECT * FROM facturasPagos WHERE recibo  = '${recibo}' AND cod='${cod}' ;`;
        return await this.queryAll(sql);
    }


    public async all() {
        let sql = `SELECT * FROM facturasPagos `;
        return await this.queryAll(sql);
    }


    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS facturasPagos`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
