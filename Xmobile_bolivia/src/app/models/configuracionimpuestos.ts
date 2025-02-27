import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Configuracionimpuestos extends Databaseconf {
    public configService: ConfigService;

    public async selectConfiguracionesImpuestos() {
        let sql = `SELECT * FROM configuracionImpuestos`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM configuracionImpuestos';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO configuracionImpuestos VALUES ';
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.STCCode}', ${o.Line_ID}, '${o.STACode}', ${o.STAType}, ${o.EfctivRate}),`;
        }                          
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

    public async findBySTACode(code: any) {
        let dataConfImp= await this.selectConfiguracionesImpuestos();
        console.log("data impuestos configuraciones",dataConfImp);
        let sql = `SELECT * FROM configuracionImpuestos WHERE STCCode = '${code}'`;
        console.log("sql findBySTACode->",sql);
        return await this.queryAll(sql);
    }
    
    public async findByStcPer(code: any) {
        let dataConfImp= await this.selectConfiguracionesImpuestos();
        console.log("data impuestos configuraciones",dataConfImp);
        let sql = `SELECT * FROM configuracionImpuestos WHERE STCCode = '${code}' AND substr(STACode,1,3) = 'PER'`;
        console.log("sql findBySTACode->",sql);
        return await this.queryAll(sql);
    }   
    
    
}
