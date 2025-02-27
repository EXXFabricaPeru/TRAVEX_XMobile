import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Bancos extends Databaseconf {
    public configService: ConfigService;

    public async find() {
        let sql = `SELECT * FROM bancos`;
        return await this.queryAll(sql);
    }

    public async findOne() {
        let sql = `SELECT * FROM bancos`;
        return await this.queryAll(sql);
    }


    public async findOnecuenta(cuenta) {
        let sql = `SELECT  * FROM bancos where cuenta = '${cuenta}'`;
        console.log("consulta",sql);
        
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS bancos`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {

        if (contador == 0) {
            let sql = 'DELETE FROM bancos;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO bancos VALUES ';
            for (let o of obj.respuesta) {
                sql += `(NULL, '${o.codigo}', '${o.cuenta}', '${o.nombre}'),`;
            }
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
