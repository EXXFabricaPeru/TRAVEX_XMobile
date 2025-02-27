import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Cuotas extends Databaseconf {
    public configService: ConfigService;

    public async insertAll(objeto: any) {
        return new Promise((resolve, reject) => {
            let sql = 'INSERT INTO cuotasfactura VALUES ';
            for (let o of objeto)
                sql += `(NULL,'${o.iddocpedido}','${o.DueDate}',${o.Percentage},${o.Total}, ${o.InstallmentId},0,'${o.fecharegistro}','0'),`;
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async find(cod: any) {
        let sql = `SELECT * FROM cuotasfactura WHERE iddocpedido  = '${cod}';`;
        return await this.queryAll(sql);
    }


    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS cuotasfactura`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
