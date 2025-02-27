import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';
export class promocionaes extends Databaseconf {
    public configService: ConfigService;


    public async findCurrent(cardCode) {
        let fecha = moment().format('YYYY-MM-DD');
        let sql = `SELECT * FROM promociones where U_CardCode='${cardCode}'  AND U_FechaMaximoCobro>=${fecha} AND cumpleMeta=1 AND  DATE(U_FechaMaximoCobro) >= DATE()`; // 

        console.log("sql ", sql);
        return await this.queryAll(sql);
    }
    public async findCurrentAll(cardCode) {
        let fecha = moment().format('YYYY-MM-DD');
        let sql = `SELECT * FROM promociones where U_CardCode='${cardCode}'  AND U_FechaMaximoCobro>=${fecha} AND DATE(U_FechaMaximoCobro) >= DATE()`; // AND cumpleMeta=1

        console.log("sql ", sql);
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS promociones`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {

        let sql = 'DELETE FROM promociones;';
        await this.exe(sql);
        
        let sql_exist = `SELECT * from  promociones ;`;
        let dataPromos: any = await this.queryAll(sql_exist);
        console.log("dataPromos ", dataPromos);

        //if (contador == 0) {
      
       // }
        return new Promise((resolve, reject) => {


            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO promociones VALUES ';

            for (let o of obj.respuesta) {

                // if (o.id == 1) {
                //     o.U_CardCode = "1002900015";
                // }
                const saldoInMemory = dataPromos.filter(function (value) {
                    return value.U_CardCode == o.U_CardCode && value.U_CodigoCampania == o.U_CodigoCampania
                })

                if (saldoInMemory.length > 0) {

                    o.U_Saldo = saldoInMemory[0].U_Saldo;
                    console.log("data encontrado ", saldoInMemory)
                    console.log("de ", o)
                }

                o.cumpleMeta = 0;
                console.log("o.U_Acumulado ", Number(o.U_Acumulado));
                console.log("o.U_Meta ", Number(o.U_Meta));
                if (Number(o.U_Acumulado) >= Number(o.U_Meta)) {
                    console.log("es mayor o iguak ", o);
                    o.cumpleMeta = 1;
                }
                // } else {
                //     o.U_Saldo = 0;
                // }

                sql += `(NULL, 
                    '${o.Code}',
                    '${o.Name}',
                '${o.U_CardCode}',
                '${o.U_CodigoCampania}',
                '${o.U_ValorGanado}',
                '${o.U_FechaInicio}',
                '${o.U_FechaFinal}',
                '${o.U_DocEntry}',
                '${o.U_DocType}',
                '${o.U_FechaMaximoCobro}',
                '${o.U_ValorSaldo}',
                '${o.U_Saldo}',
                '${o.U_Meta}',
                '${o.U_Acumulado}',
                '${o.cumpleMeta}'
                
                ),`;
            }

            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then(async (data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async showAll() {
        let sql = `SELECT * FROM promociones;`;
        console.log(sql);
        return await this.queryAll(sql);
    }
    /*
    
        CREATE TABLE IF NOT EXISTS promocionesUsadas(
            id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            code varchar(255) NULL,
            name varchar(255) NULL,
            U_CardCode varchar(255) NULL,
            cod varchar(255) NULL,
            fecha varchar(255) NULL,
            U_ValorSaldo integer NULL,
            U_Saldo integer NULL
            );
    */

    public async insertUse(objeto: any, cod: any, montoUsar: any) {
        let fechaMoment = moment().format('YYYY-MM-DD');
        let sqlaux = `UPDATE promociones SET U_Saldo=U_Saldo-${montoUsar} where U_CodigoCampania='${objeto.U_CodigoCampania}' AND  U_CardCode ='${objeto.U_CardCode}' `;
        console.log("sqlaux ", sqlaux);
        await this.executeSQL(sqlaux);
        if (objeto.U_ValorSaldo == null) {
            objeto.U_ValorSaldo = 0;
        }

        /*let sqlauxDoc = `UPDATE documentos SET U_CodigoCampania = '${objeto.U_CodigoCampania}' , U_Saldo = '${objeto.U_Saldo}', U_ValorSaldo= '${montoUsar}' WHERE cod = '${cod}'`;
        console.log("sql sqlauxDoc", sqlauxDoc);
        await this.executeSQL(sqlauxDoc);*/

        let sqlauxDoc = `UPDATE documentos SET U_CodigoCampania = '${objeto.U_CodigoCampania}'  WHERE cod = '${cod}'`;
        console.log("sql sqlauxDoc", sqlauxDoc);
        await this.executeSQL(sqlauxDoc);

        let sql = `INSERT INTO promocionesUsadas VALUES (NULL, '${objeto.U_CodigoCampania}','${objeto.name}','${objeto.U_CardCode}','${cod}','${fechaMoment}',${montoUsar}, ${objeto.U_Saldo});`;
        console.log("sql insert use ", sql);
        return await this.executeRaw(sql);
    }

    public async showAllUses() {
        let sql = `SELECT * FROM promocionesUsadas;`;
        return await this.queryAll(sql);
    }

    public async showAllUsesBycod(code) {
        let sql = `SELECT * FROM promocionesUsadas WHERE  cod = '${code}' ;`;
        console.log("promos usadas por dfa sql ", sql);
        return await this.queryAll(sql);
    }

    public deleteUse(cod: any) {
        return new Promise(async (resolve, reject) => {


            let sql_promoBorrar = `SELECT * from  promocionesUsadas WHERE cod = '${cod}' `;
            let dataBorrar = await this.queryAll(sql_promoBorrar);
            console.log("DEVD dataBorrar ", dataBorrar);

            let sqlDoc = `UPDATE documentos SET U_CodigoCampania = '0' , U_Saldo = '0', U_ValorSaldo= '0' WHERE cod = '${cod}'`;
            console.log("DEVD sql sqlDoc", sqlDoc);
            await this.executeSQL(sqlDoc);
            let sql_total = `SELECT SUM(Quantity * Price) as total, COUNT(*) AS countItems from  detalle where idDocumento='${cod}' and bonificacion=0 `;
            //let sql_totalbonif = `SELECT SUM(Quantity * Price) as total from  detalle where idDocumento='${id}' and bonificacion=0`;
            let totaldoc = await this.queryAll(sql_total);
            let descuentoDividido = (dataBorrar[0].U_ValorSaldo / totaldoc[0].countItems).toFixed(2);
            console.log("DEVD descuentoDividido ", descuentoDividido);

            let sqlupdate_0 = "";
            sqlupdate_0 = `UPDATE detalle SET XMPROMOCIONCABEZERA=0,  
              U_4DESCUENTO = U_4DESCUENTO - ${descuentoDividido}  where idDocumento='${cod}' and bonificacion=0  `;
            console.log("DEVD sqlupdate_0 ", sqlupdate_0);
            await this.executeSQL(sqlupdate_0);

            let sqlaux = `UPDATE promociones SET U_Saldo=U_Saldo+${dataBorrar[0].U_ValorSaldo} where U_CodigoCampania='${dataBorrar[0].code}' AND  U_CardCode ='${dataBorrar[0].U_CardCode}'  `;
            console.log("DEVD sqlaux ", sqlaux);
            await this.executeSQL(sqlaux);
            let sql = `DELETE FROM promocionesUsadas WHERE cod = '${cod}' `;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

}
