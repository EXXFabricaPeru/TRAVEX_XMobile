import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import * as moment from 'moment';

export class Visitas extends Databaseconf {
    public configService: ConfigService;

    public async exportedata() {
        let sql = `SELECT * FROM visitas WHERE estadoEnviado LIKE '0';`;
        return await this.queryAll(sql);
    }

    public async exportUpdate(id: number, idx: number) {
        let sql = `UPDATE visitas SET estadoEnviado = '${idx}' WHERE id = ${id};`;
        console.log("sql update ", sql);
        return await this.executeSQL(sql);
    }

    public async showTable() {
        let sql = `SELECT * FROM visitas;`;
        return await this.queryAll(sql);
    }

    public async listdata(CartCode: number) {
        let sql = `SELECT * FROM visitas WHERE CartCode = '${CartCode}' ORDER BY id DESC;`;
        return await this.queryAll(sql);
    }

    public async insert(objeto: any, img) {

        let fecha = this.timeStamp();
        let hora = this.getHoraSinSegundosCurrent();
        let fechaMoment = moment().format('YYYY-MM-DD');
        let sql = `INSERT INTO visitas VALUES (NULL, '${objeto.CardCode}','${objeto.CardName}','${fechaMoment}','${hora}', '0',${objeto.lat}, ${objeto.lng},'${objeto.foto}','0','${objeto.motivoCode}','${objeto.motivoRazon}' ,'${objeto.motivoName}' ,'${objeto.descripcionTxt}' , '${img}');`;
        return await this.executeRaw(sql);
    }

    public async updateHora(id: number) {
        let hora = this.getHoraSinSegundosCurrent();
        let sql = `UPDATE visitas SET horafin = '${hora}' WHERE id = ${id}`;
        return await this.executeSQL(sql);
    }

    public async updateFoto(id: number, fotox: string) {
        let sql = `UPDATE visitas SET foto = '${fotox}' WHERE id = ${id}`;
        return await this.executeSQL(sql);
    }
}
