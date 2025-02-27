import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class PerTransportista extends Databaseconf {
    public configService: ConfigService;

    public async selectPerTipoOperaciones() {
        let sql = `SELECT distinct CardCode,CardName,LicTradNum FROM perTransportistas`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM perTransportistas';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO perTransportistas VALUES ';
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.CardCode}', '${o.CardName}','${o.LicTradNum}','${o.Address}','${o.Name}','${o.Notes1}','${o.U_EXX_PLAVEH}','${o.U_EXX_MARVEH}','${o.U_EXX_PLATOL}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

    public findByCode(code: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM perTransportistas WHERE code = '${code}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS perTransportistas;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
  
}
