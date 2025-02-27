/**
 * OBJECT  REST 
 *  "id": "1",
            "CreditCard": "3",
            "CardName": "Descuento Empleado",
            "AcctCode": "10101010700",
            "Phone": "",
            "CompanyId": "0",
            "Locked": "N",
            "DataSource": "I",
            "UserSign": "32",
            "LogInstanc": "0",
            "UpdateDate": "0000-00-00 00:00:00",
            "IntTaxCode": "",
            "UserSign2": "0",
            "Country": ""
 */
import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Tarjetas extends Databaseconf {
    public configService: ConfigService;
    public async findAll() {
        let sql = `SELECT * FROM tarjetas`;
        return await this.queryAll(sql);
    }



    public async insertAll(objeto: any, idx: number) {
        let sql = 'DELETE FROM tarjetas';
        await this.exe(sql);
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO tarjetas VALUES ';
            for (let o of obj.respuesta) {
                sql += `(NULL, '${o.CreditCard}', '${o.CardName}', '${o.AcctCode}', '${o.Phone}', '${o.CompanyId}', '${o.Locked}', '${o.DataSource}', '${o.UserSign}', '${o.LogInstanc}', '${o.UpdateDate}', '${o.IntTaxCode}', '${o.UserSign2}', '${o.Country}'),`;

            }
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }




}
