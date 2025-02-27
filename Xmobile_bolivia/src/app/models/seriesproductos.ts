import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Seriesproductos extends Databaseconf {
    public ConfigService: ConfigService;

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM seriesproductos;';
            await this.exe(sql);

        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO seriesproductos VALUES ';
            for (let o of obj.respuesta)
                sqlz += `(NULL, '${o.DocEntry}', '${o.ItemCode}','${o.SerialNumber}','${o.SystemNumber}','${o.AdmissionDate}','${o.User}','${o.Status}','${o.Date}','${o.WsCode}',''),`;
            let sqlx = sqlz.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async select(ItemCode: string, WhsCode: string, data = '') {
        let like = '';
        if (data != '') like = ` AND SerialNumber LIKE '%${data}%' `;
        let sql = `SELECT s.*, true AS isChecked FROM seriesproductos s WHERE s.ItemCode = '${ItemCode}' AND WsCode = '${WhsCode}' AND Status = 1 ${like} LIMIT 30`;
        return await this.queryAll(sql);
    }

    public async updateAfter(ItemCodes: string) {
        let sqlx = `UPDATE seriesproductos SET Status = '1', producto = '' WHERE producto = '${ItemCodes}'`;
        await this.executeRaw(sqlx);
    }

    public async updateSave(ItemCodes: string) {
        let sqlx = `UPDATE seriesproductos SET Status = 0 WHERE producto = '${ItemCodes}' AND Status = 1;`;
        await this.executeRaw(sqlx);
    }

    public async update(ItemCodes: string, producto: any) {
        let sql = `UPDATE seriesproductos SET Status = '0', producto = '${producto}' WHERE SerialNumber IN (${ItemCodes})`;
        console.log("sql series:", sql);
        return await this.executeRaw(sql);
    }

    public async updatetemp(ItemCodes: string, producto: any) {
        let sql = `UPDATE seriesproductos SET producto = '${producto}' WHERE SerialNumber IN (${ItemCodes})`;
        console.log("sql series update tmp:", sql);
        
        return await this.executeRaw(sql);
    }

    public async selectserie(idpro: string) {
        let sql = `SELECT * FROM seriesproductos WHERE producto = '${idpro}';`;
        return await this.queryAll(sql);
    }

    public async selectseriecount(idpro: string) {
        let sql = `SELECT COUNT(*) AS totalx FROM seriesproductos WHERE producto = '${idpro}';`;
        console.log("sql count manager series producto: ",sql);
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS seriesproductos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
