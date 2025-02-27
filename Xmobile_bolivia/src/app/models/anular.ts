import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Anular extends Databaseconf {
    public configService: ConfigService;

    public select() {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM anular GROUP BY U_TipoAnulacion`;
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

    public async insertAll(objeto: any, idx: number, contador = 0) {

        if (contador == 0) {
            let sql = 'DELETE FROM anular;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO anular VALUES ';
            for (let o of obj.respuesta) {
                sql += ` (NULL, '${o.id}', '${o.Code}', '${o.Name}', '${o.U_TipoAnulacion}', '${o.User}', '${o.Status}', '${o.DateUpdate}'),`;
            }
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = ` DROP TABLE IF EXISTS anular;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
