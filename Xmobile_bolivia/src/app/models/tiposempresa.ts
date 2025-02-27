import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Tiposempresa extends Databaseconf {
    public configService: ConfigService;

    async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM tiposempresa';
            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO tiposempresa VALUES ';
            for (let o of obj.respuesta)
                sql += `("${o.id}", "${o.nombre}", "${o.descripcion}", "${o.User}", "${o.Status}", "${o.DateUpdate}"),`;
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public selectTipoEmpresa() {
        return new Promise((resolve, reject) => {
            let sql = 'select * from tiposempresa';
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++) {
                    arr.push(data.rows.item(i));
                }
                resolve(arr);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public selectTipoEmpresaId(id: number) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM tiposempresa WHERE id = ' + id;
            console.log("sql ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS tiposempresa;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
