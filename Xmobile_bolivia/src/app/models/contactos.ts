import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Contactos extends Databaseconf {
    public configService: ConfigService;

    public async findAll() {
        let sql = 'SELECT * FROM contactos';
        return await this.queryAll(sql);
    }

    public insert(data: any) {
        let fecha = this.getFechaView();
        return new Promise((resolve, reject) => {
            let sql = 'INSERT INTO contactos VALUES(NULL, "${data.nombre}", "${data.telefono}", "${data.comentario}", "${data.titulo}", "${data.correo}", "${data.cardCode}", 0, "${fecha}","")';
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

    public async insertAll(objeto: any, idx: number, contador = 0) {
        let fecha: any = this.getFechaPicker();
        if (contador == 0) {
            let sql = 'DELETE FROM contactos;';

            await this.exe(sql);
        }
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sql = 'INSERT INTO contactos VALUES ';
            for (let d of obj.respuesta) {
                sql += ` (NULL, '${d.nombre}', '${d.telefono}', '${d.comentario}', '${d.titulo}', '${d.correo}', '${d.cardCode}', 1, '${fecha}','${d.InternalCode}'),`;
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


    public update(data: any) {
        return new Promise((resolve, reject) => {
            let sql = 'UPDATE contactos SET nombre = "${data.nombre}",  telefono = "${data.telefono}", comentario = ${data.comentario}, titulo = ${data.titulo}, correo = ${data.correo} WHERE id = ${data.id}';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rowsAffected);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async delete(id: any) {
        return new Promise((resolve, reject) => {
            let sql = `DELETE FROM contactos WHERE cardCode = '${id}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rowsAffected);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async insertRegister(d: any, id: any) {
        let fecha = this.getFechaView();
        for await (let contacto of d) {
            let sql = `INSERT INTO contactos VALUES (NULL, '${contacto.nombrePersonaContacto}','${contacto.fonoPersonaContacto}','${contacto.comentarioPersonaContacto}','${contacto.tituloPersonaContacto}','${contacto.correoPersonaContacto}', '${id}',0 ,'${fecha}','${contacto.internalcode}')`;
            await this.executeSQL(sql);
        }
    }

    public async selectCarCode(id: any) {
        let sql = `SELECT * FROM contactos WHERE cardCode = '${id}' `;
        return await this.queryAll(sql);
    }
    public async selectSucursales(id: any) {
        let sql = `SELECT * FROM clientessucursales WHERE cardCode = '${id}' `;
        return await this.queryAll(sql);
    }


    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS contactos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
