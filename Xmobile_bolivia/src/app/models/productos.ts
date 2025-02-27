import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Productos extends Databaseconf {
    public configService: ConfigService;

    public async selectProductos() {
        let sql = `SELECT * FROM productos`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sqldelete: any = 'DELETE FROM productos;';
            await this.executeSQL(sqldelete);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO productos VALUES ';
        for (let o of obj.respuesta) {
            if (o.ItemCode == "10CAL0001") {// 
                //  producto_std3: "0.100000"
                //  producto_std4: "3.610000"
                console.log(o);
            }
            let ItemCode = o.ItemCode.replace(/['"]+/g, '');
            let ItemName = o.ItemName.replace(/['"]+/g, '');
            sql += `(NULL, '${idx}', '${o.BarCode}', '${o.CustomsGroupCode}', '${o.DateUpdate}', '${o.DefaultSalesUoMEntry}', '${o.DefaultWarehouse}', '${o.ForceSelectionOfSerialNumber}', '${o.ForeignName}', '${o.InventoryItem}', '${ItemCode}', 
                '${ItemName}', '${o.ItemsGroupCode}', '${o.ManageBatchNumbers}', '${o.ManageSerialNumbers}', '${o.ManageStockByWarehouse}', '${o.PurchaseItem}', '${o.PurchaseUnit}', '${o.QuantityOnStock}', '${o.QuantityOrderedByCustomers}', 
                '${o.QuantityOrderedFromVendors}', '${o.SalesItem}', '${o.SalesUnit}', '${o.SalesUnitHeight}', '${o.SalesUnitLength}', '${o.SalesUnitVolume}', '${o.SalesUnitWidth}', '${o.SerialNum}', '${o.Series}', '${o.Status}', '${o.UoMGroupEntry}',
                '${o.User}', '${o.UserText}', '${o.key}', '0', '0', '0', '${o.GroupName}', '${o.producto_std1}','${o.producto_std2}', '${o.producto_std3}', '${o.producto_std4}', '${o.producto_std5}', '${o.producto_std6}', '${o.producto_std7}'
                ,'${o.producto_std8}', '${o.producto_std9}', '${o.producto_std10}', '${o.priceListNoms}', '${o.priceListNames}','${o.combo}','${o.almacenes}'
                ,'${o.grupoSIN}','${o.iva}','${o.DescuentoG}','${o.DescuentoC}','${o.DescuentoCC}','${o.DescuentoA}'),`;
        }
        let sqlx = sql.slice(0, -1);
        
        return await this.executeSQL(sqlx);
    }

    public async findAll(limit: number, search: string, priList = '') {
        let sqlm = '';
        (search != '') ? sqlm = ' WHERE (p.ItemName LIKE "%' + search + '%" OR p.ItemCode LIKE "%' + search + '%" OR p.GroupName LIKE "%' + search + '%" OR p.ItemsGroupCode LIKE "%' + search + '%")' : '';
        let sql = 'SELECT * FROM productos p ' + sqlm + ' GROUP BY p.ItemCode LIMIT ' + limit + ', 30';
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public async findIn(dato: string) {
        let sql = `SELECT p.*, 1 AS bonificacionx FROM productos p WHERE p.ItemCode IN(${dato});`;
        return await this.queryAll(sql);
    }
    /*
        public async getbonificaciones(cadenaCodes, bonificacionesQuemar) {
            console.log("DEVD quemar bonificaciones ", bonificacionesQuemar)
            let sql = `SELECT p.*, 1 AS bonificacionx FROM productos p WHERE p.ItemCode IN(${cadenaCodes});`;
            console.log("getbonificaciones ", sql);
            return await this.queryAll(sql);
        }
        */
    public async getbonificaciones(cadenaCodes, bonificacionesQuemar) {
        return new Promise((resolve, reject) => {
            console.log("DEVD quemar bonificaciones ", bonificacionesQuemar);
            let sql = `SELECT p.*, 1 AS bonificacionx FROM productos p WHERE p.ItemCode IN(${cadenaCodes});`;
            console.log("getbonificaciones ", sql);
            this.executeSQL(sql).then(async (data: any) => {
                let arr = [];

                for (let i = 0; i < data.rows.length; i++) {
                    let o = data.rows.item(i);

                    o.code_bonificacion_cabezera = bonificacionesQuemar[0].code_bonificacion_cabezera;

                    arr.push(o);
                }
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findSearch(dato: string, priList = '', qr = '', grupoproductoscode = '') {
        console.log("parametros:", dato, priList, qr);
        
        let slx = '';
        let xx = '';
        (grupoproductoscode != '') ? xx = ' AND p.producto_std5 = "' + grupoproductoscode + '"' : xx = '';
        if (dato == '') {
            return [];
        }
        if (dato != '') {
            //OR p.GroupName LIKE "%' + dato + '%"
            slx += ' WHERE (p.ItemName LIKE "%' + dato + '%" OR p.ItemCode LIKE "%' + dato + '%" OR p.ItemsGroupCode LIKE "%' + dato + '%") ' + xx;
        }
        if (priList != '')
            slx += ' AND ((SELECT count(*) FROM productosprecios WHERE ItemCode = p.ItemCode AND PriceListNo = "' + priList + '") > 0) OR p.combo = 1 ';
        if (qr != '')
            slx += ' WHERE p.BarCode = "' + qr + '" ';
        
            let sql = 'SELECT p.*, 0 AS bonificacionx FROM productos p ' + slx + ' GROUP BY  p.ItemCode  LIMIT 30';
        console.log("Busqueda de productos "+sql);
        return await this.queryAll(sql);
    }

    public async select(id: number) {
        let sql = `SELECT * FROM productos WHERE ItemCode = '${id}'`;
        let rx = await this.queryAll(sql);
        return rx[0];
    }

    public async selectpro(id: any) {
        let sql = `SELECT * FROM productos WHERE ItemCode = '${id}'`;
        let rx = await this.queryAll(sql);
        return rx[0];
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS productos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    //Percepciones
    public async validateGroupPer(code_1: string) {       
        let sql = `SELECT * FROM productos WHERE IFNULL(producto_std2, '')='' AND ItemCode = '${code_1}'`;
        console.log("sql",sql)
        return await this.queryAll(sql);
    }

    public async selectProductGroupPer(code_1: string) {
       
        let sql = `SELECT IFNULL(b.Code,'0000') as Code ,IFNULL(b.U_EXX_GLP,'N') as U_EXX_GLP, IFNULL(b.U_EXX_MONMIN,0) as U_EXX_MONMIN FROM productos a, gruposPercepciones b  WHERE a.producto_std2=b.Code AND ItemCode = '${code_1}'`;
        console.log()
        return await this.queryAll(sql);
   
    }
}
