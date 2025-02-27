import { Component, OnInit,Renderer2 } from '@angular/core';
import { ModalController, NavParams } from "@ionic/angular";
import { WheelSelector } from "@ionic-native/wheel-selector/ngx";
import { Toast } from "@ionic-native/toast/ngx";
import { Documentos } from "../../models/documentos";
import { ConfigService } from "../../models/config.service";
import { Calculo } from "../../utilsx/calculo";
import { Clientes } from "../../models/clientes";
import { Pagos } from "../../models/V2/pagos";
import { DataService } from "../../services/data.service";
import { Bancos } from "../../models/bancos";
import { Centrocostos } from "../../models/centrocostos";
import { Documentopago } from "../../models/documentopago";
import { Tiempo } from "../../models/tiempo";
import * as moment from 'moment';
import { of } from 'rxjs';
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { IPagos, IMediosPagos, httpResponse, IFacturasPagos } from '../../types/IPagos';
import { PagosService } from '../../services/pagos.service';
import { GlobalConstants } from "../../../global";
import { FormBuilder, FormGroup, Validators } from "@angular/forms";
import {ModalclientePage} from "../modalcliente/modalcliente.page";

@Component({
  selector: 'app-modal-modopago',
  templateUrl: './modal-modopago.page.html',
  styleUrls: ['./modal-modopago.page.scss'],
})
export class ModalModopagoPage implements OnInit {
  public dataexport: any;
  public tipo: number;
  public data: any;
  public pagosArray: any
  public cod: any;
  public monto: number;
  public montoAuxGlobal: number;
  public montoaux: number;
  public montodocx: number;
  public montopagar: number;
  public tc: number;
  public documento: any;
  public userdata: any;
  public monedaUser: any;
  public arrcambio: any;
  public formapago: any;
  public documentosdata: Documentos;
  public clientedata: Clientes;
  public boucher: number;
  public numero_tarjeta: number;
  public numero_id: number;
  public cambio: number;
  public chequeCheque: any;
  public dateExpires: any;
  public bancosgestion: any;
  public tranferenciaBanco: any;
  public tranferenciaBancoName: any;
  public tranferenciaComprobante: any;
  public currency: any;
  public tipodocument: any;
  public tipopago: any;
  public otppex: any;
  public centroName: any;
  public centroCode: any;
  public documentoId: any;
  public limitFecha: string;
  public nrecibo: number;
  public idfrom = 6;
  public dateEmision: any;

  enviando: boolean = false;
  numberformat;
  toggleuSD: boolean = false;
  estadoEnviar: boolean = false;
  montoDolar: any = 0;
  montoDolarAbs: any = 0;
  faltaCompletar: any = 0;
  CreditCardArray: any = [];
  CreditCardCode: any = "";
  CreditCardName: any = "";
  montoEnvio = 0
  montoDolarEnvio = 0
  auxmontoEnvio = 0
  auxmontoDolarEnvio = 0
  eventoClick = null;
  emitedForModel: String;
  chequeForm: FormGroup;
  typeChek: any;

  constructor(public formBuilder: FormBuilder,public modalController: ModalController, public navParams: NavParams, public dataservis: DataService, private spinnerDialog: SpinnerDialog, public pagosService: PagosService,
    private toast: Toast, private selector: WheelSelector, private configService: ConfigService,private dataService: DataService,private renderer: Renderer2) { 
    this.dataexport = navParams.data;
    this.documentosdata = new Documentos();
    this.clientedata = new Clientes();
    this.tipo = 0;
    this.tipopago = 0;
    this.montopagar = 1;
    this.arrcambio = [];
    this.bancosgestion = [];
    this.formapago = '';
    this.tipodocument = '';
    this.chequeCheque = 0;
    this.tranferenciaBanco = '';
    this.tranferenciaBancoName = '';
    this.tranferenciaComprobante = '';
    this.dateExpires = '';
    this.boucher = 0;
    this.numero_tarjeta = 0;
    this.numero_id = 0;
    this.cambio = 0;
    this.otppex = 0;
    this.montodocx = 0;
    this.centroName = '';
    this.centroCode = '';
    this.limitFecha = moment().format('YYYY-MM-DD');

    this.chequeForm = formBuilder.group({
        monto: ["", [Validators.required, Validators.pattern("^(\d|-)?(\d|.)*\,?\d*$")]],
        banco: ["", [Validators.required, Validators.minLength(2)]],
        nroCheque: ["", [Validators.pattern("^[0-9]+$"),Validators.min(999)]],
        fecha: ["", Validators.required],
        fechaEmision: ["",[]],
        emitedFor: ["", []],
        typeChek: ["", Validators.required],
      });

    console.log("this.limitFecha ", this.limitFecha)
  }



  public async ngOnInit(){
    console.log("DATOS QUE LLEGAN",this.dataexport);
    this.tipo = this.dataexport[0].Tiposel;
    this.data = this.dataexport[0].datos;
    this.pagosArray = this.dataexport[0].pagosrealizados;
    this.formapago = this.dataexport[0].formapago;
    this.montoAuxGlobal = this.dataexport[0].monto;
    this.monto = this.montoAuxGlobal;
    this.auxmontoEnvio = this.dataexport[0].monto;

    console.log("el monto que llega",this.monto);

    // tiene los datos del documento 
    let documento = GlobalConstants.CabeceraDoc;
    this.userdata = await this.configService.getSession();

    this.monto = this.montoAuxGlobal;

    this.montoaux = 0;
    this.cod = this.data.cod;

    if (!this.userdata[0].tipoTarjeta) {
        this.CreditCardArray.push({
            CreditCard: 1,
            CardName: "Sin Tarjetas"
        });
    } else {
        this.CreditCardArray = this.userdata[0].tipoTarjeta;
        console.log("this.CreditCardArray ", this.CreditCardArray);

    }

    this.monedaUser = this.userdata[0].config[0].moneda;
    await this.selectcambio();
    
    if (this.tipo == 1) {
        this.currency = 'USD';
    } else {
        for await (let moned of  this.userdata[0].monedas) {
            if(moned.Type == "L"){
                this.currency = moned.Code;
            }
        }
    }
   
    let sumadocx: any;
    switch (this.data.modo) {
        case ('FACTURAS'):
            console.log("FACTURAS");
            this.otppex = 2;
            this.tipodocument = 'factura';
            //sumadocx = this.data.documento.reduce((sum, value) => (sum + value.pagarx), 0);
            sumadocx = this.montoAuxGlobal;
            switch (this.tipo) {
                case (1)://dolares
                    this.montodocx = Calculo.round(sumadocx / this.tc);
                    break;
                case (2): //efectivo
                    this.montodocx = Calculo.round(sumadocx);
                    break;
            }
            break;
        case ('FACTURA'):
          console.log("FACTURA");
            this.otppex = 1;
            // this.documento = await this.documentosdata.findexe(this.data.documento[0].cod);

            console.log("DEVD factura a pagar   this.documento  ", documento);
            this.documento = documento[0];
            switch (this.documento.DocType) {
                case ('DOP'):
                    this.tipodocument = 'pedido';
                    break;
                case ('DFA'):
                    this.tipodocument = 'factura';
                    break;
                case ('DOF'):
                    this.tipodocument = 'oferta';
                    break;
            }
            sumadocx = this.montoAuxGlobal//Number(this.documento.saldox).toFixed(2);
            switch (this.tipo) {
                case (1)://dolares
                    this.montodocx = Calculo.round(sumadocx / this.tc);
                    break;
                case (2): //efectivo
                    this.montodocx = Calculo.round(sumadocx);
                    break;
            }
            break;
        case ('CLIENTE'):
          console.log("CLIENTE");
            this.tipodocument = 'cuenta';
            this.otppex = 3;
            this.documentoId = 0;
            break;
    }
    let id = '';
    switch (this.tipo) {
      case 1:
          id ="contenedorcampos_dolares";
          this.idfrom = 11;
      break;
      case 2:
          id ="contenedorcampos_efectivo";
          this.idfrom = 9;
      break;
      case 3:
          id ="contenedorcampos_tarjeta";
          this.idfrom = 8;
      break;
      case 4:
          id ="contenedorcampos_chueque";
          this.idfrom = 7;
      break;
      case 5:
          id ="contenedorcampos_transferencia";
          this.idfrom = 10;
      break;
  }
  this.carga_camposusuario('',id);

  }


  public closeModal(data: any) {
      this.modalController.dismiss();
  }

  public selectcambio(exe = true) {
    let ux: any;
    this.arrcambio = [];
    let tiposcambio = [];
    try {
        tiposcambio = this.userdata[0].tiposcambio.filter((n) => {
            return n.ExchangeRate > 0;
        });
        for (let cambio of tiposcambio) {
            this.arrcambio.push({
                ExchangeRate: cambio.ExchangeRate,
                ExchangeRateDate: cambio.ExchangeRateDate,
                ExchangeRateFrom: String(cambio.ExchangeRateFrom),
                ExchangeRateTo: String(cambio.ExchangeRateTo)
            });
        }
        if (typeof this.userdata[0].tipocambioparalelo != "undefined") {
            this.tc = parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio);
            this.arrcambio.push({
                ExchangeRate: parseFloat(this.userdata[0].tipocambioparalelo.tipoCambio),
                ExchangeRateDate: this.userdata[0].tipocambioparalelo.fecha,
                ExchangeRateFrom: this.userdata[0].tipocambioparalelo.from,
                ExchangeRateTo: this.userdata[0].tipocambioparalelo.to
            });
        }
    } catch (e) {
        this.modalController.dismiss(0);
        this.toast.show(`Existen problemas con el cambio de dólar cierra tu sesión y vuelve a ingresar.`, '4000', 'top').subscribe(toast => {
        });
    }
    if (!exe) {
        if (this.arrcambio.length > 0) {
            this.selector.show({
                title: "SELECCIONAR EL TIPO DE CAMBIO A USAR.",
                items: [this.arrcambio],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR",
                displayKey: 'ExchangeRate'
            }).then((result: any) => {
                ux = this.arrcambio[result[0].index];
                this.tc = ux.ExchangeRate;
                this.montopagar = Calculo.round(this.monto * this.tc);
            }, (err: any) => {
            });
        }
    }
  }

  public async eventUsd(type: String, event: any) {

    console.log("EVENT ---".repeat(10), event);
    console.log("event.detail.value ", event.detail.value);

    let montoEvent = 0
    let montoEventUSD = 0

    if (event.detail.value == "") {
        //   alert("esta vacio");
        this.monto = 0;
    }
    if (type == 'USD') {
        montoEventUSD = Number(event.detail.value.replace(',', ''))
        this.auxmontoDolarEnvio = Number(event.detail.value.replace(',', ''))
    } else {
        this.auxmontoEnvio = Number(event.detail.value.replace(',', ''))
        montoEvent = Number(event.detail.value.replace(',', ''))
    }
    console.log("this.auxmontoDolarEnvio ", this.auxmontoDolarEnvio);
    console.log("    this.auxmontoEnvio ", this.auxmontoEnvio);


    if (Number(event.detail.value >= 0)) {
        
        if (this.auxmontoDolarEnvio > 0 && this.toggleuSD) {
            console.log("this.montoDolar  ", this.auxmontoDolarEnvio);
            this.montoDolarAbs = (this.auxmontoDolarEnvio * this.tc).toFixed(2);
            console.log("this.montoDolarAbs ", this.montoDolarAbs);
            console.log("this.cambio ", this.cambio);
            this.montoEnvio = this.auxmontoEnvio
            this.montoDolarEnvio = this.auxmontoDolarEnvio
        } else {
            console.log("ELSEEE");
            console.log("this.montoDolar  ", this.auxmontoDolarEnvio);

        }

        console.log("-- this.monto) ", this.monto);
        console.log("-- this.montoDolarAbs) ", this.montoDolarAbs);
        console.log("-- this.montoAuxGlobal) ", this.montoAuxGlobal);
        console.log("this.cambio before  ", this.cambio);
        if ((Number(this.montoDolarAbs) + Number(this.auxmontoEnvio)) > this.montoAuxGlobal) {
            this.cambio = Number(Number(this.auxmontoEnvio) + Number(this.montoDolarAbs)) - Number(this.montoAuxGlobal);
            console.log("this.cambio after  ", this.cambio);

        } else {
            this.cambio = 0;
        }
        console.log("montoEvent ", montoEvent);
        console.log("this.montoDolarAbs ", this.montoDolarAbs);


        if ((Number(this.montoDolarAbs) + Number(this.auxmontoEnvio)) < this.montoAuxGlobal) {
            console.log("(this.montoDolarAbs + this.monto)  ", (Number(this.montoDolarAbs) + Number(this.auxmontoEnvio)));
            console.log("es menor a ");
            console.log(" this.montoAuxGlobal) ", this.montoAuxGlobal);
            this.faltaCompletar = this.montoAuxGlobal - (Number(this.montoDolarAbs) + (this.auxmontoEnvio));//+ Number(montoEvent)
        } else {
            console.log("no es menor a ");
            this.faltaCompletar = 0;
        }
    } else {
        console.log("no hacer nada");
    }
 }

  public async listBancos() {
    try {
        let storage: any = await this.configService.getSession();
        this.bancosgestion = storage[0].gestionbancos;
        if (this.bancosgestion.length > 0) {
            this.selector.show({
                title: "SELECCIONA UN BANCO.",
                items: [this.bancosgestion],
                positiveButtonText: "SELECCIONAR",
                negativeButtonText: "CANCELAR",
                displayKey: 'BankName'
            }).then((result: any) => {
                let rx: any = this.bancosgestion[result[0].index];
                this.tranferenciaBanco = rx.BankCode;
                this.tranferenciaBancoName = rx.BankName;
            }, (err: any) => {
            });
        }
    } catch (e) {
        this.bancosgestion = [];
    }
  }

  public async selectBanco() {
    let bancos = new Bancos();
    let arrbancos: any = await bancos.find();
    console.log("arrbancos", arrbancos);

    if (arrbancos.length == 0) {
        arrbancos.push({ cuenta: '0', nombre: 'Sin bancos.' });

        this.toast.show(`Lista de Bancos no encontrado.`, '4000', 'top').subscribe(toast => {
        });
    }
    if (arrbancos.length > 0)
        this.selector.show({
            title: "SELECCIONA UN BANCO.",
            items: [arrbancos],
            positiveButtonText: "SELECCIONAR",
            negativeButtonText: "CANCELAR",
            displayKey: 'nombre'
        }).then((result: any) => {
            let ux: any = arrbancos[result[0].index];
            this.tranferenciaBanco = ux.cuenta;
            this.tranferenciaBancoName = ux.nombre;
        }, (err: any) => {

        });
  }


  public async carga_camposusuario(datos,id) {
      console.log("llaga aqui0");
      let usuariodata: any = await this.configService.getSession();
      let contenedorcampos = '';

      if (usuariodata[0].campodinamicos.length > 0) {

          contenedorcampos = await this.dataService.createcampususer(usuariodata[0].campodinamicos, this.idfrom, datos);
      }

      const div: HTMLDivElement = this.renderer.createElement('div');
      div.className = "col-md-12";
      div.innerHTML = contenedorcampos;
      this.renderer.appendChild(document.getElementById(id), div);

      for (let i = 0; i < usuariodata[0].campodinamicos.length; i++) {
          if (usuariodata[0].campodinamicos[i].Objeto == this.idfrom) {
              if (usuariodata[0].campodinamicos[i].tipocampo == 1) {
                  if (usuariodata[0].campodinamicos[i].flagrelacion == 1) {
                      let campo = "campousu" + usuariodata[0].campodinamicos[i].Nombre;
                      this.eventoClick = this.renderer.listen(
                          document.getElementById(campo),
                          "ionChange",
                          evt => {
                              this.cargalista_campousuario(evt, usuariodata[0].campodinamicos[i].Id);
                          }
                      );
                  }
              }
          }
      }
  }

  public async datacamposusuario() {
      let data = [];
      let valor: any;
      let sesion = await this.configService.getSession();
      let camposusuario = sesion[0].campodinamicos;
      for (let i = 0; i < camposusuario.length; i++) {
        
          if (camposusuario[i].Objeto == this.idfrom) {
              let campo = "campousu" + camposusuario[i].Nombre;
              var variable = document.getElementsByClassName(campo);
              if (camposusuario[i].tipocampo == 1) {
                  for (let i = 0; i < variable[0]["childNodes"].length; i++) {
                      if (variable[0]["childNodes"][i]["className"] == "aux-input") {
                          valor = variable[0]["childNodes"][i]["defaultValue"];
                      }
                  }

              } else {
                  if (camposusuario[i].tipocampo == 0) {
                      valor = variable[0]["childNodes"][0]["childNodes"][0]["defaultValue"];
                  } else {

                      valor = variable[0]["childNodes"][1]["value"];
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
      console.log("datos agragados",data)

      return data;
  }

  public async cargalista_campousuario(val, id) {
      let sesion = await this.configService.getSession();
      let camposusuario = sesion[0].campodinamicos;
      let codigosel = '';
      for (let i = 0; i < camposusuario.length; i++) {
          if (camposusuario[i].tipocampo == 1) {
              if (camposusuario[i].Id == id) {
                  for (let l = 0; l < camposusuario[i].lista.length; l++) {
                      if (camposusuario[i].lista[l].codigo == val.detail.value) {
                          codigosel = camposusuario[i].lista[l].Id;
                      }
                  }
              }
          }
      }


      for (let i = 0; i < camposusuario.length; i++) {
          if (camposusuario[i].Objeto == this.idfrom) {
              if (camposusuario[i].tipocampo == 1) {
                  if (camposusuario[i].relacionado == id) {
                      let campo = ".campousu" + camposusuario[i].Nombre;
                      const objeto = document.querySelector(campo);
                      let contenedorcampos = '';
                      for (let l = 0; l < camposusuario[i].lista.length; l++) {
                          if (camposusuario[i].lista[l].cabecera == id && camposusuario[i].lista[l].detalle == codigosel) {
                              let codigo = camposusuario[i].lista[l].codigo;
                              let nombre = camposusuario[i].lista[l].nombre.replace(/['"]+/g, '');
                              contenedorcampos += "<ion-select-option  value=" + codigo + ">" + nombre + "</ion-select-option>"
                          }
                      }
                      objeto.innerHTML = contenedorcampos;
                  }
              }
          }
      }
  }

  public async agregarpago(){

        let value: any = {};
        value.dataInsert = {};
        this.monto = Number(Number(this.monto).toFixed(2));
        this.montoEnvio = this.auxmontoEnvio;
        this.montoDolarEnvio = this.auxmontoDolarEnvio;

        console.log("el monto a mandar es ",this.monto);

        if (this.montoDolar > 0) {
            console.log("tiene valor A validar....");

            const regex = /^[0-9]*$/;

            const onlyNumbers = regex.test(this.montoDolar); // true
            console.log("onlyNumbers ", onlyNumbers)
            if (!onlyNumbers) {
                // this.spinnerDialog.hide();
                return this.toast.show(`Pago en USD debe ser un valor entero. `, '4000', 'top').subscribe(toast => {
                });
            }else{
                this.auxmontoEnvio = (this.auxmontoDolarEnvio * this.tc);
            }
        } else {
            console.log("no hay valor en dolar no validar nada");
        }

        if (this.formapago == 'PEF') {
            console.log("this.monmontoEnvioto ", this.montoEnvio);
            console.log("this.montoAuxGlobal ", this.montoAuxGlobal);
            console.log("this.montoDolarEnvio ", this.montoDolarEnvio);
            console.log("this.cambio ", this.cambio);
            this.faltaCompletar = Number(this.faltaCompletar).toFixed(2);
            console.log("this.faltaCompletar ", this.faltaCompletar);

        }

        let pago = new Pagos();
        //pago con tarjeta
        if (this.formapago == 'PCC') {
            if (!this.boucher || String(this.boucher).trim() == '') {
                this.toast.show(`Ingrese el numero de boucher.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }


            if (!this.CreditCardCode) {
                this.toast.show(`Seleccione el tipo de tarjeta`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }

            console.log("datos de los pagos",this.pagosArray);

            if (this.pagosArray.length > 0) {
                let validate = this.pagosArray.filter(e => e.baucher == this.boucher);
                if (validate && validate.length>0) {
                    return this.toast.show(`El número de voucher fue utilizado`, "4000", "top").subscribe((toast) => {});
                } 
            }




        }
        //pago con transferencia
        if (this.formapago == 'PBT') {


            if (!this.tranferenciaBanco || String(this.tranferenciaBanco).trim() == '') {
                this.toast.show(`Tiene que seleccionar el campo banco.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }

            if (!this.tranferenciaComprobante || String(this.tranferenciaComprobante).trim() == '') {
                this.toast.show(`Ingrese el numero de Transferencia.`, '4000', 'top').subscribe(toast => {
                });

                return false;
            }

            if (!this.dateExpires || String(this.dateExpires).trim() == '') {
                this.toast.show(`Ingrese la fecha de la Transferencia.`, '4000', 'top').subscribe(toast => {
                });
                return false;
            }

            console.log("datos de los pagos",this.pagosArray);

            if (this.pagosArray.length > 0) {
                let validate = this.pagosArray.filter(e => e.numComprobante == this.tranferenciaComprobante &&  e.bancoCode == this.tranferenciaBanco);
                if (validate && validate.length>0) {
                    return this.toast.show(`El número de transferencia ya fue utilizado`, "4000", "top").subscribe((toast) => {});
                } 
            }

            let pagoCheque: any = await pago.uniquetrasferencia(this.tranferenciaComprobante,this.tranferenciaBanco);
            if (pagoCheque >= 1) {
                
                this.toast.show(`El numero de trasferencia ${this.tranferenciaComprobante} para el banco ${this.tranferenciaBancoName} ya fue utilizado..`, "4000", "top").subscribe((toast) => { });
                return false;
            }
        }

        //pago con cheques
        if (this.formapago == 'PCH') {

            if (!this.tranferenciaBancoName || String(this.tranferenciaBancoName).trim() == '') {
                this.toast.show(`Tiene que seleccionar el campo banco.`, '4000', 'top').subscribe(toast => {
                });

                this.spinnerDialog.hide();
                return false;
            }

            if (!this.chequeCheque || String(this.chequeCheque).trim() == '') {
                this.toast.show(`Ingrese el numero del chaque.`, '4000', 'top').subscribe(toast => {
                });

                this.spinnerDialog.hide();
                return false;
            }

            if (!this.dateExpires || String(this.dateExpires).trim() == '') {
                this.toast.show(`Ingrese la fecha del chaque.`, '4000', 'top').subscribe(toast => {
                });
                //this.direccion = await this.geocodeLatLng(latlng);
                this.spinnerDialog.hide();
                return false;
            }

            if (this.typeChek == null || this.typeChek == ""){
                this.toast.show(`Tipo de cheque requerido.`,"4000","top").subscribe((toast) => {});
                return false;
              }

            if (this.typeChek == "Diferido"){        
                if(this.dateEmision == null || this.dateEmision == ""){
                  
                  this.toast.show(`Fecha emisión requerida.`,"4000","top").subscribe((toast) => {});
                  return false;
                } else{
                 let timeEmision =  (new Date(moment((this.dateEmision?this.dateEmision:new Date())).format('YYYY-MM-DD'))).getTime();
                 let timeExpires = (new Date(moment((this.dateExpires?this.dateExpires:new Date())).format('YYYY-MM-DD'))).getTime(); // (new Date(this.dateExpires)).getTime();
                 if(timeEmision>timeExpires){
                      this.toast.show(`La fecha de emisión no puede ser mayor a la fecha de vencimiento.`,"4000","top").subscribe((toast) => {});
                      return false;
                 }
                }
            }else{
            //pagos al dia        
            let today: any = new Date();
            this.dateEmision = today;
            let timeEmision =  (new Date(moment((this.dateExpires?this.dateExpires:new Date())).format('YYYY-MM-DD'))).getTime();
                let timeExpires = (new Date(moment((this.dateExpires?this.dateExpires:new Date())).format('YYYY-MM-DD'))).getTime(); // (new Date(this.dateExpires)).getTime();
    
                if(timeEmision>timeExpires){
                    this.toast.show( `La fecha de vencimiento no puede ser menor a la fecha del día.`, "4000","top").subscribe((toast) => {});
                    return false;
                }
            }

              
            let pagoCheque: any = await pago.uniqueCheque(this.chequeCheque,this.tranferenciaBanco);
            if (pagoCheque >= 1) {
                
                this.toast.show(`El CHEQUE ${this.chequeCheque} para el banco ${this.tranferenciaBancoName} ya fue utilizado..`, "4000", "top").subscribe((toast) => { });
                return false;
            }

            if (this.chequeCheque.length<3){
                    this.toast.show(`El número de cheque tiene que ser mayor a 3 dígitos`,"4000","top") .subscribe((toast) => {});
                    return false;
            }

           
            
            console.log("datos de los pagos",this.pagosArray);

            if (this.pagosArray.length > 0) {
                let validate = this.pagosArray.filter(e => e.numCheque == this.chequeCheque &&  e.bancoCode == this.tranferenciaBanco);
                if (validate && validate.length>0) {
                    return this.toast.show(`El número de cheque ya fue utilizado`, "4000", "top").subscribe((toast) => {});
                } 
            }
        }

        /*REFACT PAGOS */
        let datapag: IPagos;
        console.log("this.data ", this.data);
        let monedaDolar = (parseFloat(this.data.monto) / this.tc);
        let monto = Calculo.round(this.auxmontoEnvio);
        console.log("monto",monto);

        let dataAux = await this.dataPayFormat(monto);
        console.log("dataAux",dataAux);

        this.modalController.dismiss(dataAux);
  }

  public async dataPayFormat(monto) {

    let tiempo = moment().format('YYYY-MM-DD');
 
    let camposmediospagos: any;
    switch (this.tipo) {
        case 1:
            this.idfrom = 11;
        break;
        case 2:
            this.idfrom = 9;
        break;
        case 3:
            this.idfrom = 8;
        break;
        case 4:
            this.idfrom = 7;
        break;
        case 5:
            this.idfrom = 10;
        break;
    }
    let data = [];

    for (let x = 1; x < 6; x++) {

        if(x == this.tipo){
            let aux = await this.datacamposusuario();
            for(let datos of aux){
                data.push(datos);
            }

        }
    }
    camposmediospagos = data;

    let mediosPagoData: IMediosPagos = {
        formaPago: this.formapago,
        monto: monto,
        numCheque: this.chequeCheque,
        numComprobante: this.tranferenciaComprobante,
        numTarjeta: String(this.numero_tarjeta),
        bancoCode: this.tranferenciaBanco,
        fecha: tiempo,
        cambio: this.cambio,
        monedaDolar: Number(this.auxmontoDolarEnvio),
        monedaLocal: Number(this.auxmontoEnvio),
        nro_recibo: this.cod,
        centro: this.centroCode,
        baucher: String(this.boucher),
        NumeroTarjeta: String(this.numero_tarjeta),
        NumeroID: String(this.numero_id),
        checkdate: this.dateExpires,
        transferencedate: this.dateExpires,
        CreditCard: this.CreditCardCode,
        camposusuario: camposmediospagos,
        emitidoPor:this.emitedForModel,
        tipoCheque: this.typeChek,
        dateEmision:this.dateEmision,
    }
    console.log("dataSend ", mediosPagoData);
    return mediosPagoData;
}

async selectClient(){

    console.log("ingreso para buscar");
    const modal = await this.modalController.create({
      component: ModalclientePage,
      cssClass: 'my-custom-class',
    });
   modal.onWillDismiss().then((data:any) =>{
    console.log("data for cliente",data);
     if (data.data){
      console.log (data.data.CardName);
       this.emitedForModel=data.data.CardName;
     }
   });
   
    return await modal.present();
  
  }

  onSelectTypeCheck(e) {    
    switch (e.target.value) {
      case 'Diferido':
        this.chequeForm.get('fechaEmision').setValidators([Validators.required]);
        this.chequeForm.controls['fechaEmision'].updateValueAndValidity()
        break;
      case 'Al_dia':
        // this.chequeForm.get('fechaEmision').clearValidators();
        this.chequeForm.controls['fechaEmision'].clearValidators(); 
        this.chequeForm.controls['fechaEmision'].updateValueAndValidity()
        this.chequeForm.controls['fechaEmision'].setValue(null)
        break;

      default:
        break;
    }
    console.log("se selecciono el tipo", e.target.value);
  }

  eventToogle(event) {
    console.log(this.toggleuSD);
    if (!this.toggleuSD) {
        this.montoDolar = 0;
        this.cambio = 0;
        this.monto = this.montoAuxGlobal;
        this.montoDolarAbs = 0;
        this.faltaCompletar = 0;
        this.auxmontoEnvio = this.monto
        this.auxmontoDolarEnvio = this.montoDolarAbs

    } else {
        this.monto = 0
        this.montoDolar = (Number(this.montoAuxGlobal) / this.tc).toFixed(2);
        this.auxmontoEnvio = 0

        console.log(" this.montoDolar  ", this.montoDolar);
        this.montoDolar = Math.round(this.montoDolar);
        console.log(" this.montoDolar  ", this.montoDolar);
        this.auxmontoDolarEnvio = this.montoDolar
        this.montoDolarAbs = (Number(this.montoDolar) * this.tc).toFixed(2);
        console.log("this.montoDolarAbs  ", this.montoDolarAbs);
        this.cambio = 0;

        this.faltaCompletar = this.montoAuxGlobal - (Number(this.montoDolarAbs) + Number(this.monto));
        console.log(" this.faltaCompletar ", this.faltaCompletar);
        console.log(" this.montoAuxGlobal ", this.montoAuxGlobal);
        if (this.faltaCompletar < 0) {
            this.faltaCompletar = 0
        }
        if (Number(Number(this.monto) + Number(this.montoDolarAbs)) - Number(this.montoAuxGlobal) > 0) {
            this.cambio = Number(Number(this.monto) + Number(this.montoDolarAbs)) - Number(this.montoAuxGlobal);
            console.log("this.cambio ", this.cambio);
        } else {
            this.cambio = 0
        }


    }
  }

  selectTipoTarjeta = () => {
          if (this.CreditCardArray.length == 0) return this.toast.show(`Tipos de tarjeta no encontrado.`, '4000', 'top').subscribe(toast => {
          });

          this.selector.show({
              title: "CUAL TIPO DE PAGO.",
              items: [this.CreditCardArray],
              positiveButtonText: "SELECCIONAR",
              negativeButtonText: "CANCELAR",
              displayKey: 'CardName'
          }).then((result: any) => {
              let ux: any = this.CreditCardArray[result[0].index];
              console.log(ux);
              this.CreditCardCode = ux.CreditCard;
              this.CreditCardName = ux.CardName;
          }, (err: any) => {
              console.log("Ocurrio un error ")
          });
  }

}
