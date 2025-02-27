import { Injectable } from '@angular/core';
import { NativeStorage } from "@ionic-native/native-storage/ngx";
import {ConfigService} from "../models/config.service";

@Injectable({
    providedIn: 'root'
})
export class Camposusuario {
    private nativeStorage: NativeStorage;
    constructor() {
        this.nativeStorage = new NativeStorage();
    }


    public async camposusuario(data,objeto){
        console.log("datos enviados",data);
        let configService = new ConfigService(this.nativeStorage);
        let sesion: any = await configService.getSession();
        let sql2 ='';

        if(sesion[0].campodinamicos.length > 0){

            for (let i = 0; i < sesion[0].campodinamicos.length; i++) {
                if(sesion[0].campodinamicos[i].Objeto == objeto){

                    let campo = 'campousu'+sesion[0].campodinamicos[i].Nombre;
                    let ban = 0;
                    
                    for (let j = 0; j < data.camposusuario.length; j++) {

                        if(data.camposusuario[j].campo == campo){
                            sql2 = sql2+','+`'${data.camposusuario[j].valor}'`;
                            ban = 1;
                        }
                    }

                    if(ban == 0){
                        sql2 = sql2+`,''`;
                    }
                }
            }
        }
        console.log("Retorna "+sql2);
        return sql2;
    }

    
    public async consultasesion(){
        let configService = new ConfigService(this.nativeStorage);
        let sesion: any = await configService.getSession();
        return sesion;
    }


    public async camposusuariosinc(data,objeto,sesion){
        let sql2 ='';
        for (let i = 0; i < sesion[0].campodinamicos.length; i++) {
            if(sesion[0].campodinamicos[i].Objeto == objeto){

                let campo = sesion[0].campodinamicos[i].Nombre;
                let ban = 0;
                if(data[campo]){
                    sql2 = sql2+','+`'${data[campo]}'`;
                    ban = 1;
                }
                if(ban == 0){
                    sql2 = sql2+`,''`;
                }
            }
        }
        console.log("Retorna "+sql2);
        return sql2;
    }


    public async camposusuariosinc2(data,objeto,sesion){
        let sql2 ='';
        console.log("datos que mandan",data);
        for (let i = 0; i < sesion[0].campodinamicos.length; i++) {
            if(sesion[0].campodinamicos[i].Objeto == objeto){
                let campo = "campousu"+sesion[0].campodinamicos[i].Nombre;
                let ban = 0;
                for (let d of data) {
                    if(campo == d.campo){
                        sql2 = sql2+','+`'${d.valor}'`;
                        ban = 1;
                    }
                }
                if(ban == 0){
                    sql2 = sql2+`,''`;
                }
            }
        }
        console.log("Retorna "+sql2);
        return sql2;
    }

    public async camposusuarioupdate(data,objeto){
        let configService = new ConfigService(this.nativeStorage);
        let sesion: any = await configService.getSession();
        let sql2 ='';
        if(sesion[0].campodinamicos.length > 0){
            for (let i = 0; i < sesion[0].campodinamicos.length; i++) {
                if(sesion[0].campodinamicos[i].Objeto == objeto){
                    let campo = 'campousu'+sesion[0].campodinamicos[i].Nombre;
                    let ban = 0;
                    for (let x of Object.keys(data)) {
                        if(x == campo){
                            sql2 = sql2+','+`${campo} = '${data[x]}'`;
                            ban = 1;
                        }
                    }
                    if(ban == 0){
                        sql2 = sql2+`${campo} = ''`;
                    }
                }
            }
        }
        console.log("Retorna "+sql2);
        return sql2;
    }

    public async camposusuarioupdate2(data,objeto){
        let configService = new ConfigService(this.nativeStorage);
        let sesion: any = await configService.getSession();
        let sql2 ='';

        if(sesion[0].campodinamicos.length > 0){
            for (let i = 0; i < sesion[0].campodinamicos.length; i++) {
                if(sesion[0].campodinamicos[i].Objeto == objeto){
                    let campo = 'campousu'+sesion[0].campodinamicos[i].Nombre;

                    let ban = 0;
                    for (let x = 0; x < data.length; x++) {
                        if(data[x]['campo'] == campo){
                            sql2 = sql2+','+`${campo} = '${data[x]['valor']}'`;
                            ban = 1;
                        }
                    }
                    if(ban == 0){
                        sql2 = sql2+`${campo} = '',`;
                    }
                }
            }
        }
        console.log("Retorna "+sql2);
        return sql2;
    }

}