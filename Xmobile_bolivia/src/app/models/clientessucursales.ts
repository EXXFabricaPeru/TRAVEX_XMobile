import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import {Camposusuario} from "../services/camposusuario.service"


export class Clientessucursales extends Databaseconf {
    public configService: ConfigService;
    public Camposusuario: Camposusuario;

    public async insert(o: any, id: any) {
        return new Promise((resolve, reject) => {
            let sql = `INSERT INTO clientessucursales VALUES(NULL, 0, '${o.AddresName}', '${o.Street}', '${o.State}', '${o.FederalTaxId}', '${o.CreditLimit}', '${o.CardCode}', '${o.User}', '${o.Status}', '${o.DateUpdate}', ${id},0);`;
            console.log("sql ", sql);

            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public async update(o: any, id: any) {
        return new Promise((resolve, reject) => {
            let sql = `UPDATE clientessucursales SET AddresName = '${o.AddresName}', 
            Street = '${o.Street}', State = '${o.State}', 
            FederalTaxId = '${o.FederalTaxId}', CreditLimit = '${o.CreditLimit}', 
            CardCode = '${o.CardCode}', User = '${o.User}', Status = '${o.Status}', 
            DateUpdate = '${o.DateUpdate}' WHERE idDocumento = ${id};`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM clientessucursales;';
            //let sql = 'DELETE FROM clientessucursales WHERE idUser = "' + idx + '"';
            await this.exe(sql);
        }
        return new Promise(async (resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO clientessucursales VALUES ';
            console.log("obj.respuesta ", obj.respuesta);
            let campos = new Camposusuario();
            let session = await campos.consultasesion();
            for (let o of obj.respuesta) {
                if (o.CardCode == '1002511105') {
                    console.log("--> sucursal  ", o);

                }
                
                let sql2 = await campos.camposusuariosinc(o,4,session);

                let Street = o.Street.replace(/['"]+/g, '');

                sqlz += `(NULL, ${idx}, '${o.AddresName}', '${Street}', '${o.State}', '${o.FederalTaxId}', '${o.CreditLimit}', '${o.CardCode}', '${o.User}', '${o.Status}', '${o.DateUpdate}', 0
                , '${o.TaxCode}'
                , '${o.AdresType}'
                , '${o.u_zona}'
                , '${o.u_lat}'
                , '${o.u_lon}'
                , '${o.u_territorio}'
                , '${o.u_vendedor}'
                , 1
                ,'${o.Tax}'
                ,'${o.RowNum}','${o.City}'`+sql2+`
                ),`;

               /* let sqlx = sqlz.slice(0, -1);
                console.log(sqlx);
                let respuesta  = await this.insertrdata(sqlx);*/
            }
            let sqlx = sqlz.slice(0, -1);
            console.log(sqlx);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public async insertrdata(sql){

        return new Promise((resolve, reject) => {
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertAll2(objeto: any, idx: number, contador = 0) {
        return new Promise(async (resolve, reject) => {
            //let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO clientessucursales VALUES ';
            console.log("obj.respuesta ", objeto)
            let campos = new Camposusuario();
            let session = await campos.consultasesion();
            for (let o of objeto) {
                if (o.CardCode == '1002511105') {
                    console.log("--> sucursal  ", o);

                }

               
                let sql2 = await campos.camposusuariosinc(o,4,session);

                sqlz += ` (NULL, ${idx}, '${o.AddresName}', '${o.Street}', '${o.State}', '${o.FederalTaxId}', '${o.CreditLimit}', '${o.CardCode}', '${o.User}', '${o.Status}', '${o.DateUpdate}', 0
                , '${o.TaxCode}'
                , '${o.AdresType}'
                , '${o.u_zona}'
                , '${o.u_lat}'
                , '${o.u_lon}'
                , '${o.u_territorio}'
                , '${o.u_vendedor}'
                , 1
                ,'${o.Tax}'
                ,'${o.RowNum}','${o.City}'`+sql2+`
                ),`;
            }
            let sqlx = sqlz.slice(0, -1);
            console.log("sqlx",sqlx);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public findAll(idx: string, idUser: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM clientessucursales WHERE  CardCode = '${idx}'`;
            console.log(" sql sucursales select ", sql);
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

    public findByCarcodeAddress(cardCode, address) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM clientessucursales where CardCode= '${cardCode}' AND id= '${address}'; `;
            console.log(" sql sucursales select ", sql);
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

    public async delete(id: any) {
        return new Promise((resolve, reject) => {
            let sql = `DELETE FROM clientessucursales WHERE cardCode = '${id}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rowsAffected);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public findOne(id: string) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM clientessucursales WHERE  id = '${id}'`;
            console.log(" sql sucursales select ", sql);
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

    public selectDocument(idx: string) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM clientessucursales WHERE idDocumento = '${idx}'`;
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


    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS clientessucursales;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    /*
  UPDATE clientessucursales SET AddresName = 'bbjk', u_lat = '0.001848', u_lon = '0.000195', export=0 WHERE id = '2464'

    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
    idUser varchar(11) NOT NULL,
    AddresName varchar(255) NOT NULL,
    Street varchar(255) NOT NULL,
    State varchar(255) NULL,
    FederalTaxId varchar(255) NULL,
    CreditLimit varchar(255) NULL,
    CardCode varchar(255) NULL,

    User varchar(25) NULL,
    Status varchar(25) NULL,
    DateUpdate varchar(255) NULL,
    idDocumento integer NULL,

    TaxCode varchar(255) NULL,
    AdresType varchar(25) NULL, 
    u_zona varchar(255) NULL,
    u_lat varchar(255) NULL, 
    u_lon varchar(255) NULL,
    u_territorio varchar(255) NULL, 
    u_vendedor varchar(25) NULL
    */

    public async insertRegister(d: any, id: any) {
        console.log("d ", d)
        let fecha = this.getFechaView();
        for await (let o of d) {
            
            console.log("obj para el insert ", o, " -- id : ", id);
            
            if (!o.LineNum && o.LineNum != 0) {
                console.log("DISTINTO DE CERO ");

                o.LineNum = o.RowNum
            } else {
                console.log("DISTINTO con  ", o.LineNum);

            }

            let campos = new Camposusuario();
            let sql2 = await campos.camposusuario(o,4);

            let sql = `INSERT INTO clientessucursales VALUES ( 
                NULL,
             ${id}, 
            '${o.AddresName}', 
            '${o.Street}', 
            '${o.State}',
             '${o.FederalTaxId}', 
             '${o.CreditLimit}', 
             '${o.CardCode}', 
             '${o.User}', 
             '${o.Status}', 
             '${o.DateUpdate}',
              0
            , '${o.TaxCode}'
            , '${o.AdresType}'
            , '${o.u_zona}'
            , '${o.u_lat}'
            , '${o.u_lon}'
            , '${o.u_territorio}'
            , '${o.u_vendedor}',
            0,
            0,
            '${o.LineNum}','${o.City}'`+sql2+`
            )`;
            console.log("sql clientessucursales ----->", sql);
            await this.executeSQL(sql);
        }



        /*
                    id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                    idUser varchar(11) NOT NULL,
                    AddresName varchar(255) NOT NULL,
                    Street varchar(255) NOT NULL,
                    State varchar(255) NULL,
                    FederalTaxId varchar(255) NULL,
                    CreditLimit varchar(255) NULL,
                    CardCode varchar(255) NULL,
                    User varchar(25) NULL,
                    Status varchar(25) NULL,
                    DateUpdate varchar(255) NULL,
                    idDocumento integer NULL,
                    TaxCode varchar(255) NULL,
                    AdresType varchar(25) NULL, 
                    u_zona varchar(255) NULL,
                    u_lat varchar(255) NULL, 
                    u_lon varchar(255) NULL,
                    u_territorio varchar(255) NULL, 
                    u_vendedor varchar(25) NULL
        */

    }
    public updateLocate(lat: any, lng: any, ubi: string, id: any) {
        return new Promise((resolve, reject) => {
            let sql = `UPDATE clientessucursales SET Street = '${ubi}', u_lat = '${lat}', u_lon = '${lng}', export=0 WHERE id = '${id}'`;
            console.log("sql location ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    //Percepciones
    public async selectDocumentByCarcode(Carcode: string, Address: string) {
        try {
            let sql = `SELECT TaxCode FROM clientessucursales WHERE CardCode = '${Carcode}' AND AddresName='${Address}'`;
            console.log("clientessucursales" + sql);

            return await this.queryAll(sql);
        } catch (error) {
            console.log("error al obtener consulta clientessucursales->", error);
        }
    }
}
/*        "id": "1",
            "AddresName": "asuncion",
            "Street": "",
            "State": "",
            "FederalTaxId": "",
            "CreditLimit": "0",
            "CardCode": "901000001",
            "User": "1",
            "Status": "1",
            "DateUpdate": "2020-11-26 00:00:00.000000",

            "TaxCode": "IVA_10", NO
            "AdresType": "S", // select (ENTREGA S  FACTURACION B)
            "u_zona": "", NO
            "u_lat": "",
            "u_lon": "",
            "u_territorio": null, // SERVICE
            "u_vendedor": null (USER LOGEO)
 */