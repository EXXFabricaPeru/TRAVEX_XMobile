import { Injectable } from '@angular/core';
import { NativeStorage } from '@ionic-native/native-storage/ngx';
//import { Pagos } from './pagos';
@Injectable({
    providedIn: 'root'
})
export class ConfigService {
    //pagosModel: Pagos;
    constructor(private nativeStorage: NativeStorage) {
    }

    /*********Valores mobile**********/
    public async setBasesdatas(obj: any) {
        console.log("setBasesdatas()");
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem('DATABASES', obj).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public async setDB(obj: any) {
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem('DB', obj).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }


    public async getBasesdatas() {
        console.log("getBasesdatas() ");
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('DATABASES').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public async getDB() {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('DB').then(async (data: any) => {
                let datasession: any = 0;
                try {
                    datasession = await this.nativeStorage.getItem(data + 'SESSION');

                } catch (error) {
                    datasession = "";
                }
                console.log("session old", datasession);
                let idUser = datasession && datasession[0] ? "_" + datasession[0].idUsuario : ''
                console.log("idUser ", idUser);
                // console.log("datasession ".datasession);

                resolve(data);
                //resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public async setName(obj: any) {
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem('NAME', obj).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public async getName() {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('NAME').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    /*******************/
    public async setIp(path: string) {
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem('path', path).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public async getIp() {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('path').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getConfig(attr: string) {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(attr).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setPoligono(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'POLIGONO', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public async getPoligono() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'POLIGONO').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setActionMarker(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'MARKER', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(err);
            });
        });
    }

    public async getActionMarker() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'MARKER').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setSession(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'SESSION', data).then((data: any) => {
                resolve(data);
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

    public async destroy() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.remove(basesdatas + 'SESSION').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getModo() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'MODO').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setModo(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'MODO', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getDireccion() {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('httpx').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setDireccion(data: any) {
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem('httpx', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getPuerto() {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('puerto').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setPuerto(data: any) {
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem('puerto', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getDir() {
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem('dir').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setDir(data: any) {
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem('dir', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getTipo() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'tipo').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setTipo(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'tipo', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getNumeracion() {
        let basesdatas: any = await this.getDB();
        console.log("numeracion", basesdatas);
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'numeracion').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setNumeracion(data: any) {
        let numDataLocal: any = {};
        try {
            numDataLocal = await this.getNumeracion();
            console.log("get local ", numDataLocal);
            console.log("setNumeracion nube () ", data);
            //FACTURAS
            (numDataLocal.numdfa >= data.numdfa) ?
                data.numdfa = numDataLocal.numdfa :
                data.numdfa = data.numdfa;
            //PEDIDOS
            (numDataLocal.numdop >= data.numdop) ?
                data.numdop = numDataLocal.numdop :
                data.numdop = data.numdop;

            //OFERTAS
            (numDataLocal.numdof >= data.numdof) ?
                data.numdof = numDataLocal.numdof :
                data.numdof = data.numdof;

            //ENTREGAS
            (numDataLocal.numdoe >= data.numdoe) ?
                data.numdoe = numDataLocal.numdoe :
                data.numdoe = data.numdoe;

            //numccaja
            (numDataLocal.numccaja >= data.numccaja) ?
                data.numccaja = numDataLocal.numccaja :
                data.numccaja = data.numccaja;

            //numcli
            (numDataLocal.numcli >= data.numcli) ?
                data.numcli = numDataLocal.numcli :
                data.numcli = data.numcli;

            //numgp
            (numDataLocal.numgp >= data.numgp) ?
                data.numgp = numDataLocal.numgp :
                data.numgp = data.numgp;

            //numgpa
            (numDataLocal.numgpa >= data.numgpa) ?
                data.numgpa = numDataLocal.numgpa :
                data.numgpa = data.numgpa;

            //numnota
            (numDataLocal.numnota >= data.numnota) ?
                data.numnota = numDataLocal.numnota :
                data.numnota = data.numnota;

        } catch (error) {
            console.log("Error al traer data de numeros");
        }

        console.log("a guardar numeracion ", data);
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'numeracion', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    /* public async getNumeracionpago() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'numeracionpago').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    } */
   /* public async getNumeracionpago() {
        this.pagosModel = new Pagos();
        let basesdatas: any = await this.getDB();

        return new Promise(async (resolve, reject) => {
            try {
                let dataNumeracion: any = await this.pagosModel.getNumeracion();
                console.log("dataNumeracion ", dataNumeracion);


                let numeracion = 0;
                if (dataNumeracion[0].numeracion > 0) {
                    numeracion = dataNumeracion[0].numeracion;


                } else {
                    dataNumeracion = 0;

                }
                // let numeracion = dataNumeracion && dataNumeracion[0] ? dataNumeracion[0].numeracion : 0;
                // console.log("maxnumeracion", numeracion);

                /* let numServer = await this.nativeStorage.getItem(basesdatas + 'numeracionpago');
                numeracion = Number(numServer) > Number(numeracion) ? numServer : numeracion;
                console.log("maxnumeracion pago", numServer);
                console.log("maxnumeracion updated ", numeracion); */
                /*resolve(numeracion);
            } catch (error) {
                console.log("error en momento de ", error);
                reject(0)
            }
        });
    }*/

    public async setNumeracionpago(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'numeracionpago', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getCodigo() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'codigo').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setCodigo(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'codigo', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setLbcc(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'LBCC', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getDocSC() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'DOCSC').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setDocSC(data: any) {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'DOCSC', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async removeDocSC() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.remove(basesdatas + 'DOCSC').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async setUbicacion(data: any) {
        console.log("DEVD setUbicacion() ", data);
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'UBI', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                console.log(err);
                reject(false);
            });
        });
    }

    public async getUbicacion() {
        let basesdatas: any = await this.getDB();
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'UBI').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                console.log(err);
                reject(false);
            });
        });
    }

    public async setUUID(data: any) {
        let basesdatas: any = await this.getDB();
        console.log("enviando data--info para loguearse", data);
        return new Promise((resolve, reject) => {
            this.nativeStorage.setItem(basesdatas + 'UUID', data).then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                reject(false);
            });
        });
    }

    public async getUUID() {
        let basesdatas: any = await this.getDB();
        console.log("basesdatas + 'UUID' ", basesdatas + 'UUID');
        return new Promise((resolve, reject) => {
            this.nativeStorage.getItem(basesdatas + 'UUID').then((data: any) => {
                resolve(data);
            }).catch((err: any) => {
                console.log(err);
                reject(false);
            });
        });
    }
}
