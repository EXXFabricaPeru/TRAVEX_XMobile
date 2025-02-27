import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class PerFexAfectacionIgv extends Databaseconf {
    public configService: ConfigService;

    public async selectPerfexAceptacionIgv() {
        let sql = `SELECT * FROM perFeTaigv`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM perFeTaigv';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO perFeTaigv VALUES ';
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.Code}', '${o.Name}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

    public findByCode(code: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM perFeTaigv WHERE code = '${code}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS perFeTaigv;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
  
}
