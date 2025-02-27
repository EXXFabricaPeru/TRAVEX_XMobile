import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
import { bonificacion_compras } from "./bonificacion_compras";
export class bonificacionesDocCabezera extends Databaseconf {
    public configService: ConfigService;


    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS bonificacionesDocCabezera`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {


        if (contador == 0) {
            let sql = 'DELETE FROM bonificacionesDocCabezera;';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);

            //   console.log("DEVD new bonificaciones documentos insert ", obj);
            console.log("DEVD new bonificaciones a filtrar ", obj.respuesta);
            let sql = 'INSERT INTO bonificacionesDocCabezera VALUES ';
            obj.respuesta = obj.respuesta.filter(value => {
                return value.id_regla_bonificacion == 3 ||
                    value.id_regla_bonificacion == 9 ||
                    value.id_regla_bonificacion == 10 ||
                    value.id_regla_bonificacion == 11 ||
                    value.id_regla_bonificacion == 12 ||
                    value.id_regla_bonificacion == 13
            })
            console.log("DEVD new bonificaciones a filtrar before ", obj.respuesta);
            for (let o of obj.respuesta) {

                // console.log("o ", o);
                if (!o.porcentaje) {
                    o.porcentaje = 0;
                }
                //  o.codigo_canal = 'OTROS';

                sql += `(NULL, 
                        '${o.Code}',
                        '${o.nombre}',
                    '${o.fecha_inicio}',
                    '${o.fecha_fin}',
                    '${Number(o.maximo_regalo)}',
                    '${o.U_observacion}',
                    '${o.tipo}',
                    '${Number(o.cantidad_compra)}',
                    '${o.unindad_compra}',
                    '${Number(o.cantidad_regalo)}',
                    '${o.unindad_regalo}',
                    '${o.cabezera_tipo}',
                    '${o.grupo_cliente}',
                    '${o.extra_descuento}',
                    '${o.opcional}',
                    '${o.territorio}',
                    '${o.idTerritorio}',
                    '${o.cantidad_maxima_compra}',
                    '${o.monto_total}',
                    '${o.id_cabezera_tipo}',
                    '${o.id_regla_bonificacion}',
                    '${o.TerritoryID}',
                    '${o.idUser}',
                    '${o.Description}',
                       '${o.id}',
                       '${o.porcentaje}',
                       '${o.codigo_canal}',
                       '${o.fijo}'
                    ),`;


            }

            let sqlx = sql.slice(0, -1);
            // console.log("sqlx ", sqlx);
            this.executeSQL(sqlx).then(async (data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public async showAll() {
        let sql = `SELECT * FROM bonificacionesDocCabezera;`;
        return await this.queryAll(sql);
    }

    public async selectAll($territorio) {
        let sql = `SELECT *  FROM bonificacionesDocCabezera where TerritoryID='${$territorio}' `;
        console.log("query bonificaciones cabecera-> ", sql);
        let resp: any = await this.queryAll(sql);
        console.log("respfindAll ", resp);
        let bonoData = [];
        for await (let item of resp) {
            let modelAux = new bonificacion_compras();
            let productosCompra: any = await modelAux.findForCabezera(item.id_bonificacion_cabezera);
            item.productosCompra = productosCompra;
            bonoData.push(item);

        }
        return bonoData;
    }
    public async selectOne() {
        let sql = `SELECT *  FROM bonificacionesDocCabezera `;
        let resp: any = await this.queryAll(sql);
        return resp;
    }


}
