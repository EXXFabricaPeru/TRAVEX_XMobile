import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";

export class Geolocalizacion extends Databaseconf {
    public configService: ConfigService;

    public async insert(data: any) {
        let sql = `INSERT INTO geolocalizacion VALUES (NULL, '${data.idequipox}', '${data.latitud}','${data.longitud}', '${data.fecha}', '${data.hora}', ${data.idcliente}, '${data.documentocod}','${data.tipodoc}',
                                                       ${data.estado}, '${data.actividad}', '${data.anexo}', ${data.usuario}, ${data.status}, '${data.dateUpdate}');`;
        console.log("sql geolocation ", sql);
        return await this.executeRaw(sql);
    }

    public async select() {
        let sql = `SELECT * FROM geolocalizacion;`;
        return await this.queryAll(sql);
    }

    public async clear() {
        let sql = 'DELETE FROM geolocalizacion';
        await this.exe(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS geolocalizacion`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
