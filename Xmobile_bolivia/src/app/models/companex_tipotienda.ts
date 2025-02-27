
import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
export class companex_tipotienda extends Databaseconf {
    public configService: ConfigService;


    public async findOne() {
        let sql = `SELECT * FROM companex_tipotienda`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS companex_tipotienda`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {


        if (contador == 0) {
            let sql = 'DELETE FROM companex_tipotienda;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO companex_tipotienda VALUES ';
            for (let o of obj.respuesta) {
                if (o.code == '036') {
                    console.log("DEVD cadena 036 ", o);
                }
                /*
                 docEntry  integer  null,
                          subcanal varchar(255) NULL,
                          code varchar(255) NULL,
                          name varchar(255) NULL,
                          canceled varchar(255) NULL,
                          objeto varchar(255) NULL
                          */
                sql += `(NULL, 
                    '${o.docEntry}',
                    '${o.subcanal}',
                '${o.code}',
                '${o.name}',
                '${o.canceled}',
                '${o.objeto}'
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
    public async showAll(codeSubCanal) {
        let sql = `SELECT * FROM companex_tipotienda where subcanal='${codeSubCanal}';`;
        return await this.queryAll(sql);
    }

}
