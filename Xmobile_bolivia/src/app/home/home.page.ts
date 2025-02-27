import { Component, OnInit } from '@angular/core';
import { Documentos } from "../models/documentos";
import { AlertController, MenuController, NavController } from "@ionic/angular";
import { DataService } from "../services/data.service";
import { Documentopago } from "../models/documentopago";
import { ConfigService } from "../models/config.service";
import { Migrate } from "../models/migrate";
import { Geolocation } from '@ionic-native/geolocation/ngx';
import { dataResetLocal } from "../utilsx/dataResetLocal";
import { ConstantPool } from '@angular/compiler';
import { GlobalConstants } from "../../global";

@Component({
    selector: 'app-home',
    templateUrl: 'home.page.html',
    styleUrls: ['home.page.scss'],
})

export class HomePage implements OnInit {
    public appPages = [];
    public databasex: any;

    constructor(private navCrl: NavController, private dataService: DataService, public geolocation: Geolocation, public alertController: AlertController, private configService: ConfigService, private menu: MenuController) {
        this.databasex = ''
    }

    public async ngOnInit() {
        localStorage.setItem("Duplicado_pago", "0");
        this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
            if (resp) {
                console.log('resp GEOPOSITION SIN PROBLEMAS', resp);
                let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                console.log("registerLocation() ", "" + obj);
                localStorage.setItem("lat", obj.lat);
                localStorage.setItem("lng", obj.lng);
                GlobalConstants.Longitud = obj.lng;
                GlobalConstants.latitud = obj.lat;

            }
        }).catch(error => {
            console.log("error 1 ", error);


            // alert("error"+JSON.stringify(error));
        });

        if (localStorage.getItem("newSession") == "1") {
            let alert: any = await this.alertController.create({
                header: 'SINCRONIZAR.',

                buttons: [{
                    text: 'CONTINUAR',
                    handler: (data: any) => {
                        this.pageSincronizar();
                    }
                }]
            });
            await alert.present();
            // return this.toast.show(`Debes sincronizar. `, '4000', 'top').subscribe(toast => {
            // });
        }
        this.menu.enable(true, 'menuxmobile');
        this.menu.close('menuxmobile');
        this.appPages = this.dataService.menuHome();
        await this.iniDB();        
        this.dataService.exportNumeracionSync();

        let id: any = await this.configService.getSession();
        console.log("id campos dinamicos ------>", id);
        if(id[0].campodinamicos.length > 0){
            console.log("entra a hacer el alter");
            let table = '';
            let campo = '';
            for (let i = 0; i < id[0].campodinamicos.length; i++) {
                table = id[0].campodinamicos[i].tabla_xmobile;
                campo = 'campousu'+id[0].campodinamicos[i].Nombre;
                console.log("campo a crear",campo);
                await this.alterDB(table,campo);
            }
        }

        //agregar campos        
        await this.alterDB("clientes","U_EXX_TIPOPERS");
        await this.alterDB("clientes","U_EXX_TIPODOCU");
        await this.alterDB("clientes","U_EXX_APELLPAT");
        await this.alterDB("clientes","U_EXX_APELLMAT");
        await this.alterDB("clientes","U_EXX_PRIMERNO");
        await this.alterDB("clientes","U_EXX_SEGUNDNO");

        let userdata: any = await this.configService.getSession();
        if (userdata[0].localizacion == 2) {
            console.log("importar localizaciones ");
            console.log("userdata[0] ", userdata[0]);
            console.log({ "vendedor": userdata[0].config[0].codEmpleadoVenta });
            // let poli: any = await this.dataService.actionPoligono({ "idvendedor": userdata[0].idUsuario }); //config[0].codEmpleadoVenta 
            let id: any = await this.configService.getSession();
            console.log("session info ", id);
            let dataext: any = {
                "usuario": id[0].idUsuario,
                "sucursal": id[0].sucursalxId,
                "equipo": id[0].uuid,
                "pagina": 0
            };
            let noVisita: any = await this.dataService.actionNoVisita(dataext); //config[0].codEmpleadoVenta 

            let returnVisit: any = JSON.parse(noVisita.data);
            console.log(" noVisita data ", returnVisit.respuesta);

            localStorage.setItem("motivosNoVenta", JSON.stringify(returnVisit.respuesta));
            // console.log("poligonos data ", poli);
            // let dataRutas: any = await this.configService.setPoligono(JSON.parse(poli.data));//
            //  console.log("noVisita data ", noVisita);
            // console.log("dataRutas ", dataRutas.respuesta);
        }

    }

    public async iniDB() {
        this.databasex = await this.configService.getName();
        try {
            let migrate = new Migrate();
            await migrate.create();
            let documentos = new Documentos();
            let documentospago = new Documentopago();
            await documentos.createView();
            await documentos.listadodocumentos();
            await documentospago.viewPagos();
            await this.acttionAlterTables();
        } catch (e) {
            console.log(e);
        }
    }
    
    acttionAlterTables = async () => {
        console.group("[ALTER TABLES]");
        let classDeleteData = new dataResetLocal();
        let dataMigrates = [];
        try {
            let x: any = await this.dataService.serviseMigratesMovil();
            console.log("return x migrates  ", JSON.parse(x.data).respuesta);
            dataMigrates = JSON.parse(x.data).respuesta;
        } catch (error) {
            console.log("error servicio ", error)
        }
        // await classDeleteData.deleteDatabase() // ELIMINA BD
        await classDeleteData.alterTables(dataMigrates) //ADICIONA CAMPOS 
        console.groupEnd();
    }


    public pageSincronizar() {
        this.navCrl.navigateForward(`sincronizacion`);
    }

    public async alterDB(tabla,campo) {
        try {
            let migrate = new Migrate();
            await migrate.alter(tabla,campo);
        } catch (e) {
            console.log(e);
        }
    }

    
}
