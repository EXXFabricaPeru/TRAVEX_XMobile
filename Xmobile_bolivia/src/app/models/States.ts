import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import { Lotesproductos } from "./lotesproductos";

export class States extends Databaseconf {
    public configService: ConfigService;

    
    
    async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM States';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO States VALUES ';
            for (let o of obj.respuesta) {
                sqlz += ` (NULL, '${o.Code}', '${o.Country}', '${o.Name}'),`;
            }
            let sqlx = sqlz.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async selectAll() {
        let sql = `SELECT * from States`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS States;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}