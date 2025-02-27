import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Gestionsap extends Databaseconf {
    public configService: ConfigService;

    public async selectGestionSap() {
        let sql = `SELECT * FROM gestionsap`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM gestionsap';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO gestionsap VALUES ';
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.CompnyAddr}', '${o.CompnyName}', '${o.Country}', '${o.TaxIdNum}', '${o.MainCurncy}', ${o.SumDec}),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

   

    public select(id: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM gestionsap WHERE id = '${id}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public async find(code: any) {
        let sql = `SELECT * FROM gestionsap WHERE CompnyName = '${code}'`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS gestionsap;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
  
}
