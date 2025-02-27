import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
export class companex_canal extends Databaseconf {
    public configService: ConfigService;


    public async findOne() {
        let sql = `SELECT * FROM companex_canal`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS companex_canal`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {


        if (contador == 0) {
            let sql = 'DELETE FROM companex_canal;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            /*
            canceled: "N"
code: "DPP"
docEntry: "1"
id: "1"
name: "DPP"
objeto: "CANAL"


docEntry integer  null,
code varchar(255) NULL,
name varchar(255) NULL,
canceled varchar(255) NULL,
objeto varchar(255) NULL
*/

            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO companex_canal VALUES ';
            for (let o of obj.respuesta) {
                if (o.code == "ECOMMERCE") {
                    console.log("DEVD cadena ECOMMERCE ", o);
                }
                sql += `(NULL, 
                    '${o.docEntry}',
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
    public async showAll() {
        let sql = `SELECT * FROM companex_canal;`;
        return await this.queryAll(sql);
    }

    public async getOneCanal(codeCanal) {
        let sql = `SELECT * FROM companex_canal WHERE code ='${codeCanal}' ;`;
        return await this.queryAll(sql);
    }

    public async getOneSubCanal(codeSubCanal) {
        let sql = `SELECT * FROM companex_subcanal WHERE code ='${codeSubCanal}'  ;`;
        return await this.queryAll(sql);
    }

    public async getOneTipoTienda(codeTipoTienda) {
        let sql = `SELECT * FROM companex_tipotienda WHERE code ='${codeTipoTienda}' ;`;
        return await this.queryAll(sql);
    }

    public async getOneCadena(codeCadena) {
        let sql = `SELECT * FROM companex_cadena WHERE code ='${codeCadena}'  ;`;
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public async getConsolidador() {
        let sql = `SELECT * FROM companex_consolidador ;`;
        return await this.queryAll(sql);
    }

}
