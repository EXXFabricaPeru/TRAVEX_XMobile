import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
import { connectableObservableDescriptor } from "rxjs/internal/observable/ConnectableObservable";
export class companex_cadena extends Databaseconf {
    public configService: ConfigService;


    public async findOne() {
        let sql = `SELECT * FROM companex_cadena`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS companex_cadena`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {

        /*
                 docEntry integer  null,
                        tipotienda varchar(255) NULL,
                        code varchar(255) NULL,
                        name varchar(255) NULL,
                        canceled varchar(255) NULL,
                        objeto varchar(255) NULL
        
        canceled: "N"
        code: "002"
        docEntry: "2"
        id: "2"
        name: "SOCIO PRINCIPAL"
        objeto: "CADENA"
        tipotienda: ""
        
                        
                        
                        */
        if (contador == 0) {
            let sql = 'DELETE FROM companex_cadena;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO companex_cadena VALUES ';
            for (let o of obj.respuesta) {

                if (o.code == '044') {
                    console.log("DEVD cadena 044 ", o);
                }
                sql += `(NULL, 
                    '${o.docEntry}',
                    '${o.tipotienda}',
                '${o.code}',
                '${o.name}',
                '${o.canceled}',
                '${o.objeto}',
                '${o.tipodato}'
                
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
    public async showAll(tipoTienda) {
        let sql = `SELECT * FROM companex_cadena WHERE tipotienda='${tipoTienda}';`;
        return await this.queryAll(sql);
    }

}
