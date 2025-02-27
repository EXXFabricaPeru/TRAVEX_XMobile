import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class PerTipoPrecioVenta extends Databaseconf {
    public configService: ConfigService;

    public async selectPerPrecioVenta() {
        let sql = `SELECT * FROM perFeTpvu`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM perFeTpvu';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO perFeTpvu VALUES ';
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.Code}', '${o.Name}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

    public findByCode(code: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM perFeTpvu WHERE code = '${code}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS perFeTpvu;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
  
}
