import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Condicionpago extends Databaseconf {
    public configService: ConfigService;

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM condicionpago';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO condicionpago VALUES ';
            for (let o of obj.respuesta) {
                sql += `(NULL, ${o.GroupNumber}, '${o.PaymentTermsGroupName}', '${o.StartFrom}', '${o.NumberOfAdditionalMonths}', '${o.NumberOfAdditionalDays}',
                       '${o.CreditLimit}', '${o.GeneralDiscount}', '${o.InterestOnArrears}', '${o.PriceListNo}', '${o.LoadLimit}', '${o.OpenReceipt}',
                       '${o.BaselineDate}', '${o.NumberOfInstallments}', '${o.NumberOfToleranceDays}', '${o.U_UsaLc}', '${o.User}', '${o.DateUpdated}', '${o.Status}', '${o.NumberLine}'),`;
            }
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public findAll() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM condicionpago';
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++)
                    arr.push(data.rows.item(i));
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS condicionpago;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
