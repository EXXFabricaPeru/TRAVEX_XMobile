import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class bonificacion_compras extends Databaseconf {
    public configService: ConfigService;

    public async find() {
        let sql = `SELECT * FROM bonificacion_compras`;
        return await this.queryAll(sql);
    }

    public async findForCabezera(code_bonificacion_cabezera: any) {
        console.log("DEVD filter by ", code_bonificacion_cabezera);
        let sql = `SELECT * FROM bonificacion_compras WHERE code_bonificacion_cabezera = '${code_bonificacion_cabezera}'`;
        console.log("DEVD sql findForCabezera ", sql);
        return await this.queryAll(sql);
    }

    public async validProductoInCompras(itemCode: any, code_bonificacion_cabezera: any) {
        console.log("filter validProductoInCompras ", itemCode);
        let sql = `SELECT * FROM bonificacion_compras WHERE code_compra = '${itemCode}' AND code_bonificacion_cabezera = '${code_bonificacion_cabezera}'`;
        console.log("sql validProductoInCompras ", sql);
        return await this.queryAll(sql);
    }

    public async validProductoInComprasGrupo(grupo: any, code_bonificacion_cabezera: any) {
        console.log("filter validProductoInCompras grupoo ", grupo);
        let sql = `SELECT * FROM bonificacion_compras WHERE producto_nombre_compra = '${grupo}' AND code_bonificacion_cabezera = '${code_bonificacion_cabezera}'`;
        console.log("sql validProductoInCompras ", sql);
        return await this.queryAll(sql);
    }

    public async validProductoUnindadGrupo(unindad_compra: any, code_bonificacion_cabezera: any) {
        console.log("filter validProductoUnindadGrupo bonificacion_ca ", unindad_compra);
        let sql = `SELECT * FROM bonificacion_ca WHERE unindad_compra = '${unindad_compra}' AND code = '${code_bonificacion_cabezera}'`;
        console.log("sql validProductoUnindadGrupo ", sql);
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS bonificacion_compras`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        console.log("objeto COMPRADOS INSERT ", objeto);
        if (contador == 0) {
            let sql = 'DELETE FROM bonificacion_compras;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO bonificacion_compras VALUES ';
            for (let o of obj.respuesta) {
                // if (o.id_bonificacion_cabezera == "142") {
                // console.log("o.id_bonificacion_cabezera  ", o.id_bonificacion_cabezera);


                if (!o.producto_cantidad) {
                    o.producto_cantidad = 0;
                }
                sql += `(NULL, '${o.code_compra}','${o.producto_nombre_compra}','${o.id_bonificacion_cabezera}','${o.U_bonificacion}', '${o.producto_cantidad}','${o.estado}'),`;
                // }

            }
            let sqlx = sql.slice(0, -1);
            console.log("INSERT INTO COMPRADOS ", sqlx);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async showAll() {
        let sql = `SELECT * FROM bonificacion_compras;`;
        return await this.queryAll(sql);
    }

    public async getIdCabezera(unindad_compra: any) {

        let sql = `SELECT * FROM bonificacion_compras WHERE code_compra = '${unindad_compra}' `;
        console.log("sql getIdCabezera ", sql);
        return await this.queryAll(sql);
    }

    public async getGruposIdCabezera(value: any) {

        let sql = `SELECT * FROM bonificacion_compras WHERE producto_nombre_compra = '${value}' `;
        console.log("sql getIdCabezera ", sql);
        return await this.queryAll(sql);
    }
    public async getGruposClienteIdCabezera(value: any) {

        let sql = `SELECT * FROM bonificacion_compras WHERE producto_nombre_compra = '${value}' `;
        console.log("sql getIdCabezera ", sql);
        return await this.queryAll(sql);
    }
    /*
        public markeUseCardCodeCompra(code) {
            return new Promise((resolve, reject) => {
             
                let sql = `UPDATE bonificacion_compras SET U_bonificacion='1' WHERE code_compra='${code}'`;
                this.executeSQL(sql).then((data: any) => {
                    resolve(data);
                }).catch((e: any) => {
                    reject(e);
                });
            });
        }
    
        public async getCodeUsesCompras(id_cabezera) {
    
            let sql = `SELECT * FROM bonificacion_compras WHERE U_bonificacion='1'  AND code_bonificacion_cabezera='${id_cabezera}'`;
            console.log("sql getIdCabezera ", sql);
            return await this.queryAll(sql);
        }*/
}
