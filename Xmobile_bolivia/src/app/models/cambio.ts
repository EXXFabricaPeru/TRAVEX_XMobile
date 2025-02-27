import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Cambio extends Databaseconf {
    public configService: ConfigService;

    public async insertAll(objeto: any, idx: number, contador = 0) {

        if (contador == 0) {
            let sql = 'DELETE FROM cambio;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO cambio VALUES ';
            for (let d of obj.respuesta) {
                sqlz += `(NULL,'${idx}','${d.User}','${d.Status}','${d.DateUpdate}','${d.ExchangeRateFrom}','${d.ExchangeRateTo}','${d.ExchangeRateDate}','${d.ExchangeRate}'),`;
            }
            let sqlx = sqlz.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async select() {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM cambio`;
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

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = ` DROP TABLE IF EXISTS cambio;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
