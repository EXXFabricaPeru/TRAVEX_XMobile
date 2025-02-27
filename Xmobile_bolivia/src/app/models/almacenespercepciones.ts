import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Almacenespercepciones extends Databaseconf {
    public configService: ConfigService;

    public async selectAlmacenesPercepciones() {
        let sql = `SELECT * FROM almacenesPercepciones`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM almacenesPercepciones';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO almacenesPercepciones VALUES ';
        
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.WhsCode}', '${o.WhsName}', '${o.U_EXX_PERDGH}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

    public async findByWhsCode(WhsCode:any) {
        let sql = `SELECT * FROM almacenesPercepciones WHERE WhsCode='${WhsCode}'`;
        console.log("almacenes sql ->",sql);
        return await this.queryAll(sql);
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
        let sql = `SELECT * FROM clientesPercepciones WHERE WhsCode = '${code}'`;
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
