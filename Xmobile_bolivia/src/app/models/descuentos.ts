import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Descuentos extends Databaseconf {
    public configService: ConfigService;

    public async insertAll(objeto: any, idx: number, contador = 0) {
        console.log("llama al insertAll");
        if (contador == 0) {
            let sql = 'DELETE FROM descuentos;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO descuentos VALUES ';
            for (let d of obj.respuesta) {
                sql += `(NULL , '${d.PRODUCTO}', '${d.CLIENTE}', '${d.PRIORIDAD}' , 
                '${d.LINEA}', '${d.LISTA_PRECIO}', '${d.DESCUENTO}', '${d.DESDE}', '${d.HASTA}', '${d.TIPO}', 
                '${d.CANTIDAD}', '${d.PROPIEDADES}', '${d.FABRICANTE}', '${d.GRUPO_CLIENTE}', '${d.GRUPO_PRODUCTO}'
                , '${d.CANTIDAD_LIBRE}', '${d.MAXIMO_LIBRE}'),`;
            }
            let sqlx = sql.slice(0, -1);
            console.log("sqlx",sqlx);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

     public async selectDescuento(codigoProducto: string, codigoCliente: string, grupoCliente: string, listaPrecio: string, fecha: string) {
       /* let sql = `SELECT tipodescuento, ItemCode, CardCode, PriceListNum, Price, Currency, DiscountPercent, paid, free, max, prioridad,
                    linea, ValidTo, ValidFrom FROM descuentos WHERE ItemCode = '${codigoProducto}' AND ((CardCode = '${codigoCliente}') OR
                    (CardCode = '${grupoCliente}') OR (CardCode = '*') OR (PriceListNum = '${listaPrecio}')) AND
                    (((ValidFrom='0000-00-00') AND (ValidTo='0000-00-00')) or ('${fecha}' BETWEEN ValidFrom and ValidTo)) ORDER BY prioridad ASC, linea ASC,
                    DiscountPercent ASC LIMIT 1;`;*/
        let sql = `SELECT * FROM descuentos WHERE ItemCode = '${codigoProducto}' AND ((CardCode = '${codigoCliente}') OR
        (CardCode = '${grupoCliente}') OR (CardCode = '*') OR (LISTA_PRECIO = '${listaPrecio}'))
        ORDER BY prioridad ASC, linea ASC LIMIT 1;`;
        return await this.queryAll(sql);
    }
     public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS descuentos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async selectDatosDescuentosEspeciales(sql: string) {

        console.log(sql);

        return await this.queryAll(sql);

       return new Promise((resolve, reject) => {
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

    public selectDatosClientes(Cliente: string) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT GroupCode FROM Clientes WHERE ItemCode = "' + Cliente + '" ';
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

    public async ActualizadecuentosSap(objeto: any, ItemCode: string, CardCode: string) {

        let obj = JSON.parse(objeto.data);
        await this.Borradescuentosduplicados(obj);
        
        return new Promise((resolve, reject) => {
            console.log(obj);
            let sql = 'INSERT INTO descuentos VALUES ';
            for (let d of obj.respuesta) {

                sql += `(NULL , '${d.PRODUCTO}', '${d.CLIENTE}', '${d.PRIORIDAD}' , 
                        '${d.LINEA}', '${d.LISTA_PRECIO}', '${d.DESCUENTO}', '${d.DESDE}', '${d.HASTA}', '${d.TIPO}', 
                        '${d.CANTIDAD}', '${d.PROPIEDADES}', '${d.FABRICANTE}', '${d.GRUPO_CLIENTE}', '${d.GRUPO_PRODUCTO}'
                        , '${d.CANTIDAD_LIBRE}', '${d.MAXIMO_LIBRE}'),`;
            }
            let sqlx = sql.slice(0, -1);
            console.log(sqlx);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async Borradescuentosduplicados(objeto: any) {

        for (let d of objeto.respuesta) {

            let sql = `SELECT count(*) as cantidad FROM descuentos WHERE ItemCode = '${d.PRODUCTO}' AND CardCode='${d.CLIENTE}' AND PRIORIDAD = '${d.PRIORIDAD}' AND LINEA = '${d.LINEA}' AND LISTA_PRECIO = '${d.LISTA_PRECIO}' AND TIPO = '${d.TIPO}'  AND PROPIEDADES = '${d.PROPIEDADES}'  AND FABRICANTE = '${d.FABRICANTE}' AND GRUPO_CLIENTE = '${d.GRUPO_CLIENTE}' AND GRUPO_PRODUCTO = '${d.GRUPO_PRODUCTO}'`;
            console.log(sql);
            
            let cantidad = await this.queryAll(sql);
            console.log("catidad conseguida",cantidad[0].cantidad);

            if(cantidad[0].cantidad >= 1){
                let sql = `DELETE FROM descuentos WHERE ItemCode = '${d.PRODUCTO}' AND CardCode='${d.CLIENTE}' AND PRIORIDAD = '${d.PRIORIDAD}' AND LINEA = '${d.LINEA}' AND LISTA_PRECIO = '${d.LISTA_PRECIO}' AND TIPO = '${d.TIPO}'  AND PROPIEDADES = '${d.PROPIEDADES}'  AND FABRICANTE = '${d.FABRICANTE}' AND GRUPO_CLIENTE = '${d.GRUPO_CLIENTE}' AND GRUPO_PRODUCTO = '${d.GRUPO_PRODUCTO}'`;
                console.log(sql);
                await this.exe(sql);
            }

        }
    }

}
