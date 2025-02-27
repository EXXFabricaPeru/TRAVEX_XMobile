
import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
export class companex_subcanal extends Databaseconf {
    public configService: ConfigService;


    public async findOne() {
        let sql = `SELECT * FROM companex_subcanal`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS companex_subcanal`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {


        if (contador == 0) {
            let sql = 'DELETE FROM companex_subcanal;';
            await this.exe(sql);
        }
        /*
        canal: "HSM"
        canceled: "N"
        code: "001"
        docEntry: "1"
        id: "1"
        name: "SUPER"
        objeto: "SUBCANAL"

        docEntry integer  null,
                canal varchar(255) NULL,
                code varchar(255) NULL,
                name varchar(255) NULL,
                objeto varchar(255) NULL,
                canceled varchar(255) NULL

*/
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO companex_subcanal VALUES ';
            for (let o of obj.respuesta) {
                if (o.code == '021') {
                    console.log("DEVD cadena 021 ", o);
                }
                sql += `(NULL, 
                    '${o.docEntry}',
                    '${o.canal}',
                    '${o.code}',
                    '${o.name}',
                    '${o.objeto}',
                    '${o.canceled}'
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
    public async showAll(codeCanal) {
        let sql = `SELECT * FROM companex_subcanal where canal='${codeCanal}';`;
        return await this.queryAll(sql);
    }

}
