import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Servicioventas extends Databaseconf {
    public configService: ConfigService;

    public async selectServicioVentas() {
        let sql = `SELECT * FROM servicioVentas`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM servicioVentas';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO servicioVentas VALUES ';
        
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.Code}', '${o.Name}', '${o.U_EXX_GRUPER}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }   

    public select(id: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM servicioVentas WHERE id = '${id}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async find(code: any) {
        let sql = `SELECT * FROM servicioVentas WHERE Code = '${code}'`;
        return await this.queryAll(sql);
    }

    public async validateServicioVentaGrupoPer(code_1: any) {
        let sql = `SELECT IFNULL(T1.Code,'0000') as Code, ifnull(T1.U_EXX_GLP,'N') as U_EXX_GLP ,ifnull(T1.U_EXX_MONMIN,0) as U_EXX_MONMIN FROM servicioVentas T0 INNER JOIN gruposPercepciones T1 ON T0.U_EXX_GRUPER=T1.Code WHERE T0.Code = '${code_1}'`;
        console.log("validateServicioVentaGrupoPer slq->"+sql);
        try {
            return await this.queryAll(sql);
        } catch (error) {
            console.log("error en sql",error)
        }       
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS servicioVentas;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
  
}
