import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Centrocostos extends Databaseconf {
    public configService: ConfigService;

    public async insertAll(objeto: any, idx: number, contador = 0) {

        if (contador == 0) {
            let sql = 'DELETE FROM centrocostos;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO centrocostos VALUES ';
            for (let o of obj.respuesta) {
                sql += `(NULL, '${o.PrcCode}', '${o.PrcName}'),`;
            }
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findAll() {
        let sql = `SELECT * FROM centrocostos`;
        return await this.queryAll(sql);
    }


    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS centrocostos`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
