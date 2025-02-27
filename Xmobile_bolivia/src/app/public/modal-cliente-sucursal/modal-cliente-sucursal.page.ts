import { Component, OnInit,Renderer2 } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Toast } from '@ionic-native/toast/ngx';
import { WheelSelector } from '@ionic-native/wheel-selector/ngx';
import { ModalController, NavParams, ToastController } from '@ionic/angular';
import { ConfigService } from '../../models/config.service';
import { DataService } from '../../services/data.service';
import { Territorios } from '../../models/territorios';
@Component({
  selector: 'app-modal-cliente-sucursal',
  templateUrl: './modal-cliente-sucursal.page.html',
  styleUrls: ['./modal-cliente-sucursal.page.scss'],
})
export class ModalClienteSucursalPage implements OnInit {

  public AddresName2: string;
  public idfrom = 4;
  public Street2: string;
  public eventoClick = null;
  public AdresType2: string;

  public u_territorio2: string;
  public code_territorio2: string;

  public sucursalForm: FormGroup;
  territoriosJson: any;
  isSubmitted = false;
  constructor(public formBuilder: FormBuilder,
    private toastController: ToastController,
    public navParams: NavParams,
    private toast: Toast,
    
    public modalController: ModalController, private selector: WheelSelector,private configService: ConfigService,private dataService: DataService,private renderer: Renderer2) {
    this.sucursalForm = formBuilder.group({
      AddresName: ['', [Validators.required, Validators.minLength(2)]],
      Street: ['', [Validators.required, Validators.minLength(2)]],
      AdresType: ['', Validators.required],
      u_territorio: ['', Validators.required]
    });
  }
  /**
   * example validators
   *    name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.pattern('[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$')]],
      dob: [this.defaultDate],
      mobile: ['', [Validators.required, Validators.pattern('^[0-9]+$')]]
   */
  /*
   //  idUser varchar(11) NOT NULL,
    AddresName varchar(255) NOT NULL,
    Street varchar(255) NOT NULL,
    //State varchar(255) NULL,
    //FederalTaxId varchar(255) NULL,
    //CreditLimit varchar(255) NULL,
    ////CardCode varchar(255) NULL,
    //User varchar(25) NULL,
    //Status varchar(25) NULL,
    //DateUpdate varchar(255) NULL,
    //idDocumento integer NULL,
    ////TaxCode varchar(255) NULL,
    AdresType varchar(25) NULL, 
    ////u_zona varchar(255) NULL,
    u_lat varchar(255) NULL, 
    u_lon varchar(255) NULL,
    u_territorio varchar(255) NULL, 
   //// u_vendedor varchar(25) NULL
  
    "AddresName": "asuncion",
    "Street": "",
    "State": "",
    "FederalTaxId": "",
    "CreditLimit": "0",
    "CardCode": "901000001",
    "User": "1",
    "Status": "1",
    "DateUpdate": "2020-11-26 00:00:00.000000",
  
    "TaxCode": "IVA_10", NO
    "AdresType": "S", // select (ENTREGA S  FACTURACION B)
    "u_zona": "", NO
    "u_lat": "", 
    "u_lon": "",
    "u_territorio": null, // SERVICE
    "u_vendedor": null (USER LOGEO)
  */

    public async  ngOnInit() {
    console.log("ngOnInit Modal ");
   
    console.log("this.navParams.data ", this.navParams.data)
    // Retrieve the JSON string


    let Territorio = new Territorios();
    this.territoriosJson = await Territorio.findAll();
    console.log("this.territoriosJson",this.territoriosJson);

    //this.territoriosJson = JSON.parse(localStorage.getItem("territorios"));
    console.log("propssssss ", this.territoriosJson);
    console.log("props ", this.navParams.data.direccion);
    console.log("props nombresSucursales", this.navParams.data.nombresSucursales);

    console.log(this.territoriosJson);
    this.Street2 = this.navParams.data.direccion;
    console.log("this.AddresName2 ", this.Street2);
    this.carga_camposusuario('');
  }

  public closeModal(data: any) {
    this.modalController.dismiss(data);
  }

  public async presentToast(text) {
    const toast: any = await this.toastController.create({
      message: text,
      position: 'bottom',
      duration: 3000
    });
    toast.present();
  }
  /**
   * validators */
  get errorControl() {
    return this.sucursalForm.controls;
  }
  public async submitForm() {
    console.log("AddresName ", this.AddresName2);
    console.log("AdresType2 ", this.AdresType2);
    console.log("code_territorio2 ", this.code_territorio2);
    console.log("Street2 ", this.Street2);

    //this.isSubmitted = true;
    try {
      if (this.AddresName2.trim() == "" || this.Street2.trim() == "" || this.AdresType2.trim() == ""
        || this.AddresName2 == undefined || this.Street2 == undefined || this.AdresType2 == undefined || this.code_territorio2 == undefined
      ) {

        this.toast.show("Complete los campos.", "3000", "top").subscribe(toast => {
        });
        return false;

      } else {

        if (this.navParams.data.nombresSucursales.includes(this.AddresName2)) {
          this.toast.show("El nombre de la dirección ya fué registrado.", "3000", "top").subscribe(toast => {
          });
          return false;
        }


        let sumallLineNum: any = 0;
        let LineNum = 0;
        if (this.navParams.data.dataSucursales.length > 0) {
          sumallLineNum = this.navParams.data.dataSucursales.reduce(function (prev, current) {
            return (prev.LineNum > current.LineNum) ? prev : current
          })
          LineNum = Number(sumallLineNum.LineNum) + 1;
        }

        let camposusuario = await this.datacamposusuario();

        let dataNew: any = [{
          AddresName: this.AddresName2,
          Street: this.Street2,
          AdresType: this.AdresType2,
          u_territorio: this.code_territorio2,
          LineNum: LineNum,
          lat: 0,
          lon: 0,
          camposusuario: camposusuario
        }]
        console.log("data usuario sucursal", dataNew)

        this.closeModal(dataNew);
      }

    } catch (error) {
      this.toast.show("Complete los campos.", "3000", "top").subscribe(toast => {
      });
      return false;
    }

  }
  onChangeAddresName2(event: any) {
    console.log(event.detail.value)
    this.AddresName2 = event.detail.value;
  }
  onChangeStreet2(event: any) {
    console.log(event.detail.value)
    this.Street2 = event.detail.value;
  }

  onChangeAdresType2(event: any) {
    console.log(event.detail.value)
    this.AdresType2 = event.detail.value;
  }

  onChangeAdresu_territorio2(event: any) {
    console.log(event.detail.value)
    this.u_territorio2 = event.detail.value;
  }
  listarTerritorios() {
    if (this.territoriosJson.length > 0) {
      let arr = [];
      for (let x of this.territoriosJson)
        arr.push({ description: x.Description });
      this.selector.show({
        title: "TERRITORIO.",
        items: [arr],
        positiveButtonText: "SELECCIONAR",
        negativeButtonText: "CANCELAR"
      }).then((result: any) => {
        // this.litPreciosSelect = this.territoriosJson[result[0].index];
        console.log("result ", result);

        this.u_territorio2 = result[0].description;
        this.code_territorio2 = this.territoriosJson.filter(value => value.Description == this.u_territorio2)[0].TerritoryID;

      }, (err: any) => {
        console.log(err);
      });
    }
  }

  /* CAMPOS DINAMICOS DE USUARIO*/

  public async carga_camposusuario(datos){
    console.log("inicia la carga");
    let usuariodata: any = await this.configService.getSession();
    let contenedorcampos = '';
    console.log(usuariodata[0].campodinamicos);

    console.log(usuariodata[0].campodinamicos);
    if(usuariodata[0].campodinamicos.length > 0){
        console.log("length es mayor a 0")
        contenedorcampos = await this.dataService.createcampususer(usuariodata[0].campodinamicos,this.idfrom,datos);
    }

    const div: HTMLDivElement = this.renderer.createElement('div');
    div.className = "col-md-12";
    div.innerHTML = contenedorcampos;
    this.renderer.appendChild(document.getElementById("contenedorcamposs"), div);
    console.log("datos",div);

    for (let i = 0; i < usuariodata[0].campodinamicos.length; i++) {
        if(usuariodata[0].campodinamicos[i].Objeto == this.idfrom){
            if(usuariodata[0].campodinamicos[i].tipocampo == 1){
                if(usuariodata[0].campodinamicos[i].flagrelacion == 1){
                    let campo = "campousu"+usuariodata[0].campodinamicos[i].Nombre;
                    this.eventoClick = this.renderer.listen(
                        document.getElementById(campo),
                        "ionChange",
                        evt => {
                            this.cargalista_campousuario(evt,usuariodata[0].campodinamicos[i].Id);
                        }
                    );
                }
            }
        }
    }
}


public async datacamposusuario(){
    let data= [];
    let valor: any;
    let sesion = await this.configService.getSession();
    let camposusuario = sesion[0].campodinamicos;
    for (let i = 0; i < camposusuario.length; i++) {
        if(camposusuario[i].Objeto == this.idfrom){
            let campo = "campousu"+camposusuario[i].Nombre;
            var variable = document.getElementsByClassName(campo);
            if(camposusuario[i].tipocampo == 1){ 
               for (let i = 0; i < variable[0]["childNodes"].length; i++) {
                if(variable[0]["childNodes"][i]["className"] == "aux-input"){
                    valor =  variable[0]["childNodes"][i]["defaultValue"];
                }
               }
            }else{
                if(camposusuario[i].tipocampo == 0){ 
                    valor =  variable[0]["childNodes"][0]["childNodes"][0]["defaultValue"];
                }else{
                    console.log("datos del campo numerico");
                    valor =variable[0]["childNodes"][1]["value"];
                }
            }
            data.push({
                Objeto: camposusuario[i].Objeto,
                cmidd: camposusuario[i].cmidd,
                tabla: camposusuario[i].tabla,
                campo: campo,
                valor: valor
            });
        }
    }
    return data;
}

public async cargalista_campousuario(val,id){

    console.log(val);

    let sesion = await this.configService.getSession();
    let camposusuario = sesion[0].campodinamicos;
    let codigosel= '';
    for (let i = 0; i < camposusuario.length; i++) {
        if(camposusuario[i].tipocampo == 1){ 
            if(camposusuario[i].Id == id){
                for (let l = 0; l < camposusuario[i].lista.length; l++){
                    if(camposusuario[i].lista[l].codigo == val.detail.value){
                        codigosel = camposusuario[i].lista[l].Id;
                    }
                }
            }
        }
    }


    for (let i = 0; i < camposusuario.length; i++) {
        if(camposusuario[i].Objeto == this.idfrom){
            if(camposusuario[i].tipocampo == 1){ 
                if(camposusuario[i].relacionado == id){
                    let campo = ".campousu"+camposusuario[i].Nombre;
                    const objeto = document.querySelector(campo);
                    let contenedorcampos = '';
                    for (let l = 0; l < camposusuario[i].lista.length; l++) {
                        if(camposusuario[i].lista[l].cabecera == id && camposusuario[i].lista[l].detalle == codigosel){
                            let codigo = camposusuario[i].lista[l].codigo;
                            let nombre = camposusuario[i].lista[l].nombre.replace(/['"]+/g, '');
                            contenedorcampos += "<ion-select-option  value=\""+codigo+"\">\""+nombre+"\"</ion-select-option>"
                        }
                    }
                    objeto.innerHTML = contenedorcampos;
                }
            }
        }
    }
}

}
