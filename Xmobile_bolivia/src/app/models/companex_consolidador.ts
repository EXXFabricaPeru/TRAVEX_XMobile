
import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
export class companex_consolidador extends Databaseconf {
    public configService: ConfigService;

    public async findOne() {
        let sql = `SELECT * FROM companex_consolidador`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS companex_consolidador`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {


        if (contador == 0) {
            let sql = 'DELETE FROM companex_consolidador;';
            await this.exe(sql);
        }
        /*
            "docentry": "1",
            "code": "EAPRE14766",
            "name": "Ketal"

                   docEntry  integer  null,
                  code varchar(255) NULL,
                  name varchar(255) NULL

        */
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO companex_consolidador VALUES ';
            for (let o of obj.respuesta) {

                sql += `(NULL, 
                    '${o.docEntry}',
                    '${o.code}',
                    '${o.name}'
                ),`;
            }

            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then(async (data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public async showAll() {
        let sql = `SELECT * FROM companex_consolidador ;`;
        return await this.queryAll(sql);
    }
    public async showOne(code) {
        let sql = `SELECT * FROM companex_consolidador WHERE code = '${code}';`;
        return await this.queryAll(sql);
    }

}
