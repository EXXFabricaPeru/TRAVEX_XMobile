import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Reimpresion extends Databaseconf {
    public configService: ConfigService;

    public async export() {
        let sql = `SELECT * FROM reimpresion WHERE estado = '0'`;
        return await this.queryAll(sql);
    }

    public async selectAll() {
        let sql = `SELECT * FROM reimpresion`;
        return await this.queryAll(sql);
    }


    public async insert(data: any) {
        let sql = `INSERT INTO reimpresion VALUES(NULL,'${data.fechahora}', '${data.tipodocumento}', '${data.iddocumento}', '${data.usuario}', '${data.equipo}', '0')`;
        return await this.executeRaw(sql);
    }

    public async updatex() {
        let sql = `UPDATE reimpresion SET estado = '1' WHERE estado = '0'`;
        return await this.executeSQL(sql);
    }

    public async buscarreimpresion(documento: any) {
        let sql = `SELECT count(id)as contador FROM reimpresion WHERE iddocumento = '${documento}'`;
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS reimpresion`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
