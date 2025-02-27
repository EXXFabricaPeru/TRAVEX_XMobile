import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
export class Territorios extends Databaseconf {
    public configService: ConfigService;

    public  async insertAll(objeto: any) {
       console.log("INSERTA EN TERRITORIO",objeto);
        let sql = 'DELETE FROM territorios;';
        await this.exe(sql);
        
        return new Promise((resolve, reject) => {
            let obj = JSON.parse(objeto.data);
            let sqlz = 'INSERT INTO territorios VALUES ';
            for (let o of obj.respuesta) {
                sqlz += ` ('${o.id}', '${o.TerritoryID}', '${o.Description}', '${o.LocationIndex}', '${o.Inactive}', '${o.Parent}', '${o.User}', '${o.Status}', '${o.DateUpdate}'),`;
            }
            let sqlx = sqlz.slice(0, -1);
            console.log("territorios:",sqlx);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findAll() {
        let sqlm = '';
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM territorios where Inactive = "tNO"';
            console.log("sql ", sql);
            this.queryAll(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
