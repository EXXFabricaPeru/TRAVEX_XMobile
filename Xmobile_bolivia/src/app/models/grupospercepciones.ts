import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class Grupospercepciones extends Databaseconf {
    public configService: ConfigService;

    public async selectGrupospercepciones() {
        let sql = `SELECT * FROM gruposPercepciones`;
        return await this.queryAll(sql);
    }

    public async insertAll(objeto: any, idx: number, contador = 0) {
        if (contador == 0) {
            let sql = 'DELETE FROM gruposPercepciones';
            await this.executeSQL(sql);
        }
        let obj = JSON.parse(objeto.data);
        let sql: string = 'INSERT INTO gruposPercepciones VALUES ';
        for (let o of obj.respuesta) {            
            sql += `(NULL, '${o.Code}', '${o.Name}', '${o.U_EXX_PORPER}', '${o.U_EXX_MONMIN}', '${o.U_EXX_TaxCode}', '${o.U_EXX_GLP}'),`;
        }
        let sqlx = sql.slice(0, -1);
        console.log(sqlx);
        return await this.executeSQL(sqlx);
    }

    public async findAll(limit: number, search: string, priList = '') {
        let sqlm = '';
        (search != '') ? sqlm = ' WHERE (p.ItemName LIKE "%' + search + '%" OR p.ItemCode LIKE "%' + search + '%" OR p.GroupName LIKE "%' + search + '%" OR p.ItemsGroupCode LIKE "%' + search + '%")' : '';
        let sql = 'SELECT * FROM gruposPercepciones p ' + sqlm + ' LIMIT ' + limit + ', 20';
        return await this.queryAll(sql);
    }

    public async findByCode(Code: string) {
        try {
            let gruppercepciones= await this.selectGrupospercepciones();
            console.log("grupo de percepciones-->",gruppercepciones);

            let sql = `SELECT T0.U_EXX_TaxCode  FROM gruposPercepciones T0 WHERE T0.Code=IFNULL('${Code}','0000');`;
            console.log("sql-->",sql);
            return await this.queryAll(sql); 
        } catch (error) {
            return console.log("error en sql",error);
        }
       
    }

    public async findIn(dato: string) {
        let sql = `SELECT p.*, 1 AS bonificacionx FROM productos p WHERE p.ItemCode IN(${dato});`;
        return await this.queryAll(sql);
    }

    public async findSearch(dato: string, priList = '', qr = '', grupoproductoscode = '') {
        let slx = '';
        let xx = '';
        (grupoproductoscode != '') ? xx = ' AND p.producto_std5 = "' + grupoproductoscode + '"' : xx = '';
        if (dato == '') {
            return [];
        }
        if (dato != '') {
            slx += ' WHERE (p.ItemName LIKE "%' + dato + '%" OR p.ItemCode LIKE "%' + dato + '%" OR p.GroupName LIKE "%' + dato + '%" OR p.ItemsGroupCode LIKE "%' + dato + '%") ' + xx;
        }
        if (priList != '')
            slx += ' AND ((SELECT count(*) FROM productosprecios WHERE ItemCode = p.ItemCode AND PriceListNo = "' + priList + '") > 0) OR p.combo = 1 ';
        if (qr != '')
            slx += ' WHERE p.BarCode = "' + qr + '" ';
        let sql = 'SELECT p.*, 0 AS bonificacionx FROM productos p ' + slx + ' LIMIT 30';
        return await this.queryAll(sql);
    }

    public select(id: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM productos WHERE ItemCode = '${id}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS productos;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findItem(Itemcode){
        let sql = 'SELECT * FROM productos where Itemcode="'+Itemcode+'"';
        return await this.queryAll(sql);
    }

    public async findPorcentajeByCode(Code: string) {
        try {
            let sql = `SELECT *  FROM gruposPercepciones T0 WHERE T0.Code=IFNULL('${Code}','0000');`;
            console.log("sql-->",sql);
            return await this.queryAll(sql); 
        } catch (error) {
            return console.log("error en sql",error);
        }       
    }
}
