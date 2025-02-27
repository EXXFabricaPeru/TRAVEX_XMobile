import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Nit extends Databaseconf {
    public configService: ConfigService;

    public  async insertAll(objeto: any) {
       console.log("INSERTA EN NIT",objeto);
        let sql = 'DELETE FROM NIT;';
        await this.exe(sql);
        
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO nit VALUES ';
            for (let o of obj.respuesta) {
                sqlz += ` ('${o.id}', '${o.nit}', '${o.razon_social}'),`;
            }
            let sqlx = sqlz.slice(0, -1);
            console.log("Nit:",sqlx);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findAll() {
        let sqlm = '';
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM nit';
            console.log("sql ", sql);
            this.queryAll(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public async findSearch(dato: string, tipo = '') {

        let sql = `SELECT * FROM nit c WHERE (c.nit LIKE '%${dato}%' OR c.razon_social LIKE '%${dato}%')  LIMIT 25`;
        console.log("sql search ", sql);
        return await this.queryAll(sql);
    }

    public async find(code: any) {
        let sql = `SELECT * FROM nit WHERE nit = '${code}'`;
        console.log(sql);
        return await this.queryAll(sql);
    }

    public async busqueda_inicial() {
        let sql = `SELECT * FROM nit LIMIT 25`;
        console.log(sql);
        return await this.queryAll(sql);
    }
}
