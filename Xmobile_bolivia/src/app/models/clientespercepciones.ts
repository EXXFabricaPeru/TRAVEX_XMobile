import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Clientespercepciones extends Databaseconf {
    public configService: ConfigService;

    public async selectClientePercepciones() {
        let sql = `SELECT * FROM clientesPercepciones`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM clientesPercepciones';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO clientesPercepciones VALUES ';
        
        for (let o of obj.respuesta) {  
            console.log("el valor es:",o.iU_EXX_PERCDI);          
            sql += `(NULL, '${o.CardCode}', '${o.CardType}', '${o.U_EXX_TIPOPERS}', '${o.QryGroup2}', '${o.QryGroup3}', '${o.QryGroup4}', '${o.QryGroup6}', '${o.QryGroup7}', '${o.QryGroup8}', '${o.U_EXX_PERCOM}','${o.LicTradNum}','${o.QryGroup1}','${o.iU_EXX_PERCDI}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

    public select(id: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM clientesPercepciones WHERE id = '${id}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async find(code: any) {
        let sql = `SELECT * FROM clientesPercepciones WHERE CardCode = '${code}'`;
        return await this.queryAll(sql);
    }

    public async allByQr1Group() {
        let sql = `SELECT * FROM clientesPercepciones WHERE QryGroup1 = 'Y'`;
        return await this.queryAll(sql);
    }

    public async vlidateGestionSap() {
        let sql = `SELECT  T1.QryGroup3, T1.QryGroup4 FROM clientesPercepciones T1 WHERE T1.CardType = 'C' AND ( T1.LicTradNum=(SELECT T2.TaxIdNum FROM gestionsap T2 ))`;
        return await this.queryAll(sql);
    }
    
    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS clientesPercepciones;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
  
}
