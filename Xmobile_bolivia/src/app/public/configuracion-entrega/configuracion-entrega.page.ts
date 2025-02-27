import { NavigationEnd, Router, RoutesRecognized, ActivatedRoute } from '@angular/router';
import { Component, OnInit } from '@angular/core';
import { Clientespercepciones } from 'src/app/models/clientespercepciones';
import { Location } from '@angular/common';
import { map, filter, scan, pairwise } from 'rxjs/operators';
import { PerTransportista } from 'src/app/models/pertransportistas';
import { PerConfigEntrega } from 'src/app/models/perconfigentrega';
import { Toast } from '@ionic-native/toast/ngx';
import {ConfigService} from "../../models/config.service";
import { IonCheckbox, ModalController, NavParams } from '@ionic/angular';

@Component({
  selector: 'app-configuracion-entrega',
  templateUrl: './configuracion-entrega.page.html',
  styleUrls: ['./configuracion-entrega.page.scss'],
})
export class ConfiguracionEntregaPage implements OnInit {
  public transportistasModel=new PerTransportista();
  public configEntregaModel=new PerConfigEntrega();

  selectTransportis:any;
  dataTransportistas:any=[];
  selectTransportista:any;
  dataAddress:any=[];
  selectAddress:any=[];
  dataNotes1:any=[];
  selectNotes1:any=[];
  dataPLAVEH:any=[];
  selectPLAVEH:any=[];
  selectModalidad:any=[];
  dataModalidad:any=[];
  nombretrasportista:string;
  marcavehiculo:string;
  placatolva:string;
  permiso:boolean;
  editbo:string;
  selectTransportisEnabled:boolean;
  selectAddressEnabled:boolean;
  selectNotes1Enabled:boolean;
  selectPLAVEHEnabled:boolean;
  selectModalidadEnabled:boolean;
  urlAnterior="";
  showMenu:any=true;
  public pedidoData: any;
  public flagTipoDoc: boolean;

  constructor(private _routerActivated:ActivatedRoute ,
    private _toast:Toast,
    private configService: ConfigService,
    public navParams: NavParams,
    public modalController: ModalController
    ) {
      this.showMenu=this._routerActivated.snapshot.paramMap.get('showmenu');
      console.log("menu ocultar",this.showMenu);      
      this.pedidoData = navParams.data;
    }

  ngOnInit() {
    this.initPercepciones();
  }

  public async initPercepciones(){
    let dataExist:any=await this.configEntregaModel.selectPerEntrega();
    console.log("existe una configuracion",dataExist);

    this.dataTransportistas= await this.transportistasModel.selectPerTipoOperaciones();

    if(dataExist && dataExist.length>0){
      this.editbo = '1';
      this.permiso = false;

      this.selectTransportista={
        CardCode:dataExist[0].U_EXX_CODTRANS,
        CardName:dataExist[0].U_EXX_NOMTRANS,
        LicTradNum:dataExist[0].U_EXX_RUCTRANS,
        Address:dataExist[0].U_EXX_DIRTRANS,
        Name:dataExist[0].U_EXX_NOMCONDU,
        Notes1:dataExist[0].U_EXX_LICCONDU,
        U_EXX_PLAVEH:dataExist[0].U_EXX_PLACAVEH,
        U_EXX_MARVEH:dataExist[0].U_EXX_MARCAVEH,
        U_EXX_PLATOL:dataExist[0].U_EXX_PLACATOL,
        U_EXX_FE_MODTRA:dataExist[0].U_EXX_FE_MODTRA,
      }

      console.log(this.selectTransportista);

      let where = "Cardcode = '"+this.selectTransportista.CardCode+"'";
      let campos = 'Distinct Address';
      this.dataAddress = await this.configEntregaModel.findByform(where,campos);

      where = "Cardcode = '"+this.selectTransportista.CardCode+"' and Address ='"+this.selectTransportista.Address+"'";
      campos = 'Distinct Notes1';
      this.dataNotes1=await this.configEntregaModel.findByform(where,campos);

      where = "Cardcode = '"+this.selectTransportista.CardCode+"' and Address ='"+this.selectTransportista.Address+"' and Notes1 = '"+this.selectTransportista.Notes1+"'";
      campos = 'Distinct U_EXX_PLAVEH';
      this.dataPLAVEH=await this.configEntregaModel.findByform(where,campos);

      let userdata = await this.configService.getSession();

      this.dataModalidad=userdata[0]['modalidad'];

      this.selectTransportis = this.selectTransportista.CardCode;
      this.nombretrasportista = this.selectTransportista.Name;
      this.selectAddress = this.selectTransportista.Address;
      this.selectNotes1 = this.selectTransportista.Notes1;
      this.selectPLAVEH = this.selectTransportista.U_EXX_PLAVEH;
      this.marcavehiculo = this.selectTransportista.U_EXX_MARVEH;
      this.placatolva = this.selectTransportista.U_EXX_PLATOL;
      this.selectModalidad = this.selectTransportista.U_EXX_FE_MODTRA;
      this.selectTransportisEnabled = true;
      this.selectAddressEnabled= true;
      this.selectNotes1Enabled= true;
      this.selectPLAVEHEnabled= true;
      this.selectModalidadEnabled= true;

    }else{
      this.selectTransportisEnabled = false;
      this.selectAddressEnabled= false;
      this.selectNotes1Enabled= false;
      this.selectPLAVEHEnabled= false;
      this.selectModalidadEnabled= false;
      this.editbo = '0';
      this.permiso = true;
      this.selectTransportista={
        CardCode:'',
        CardName:'',
        LicTradNum:'',
        Address:'',
        Name:'',
        Notes1:'',
        U_EXX_PLAVEH:'',
        U_EXX_MARVEH:'',
        U_EXX_PLATOL:'',
        U_EXX_FE_MODTRA:''
      }

    }
 }

  public async saveData(){
    // debugger;
    console.log("transportista seleccionado", this.selectTransportista)
    if(this.selectTransportista.CardCode == "" && this.flagTipoDoc){
      return this._toast.show('Tiene que seleccionar un transportista','2500', 'bottom').subscribe();
    }
    if(this.selectTransportista.Address == "" && this.flagTipoDoc){
      return this._toast.show('Tiene que seleccionar una direccion','2500', 'bottom').subscribe();
    }
    if(this.selectTransportista.Notes1 == "" && this.flagTipoDoc){
      return this._toast.show('Tiene que seleccionar una licencia de conductor','2500', 'bottom').subscribe();
    }
    if(this.selectTransportista.U_EXX_PLAVEH == "" && this.flagTipoDoc){
      return this._toast.show('Tiene que seleccionar una placa de vehiculo','2500', 'bottom').subscribe();
    }
    if(this.selectTransportista.U_EXX_FE_MODTRA == ""){
      return this._toast.show('Tiene que seleccionar un motivo de traslado','2500', 'bottom').subscribe();
    }
    
    this.pedidoData.transportista = this.selectTransportista;
    this.pedidoData.transportista.flagTipoDoc = this.flagTipoDoc;

    this.modalController.dismiss(this.pedidoData);

    // // // // // let dataExist:any=await this.configEntregaModel.selectPerEntrega();
    // // // // // console.log("dataconfig encontrado",dataExist);
    // // // // // if(dataExist && dataExist.length>0){
    // // // // //   //existe actualizamos
    // // // // //   this.configEntregaModel.update(this.selectTransportista,dataExist[0].id);
    // // // // //   this._toast.show('Se actualizo exitosamente','3000', 'bottom').subscribe();
    // // // // // }else{
    // // // // //   // no existe se crea configuracion 
    // // // // //   this.configEntregaModel.insert(this.selectTransportista);
    // // // // //   this._toast.show('Se registro su configuración exitosamente','3000', 'bottom').subscribe();
    // // // // // }
  }

  public async cargadatos(val,acc){
    console.log(acc);
    console.log("datos",this.selectTransportista);
    if(this.permiso){ 
      if(acc == 1){
        if(val){
          this.dataAddress =[];
          this.selectAddress =[];
          this.dataNotes1 =[];
          this.selectNotes1 =[];
          this.dataPLAVEH =[];
          this.selectPLAVEH =[];
          this.nombretrasportista = '';
          this.marcavehiculo = '';
          this.placatolva = '';
          this.selectTransportista.Address = '';
          this.selectTransportista.Notes1 = '';
          this.selectTransportista.Name = '';
          this.selectTransportista.U_EXX_PLAVEH = '';
          this.selectTransportista.U_EXX_MARVEH = '';
          this.selectTransportista.U_EXX_PLATOL = '';
          this.selectTransportista.U_EXX_FE_MODTRA = '';
          this.selectModalidad=[];
          this.dataModalidad=[];
          this.permiso = false;
          let campos = 'Distinct CardName,LicTradNum,Cardcode';
          let where = "Cardcode = '"+val+"'";
          let aux =await this.configEntregaModel.findByform(where,campos);

          console.log("daros",aux);

          this.selectTransportista.CardCode = aux[0].CardCode;
          this.selectTransportista.CardName = aux[0].CardName;
          this.selectTransportista.LicTradNum = aux[0].LicTradNum;

          campos = 'Distinct Address';
          this.dataAddress=await this.configEntregaModel.findByform(where,campos);
          this.permiso = true;
        }
      }
      if(acc == 2){
        if(val){
          this.dataNotes1 =[];
          this.selectNotes1 =[];
          this.dataPLAVEH =[];
          this.selectPLAVEH =[];
          this.nombretrasportista = '';
          this.marcavehiculo = '';
          this.placatolva = '';
          this.selectTransportista.Notes1 = '';
          this.selectTransportista.Name = '';
          this.selectTransportista.U_EXX_PLAVEH = '';
          this.selectTransportista.U_EXX_MARVEH = '';
          this.selectTransportista.U_EXX_PLATOL = '';
          this.selectTransportista.U_EXX_FE_MODTRA = '';
          this.selectModalidad=[];
          this.dataModalidad=[];
          this.permiso = false;
          this.selectTransportista.Address = val;
          let where = "Cardcode = '"+this.selectTransportista.CardCode+"' and Address ='"+val+"'";
          let campos = 'Distinct Notes1';
          this.dataNotes1=await this.configEntregaModel.findByform(where,campos);
          this.permiso = true;
        }
      }
      if(acc == 3){
        if(val){
          this.dataPLAVEH =[];
          this.selectPLAVEH =[];
          this.nombretrasportista = '';
          this.marcavehiculo = '';
          this.placatolva = '';
          this.selectTransportista.U_EXX_PLAVEH = '';
          this.selectTransportista.U_EXX_MARVEH = '';
          this.selectTransportista.U_EXX_PLATOL = '';
          this.selectTransportista.U_EXX_FE_MODTRA = '';
          this.selectModalidad=[];
          this.dataModalidad=[];
          this.permiso = false;

          this.selectTransportista.Notes1 = val;
          let where = "Cardcode = '"+this.selectTransportista.CardCode+"' and Address ='"+this.selectTransportista.Address+"' and Notes1 = '"+val+"'";
          let campos = 'Distinct Name';
          let aux =await this.configEntregaModel.findByform(where,campos);
          this.selectTransportista.Name = aux[0].Name;
          this.nombretrasportista = aux[0].Name;
          campos = 'Distinct U_EXX_PLAVEH';
          this.dataPLAVEH=await this.configEntregaModel.findByform(where,campos);

          this.permiso = true;
        }
      }
      if(acc == 4){
        if(val){
          // debugger;
          this.selectModalidad=[];
          this.dataModalidad=[];
          this.selectTransportista.U_EXX_FE_MODTRA = '';
          this.permiso = false;
          this.selectTransportista.U_EXX_PLAVEH = val;
          let where = "Cardcode = '"+this.selectTransportista.CardCode+"' and Address ='"+this.selectTransportista.Address+"' and Notes1 = '"+this.selectTransportista.Notes1 +"' and U_EXX_PLAVEH = '"+val +"'";
          let campos = 'Distinct U_EXX_MARVEH,U_EXX_PLATOL ';
          let aux =await this.configEntregaModel.findByform(where,campos);
          this.selectTransportista.U_EXX_MARVEH = aux[0].U_EXX_MARVEH;
          this.selectTransportista.U_EXX_PLATOL = aux[0].U_EXX_PLATOL;
          this.marcavehiculo = aux[0].U_EXX_MARVEH;
          this.placatolva = aux[0].U_EXX_PLATOL == "null" ? "" : aux[0].U_EXX_PLATOL;

          let userdata = await this.configService.getSession();
          console.log("userdata ------>", userdata[0])
          // this.dataModalidad=userdata[0]['modalidad'];
          this.dataModalidad = [{ codigo: '01', nombre:	'Venta' },
                                { codigo: '02', nombre:	'Compra' },
                                { codigo: '04', nombre:	'Traslado entre establecimientos de la misma empresa' },
                                { codigo: '08', nombre:	'Importación' },
                                { codigo: '09', nombre:	'Exportación' },
                                { codigo: '13', nombre:	'Otros' },                                              
                                { codigo: '14', nombre:	'Venta sujeta a confirmación del comprador' },
                                { codigo: '18', nombre:	'Traslado emisor itinerante CP' },
                                { codigo: '19', nombre:	'Traslado a zona primaria' },
                                { codigo: '03', nombre:	'Venta con entrega a terceros' },
                                { codigo: '05', nombre:	'Consignación' },
                                { codigo: '06', nombre:	'Devolución' },
                                { codigo: '07', nombre:	'Recojo de bienes transformados' },
                                { codigo: '17', nombre:	'Traslado de bienes para transformación' },
            ];

          this.permiso = true;
        }
      }
      if(acc == 5){
        if(val){
          this.selectTransportista.U_EXX_FE_MODTRA = val;
        }
      }
    }    
  }

  public activaedi(){
    this.permiso = true;
    this.selectTransportisEnabled = false;
    this.selectAddressEnabled= false;
    this.selectNotes1Enabled= false;
    this.selectPLAVEHEnabled= false;
    this.selectModalidadEnabled= false;
  }
}
