import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
export class Productosalmacenes extends Databaseconf {

    public async insertAll(objeto: any, idx: number, contador = 0) {

        let session: any = await this.getSession();
        // console.log("session ", session);
        console.log("validaciondisponible ", session[0].validaciondisponible);

        if (contador == 0) {
            let sql = 'DELETE FROM productosalmacenes;';
            // let sql = 'DELETE FROM productosalmacenes WHERE idUser = ' + idx;
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO productosalmacenes VALUES';
            for (let o of obj.respuesta) {
                if (session[0].validaciondisponible == 1) {
                    o.InStock = o.InStock - o.Committed;
                    o.Committed = 0;
                }
                console.log("modif ", o);

                sql += `(NULL, '${idx}', '${o.ItemCode}','${o.WarehouseCode}','${o.InStock}','${o.Committed}','${o.Locked}','${o.Ordered}','${o.User}','${o.Status}','${o.DateUpdate}'),`;

            }
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertProdAlmacenForce(almacenCode: any, codeItem: any) {
        let dateDoc = moment().format('YYYY-MM-DD');

        return new Promise((resolve, reject) => {

            let sql = 'INSERT INTO productosalmacenes VALUES';

            sql += `(NULL, '159', '${codeItem}','${almacenCode}','0','0','N','0','1','1','${dateDoc}');`;
            // INSERT INTO productosalmacenes VALUES(NULL, '159', 'ITG-0000008','AGD.037','1.000000','0.000000','N','0.000000','1','1','2021-08-04 00:00:00')

            console.log("sql productosalmacenes ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    async addUpdateprodcualmacenessap(sqlresp) {
        console.log("DEVD addUpdateprodcualmacenessap() ", sqlresp);
        let sqlrex = ` UPDATE productosalmacenes SET InStock = ${sqlresp.InStock}, Committed= ${sqlresp.Committed}, Ordered = ${sqlresp.Ordered}
                              WHERE WarehouseCode = '${sqlresp.WarehouseCode}' AND ItemCode = '${sqlresp.ItemCode}'`;
        console.log("DEVD sqlrex ", sqlrex);
        return await this.executeSQL(sqlrex);
    }

    public async updateCompometidoProdutosalmacenes(sqlresp) {
        console.log("DEVD updateCompometidoProdutosalmacenes() ", sqlresp);
        console.log("updateprodcualmacenes() ", sqlresp);
        if (!sqlresp.BaseQty && sqlresp.BaseQty != 0) {
            sqlresp.BaseQty = sqlresp.dataproducto.BaseQty;
        }
        if (sqlresp.BaseQty == "NaN" || sqlresp.BaseQty == "undefined") {
            sqlresp.BaseQty = 1;
        }

        console.log(sqlresp.BaseQty);

        let sqlrex = ` UPDATE productosalmacenes SET Committed = (Committed + ${(sqlresp.cantidad * sqlresp.BaseQty)}) 
                       WHERE WarehouseCode = '${sqlresp.WhsCode}' AND ItemCode = '${sqlresp.ItemCode}';`;
        console.log("DEVD sqlrex ", sqlrex);
        return await this.executeSQL(sqlrex);
    }

    public async addUpdateCompometidoProdutosalmacenes(sqlresp) {
        console.log("DEVD addUpdateCompometidoProdutosalmacenes() ", sqlresp);
        if (sqlresp.BaseQty == "NaN" || sqlresp.BaseQty == "undefined") {
            sqlresp.BaseQty = 1;
        }

        let sqlrex = ` UPDATE productosalmacenes SET Committed = (Committed - ${(sqlresp.Quantity * sqlresp.BaseQty)}) 
                       WHERE WarehouseCode = '${sqlresp.WhsCode}' AND ItemCode = '${sqlresp.ItemCode}';`;
        console.log("DEVD sqlrex ", sqlrex);
        return await this.executeSQL(sqlrex);
    }

    public async addUpdateprodcualmacenes(sqlresp) {
        console.log("DEVD addUpdateprodcualmacenes() ", sqlresp);
        if (sqlresp.BaseQty == "NaN" || sqlresp.BaseQty == "undefined") {
            sqlresp.BaseQty = 1;
        }

        let sqlrex = ` UPDATE productosalmacenes SET InStock = (InStock + ${(sqlresp.Quantity * sqlresp.BaseQty)}) 
                       WHERE WarehouseCode = '${sqlresp.WhsCode}' AND ItemCode = '${sqlresp.ItemCode}'`;
        console.log("DEVD sqlrex ", sqlrex);
        return await this.executeSQL(sqlrex);
    }

    public async updateprodcualmacenes(sqlresp) {
        console.log("updateprodcualmacenes() ", sqlresp);
        /*if (!sqlresp.BaseQty) {
            sqlresp.BaseQty = sqlresp.dataproducto.BaseQty;
        }*/
        if (sqlresp.BaseQty == "NaN" || sqlresp.BaseQty == "undefined") {
            sqlresp.BaseQty = 1;
        }
        let sqlrex = ` UPDATE productosalmacenes SET InStock = (InStock - ${(sqlresp.cantidad * sqlresp.BaseQty)}) 
                       WHERE WarehouseCode = '${sqlresp.WhsCode}' AND ItemCode = '${sqlresp.ItemCode}'  AND InStock > 0;;`;
        console.log("DEVD sqlrex ", sqlrex);
        return await this.executeSQL(sqlrex);
    }

    public async find(almacenCode: any, codeItem: any) {
        let sql = `SELECT * FROM productosalmacenes WHERE ItemCode = '${codeItem}' AND WarehouseCode = '${almacenCode}' ORDER BY id ASC LIMIT 1`;
        console.log("busqueda producto ", sql);
        let r: any = await this.queryAll(sql);
        return r[0];
    }

    public async find2(almacenCode: any, codeItem: any) {
        let sql = `SELECT * FROM productosalmacenes WHERE ItemCode = '${codeItem}'`;
        console.log("busqueda producto ", sql);
        let r: any = await this.queryAll(sql);
        return r[0];
    }

    public async findAll() {
        let sql = `SELECT * FROM productosalmacenes`;
        return await this.queryAll(sql);
    }

    public async findOne(codeItem) {
        let sql = `SELECT * FROM productosalmacenes WHERE ItemCode = '${codeItem}' `;
        return await this.queryAll(sql);
    }



    public async findAllReport() {
        let sql = `SELECT p.*, i.ItemName,i.SalesUnit,i.ItemCode FROM productosalmacenes p INNER JOIN productos i ON p.ItemCode = i.ItemCode where p.InStock >= 1 order by i.ItemCode asc`;
        return await this.queryAll(sql);
    }

    public async findAllReportDis() {
        let sql = `SELECT p.*, i.ItemName,i.SalesUnit FROM productosalmacenes p INNER JOIN productos i ON p.ItemCode = i.ItemCode where p.InStock >= 1 and (p.InStock - p.Committed) > 0 `;
        return await this.queryAll(sql);
    }


    public async almacenes() {
        let sql = `SELECT distinct WarehouseCode FROM productosalmacenes p `;
        return await this.queryAll(sql);
    }

    

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS productosalmacenes;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
