import { SQLite, SQLiteObject } from '@ionic-native/sqlite/ngx';
import { File, FileEntry } from '@ionic-native/file/ngx';
import { FileTransfer, FileTransferObject } from '@ionic-native/file-transfer/ngx';
import { NativeStorage } from "@ionic-native/native-storage/ngx";
import { DirectoryEntry } from '@ionic-native/file';
import {ConfigService} from "../models/config.service";
import { relationalTable } from '../types/IPagos'
declare let window: any;

export class Databaseconf {
    public sqlite: SQLite;
    public db = null;
    public file: File;

    private nativeStorage: NativeStorage;


    constructor() {
        this.sqlite = new SQLite;
        this.file = new File;
        this.nativeStorage = new NativeStorage();

    }



    public async data() {
        let basesdatas: any = await this.nativeStorage.getItem('DB');
        let datasession: any = await this.nativeStorage.getItem(basesdatas + 'SESSION');
        console.log("Nombre de la base de datos", datasession);
        console.log("data session", basesdatas);

        return new Promise((resolve, reject) => {
            this.sqlite.create({
                name: basesdatas + "_" + datasession[0].idUsuario + 'xm.db',
                location: 'default'
            }).then((db: SQLiteObject) => {
                resolve(db);
            }).catch(e => {
                reject(e);
            });
        });
    }

    public queryAll(sql: string) {
        return new Promise((resolve, reject) => {
            try {
                this.executeSQL(sql).then((data: any) => {
                    let arr = [];
                    for (let i = 0; i < data.rows.length; i++)
                        arr.push(data.rows.item(i));
                    resolve(arr);
                }).catch((e: any) => {
                    reject(e);
                });
            } catch (e) {
                reject(e);
            }
        });
    }
    public queryAllByDetalle(sql: string, dataRelacion:
        relationalTable[] = []) {
        return new Promise((resolve, reject) => {
            try {
                this.executeSQL(sql).then(async (data: any) => {
                    let arr = [];
                    for (let i = 0; i < data.rows.length; i++) {
                        let dataRow = data.rows.item(i);
                        if (dataRelacion.length > 0) {

                            for (let j = 0; j < dataRelacion.length; j++) {
                                let sqlAux = "";
                                sqlAux = `select * from ${dataRelacion[j].table} where  ${dataRelacion[j].relationshipFieldSeg}=${dataRow[dataRelacion[j].relationshipFieldPrin]}`;
                                console.log("query detalle", sqlAux);
                                dataRow[dataRelacion[j].table] = await this.queryAll(sqlAux);
                            }
                        }
                        arr.push(dataRow);
                        // arr.push(data.rows.item(i));
                    }

                    resolve(arr);
                }).catch((e: any) => {
                    reject(e);
                });
            } catch (e) {
                reject(e);
            }
        });
    }


    public exe(sql: any) {
        return new Promise((resolve, reject) => {
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        })
    }

    public async executeSQL(sql: any, arr = []) {
        this.db = await this.data();
        return new Promise((resolve, reject) => {
            this.db.executeSql(sql, arr).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async executeRaw(sql: any, arr = []) {
        this.db = await this.data();
        return new Promise((resolve, reject) => {
            this.db.executeSql(sql, arr).then((data: any) => {
                resolve(data.insertId);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public horaActual() {
        let d = new Date();
        let h = this.addZero(d.getHours());
        let m = this.addZero(d.getMinutes());
        let s = this.addZero(d.getSeconds());
        return `${h}:${m}:${s}`;
    }

    public tiempo(): number {
        return Date.now();
    }

    public converterTime(fecha: string): number {
        let f: any[] = fecha.split('-');
        let hora: any = new Date();
        let fechax = new Date(f[0], f[1], f[2], hora.getHours(), hora.getMinutes(), hora.getSeconds());
        return fechax.getTime();
    }

    public getFechaTime(time: number): string {
        let today: any = new Date(time);
        let dd: any = today.getDate();
        let mm: any = today.getMonth() + 1;
        let yyyy: any = today.getFullYear();
        if (dd < 10)
            dd = `0${dd}`;
        if (mm < 10)
            mm = `0${mm}`;
        return `${dd}-${mm}-${yyyy} ${today.getHours()}:${today.getMinutes()}:${today.getSeconds()}`;
    }

    public getFechaView(): string {
        let today: any = new Date();
        let dd: any = today.getDate();
        let mm: any = today.getMonth() + 1;
        let yyyy: any = today.getFullYear();
        if (dd < 10)
            dd = `0${dd}`;
        if (mm < 10)
            mm = `0${mm}`;
        return `${dd}-${mm}-${yyyy}`;
    }

    public getFechaPicker(): string {
        let today: any = new Date();
        let dd: any = today.getDate();
        let mm: any = today.getMonth() + 1;
        let yyyy: any = today.getFullYear();
        if (dd < 10)
            dd = `0${dd}`;
        if (mm < 10)
            mm = `0${mm}`;
        return `${yyyy}-${mm}-${dd}`;
    }
    public getFechaPickerMasAnio(): string {
        let today: any = new Date();
        let dd: any = today.getDate();
        let mm: any = today.getMonth() + 1;
        let yyyy: any = today.getFullYear() + 1;
        if (dd < 10)
            dd = `0${dd}`;
        if (mm < 10)
            mm = `0${mm}`;
        console.log("   return `${yyyy}-${mm}-${dd}`; ", `${yyyy}-${mm}-${dd}`)
        return `${yyyy}-${mm}-${dd}`;
    }

    public getHoraCurrent(): string {
        let today: any = new Date();
        return today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    }

    public addZero(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }

    public getHoraSinSegundosCurrent(): string {
        let today: any = new Date();
        return today.getHours() + ":" + today.getMinutes();
    }

    public timeStamp() {
        let currentDate = new Date();
        let day = currentDate.getDate();
        let month = currentDate.getMonth() + 1;
        let year = currentDate.getFullYear();
        return year + "-" + month + "-" + day;
    }

    public timestampdate(date) {
        let monthNames = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        let dd = date.getDate();
        if (dd < 10) dd = `0${dd}`;
        let monthIndex = date.getMonth();
        let year = date.getFullYear();
        return year + '-' + monthNames[monthIndex] + '-' + dd;
    }

    public async createdblocal(nombre) {

        let basesdatas: any = await this.nativeStorage.getItem('DB');
        let datasession: any = await this.nativeStorage.getItem(basesdatas + 'SESSION');
        const fileName: string = "." + basesdatas + "_" + datasession[0].idUsuario + "_" + nombre + ".txt";
        console.log("direccorio", this.file);
        console.log("file");

        console.log("Nombre", fileName);
        return new Promise((resolve, reject) => {
            this.file.checkFile(this.file.externalRootDirectory + 'Download/', fileName).then((result) => {
                window.resolveLocalFileSystemURL(this.file.externalRootDirectory + 'Download/', (dirEntry: DirectoryEntry) => {
                    dirEntry.getFile(fileName, { create: false, exclusive: false }, function (fileEntry) {
                        if (result) {
                            console.log("archivo ya esta creado");
                            resolve(1);
                        } else {
                            console.log("llama para crear documento", fileEntry);
                            resolve(fileEntry);
                        }
                    }, (er) => {
                        reject(er);
                    });
                });
            }, () => {
                this.file.createFile(this.file.externalRootDirectory + 'Download/', fileName, true)
                    .then((fileEntry) => {
                        console.log("Documento Creado")
                        console.log(fileEntry);
                        resolve(fileEntry);
                    }).catch((er) => {
                        console.log("Error al crear archivo local", er);
                        reject(er);
                    });
            })
                .catch((er) => {
                    console.log("Error al validar archiv local", er);
                    reject(er);
                });
        });
    }

    public async writedblocal(datos: any, nombre) {
        let basesdatas: any = await this.nativeStorage.getItem('DB');
        let datasession: any = await this.nativeStorage.getItem(basesdatas + 'SESSION');
        const fileName: string = "." + basesdatas + "_" + datasession[0].idUsuario + "_" + nombre + ".txt";
        console.log("direccorio", this.file);
        console.log("Nombre", fileName);

        return new Promise((resolve, reject) => {
            this.file.checkFile(this.file.externalRootDirectory + 'Download/', fileName).then((result) => {
                console.log("pasa1");
                window.resolveLocalFileSystemURL(this.file.externalRootDirectory + 'Download/', (dirEntry: DirectoryEntry) => {
                    console.log("pasa2");
                    dirEntry.getFile(fileName, { create: false, exclusive: false }, function (fileEntry) {
                        console.log("pasa3");
                        if (result) {
                            console.log("Consigue el archivo y lo prepara para escribir");
                            fileEntry.createWriter(function (fileWriter) {
                                fileEntry.file(function (file) {
                                    var reader = new FileReader();
                                    reader.readAsText(file);
                                    console.log("resultado de lo guardado ", reader);

                                    reader.onloadend = function () {
                                        console.log("Lectura exitosa del archivo: ");
                                        let dataObj;

                                        if (this.result == '') {
                                            console.log("Archivo en blanco");
                                            dataObj = new Blob([datos], { type: 'text/plain' });
                                        } else {
                                            console.log("Archivo no esta en blanco");
                                            let texto = this.result + ',' + datos;
                                            console.log("datos a guardar", texto);
                                            dataObj = new Blob([texto], { type: 'text/plain' });
                                        }

                                        fileWriter.write(dataObj);

                                        fileWriter.onwriteend = function () {
                                            console.log("Escritura exitosa del archivo...");
                                            resolve(1);
                                        };
                                        fileWriter.onerror = function (e) {
                                            console.log("Error en al escritura " + e.toString());
                                        };
                                    };

                                });

                            });
                        } else {
                            resolve(0);
                        }
                    }, (er) => {
                        reject(er);
                    });
                });

            }, () => { })
                .catch((er) => {
                    console.log("Error al validar archiv local", er);
                    reject(er);
                });
        });

    }

    public async limpiardblocal(nombre) {
        let basesdatas: any = await this.nativeStorage.getItem('DB');
        let datasession: any = await this.nativeStorage.getItem(basesdatas + 'SESSION');
        const fileName: string = "." + basesdatas + "_" + datasession[0].idUsuario + "_" + nombre + ".txt";
        return new Promise((resolve, reject) => {
            this.file.checkFile(this.file.externalRootDirectory + 'Download/', fileName).then((result) => {
                window.resolveLocalFileSystemURL(this.file.externalRootDirectory + 'Download/', (dirEntry: DirectoryEntry) => {
                    dirEntry.getFile(fileName, { create: false, exclusive: false }, function (fileEntry) {
                        if (result) {
                            console.log("Consigue el archivo y lo prepara para limpiar");
                            fileEntry.createWriter(function (fileWriter) {
                                fileEntry.file(function (file) {
                                    var reader = new FileReader();
                                    reader.readAsText(file);
                                    console.log("resultado de lo guardado ", reader);

                                    reader.onloadend = function () {
                                        console.log("Lectura exitosa del archivo: ");
                                        let dataObj = new Blob([''], { type: 'text/plain' });
                                        fileWriter.write(dataObj);
                                        fileWriter.onwriteend = function () {
                                            console.log("Escritura exitosa del archivo...");
                                            resolve(1);
                                        };
                                        fileWriter.onerror = function (e) {
                                            console.log("Error en al escritura " + e.toString());
                                        };
                                    };
                                });
                            });
                        } else {
                            resolve(0);
                        }
                    }, (er) => {
                        reject(er);
                    });
                });

            }, () => { })
                .catch((er) => {
                    console.log("Error al validar archiv local", er);
                    reject(er);
                });
        });

    }

    public async loadenddblocal(nombre) {
        let basesdatas: any = await this.nativeStorage.getItem('DB');
        let datasession: any = await this.nativeStorage.getItem(basesdatas + 'SESSION');
        const fileName: string = "." + basesdatas + "_" + datasession[0].idUsuario + "_" + nombre + ".txt";
        return new Promise((resolve, reject) => {
            this.file.checkFile(this.file.externalRootDirectory + 'Download/', fileName).then((result) => {
                window.resolveLocalFileSystemURL(this.file.externalRootDirectory + 'Download/', (dirEntry: DirectoryEntry) => {
                    dirEntry.getFile(fileName, { create: false, exclusive: false }, function (fileEntry) {
                        if (result) {
                            console.log("Consigue el archivo y lo prepara para leerlo");
                            fileEntry.createWriter(function (fileWriter) {
                                fileEntry.file(function (file) {
                                    var reader = new FileReader();
                                    reader.readAsText(file);


                                    console.log("resultado de lo guardado ", reader);

                                    reader.onloadend = function () {
                                        console.log("Lectura exitosa del archivo: ");

                                        if (this.result == '') {
                                            resolve(0);
                                        } else {
                                            resolve(this.result);
                                        }


                                    };
                                });

                            });
                        } else {
                            resolve(0);
                        }
                    }, (er) => {
                        reject(er);
                    });
                });

            }, () => { })
                .catch((er) => {
                    console.log("Error al validar archiv local", er);
                    reject(er);
                });
        });

    }

    public async deletedblocal(nombre) {
        let basesdatas: any = await this.nativeStorage.getItem('DB');
        let datasession: any = await this.nativeStorage.getItem(basesdatas + 'SESSION');
        const fileName: string = "." + basesdatas + "_" + datasession[0].idUsuario + "_" + nombre + ".txt";
        return new Promise((resolve, reject) => {
            this.file.checkFile(this.file.externalRootDirectory + 'Download/', fileName).then((result) => {
                window.resolveLocalFileSystemURL(this.file.externalRootDirectory + 'Download/', (dirEntry: DirectoryEntry) => {
                    dirEntry.getFile(fileName, { create: false, exclusive: false }, function (fileEntry) {
                        if (result) {
                            fileEntry.remove(function () { });
                            resolve(1);
                        }
                    }, (er) => {
                        reject(er);
                    });
                });
            }, () => { })
                .catch((er) => {
                    console.log("Error al validar archiv local", er);
                    reject(er);
                });
        });

    }

    public async getDB() {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('DB').then(async (data: any) => {
                let datasession: any;
                try {
                    datasession = await this.nativeStorage.getItem(data + 'SESSION');

                } catch (error) {
                    datasession = "";
                }
                console.log("session old", datasession);
                let idUser = datasession && datasession[0] ? "_" + datasession[0].idUsuario : ''
                console.log("idUser", idUser);
                resolve(data);
                //resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }



    public async getSession() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'SESSION').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }
}


function successCallback(arg0: string, arg1: { create: boolean; }, successCallback: any, errorCallback: any) {
    throw new Error('Function not implemented.');
}

function errorCallback(arg0: string, arg1: { create: boolean; }, successCallback: any, errorCallback: any) {
    throw new Error('Function not implemented.');
}

