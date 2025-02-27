import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Lotesproductos extends Databaseconf {
    public ConfigService: ConfigService;

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM lotesproductos;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO lotesproductos VALUES ';
            for (let o of obj.respuesta)
                sqlz += `(NULL, '${o.ItemCode}', '${o.BatchNum}','${o.WhsCode}','${o.Quantity}','${o.InDate}','${o.ExpDate}','${o.BaseNum}'),`;
            let sqlx = sqlz.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async select(code: string, almacen: any) {
        let sql = `SELECT ItemCode, BatchNum, WhsCode, InDate, BaseNum, Quantity FROM lotesproductos WHERE ItemCode = '${code}' AND WhsCode = '${almacen}' `;
        return await this.queryAll(sql);
    }

    public async select2(code: string, almacen: any) {
        let sql = `SELECT ItemCode, BatchNum, WhsCode, InDate, BaseNum, Quantity FROM lotesproductos 
                          WHERE ItemCode = '${code}' AND WhsCode = '${almacen}' AND Quantity > 0`;
        return await this.queryAll(sql);
    }

    /**********************/
    public async updateDiferenciaLotes(Stock: number, ItemCode: string, WhsCode: string, BatchNum: string) {
        let sql = `UPDATE lotesproductos SET Quantity = (Quantity - ${Stock}) WHERE ItemCode = '${ItemCode}' AND WhsCode = '${WhsCode}' AND BatchNum = '${BatchNum}'`;
        return await this.executeSQL(sql);
    }

    public async updateAdicionLotes(Stock: number, ItemCode: string, WhsCode: string, BatchNum: string) {
        let sql = `UPDATE lotesproductos SET Quantity = (Quantity + ${Stock}) WHERE ItemCode = '${ItemCode}' AND WhsCode = '${WhsCode}' AND BatchNum = '${BatchNum}'`;
        return await this.executeSQL(sql);
    }

    /**********************/

    public async lotesproductos(code: string) {
        let sql = `SELECT ItemCode, BatchNum, WhsCode, InDate, BaseNum, Quantity, ExpDate FROM lotesproductos WHERE ItemCode = '${code}'`;
        return await this.queryAll(sql);
    }
}
