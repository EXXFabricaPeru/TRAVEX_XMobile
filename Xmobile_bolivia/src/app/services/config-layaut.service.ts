import { Injectable } from '@angular/core';
import { HTTP } from '@ionic-native/http/ngx';
import { Network } from '@ionic-native/network/ngx';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { ConfigService } from '../models/config.service';
import { IConfigLayaut } from '../types/IConfiglayaut';
import { httpResponse } from '../types/IPagos';

@Injectable({
  providedIn: 'root'
})
export class ConfigLayautService {
  path; 
  constructor(
    private http: HTTP, private configService: ConfigService,
    private network: Network, private spinnerDialog: SpinnerDialog,

  ) { }
  async getConfig(): Promise<any>{

    this.path = await this.configService.getIp();
    console.log(this.path + 'v2/configlayaut');
    return new Promise((resolve, reject) =>
    { 
      this.http.post(this.path + 'v2/configlayaut', {}, {}).then((data: any) => {
        console.log("response", data);
        let responseMid = JSON.parse(data.data);
        console.log("responseMid ", responseMid);

        /* let response: httpResponse = {
            mensaje: "Registro exitoso.",
            codigo: 200,
            estado: 1,
            data: responseMid.respuesta ? responseMid.respuesta : responseMid.mensaje
        }; */
        resolve(responseMid);
    }).catch((error: any) => {
        console.log("error al enviar a mid ", error);
        let response: httpResponse = {
            mensaje: "1. Ocurrió un error al intentar comunicarse con el servidor.",
            codigo: 500,
            estado: 0,
        };
        reject(response);
    });
    })
   
    //return responseData;
   }

  saveConfigLayaut = async (data: IConfigLayaut[]) =>
  { 
     console.log("guardando-->",data)
    localStorage.setItem("configlayaut", JSON.stringify(data));
   }
  
  getConfigLayaut = async () =>
  { 
    console.log("retornando-->")
    return JSON.parse(localStorage.getItem("configlayaut"))
  }
}
