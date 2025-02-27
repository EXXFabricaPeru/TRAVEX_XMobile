import { Databaseconf } from "../databaseconf";
import { ConfigService } from "../config.service";
import * as moment from 'moment';
export class Bonificaciones extends Databaseconf {
    public configService: ConfigService;

    public async findOne() {
        let sql = `SELECT * FROM bonificacion_ca`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS bonificacion_ca`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {

        console.log("=--------- insertAll ");

        if (contador == 0) {
            let sql = 'DELETE FROM bonificacion_ca;';
            console.log(sql);
            await this.exe(sql);
        }

        return new Promise(async (resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            console.log("  obj.respuesta  ", obj.respuesta);
            let sql = 'INSERT INTO bonificacion_ca VALUES ';
            let TiposBonos = []
            let rta = obj.respuesta.find(item => item.id_regla_bonificacion == '1')
            console.log("------->  uno ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '2')
            console.log("------->  2 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '3')
            console.log("------->  3 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '4')
            console.log("------->  4 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '5')
            console.log("------->  5 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '6')
            console.log("------->  6 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '7')
            console.log("------->  7 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '8')
            console.log("------->  8 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '9')
            console.log("------->  9 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '10')
            console.log("------->  10 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '11')
            console.log("------->  11 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '12')
            console.log("------->  12 ", { rta });

            rta = obj.respuesta.find(item => item.id_regla_bonificacion == '13')
            console.log("------->  13", { rta });



            for (let o of obj.respuesta) {

                // /*        
                // if (o.grupo_cliente == 1 || o.grupo_cliente == "1") {
                //     o.grupo_cliente = "LPZ PREVENTA";
                // }*/


                // if (o.tipo != "PRODUCTOS ESPECIFICOS") {
                //     console.log("distinto a productos especificos ", o);

                // }


                // if (o.id_regla_bonificacion == "8") {
                //     console.log("++++++++++++++++++++++ 8 ", o);
                // }
                sql += `(NULL, 
                            '${o.id}',
                            '${o.U_observacion}',
                            '${o.cabezera_tipo}',
                            '${o.cantidad_compra}',
                            '${o.cantidad_regalo}',
                            '${o.fecha_fin}',
                            '${o.fecha_inicio}',
                            '${o.grupo_cliente}',
                            '${o.maximo_regalo}',
                            '${o.nombre}',
                            '${o.tipo}',
                            '${o.unindad_compra}',
                            '${o.unindad_regalo}',
                            '${o.extra_descuento}',
                            '${o.opcional}',
                            '${o.Code}',
                            '${o.codigo_canal}',
                            '${o.id_regla_bonificacion}',
                                '${o.Description}',
                                    '${o.TerritoryID}',
                                    '${o.id_cliente_dosificacion}',
                                    '${o.cliente_dosificacion}',
                                    '${o.fijo}'
                            ),`;


            }
            let sqlx = sql.slice(0, -1);

            if (contador == 0) {
                let sql = 'DELETE FROM bonificacionesDocCabezera;';
                await this.exe(sql);
            }

            let sqlDoc = 'INSERT INTO bonificacionesDocCabezera VALUES ';
            obj.respuesta = obj.respuesta.filter(value => {
                return value.id_regla_bonificacion == 3 ||
                    value.id_regla_bonificacion == 9 ||
                    value.id_regla_bonificacion == 10 ||
                    value.id_regla_bonificacion == 11 ||
                    value.id_regla_bonificacion == 12 ||
                    value.id_regla_bonificacion == 13
            })



            console.log("DEVD new bonificaciones a filtrar before ", obj.respuesta);
            if(obj.respuesta.length > 0){
                for (let o of obj.respuesta) {

                    // console.log("o ", o);
                    if (!o.porcentaje) {
                        o.porcentaje = 0;
                    }
                    //  o.codigo_canal = 'OTROS';

                    sqlDoc += `(NULL, 
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

                let sqlxD = sqlDoc.slice(0, -1);
                console.log(sqlxD);
                this.executeSQL(sqlxD).then(data => {
                    console.log('BONIFICACIONES 2DA PARTE INSERTADOS CORRECTAMENTE');

                })
            }

            console.log(sqlx);
            this.executeSQL(sqlx).then(async (data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async getBonificacionesDisponibles(
        TerritoryID, codigo_canal, grupo_cliente, grupoDosificacion, itemCode, itemGroup

    ) {
        // let dateNow = moment().format('YYYY-MM-DD');
        console.log("getBonificacionesDisponibles()")

        console.log({ TerritoryID });
        console.log({ codigo_canal });
        console.log({ grupo_cliente });
        console.log({ grupoDosificacion });
        console.log({ itemCode });
        console.log({ itemGroup });

        let sql = `SELECT * FROM bonificacion_ca
         WHERE DATE() BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)  

         ORDER BY id DESC LIMIT 1 ;`;
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public async insertCompraUsados(code_bonificacion_cabezera: any,
        code_compra: any, cantidad: any, unidad: any, cardCode: any, estado: any, id_vendedor: any, idDocumento: any, idDocumentoDetalle: any, total: any) {
        console.log("insertCompraUsados() ")

        let sql = `INSERT INTO  bonificaciones_usadas VALUES 
        (NULL, '${code_bonificacion_cabezera}', '${code_compra}', '${cantidad}', '${unidad}', '${cardCode}', '${estado}', '${id_vendedor}', '${idDocumento}', '${idDocumentoDetalle}', '${total}')`;
        console.log("sql ", sql);
        return await this.executeSQL(sql);
    }
    public async getCompraUsados() {
        let sql = `SELECT * FROM  bonificaciones_usadas WHERE code_compra IN (SELECT code_compra FROM bonificacion_compras WHERE estado ="1" );`;
        return await this.queryAll(sql);
    }

    public async getFind(code: any, territorio: string) {
        console.log("buscar master ", code);
        let sql = `SELECT * FROM bonificacion_ca where code=${code} AND TerritoryID = '${territorio}';`; //TERRITPORIO TODO
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }


    public async getCompraUsadosAgrupadoDoc(territorio: string) {
        let sql = `SELECT ca.cantidad_compra,  SUM(u.cantidad) AS totalCantidad, u.code_bonificacion_cabezera, u.idDocumento, ca.nombre, ca.cabezera_tipo
         FROM  bonificaciones_usadas u, bonificacion_ca ca 
         WHERE ca.code=u.code_bonificacion_cabezera  
         AND  ca.TerritoryID = '${territorio}'
         GROUP BY u.code_bonificacion_cabezera ORDER BY ca.cabezera_tipo DESC;`;
        return await this.queryAll(sql);
    }


    public async DeleteBoniUsadas() {
        let sql = 'DELETE FROM bonificaciones_usadas;';
        console.log("sql ", sql);
        return await this.executeSQL(sql);
    }

    public async getBonificacionExist(code: any) {
        let dateNow = moment().format('YYYY-MM-DD');

        //  console.log("DEVD for sql grupoCliente ", grupoCliente, " grupoProducto ", grupoProducto);//${sqlgrupoProducto}  ${sqlgrupoCliente}
        let sql2 = `SELECT * FROM bonificacion_ca WHERE DATE() BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)  AND code=${code} ;`;// ORDER BY id DESC LIMIT 1 
        console.log("sql cabezera valid", sql2);
        let databoni = await this.queryAll(sql2);
        console.log("cabezeraBonificaion", databoni);
        let sql = `SELECT * FROM bonificacion_ca WHERE DATE() BETWEEN DATE(fecha_inicio) AND DATE(fecha_fin)  AND code=${code} AND id_regla_bonificacion !='3' ;`;// ORDER BY id DESC LIMIT 1 
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }


    public async DeleteBoniUsadasOne(code, id_cabezera) {
        console.log("DeleteBoniUsadasOne()");
        let sql = 'DELETE FROM bonificaciones_usadas WHERE code_bonificacion_cabezera = "' + id_cabezera + '" ;'; //AND code_compra = "' + code + '"
        console.log("sql ", sql);
        return await this.executeSQL(sql);
    }

    /*
        bonificaciones_usadas (
            id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            code_bonificacion_cabezera VARCHAR(255) NULL,
            code_compra VARCHAR(255) NULL,
            cantidad  integer NULL,
            unidad VARCHAR(255) NULL,
            cardCode VARCHAR(255) NULL,
            estado VARCHAR(255) NULL,
            id_vendedor VARCHAR(255) NULL,
            idDocumento VARCHAR(255) NULL,
            idDocumentoDetalle VARCHAR(255) NULL,
            total VARCHAR(255) NULL
    */
    /*
        public async showTable() {
            let sql = `SELECT * FROM bonificacion_ca;`;
            return await this.queryAll(sql);
        }*/


    public async getFindBonoDocument(code: any, territorio) {
        console.log("buscar master ", code);
        let sql = `SELECT * FROM bonificacionesDocCabezera where id_bonificacion_cabezera=${code} AND  TerritoryID = '${territorio}' ;`; //AND ( id_regla_bonificacion='9' OR id_regla_bonificacion='10')
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }


    public async getFindBonoDocumentAll() {
        console.log("buscar master code ");
        let sql = `SELECT * FROM bonificacionesDocCabezera `; //AND ( id_regla_bonificacion='9' OR id_regla_bonificacion='10')
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    ///// this


    public async showAll() {
        let sql = `SELECT * FROM bonificacion_ca;`;
        console.log(sql);
        return await this.queryAll(sql);
    }
    public async showAllCompras() {
        let sql = `SELECT * FROM bonificacion_compras;`;
        return await this.queryAll(sql);
    }
    public async showAllRegalos() {
        let sql = `SELECT * FROM bonificacion_regalos;`;
        return await this.queryAll(sql);
    }








    public async getBonificacionExistByCode(code: any) {
        // let dateNow = moment().format('YYYY-MM-DD');

        //  console.log("DEVD for sql grupoCliente ", grupoCliente, " grupoProducto ", grupoProducto);//${sqlgrupoProducto}  ${sqlgrupoCliente}
        let sql = `SELECT * FROM bonificacion_ca WHERE  code=${code} ;`;// ORDER BY id DESC LIMIT 1 
        console.log("sql ", sql);
        return await this.queryAll(sql);
    }


    /*
        bonificaciones_usadas (
            id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            code_bonificacion_cabezera VARCHAR(255) NULL,
            code_compra VARCHAR(255) NULL,
            cantidad  integer NULL,
            unidad VARCHAR(255) NULL,
            cardCode VARCHAR(255) NULL,
            estado VARCHAR(255) NULL,
            id_vendedor VARCHAR(255) NULL,
            idDocumento VARCHAR(255) NULL,
            idDocumentoDetalle VARCHAR(255) NULL,
            total VARCHAR(255) NULL
    */
    /*
        public async showTable() {
            let sql = `SELECT * FROM bonificacion_ca;`;
            return await this.queryAll(sql);
        }*/




}
