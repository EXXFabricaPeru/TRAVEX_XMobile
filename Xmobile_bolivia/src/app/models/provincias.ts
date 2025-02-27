import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import { Lotesproductos } from "./lotesproductos";

export class Provincias extends Databaseconf {
    public configService: ConfigService;

    
    
    async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM cliprovincias';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO cliprovincias VALUES ';
            for (let o of obj.respuesta) {
                sqlz += ` (NULL, '${o.Code}', '${o.Name}', '${o.U_EXX_CODPAI}', '${o.U_EXX_CODDEP}'),`;
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
        let sql = `SELECT * from cliprovincias`;
        return await this.queryAll(sql);
    }
    public async findCode(Name:string) {
        let sql = `SELECT * from cliprovincias where Name='${Name}'`;
        return await this.queryAll(sql);
    }
    public async findByCodDep(codDep:string) {
        let sql = `SELECT * from cliprovincias where U_EXX_CODDEP='${codDep}'`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS cliprovincias;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}