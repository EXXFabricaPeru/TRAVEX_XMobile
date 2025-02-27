import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";


export class Productosprecios extends Databaseconf {
    public configService: ConfigService;

    async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {

            let sql = 'DELETE FROM productosprecios;';
            // let sql = 'DELETE FROM productosprecios WHERE idUser = ' + idx;
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO productosprecios VALUES ';
            for (let o of obj.respuesta) {

                sql += `(NULL, '${idx}','${o.ItemCode}','${o.IdListaPrecios}','${o.IdUnidadMedida}','${Number(o.Price).toFixed(4)}','${o.Currency}','${o.User}','${o.Status}','${o.DateUpdate}','${o.Code}','${o.Name}','${o.PriceListName}','${o.PriceListNo}','${o.BaseQty}'),`;

            }
            let sqlx = sql.slice(0, -1);
            //console.log("sql ", sql);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public selectPrecios(dato: string) {
        console.log("articulo--->", dato);
        
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM productosprecios WHERE ItemCode = "' + dato + '" ';
            console.log(sql);
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

    public async selectPreciosproducto(ItemCode: string, PriceListNo: any) {
        let outSql = await this.queryAll("SELECT * FROM productosprecios");
        console.log("productos precio",outSql);
        
        let sql = `SELECT * FROM productosprecios WHERE ItemCode = '${ItemCode}' AND IdListaPrecios = '${PriceListNo}' order by IdUnidadMedida asc`;
        console.log("selectPreciosproducto sql ", sql)
        return await this.queryAll(sql);
    }

    public async selectunidadmedida(ItemCode: string, PriceListNo: any, unidad: any) {
        let sql = `SELECT * FROM productosprecios WHERE ItemCode = '${ItemCode}' AND IdListaPrecios = '${PriceListNo}' and IdUnidadMedida  = '${unidad}' order by IdUnidadMedida asc`;
        console.log("selectPreciosproducto sql ", sql)
        return await this.queryAll(sql);
    }


    public async selectPreciosproductoBonificacion(ItemCode: string, PriceListNo: any) {
        let sql = `SELECT * FROM productosprecios WHERE ItemCode = '${ItemCode}' AND IdListaPrecios = '${PriceListNo}' `;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS productosprecios;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public async selectPreciosAll() {

        let sql = 'SELECT * FROM productosprecios ';
        return await this.queryAll(sql);
    }


    public async selectPreciosClient(PriceListNo: any) {
        let sql = `SELECT  * FROM productosprecios WHERE PriceListNo = '${PriceListNo}' `;
        return await this.queryAll(sql);
    }


    public async selectPreciosClient2(PriceListNo: any) {
        let sql = `SELECT distinct PriceListName,Currency FROM productosprecios WHERE PriceListNo = '${PriceListNo}' `;
        return await this.queryAll(sql);
    }

}
