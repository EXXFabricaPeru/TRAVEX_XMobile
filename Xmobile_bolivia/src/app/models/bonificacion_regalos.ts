import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class bonificacion_regalos extends Databaseconf {
    public configService: ConfigService;

    public async find() {
        let sql = `SELECT * FROM bonificacion_regalos`;
        return await this.queryAll(sql);
    }

    public async findOne(code_bonificacion_cabezera: any, territorio: string) {
        let sql = `SELECT r.* , ca.codeMid,  ca.nombre, ca.cantidad_regalo, ca.maximo_regalo, ca.unindad_regalo, ca.cantidad_compra
        FROM bonificacion_regalos r, bonificacion_ca ca 
        WHERE r.code_bonificacion_cabezera = '${code_bonificacion_cabezera}' 
        AND ca.TerritoryID ='${territorio}'
        AND r.code_bonificacion_cabezera=ca.code`;//TERRITORIO TODO
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS bonificacion_regalos`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {

        if (contador == 0) {
            let sql = 'DELETE FROM bonificacion_regalos;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO bonificacion_regalos VALUES ';
            for (let o of obj.respuesta) {

                // if (o.id_bonificacion_cabezera == "142") {
                sql += `(NULL, '${o.code_regalo}','${o.producto_nombre_regalo}','${o.id_bonificacion_cabezera}','${o.U_regla}'),`;

                // }
            }
            let sqlx = sql.slice(0, -1);
            console.log("INSERT INTO REGALOS ", sqlx);

            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async showTable() {
        let sql = `SELECT * FROM bonificacion_regalos;`;
        return await this.queryAll(sql);
    }
    /*
     public async markTable2(item: string, cantidad: number, id: string, baseqty: string) {
         return new Promise((resolve, reject) => {
             let sql = `UPDATE bonificacion_regalos SET cantidad_en_linea = (Select sum(Quantity*BaseId) from detalles where idDocumento='${id}') WHERE U_regla = '${item}';`;
             this.executeSQL(sql).then((data: any) => {
                 resolve(data);
             }).catch((e: any) => {
                 reject(e);
             });
         });
     }
 
     public async markTable(item: string, cantidad: number, id: string, baseqty: string) {
         return new Promise((resolve, reject) => {
             let sql = `UPDATE bonificacion_regalos SET cantidad_en_linea = ${cantidad}, baseqty = '${baseqty}', idDocumento = '${id}' WHERE U_regla = '${item}';`;
             this.executeSQL(sql).then((data: any) => {
                 resolve(data);
             }).catch((e: any) => {
                 reject(e);
             });
         });
     }
 
     public async cleanTable() {
         console.log("accion bonificvacion cleanTable");
         return new Promise((resolve, reject) => {
             return true;
             let sql = `UPDATE bonificacion_regalos SET cantidad_en_linea = 0, cantidad_usado = 0, idDocumento = '0', baseqty = '0';`;
             this.executeSQL(sql).then((data: any) => {
                 resolve(data);
             }).catch((e: any) => {
                 reject(e);
             });
         });
     }
 
     public async totalCantidad() {
         let sql = `SELECT U_ID_bonificacion, SUM(cantidad_en_linea) AS cantidadlinea, SUM(cantidad_usado) AS usado, 
                         (SUM(cantidad_en_linea) + SUM(cantidad_usado)) AS total 
                  FROM bonificacion_regalos GROUP BY U_ID_bonificacion HAVING (SUM(cantidad_en_linea) + SUM(cantidad_usado)) > 0 `;
         return await this.queryAll(sql);
     }
     public async selectAll() {
         let sql = `SELECT * FROM bonificacion_regalos`;
         return await this.queryAll(sql);
     }
 */
}
