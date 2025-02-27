import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import { Lotesproductos } from "./lotesproductos";

export class Lotes extends Databaseconf {
    public configService: ConfigService;

    public async limpiarlotes(ItemCode, Batch) {
        let sql = `DELETE FROM lotes WHERE ItemCode = '${ItemCode}' AND Batch = '${Batch}';`;
        return await this.executeSQL(sql);
    }

    public async insertLote(id, Batch, Stock, baseid, WhsCode, ItemCode) {
        console.log("insertLote()");
        let StockTotal = (Stock * baseid);
        let sql = `INSERT INTO lotes(id, ItemCode, Batch, Stock, ItemDescription)VALUES(NULL, '${id}', '${Batch}', '${StockTotal}','${Stock}');`;
        let lotesproductos = new Lotesproductos();
        console.log("sql lote ", sql);
        await lotesproductos.updateDiferenciaLotes(StockTotal, ItemCode, WhsCode, Batch);
        return await this.executeRaw(sql);
    }

    public deleteLote(id: any) {
        console.log("deleteLote ", id);
        return new Promise(async (resolve, reject) => {
            let sqlx = `SELECT d.ItemCode, d.Quantity, d.WhsCode, l.ItemCode AS loteItemCode, l.Batch AS loteBatch, l.Stock AS loteStock 
                        FROM detalle d LEFT JOIN lotes l ON l.ItemCode = d.id WHERE d.id = ${id};`;
            let lotesarr: any = await this.queryAll(sqlx);
            for (let lote of lotesarr) {
                if (lote.loteBatch != null) {
                    let lotesproductos = new Lotesproductos();
                    await lotesproductos.updateAdicionLotes(lote.loteStock, lote.ItemCode, lote.WhsCode, lote.loteBatch);
                }
            }
            let sql = `DELETE FROM lotes WHERE ItemCode = ${id}`;
            console.log("sql ", sql);
            let rex: any = await this.executeSQL(sql);
            resolve(rex);
        });
    }

    public async selectLoteProducto(Item: any, codaBatch: any) {
        let sql = `SELECT * FROM lotes WHERE ItemCode = '${Item}' AND Batch = '${codaBatch}' `;
        return await this.queryAll(sql);
    }

    public async selectSumLoteProducto(ItemCode: any) {
        let sql = `SELECT SUM(Stock) as Stock FROM lotes WHERE ItemCode = '${ItemCode}'`;
        return await this.queryAll(sql);
    }

    public async selectLoteIdLInea(Item: any) {
        let sql = `SELECT Batch,Stock  FROM lotes WHERE ItemCode = '${Item}' `;
        return await this.queryAll(sql);
    }

    public async ActualizarLoteProducto(ItemCode: any, Bach: any, Quantity: any, Almacen: any) {
        let sql = `Update lotesproductos set Quantity=Quantity-${Quantity} WHERE ItemCode = '${ItemCode}' and BatchNum='${Bach}' and WhsCode='${Almacen}'`;
        return await this.queryAll(sql);
    }

    async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM lotes;';// WHERE detalleId = 0
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO lotes VALUES ';
            for (let o of obj.respuesta) {
                sqlz += ` (NULL, '${idx}', '${o.ItemCode}', '${o.ItemDescription}', '${o.ItemStatus}', '${o.Batch}', '${o.AdmissionDate}', '${o.ExpirationDate}', '${o.Stock}', '${o.User}', '${o.Status}', '${o.DateUpdate}'),`;
            }
            let sqlx = sqlz.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async selectAll(data: any) {
        let sql = `SELECT Batch AS BatchNum, Stock AS Quantity FROM lotes WHERE ItemCode = '${data}'`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS lotes;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}