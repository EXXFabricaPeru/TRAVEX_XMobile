import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Divisa extends Databaseconf {
    public configService: ConfigService;

    public async insert(data: any) {
        let fecha = this.getFechaPicker();
        let sql = `INSERT INTO divisa VALUES(NULL, '${data.iddocdocumento}', '${data.CardCode}', '${data.monedaDe}', '${data.monedaA}', ${data.ratio}, ${data.monto}, ${data.cambio}, '${data.usuario}', '${fecha}', '${fecha}', '0',0);`;
        return await this.executeRaw(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS divisa`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
