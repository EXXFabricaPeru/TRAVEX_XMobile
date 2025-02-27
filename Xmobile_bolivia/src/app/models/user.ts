import {Databaseconf} from "./databaseconf";
import {ConfigService} from "./config.service";

export class User extends Databaseconf {
    public configService: ConfigService;

    public async create() {
        let sql = 'CREATE TABLE IF NOT EXISTS user (\n' +
            '  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,\n' +
            '  username varchar(255) NOT NULL,\n' +
            '  password_hash varchar(255) NOT NULL,\n' +
            '  created_at integer NOT NULL,\n' +
            '  access_token text NOT NULL,\n' +
            '  idPersona integer NOT NULL,\n' +
            '  idUsuario integer NOT NULL,\n' +
            '  idEmpresa integer NOT NULL,\n' +
            '  paterno varchar(255) NOT NULL,\n' +
            '  materno varchar(255) NOT NULL,\n' +
            '  nombre varchar(255) NOT NULL,\n' +
            '  estadoUsuario integer NOT NULL,\n' +
            '  plataformaPlataforma varchar(50) NOT NULL,\n' +
            '  plataformaEmei varchar(255) \n' +
            ') ';
        return await this.executeSQL(sql);
    }


    public insert(datos: any) {
        let fecha = this.timeStamp();
        return new Promise((resolve, reject) => {
            let sql = 'INSERT INTO user VALUES(NULL, "' + datos.nombreUsuario + '", "' + datos.pass + '",' +
                '"' + fecha + '","' + datos.token + '",' + datos.idUsuario + ',' + datos.idUsuario + ',' +
                '' + datos.idEmpresa + ',"' + datos.apellidoPPersona + '","' + datos.apellidoMPersona + '",' +
                '"' + datos.nombreUsuario + '",1,"' + datos.plataforma + '","' + datos.uuid + '")';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.insertId);
            }).catch((e: any) => {
                reject(e);
            });
        });

    }

    public findAll() {
        let sql = 'SELECT * FROM user';
        this.executeSQL(sql).then((data: any) => {
        }).catch((e: any) => {
        })
    }

    public find(id: number) {

    }


    public update(...args) {

    }

    public login(username, pass) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM user WHERE username = "' + username + '" AND password_hash = "' + pass + '" ORDER BY id DESC LIMIT 1';
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
}
