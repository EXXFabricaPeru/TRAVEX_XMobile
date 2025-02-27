import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import { Lotesproductos } from "./lotesproductos";

export class Distritos extends Databaseconf {
    public configService: ConfigService;

    
    
    async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM clidistritos';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO clidistritos VALUES ';
            for (let o of obj.respuesta) {
                sqlz += ` (NULL, '${o.Code}', '${o.Name}', '${o.U_EXX_CODPRO}', '${o.U_EXX_DESDIS}'),`;
            }
            let sqlx = sqlz.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public async findByCodProv(codProv:string) {
        let sql = `SELECT * from clidistritos where U_EXX_CODPRO='${codProv}'`;
        return await this.queryAll(sql);
    }

    public async findCode(Name:string) {
        let sql = `SELECT * from clidistritos where Name='${Name}'`;
        return await this.queryAll(sql);
    }

    public async selectAll() {
        let sql = `SELECT * from clidistritos`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS clidistritos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}