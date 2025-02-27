import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Dosificacionproductos extends Databaseconf {
    public configService: ConfigService;



    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM dosificacionproductos;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO dosificacionproductos VALUES ';
            for (let d of obj.respuesta) {
                sql += ` (NULL, '${d.nombre}', '${d.id}'),`;
            }
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async find() {
        let sql = `SELECT * FROM dosificacionproductos;`;
        return await this.queryAll(sql);
    }
}
