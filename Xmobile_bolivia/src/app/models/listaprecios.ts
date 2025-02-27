import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Listaprecios extends Databaseconf {
    public configService: ConfigService;

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM listaprecios;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO listaprecios VALUES ';
            for (let o of obj.respuesta) {
                sqlz += ` (NULL, '${idx}', '${o.GroupNum}','${o.BasePriceList}','${o.PriceListNo}','${o.PriceListName}','${o.DefaultPrimeCurrency}','${o.User}','${o.Status}','${o.DateUpdate}''${o.IsGrossPrice}'),`;
            }
            let sqlx = sqlz.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findSelect(idUSer: number) {
        let sql = `SELECT * FROM listaprecios WHERE idUser = ${idUSer}`;
        return await this.queryAll(sql);
    }

    public async findAll(id: number, idUSer: number) {
        let sql = `SELECT * FROM listaprecios WHERE PriceListNo = '${id}' AND idUser = ${idUSer}`;
        return await this.queryAll(sql);
    }


    public async selectAll() {
        let sql = `SELECT * FROM listaprecios`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS listaprecios;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
