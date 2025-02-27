import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class PerConfigEntrega extends Databaseconf {
    public configService: ConfigService;

    public async selectPerEntrega() {
        let sql = `SELECT * FROM perConfigEntrega`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM perConfigEntrega';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO perConfigEntrega VALUES ';
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.CardCode}', '${o.CardName}','${o.LicTradNum}','${o.Address}','${o.Name}','${o.Notes1}','${o.U_EXX_PLAVEH}','${o.U_EXX_MARVEH}','${o.U_EXX_PLATOL}','${objeto.U_EXX_FE_MODTRA}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

    public async insert(objeto: any) {
         let  sql = `INSERT INTO perConfigEntrega VALUES (NULL, '${objeto.CardCode}', '${objeto.CardName}','${objeto.LicTradNum}','${objeto.Address}','${objeto.Name}','${objeto.Notes1}','${objeto.U_EXX_PLAVEH}','${objeto.U_EXX_MARVEH}','${objeto.U_EXX_PLATOL}','${objeto.U_EXX_FE_MODTRA}')`;
      
        console.log(sql);
        return await this.executeSQL(sql);
    }

    public async update(objeto: any, id) {
       // let sql: string = `(NULL, '${objeto.CardCode}', '${objeto.CardName}','${objeto.LicTradNum}','${objeto.Address}')`;
        let sql: string = `UPDATE perConfigEntrega SET 
        U_EXX_CODTRANS='${objeto.CardCode}',
        U_EXX_NOMTRANS='${objeto.CardName}',
        U_EXX_RUCTRANS='${objeto.LicTradNum}',
        U_EXX_DIRTRANS='${objeto.Address}', 
        U_EXX_NOMCONDU='${objeto.Name}', 
        U_EXX_LICCONDU='${objeto.Notes1}',
        U_EXX_PLACAVEH='${objeto.U_EXX_PLAVEH}',
        U_EXX_MARCAVEH='${objeto.U_EXX_MARVEH}',
        U_EXX_PLACATOL='${objeto.U_EXX_PLATOL}',
        U_EXX_FE_MODTRA='${objeto.U_EXX_FE_MODTRA}'  where id=${id}
        `;
         
        console.log(sql);
        return await this.executeSQL(sql);
    }

    public findByCode(code: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM perTransportistas WHERE code = '${code}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS perTransportistas;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public findByform(where: string,campos: string) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT `+campos+` FROM perTransportistas WHERE `+ where;            
            console.log(sql);
            this.queryAll(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
