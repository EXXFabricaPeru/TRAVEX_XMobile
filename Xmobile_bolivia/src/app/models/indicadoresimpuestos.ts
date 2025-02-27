
import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Indicadoresimpuestos extends Databaseconf {
    public configService: ConfigService;

    public async selectIndicadoresImpuestos() {
        let sql = `SELECT * FROM indicadoresimpuestos`;
        return await this.queryAll(sql);
    }

    public async selectIndicadoresImpuestosNoRepetidos() {
        let sql = `SELECT * FROM indicadoresimpuestos GROUP BY Code`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM indicadoresimpuestos';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO indicadoresimpuestos VALUES ';
        
        for (let o of obj.respuesta) {   
/*          Code
            Rate
            RowNumber
            STCCode
            STACode
            EffectiveRate
            User
            Status
            DateUpdate */         
            sql += `(NULL, '${o.Code}', '${o.Rate}', '${o.RowNumber}', '${o.STCCode}', '${o.STACode}', '${o.EffectiveRate}', '${o.User}', '${o.Status}', '${o.DateUpdate}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

   

    public select(id: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM indicadoresimpuestos WHERE id = '${id}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public async find(code: any) {
        let sql = `SELECT * FROM indicadoresimpuestos WHERE Code = '${code}'`;
        return await this.queryAll(sql);
    }    
    public async vlidateGestionSap() {
        let sql = `SELECT  T1.QryGroup3, T1.QryGroup4 FROM indicadoresimpuestos T1 WHERE T1.CardType = 'C' AND ( T1.LicTradNum=(SELECT T2.TaxIdNum FROM gestionsap T2 ))`;
        return await this.queryAll(sql);
    }
    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS indicadoresimpuestos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }    
  
}
