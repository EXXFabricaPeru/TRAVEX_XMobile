import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Agendas extends Databaseconf {
    public ConfigService: ConfigService;

    public selectEstadoActividades() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM estadoActividades';
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

    public selectAsunto() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM asunto';
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

    public selectTipoActividades() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM tipoActividades';
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


    public selectActividades() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM actividades';
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


    public selectCode(id: any) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM actividades WHERE id = ' + id;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
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

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (objeto.url.includes('asunto')) {
            if (contador == 0) {
                let sql = 'DELETE FROM asunto;';
                await this.exe(sql);
            }

            return new Promise((resolve, reject) => {
                let obj = JSON.parse(objeto.data);
                let sqlz = 'INSERT INTO asunto VALUES ';
                for (let o of obj.respuesta)
                    sqlz += `(NULL, '${o.descripcion}'),`;
                let sqlx = sqlz.slice(0, -1);
                this.executeSQL(sqlx).then((data: any) => {
                    resolve(data);
                }).catch((e: any) => {
                    reject(e);
                });
            });
        }
        else if (objeto.url.includes('tipoactividades')) {
            let sql = 'DELETE FROM tipoActividades';
            await this.exe(sql);
            return new Promise((resolve, reject) => {
                let obj = JSON.parse(objeto.data);
                let sqlz = 'INSERT INTO tipoActividades VALUES ';
                for (let o of obj.respuesta)
                    sqlz += `('${o.id}','${o.descripcion}'),`;
                let sqlx = sqlz.slice(0, -1);
                this.executeSQL(sqlx).then((data: any) => {
                    resolve(data);
                }).catch((e: any) => {
                    reject(e);
                });
            });
        }
        else if (objeto.url.includes('estadoactividades')) {
            let sql = 'DELETE FROM estadoActividades';
            await this.exe(sql);
            return new Promise((resolve, reject) => {
                let obj = JSON.parse(objeto.data);
                let sqlz = 'INSERT INTO estadoActividades VALUES ';
                for (let o of obj.respuesta)
                    sqlz += `('${o.id}','${o.descripcion}'),`;
                let sqlx = sqlz.slice(0, -1);
                this.executeSQL(sqlx).then((data: any) => {
                    resolve(data);
                }).catch((e: any) => {
                    reject(e);
                });
            });
        }
    }

    public findAll(limit: number, searchData: string) {
        return new Promise((resolve, reject) => {
            let addSql = '';
            (searchData != '') ? addSql = 'AND (c.CardName LIKE "%' + searchData +
                '%"  OR c.CardCode LIKE "%' + searchData + '")' : addSql = '';
            let sql = 'SELECT DISTINCT a.id, c.CardName, t.descripcion AS "actvidad", e.descripcion AS "estado", a.hora ' +
                'FROM actividades a, clientes c, asunto s, tipoActividades t, estadoActividades e ' +
                'WHERE a.cardCode=c.CardCode AND a.tipoActividad=t.id AND a.estado=e.id ' +
                addSql +
                ' ORDER BY c.CardName ASC LIMIT ' + limit + ', 20';
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public findById(id: any) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT DISTINCT a.id, c.CardName, t.descripcion AS "actvidad", e.descripcion AS "estado", a.hora,' +
                'a.fecha, s.descripcion AS "asunto",a.comentarios,a.PhoneNumber, a.cardCode, a.tipoActividad AS "idActividad", ' +
                'a.estado AS "idEstado", a.asunto AS "idAsunto" ' +
                'FROM actividades a, clientes c, asunto s, tipoActividades t, estadoActividades e ' +
                'WHERE a.cardCode=c.CardCode AND a.asunto=s.id AND a.tipoActividad=t.id AND a.estado=e.id ' +
                'AND a.id=' + id +
                ' ORDER BY c.CardName ASC';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public insertRegister(d: any, id: any) {
        return new Promise((resolve, reject) => {
            let sql = `INSERT INTO actividades VALUES (NULL, '${d.Actividad}','${d.Estado}','${d.CardCode}','${d.Telefono}',
            '${d.Fecha}','${d.Hora}','${d.Asunto}','${d.Comentarios}','${d.idUser}','${d.DateUpdate}')`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public updateRegister(id: any, estado: any, asunto: any, comentarios: any, fecha: any) {
        return new Promise((resolve, reject) => {
            let sql = 'UPDATE actividades SET estado=' + estado +
                ',asunto=' + asunto +
                ',comentarios="' + comentarios +
                '",DateUpdate="' + fecha +
                '" WHERE id=' + id;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS tipoActividades;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
