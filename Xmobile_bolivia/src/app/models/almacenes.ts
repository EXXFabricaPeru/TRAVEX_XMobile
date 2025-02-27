import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Almacenes extends Databaseconf {
    public configService: ConfigService;

    public select(idUser: number) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM almacenes WHERE idUser = ' + idUser;
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

    public async insert(o: any, idDoc: any) {
        return new Promise((resolve, reject) => {
            let sql = `INSERT INTO almacenes VALUES (NULL, '0', '${o.Street}','${o.WarehouseCode}','${o.State}','${o.Country}','${o.City}','${o.WarehouseName}','${o.User}','${o.Status}','${o.DateUpdate}',${idDoc},0);`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public async update(o: any, idDoc: any) {
        return new Promise((resolve, reject) => {
            let sql = `UPDATE almacenes SET 
            Street = '${o.Street}', 
            WarehouseCode = '${o.WarehouseCode}',
            State = '${o.State}',
            Country = '${o.Country}',
            City = '${o.City}',
            WarehouseName = '${o.WarehouseName}',
            User = '${o.User}',
            Status = '${o.Status}', 
            DateUpdate = '${o.DateUpdate}' 
            WHERE idDocumento = ${idDoc}`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public async actualizaiddocumento(cod: any,cod_and: any) {
        console.log("DEVD actualizaiddocumento()");
        let sql = `UPDATE almacenes SET idDocumento='${cod}' WHERE idDocumento = '${cod_and}' `;
        console.log("sql update actualizacion", sql);
        return await this.executeSQL(sql);
    }
    


    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM almacenes ;';
            // let sql = 'DELETE FROM almacenes WHERE idUser = ' + idx;
            await this.exe(sql);
        }


        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO almacenes VALUES ';
            for (let o of obj.respuesta)
                sqlz += `(NULL, '${idx}', '${o.Street}','${o.WarehouseCode}','${o.State}','${o.Country}','${o.City}','${o.WarehouseName}','${o.User}','${o.Status}','${o.DateUpdate}',0,0),`;
            let sqlx = sqlz.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS almacenes;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
