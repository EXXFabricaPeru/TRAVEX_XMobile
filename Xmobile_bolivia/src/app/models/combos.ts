import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Combos extends Databaseconf {
    public configService: ConfigService;

    public async insertAll(objx: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM combos;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objx.data);
            let sql = `INSERT INTO combos VALUES `;
            for (let d of obj.respuesta) {
                sql += `(NULL, '${d.TreeCode}', '${d.ItemCode}', ${d.Quantity}, '${d.Warehouse}', 
                        ${d.Price}, '${d.Currency}', '${d.PriceList}', '${d.ChildNum}', 
                        '${d.ItemName}', '${d.TreeType}', '${d.Price2}'),`;
            }
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async find(ItemCode: any) {
        let sql = `SELECT * FROM combos WHERE ItemCode = '${ItemCode}'`;
        return await this.queryAll(sql);
    }

    public async findTreecode(ItemCode: any) {
        let sql = `SELECT * FROM combos WHERE TreeCode = '${ItemCode}'`;
        return await this.queryAll(sql);
    }

    public async searchCombo(dato: any) {
        let sql = `SELECT * FROM combos WHERE TreeCode = '${dato}'`;
        return await this.queryAll(sql);
    }

    public async findAll(dato: any) {
        let sql = `SELECT (CASE WHEN COUNT(*) IS NULL THEN 0 ELSE COUNT(*) END) as total FROM combos WHERE TreeCode = '${dato}'`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS combos`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
