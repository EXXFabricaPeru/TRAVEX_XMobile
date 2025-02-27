import { Component, OnInit } from '@angular/core';
import { Productos } from "../../models/productos";
import { ModalController, NavParams, Platform } from "@ionic/angular";
import { DetalleventaPage } from "../detalleventa/detalleventa.page";
import { ConfigService } from "../../models/config.service";
import { Documentos } from "../../models/documentos";
import { Detalle } from "../../models/detalle";
import { Combos } from "../../models/combos";
import { BarcodeScanner } from '@ionic-native/barcode-scanner/ngx';
import { NativeService } from "../../services/native.service";
import { DataService } from "../../services/data.service";
import { Lotes } from "../../models/lotes";
import { Seriesproductos } from "../../models/seriesproductos";
import { bonificacion_regalos } from '../../models/bonificacion_regalos';
import { bonificacion_compras } from "../../models/bonificacion_compras";
import { Productosprecios } from "../../models/productosprecios";
import { SpinnerDialog } from "@ionic-native/spinner-dialog/ngx";
import { Bonificaciones as Bonificacion_ca } from '../../models/V2/bonificaciones';
import { relativeTimeThreshold } from 'moment';
import { Productosalmacenes } from '../../models/productosalmacenes';
import { of } from 'rxjs';
import { GlobalConstants } from "../../../global";
import { Geolocation } from '@ionic-native/geolocation/ngx';


@Component({
    selector: 'app-modalproducto',
    templateUrl: './modalproducto.page.html',
    styleUrls: ['./modalproducto.page.scss'],
})

export class ModalproductoPage implements OnInit {
    public bonificacion_regalos = new bonificacion_regalos();
    public bonificacion_compras = new bonificacion_compras();
    public Bonificacion_ca = new Bonificacion_ca();
    public items: any;
    public loadItem: boolean;
    public dataexport: any;
    public idUser: number;
    public idPedido: any;
    public latitude: number;
    public longitude: number;
    public loadingRegister: boolean;
    public dataUser: any;
    public combos: Combos;
    public documetosData: Documentos;
    public grupoBonificacion: any;
    public cantidadproductoslimit: any;
    public cantidadproductosusada: any;
    public nombreBoni: string;
    public unidBoni: string;
    public estadoSelect: boolean;
    opcionalBono: any = "";
    dataCachePedido: any = {};
    stockProductos: any = 0;
    validstockProductos: boolean = false;
    validListPricesBoni: boolean = false;


    constructor(public modalController: ModalController, public navParams: NavParams, private spinnerDialog: SpinnerDialog,
        private barcodeScanner: BarcodeScanner, private native: NativeService, private platform: Platform,
        private configService: ConfigService, private data: DataService,public geolocation: Geolocation) {
        this.dataexport = navParams.data;
        this.latitude = 0;
        this.longitude = 0;
        this.grupoBonificacion = 0;
        this.loadingRegister = false;
        this.combos = new Combos();
        this.documetosData = new Documentos();
        this.cantidadproductoslimit = 0;
        this.cantidadproductosusada = 0;
        this.nombreBoni = '';
        this.estadoSelect = false;
        console.log("modalProducto");
        
        /* this.platform.backButton.subscribe(() => {
             // do something here
             // alert("back ");
             this.cerrar();
             console.log("this.cerrar()");
 
         });
         */

        /* document.addEventListener('backbutton', function (event) {
             //event.preventDefault();
             //  event.stopPropagation();
             alert('hello');
         }, false);
 */


    }

    public async ngOnInit() {
        console.log("entro...!");

        this.idPedido = this.dataexport.idPedido;
        this.dataUser = await this.configService.getSession();
        this.idUser = this.dataUser.idUsuario;
        this.items = [];
        this.loadItem = false;
        if (this.dataexport.grupoBonificacion == 1) {

            //  console.log("*************** QUEMANDO BONIFICACION");

            console.log("this.dataexport con bonificaciones ", this.dataexport);
            console.log("this.dataexport con bonificaciones databoni ", this.dataexport.databoni);
            this.opcionalBono = this.dataexport.databoni.opcional;

            let dataItemsCodeBoni = [];
            var cadenaCodes = "";
            for (let i = 0; i < this.dataexport.databoni.length; i++) {
                dataItemsCodeBoni.push(this.dataexport.databoni[i].code_regalo);
                console.log("this.dataexport.databoni.code_regalo ", this.dataexport.databoni[i].code_regalo);
                cadenaCodes = cadenaCodes + "'" + this.dataexport.databoni[i].code_regalo + "',";
            }
            /**
             * BONOS DOCUMENT CASO 9 
             */
             //debugger;
            let reagoPorcentaje = 0;
            let masterBoniDocument: any = await this.Bonificacion_ca.getFindBonoDocument(this.dataexport.databoni[0].code_bonificacion_cabezera, this.dataexport.territorioCliente);
            console.log("masterBoniDocument ", masterBoniDocument);
            if (masterBoniDocument.length > 0) {
                if (masterBoniDocument[0].id_regla_bonificacion == "9") {// es bono 
                    console.log("BONIFICACION tipo 9 DOCUMENT CUMPLE ");
                    //this.cantidadproductoslimit = this.dataexport.databoni[0].cantidad_regalo * this.dataexport.databoni.cantidadConsumo;
                    console.log(this.dataexport.databoni[0]);
                    console.log(this.dataexport.databoni);
                    if(this.dataexport.databoni[0].maximo_regalo>0){
                        var aux_div=this.dataexport.databoni.cantidadConsumo/this.dataexport.databoni[0].maximo_regalo;
                        aux_div=Math.floor(aux_div);
                        this.cantidadproductoslimit = this.dataexport.databoni[0].cantidad_regalo*aux_div;
                    }else{
                        this.cantidadproductoslimit = this.dataexport.databoni[0].cantidad_regalo;
                    }
                    //var aux_div=this.dataexport.databoni.cantidadConsumo/
                    
                }
                if (masterBoniDocument[0].id_regla_bonificacion == "13") {// es bono 
                    console.log("BOPNIFICACION DOCUMENT CUMPLE ");
                    // this.cantidadproductoslimit = 1;
                    reagoPorcentaje = Number(masterBoniDocument[0].porcentaje) / 100;
                }
            }

            //  console.log("cadenaCodes ", cadenaCodes);
            cadenaCodes = cadenaCodes.slice(0, -1);
            // console.log("cadenaCodes ", cadenaCodes);

            this.nombreBoni = this.dataexport.databoni[0].producto_nombre_regalo;
            this.unidBoni = this.dataexport.databoni[0].unindad_regalo;
            let newRegaloPorcentual = 0;
            let newRegaloPorcentualLimit = 0;
            if (this.dataexport.databoni[0].cantidad_regalo > 0 && this.cantidadproductoslimit == 0) {
                /*  cantidad_compra: 12
                  cantidad_regalo: 1
                  maximo_regalo: 3*/
                this.spinnerDialog.show(null, null, true);

                let cantidadRegaloAux = this.dataexport.databoni[0].cantidad_regalo;
                let multiploRegaloAux = this.dataexport.databoni[0].cantidad_compra;
                this.dataexport.databoni[0].cantidad_regalo = 0;
                if (this.dataexport.databoni[0].maximo_regalo == 0) {
                    console.log("BONOS cantidad de regalo abierto");
                    let cantCompra = this.dataexport.databoni[0].cantidad_compra;
                    let cantCompraAux = 1;


                    for (let i = 1; i <= this.dataexport.databoni.cantidadConsumo; i++) {
                        //cantCompra=cantCompra+1;
                        console.log("cantCompraAux ", cantCompraAux, " == ", " cantCompra ", cantCompra)

                        if (Number(cantCompraAux) == Number(cantCompra)) {
                            //   console.log("cantidad de regalo abiertto adiciono 1 ");

                            if (masterBoniDocument.length > 0 && masterBoniDocument[0].id_regla_bonificacion == "11") {
                                await this.logicMauMinItem(masterBoniDocument, multiploRegaloAux, cantidadRegaloAux);
                                //this.dataexport.databoni[0].cantidad_regalo = cantidadRegaloAux * mincant;
                                cantCompraAux = 0;
                                break;
                            }
                            if (reagoPorcentaje > 0) {
                                // console.log("NUEVO   this.dataexport.databoni[0].maximo_regalo ", this.dataexport.databoni[0].maximo_regalo);
                                newRegaloPorcentual = Math.trunc(this.dataexport.databoni.cantidadConsumo / multiploRegaloAux);
                                console.log("newRegaloPorcentual BREACK ", newRegaloPorcentual)
                                break;
                            } else {
                                this.dataexport.databoni[0].cantidad_regalo = this.dataexport.databoni[0].cantidad_regalo + cantidadRegaloAux;
                                cantCompraAux = 0;
                            }
                        }
                        console.log("--- > each  this.dataexport.databoni[0].cantidad_regalo  ", i, cantidadRegaloAux);
                        cantCompraAux = cantCompraAux + 1;

                    }
                    //console.log("MAXIMO BONIFICACION 0");

                } else {

                    if (masterBoniDocument.length > 0 && masterBoniDocument[0].id_regla_bonificacion == "11") {
                        console.log("CASO 11 ", this.dataexport.databoni[0]);
                        console.log("MAXIMO BONIFICACION ", this.dataexport.databoni[0].maximo_regalo);
                        console.log("MAXIMO multiploRegaloAux ", multiploRegaloAux);
                        console.log("MAXIMO this.dataexport.databoni.cantidadConsumo ", this.dataexport.databoni.cantidadConsumo);
                        console.log("MAXIMO cantidadRegaloAux ", cantidadRegaloAux);
                        await this.logicMauMinItem(masterBoniDocument, multiploRegaloAux, cantidadRegaloAux);
                        if (this.dataexport.databoni[0].cantidad_regalo > (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux)) {//ULTIMO CAMBIO AL LIMITE
                            console.log("MAXIMO supera el maximo ");
                            this.dataexport.databoni[0].cantidad_regalo = (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux);//ULTIMO CAMBIO AL LIMITE
                        } else {
                            console.log("MAXIMO no supera el maximo ");
                        }
                        // let maxbon = Math.trunc(this.dataexport.databoni.cantidadConsumo / multiploRegaloAux);


                    } else {

                        // console.log("cantidad de regalo cerrado");
                        console.log("MAXIMO BONIFICACION ", this.dataexport.databoni[0].maximo_regalo);
                        console.log("MAXIMO cantidadRegaloAux ", cantidadRegaloAux);
                        if (reagoPorcentaje > 0) {
                            //this.dataexport.databoni[0].maximo_regalo = this.dataexport.databoni.cantidadConsumo;
                            console.log("NUEVO   this.dataexport.databoni[0].maximo_regalo ", this.dataexport.databoni[0].maximo_regalo);
                            newRegaloPorcentual = Math.trunc(this.dataexport.databoni.cantidadConsumo / multiploRegaloAux);

                            newRegaloPorcentualLimit = 1;
                        } else {
                            this.dataexport.databoni[0].cantidad_regalo = (Math.trunc(this.dataexport.databoni.cantidadConsumo / multiploRegaloAux) * cantidadRegaloAux);
                            if (this.dataexport.databoni[0].cantidad_regalo >= (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux)) { //ULTIMO CAMBIO AL LIMITE

                                this.dataexport.databoni[0].cantidad_regalo = (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux)
                            }
                            // for (let i = 0; i < this.dataexport.databoni[0].maximo_regalo; i++) {
                            //     console.log("--- > each  this.dataexport.databoni[0].cantidad_regalo i ", this.dataexport.databoni[0].cantidad_regalo);
                            //     console.log("--- > each  multiploRegaloAux ", multiploRegaloAux);
                            //     //cantidadRegaloAux=cantidadRegaloAux
                            //     //cantidadRegaloAux=cantidadRegaloAux
                            //     console.log("this.dataexport.databoni.cantidadConsumo ", this.dataexport.databoni.cantidadConsumo);
                            //     console.log("(this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux) ", (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux));
                            //     console.log("--- > each  cantidadRegaloAux ", cantidadRegaloAux);
                            //     // if (this.dataexport.databoni.cantidadConsumo >= (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux)) { //ULTIMO CAMBIO AL LIMITE
                            //         this.dataexport.databoni[0].cantidad_regalo = this.dataexport.databoni[0].cantidad_regalo + cantidadRegaloAux;
                            //         multiploRegaloAux = multiploRegaloAux + this.dataexport.databoni[0].cantidad_compra;


                            //     // } else {
                            //     //     break;
                            //     // }

                            //     //  console.log("new cant regalo ", this.dataexport.databoni[0].cantidad_regalo);
                            // }
                        }
                    }
                }
                this.cantidadproductoslimit = this.dataexport.databoni[0].cantidad_regalo;
                console.log("this.cantidadproductoslimit  ", this.cantidadproductoslimit);
                if (reagoPorcentaje > 0) {
                    console.log("newRegaloPorcentual  ", newRegaloPorcentual);
                    console.log("reagoPorcentaje  ", reagoPorcentaje);
                    console.log("cantidadRegaloAux  ", cantidadRegaloAux);
                    newRegaloPorcentual = (newRegaloPorcentual * cantidadRegaloAux);
                    //newRegaloPorcentual = this.cantidadproductoslimit;
                    console.log("newRegaloPorcentual  ", newRegaloPorcentual);//11.2
                    console.log("DESCONTAR AL REGALO % ", reagoPorcentaje);//0.63
                    this.cantidadproductoslimit = Math.trunc(newRegaloPorcentual * reagoPorcentaje);//7

                    console.log("this.cantidadproductoslimit before   ", this.cantidadproductoslimit)
                    console.log("reagoPorcentaje ", reagoPorcentaje)
                    if (newRegaloPorcentualLimit > 0) {

                        console.log("SUPERA EL MAXIMO 1");
                        if (this.cantidadproductoslimit >= (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux)) {
                            console.log("SUPERA EL MAXIMO ");
                            this.cantidadproductoslimit = (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux);
                        }
                        console.log("this.cantidadproductoslimit before   ", this.cantidadproductoslimit)
                        console.log("this.dataexport.databoni[0].maximo_regalo  ", this.dataexport.databoni[0].maximo_regalo)
                        // if (this.cantidadproductoslimit > (this.dataexport.databoni[0].maximo_regalo * cantidadRegaloAux)) {
                        //     console.log("SUPERA EL MAXIMO 2");
                        //     this.cantidadproductoslimit = this.dataexport.databoni[0].maximo_regalo;
                        // }
                    }
                    if (this.cantidadproductoslimit == 0) {
                        this.native.mensaje(`Bonificación al ${masterBoniDocument[0].porcentaje} % no cumple el mínimo en cantidad, por favor omite la acción.`);
                        this.opcionalBono = 'OPCIONAL';
                        this.dataexport.databoni.opcional = 'OPCIONAL';
                        localStorage.setItem("omitir", "SI");
                    }
                    this.dataexport.databoni[0].cantidad_regalo = this.cantidadproductoslimit;
                }
                this.spinnerDialog.hide();
            } else {
                //alert("close ");
                if (this.cantidadproductoslimit == 0) {
                    this.native.mensaje(`Regalo no encontrado.`, '3000', 'top');

                    this.modalController.dismiss(this.idPedido);
                }

            }
            localStorage.setItem('unindad_regaloBONI', this.unidBoni);
            localStorage.setItem('cantidadproductoslimitBONI', this.cantidadproductoslimit);
            localStorage.setItem('stockBoni', "0");
            /*
            this.grupoBonificacion = this.dataexport.grupoBonificacion;
            let maxcant = 0;
            (this.dataexport.databoni.cantidadproductos > this.dataexport.databoni.bonificacionCantidadMaximo) ? maxcant = this.dataexport.databoni.bonificacionCantidadMaximo : maxcant = this.dataexport.databoni.cantidadproductos;
            this.cantidadproductoslimit = maxcant;
            this.nombreBoni = this.dataexport.databoni.nombre;
            this.unidBoni = this.dataexport.databoni.U_bonificacionunidad;
            let bonificaciond2: any = new bonificacion_compras();
            let respx: any = await bonificaciond2.findOne(this.grupoBonificacion);
            let rx: any = respx.map(rep => `"${rep.U_bonificacion}"`);
            */
            this.buscarbonificaciones(cadenaCodes);
        } else {
            console.log("entro al modal....!!!")
            localStorage.setItem('unindad_regaloBONI', '0');
            localStorage.setItem('cantidadproductoslimitBONI', '0');
        }

    }

    logicMauMinItem = async (masterBoniDocument, multiploRegaloAux, cantidadRegaloAux) => {
        let auxValid: any = await this.bonificacion_compras.findForCabezera(masterBoniDocument[0].id_bonificacion_cabezera);
        //let detalle = new Detalle();

        //let auxItems: any = await detalle.findAll(this.idPedido);
        let auxItems: any = GlobalConstants.DetalleDoc;
       
        //console.log("items pedido MMMM",auxItems);
        let mincant = 0;
        let maxbon = 0;
        maxbon = Math.trunc(this.dataexport.databoni.cantidadConsumo / multiploRegaloAux);
        for (let value of auxValid) {
            let filterItemValid = auxItems.filter(x => value.code_compra == x.ItemCode);
            if (filterItemValid.length > 0) {
                let xcantidad = 0;
                let desc_asig = 0;
                for (let item of filterItemValid) {
                    xcantidad = xcantidad + (item.Quantity * item.BaseQty);
                }
                if (value.producto_cantidad > 0) {
                    desc_asig = Math.trunc(xcantidad / value.producto_cantidad);
                } else {
                    desc_asig = xcantidad;
                }

                if (mincant === 0) {
                    mincant = desc_asig;
                }
                if (desc_asig < mincant) {
                    mincant = desc_asig;
                }

            }
        }
        console.log("Cantidad posible bonificar", mincant);
        console.log("Cantidad posible bonificar segun cantidad", maxbon);
        if (mincant > maxbon) {
            this.dataexport.databoni[0].cantidad_regalo = cantidadRegaloAux * maxbon;
        } else {
            this.dataexport.databoni[0].cantidad_regalo = cantidadRegaloAux * mincant;
        }
        console.log("RESULT 11 this.dataexport.databoni[0].cantidad_regalo  ", this.dataexport.databoni[0].cantidad_regalo)
    }
    ionViewDidEnter() {
        console.log("********************************* DEVD ionViewDidEnter");
        console.log("DEVD this.cantidadproductoslimit  ", this.cantidadproductoslimit);
        console.log("DEVD this.stockProductos ", this.stockProductos);
        if (this.cantidadproductoslimit > this.stockProductos && (this.dataexport.tipoDoc == 'DFA' || this.dataexport.tipoDoc == 'DOE')) {
            this.validstockProductos = true;

        }
    }

    validStockExtra() {
        console.log("********************************* DEVD validStockExtra");
        console.log("DEVD this.cantidadproductoslimit  ", this.cantidadproductoslimit);
        console.log("DEVD this.stockProductos ", this.stockProductos);
        console.log("this.dataexport.tipoDoc ", this.dataexport.tipoDoc);

        if (this.cantidadproductoslimit > this.stockProductos && (this.dataexport.tipoDoc == 'DFA' || this.dataexport.tipoDoc == 'DOE')) {
            this.validstockProductos = true;
            console.log(true);
        } else {
            this.validstockProductos = false;
            console.log(false);
        }
        // } else {
        //     this.validstockProductos = true;
        //     console.log(true);
        // }

    }


    public async buscarbonificaciones(cadenaCodes) {
        console.log("buscarbonificaciones()", cadenaCodes); //code_bonificacion_cabezera
        this.items = [];
        this.loadItem = true;
        let model = new Productos();

        this.items = await model.getbonificaciones(cadenaCodes, this.dataexport.databoni);

        console.log("DEVD items enciontrados boni ", this.items);
        console.log("DEVD this.dataexport[0].almacen ", this.dataexport.almacen);

        let productosalmacenes = new Productosalmacenes();

        this.items.forEach(async element => {
            let dataStock = await productosalmacenes.find(this.dataexport.almacen.WarehouseCode, element.ItemCode);
            console.log("DEVD dataStock ", dataStock);
            if (dataStock) {

                this.stockProductos = this.stockProductos + Number(dataStock.InStock);
                console.log("dataStock final  ", dataStock);
                console.log("call function valid ")
                this.validStockExtra();
                console.log("this.stockProductos ", this.stockProductos);

            } else {
                console.log(" no cumpple Stock ")
            }

        });

        this.loadItem = false;
    }



    public async actionCodeBarras() {
        try {
            let resp: any = await this.barcodeScanner.scan();
            if (resp.cancelled != true) {
                let model = new Productos();
                let item: any = await model.findSearch('', '', resp.text);
                this.addProducto(item[0]);
            } else {
                this.native.mensaje(`No se encontraron resultados.`, '3000', 'top');
            }
        } catch (e) {
            this.native.mensaje(`Ocurrió un error  el lector de código de barras o no se encuentra para el soporte.`);
        }
    }

    public async buscar(event: any) {
        console.log("buscar()");
        this.items = [];
        this.loadItem = true;
        let search = event.detail.value;
        let model = new Productos();
        console.log(this.dataexport);

        this.items = await model.findSearch(search, this.dataexport.listaPrecio.PriceListNum, '', this.dataexport.grupoproductoscode);

        this.loadItem = false;
    }

    public async cerrar() {
        console.log("CONSOLA: LLAMA FUNCION cerrar 460");
        if (this.cantidadproductoslimit > this.stockProductos && (this.dataexport.tipoDoc == 'DFA' || this.dataexport.tipoDoc == 'DOE')) {
            this.validstockProductos = true;

        }

        let productosalmacenes = new Productosalmacenes();
        let prodAlma = await productosalmacenes.find(this.dataCachePedido.WhsCode, this.dataCachePedido.ItemCode);


        console.log("CONSOLA: VALIDA SI EL PRODUCTO AGREGADO FUE BONIFICACION 470");
        if (this.grupoBonificacion > 0) {
            
            if (this.opcionalBono == 'OBLIGATORIO' && Number(localStorage.getItem("cantidadproductoslimitBONI")) > 0) {
                if (this.items.length > 0) {
                    if (this.validListPricesBoni) {
                        localStorage.setItem("stockBoni", "3");
                    } else
                        if (this.validstockProductos) {
                            this.native.mensaje(`Stock insuficiente.`, '3000');
                            localStorage.setItem("stockBoni", "2");

                            localStorage.setItem("boniDelete", this.dataexport.databoni[0].nombre)
                        } else
                            if (localStorage.getItem("stockBoni") == "0") {
                                this.native.mensaje(`la bonificación es oblicatoria, cantidad a completar ${localStorage.getItem("cantidadproductoslimitBONI")} `);
                                return false;
                            }

                } else {
                    localStorage.setItem("stockBoni", "1");
                }

            }
        }

        if (this.grupoBonificacion > 0 && this.items.length == 0) {
            localStorage.setItem("omitir", "SI");
        }
        console.log("CONSOLA: CIERRA MODAL 499");
        this.modalController.dismiss(this.idPedido);
    }


    cerrarOmitir() {
        localStorage.setItem("omitir", "SI");
        //  localStorage.setItem("cancelado", "SI");

        this.modalController.dismiss(this.idPedido);
    }

    public async addDetalle(pedido: any) {

        console.log("CONSOLA: INICIA addDetalle 715");

        console.log("CONSOLA: LLAMA FUNCION dataDetalle 527");
        let datosDetalle: any = await this.data.dataDetalle(pedido, this.dataexport);

        if (this.idPedido == '0') {
            console.log("CONSOLA: LLAMA FUNCION saveDocument 531");
            await this.saveDocument(pedido);
            console.log("CONSOLA: LLAMA FUNCION addDetalle 533");
            this.addDetalle(pedido);
        } else {
            console.log("CONSOLA: LLAMA FUNCION dataDetalle 536");
            let datosDetalle: any = await this.data.dataDetalle(pedido, this.dataexport);
            console.log("CONSOLA: LLAMA FUNCION saveDetalle 538");
            this.saveDetalle(datosDetalle, pedido);
        }


    }

    public async saveDocument(pedido: any) {
        console.log("CONSOLA: INICIA saveDocument 546");

        console.log("CONSOLA: CONSULTA COORDENADAS 548");
        let posiciones: any = await this.native.coordenadas();

        if (posiciones != false) {
            this.latitude = posiciones.lat;
            this.longitude = posiciones.lng;
        } else {
            this.latitude = Number(localStorage.getItem('lat'));
            this.longitude = Number(localStorage.getItem('lng'));
        }

        console.log("CONSOLA: LLAMA FUNCION dataDocumet 559");
        let documetosData: any = await this.data.dataDocumet(pedido, this.dataexport, this.latitude, this.longitude);

        console.log("CONSOLA: CONSULTA DATOS DE SESSION 562");
        let codigox: any = await this.configService.getCodigo();

        console.log("CONSOLA: CONSULTA DATOS DE GEOLOCALIZACION 565");
        this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
            if (resp) {
                let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                localStorage.setItem("lat", obj.lat);
                localStorage.setItem("lng", obj.lng);
                GlobalConstants.Longitud = obj.lng; 
                GlobalConstants.latitud = obj.lat;
            }
        }).catch(error => {
            console.log("error 1 ", error);
        });

        console.log("CONSOLA: LLAMA FUNCION insertLocal 578");
        this.idPedido = await this.documetosData.insertLocal(documetosData, this.idUser, codigox);
    }

    public async saveDetalle(dataPedido: any, pedido: any) {

        console.log("CONSOLA: INICIA dataPedido 584");

        let detalle = new Detalle();
        console.log("CONSOLA: CONSULTA DATOS DE GEOLOCALIZACION 587");
        this.geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 }).then((resp) => {
            if (resp) {
                let obj: any = { lat: resp.coords.latitude, lng: resp.coords.longitude };
                localStorage.setItem("lat", obj.lat);
                localStorage.setItem("lng", obj.lng);
                GlobalConstants.Longitud = obj.lng;
                GlobalConstants.latitud = obj.lat;
            }
        }).catch(error => {
            console.log("error 1 ", error);
        });

        console.log("CONSOLA: LLAMA FUNCION insertLocal 600");
        let resp: any = await detalle.insertLocal(dataPedido, this.idPedido, 0, this.dataexport.tipoDoc, this.dataexport.tipoDocx);

        this.cantidadproductosusada = this.cantidadproductosusada+dataPedido.cantidad;

    }

    private countDetail(idPedido) {
        return new Promise(async (resolve, reject) => {
            let detalle = new Detalle();
            let detalleActual: any = await detalle.showTable(idPedido);
            let productosParaMarcar = [];
            let detalleCopia = detalleActual;
            let insertados: any = [];
            detalleActual.forEach(producto => {
                let cantidadProducto = 0;
                detalleCopia.forEach(productoCopia => {
                    if (productoCopia.ItemCode === producto.ItemCode && producto.bonificacion === 0)
                        cantidadProducto += productoCopia.Quantity * productoCopia.BaseId;
                });
                if (!(insertados.includes(producto.ItemCode))) {
                    productosParaMarcar.push({
                        ItemCode: producto.ItemCode,
                        Cantidad: cantidadProducto
                    });
                    insertados.push(producto.ItemCode);
                }
            });
            resolve(productosParaMarcar);
        })
    }

    public async addProducto(item: any) {
        /*if (this.grupoBonificacion != 0) {
            let detalles = new Detalle();
            let respboni: any = await detalles.bonificacionAuth(this.idPedido, this.grupoBonificacion);
            if (respboni[0].total >= this.cantidadproductoslimit) {
                this.native.mensaje('Superaste el limite de bonificaciones.', '3500', 'top');
                return false;
            }
        }
        */
        item.dataexport = this.dataexport;
        item.edit = false;
        item.BaseId = 1;
        this.estadoSelect = false;
        let mcproducto: any = { component: DetalleventaPage, cssClass: 'transparente', componentProps: item };
        let modalventa: any = await this.modalController.create(mcproducto);
        modalventa.onDidDismiss().then(async (data: any) => {

            console.log("CONSOLA: DATOS QUE LLEGAN DEL MODAL",data );

            if (this.grupoBonificacion == 1) {
                switch (data.data) {
                    case (1):
                        this.validListPricesBoni = true;
                        break;
                    case (2):
                        this.validListPricesBoni = true;
                        break;
                    case (3):
                        this.validListPricesBoni = true;
                        break;

                    default:
                        break;
                }
            }
            console.log("CONSOLA: VALIDA grupoBonificacion Y data.data 663");
            if (this.grupoBonificacion != 1 && (data.data == 1 || data.data == 2 || data.data == 3 || typeof data.data == "undefined")) {
                console.log("Ingresa a validar switch");
                switch (data.data) {
                    case (1):
                        this.native.mensaje(`${this.dataUser[0].nombrePersona} debes seleccionar un producto para continuar.`, '3000', 'center');
                        break;
                    case (2):
                        this.native.mensaje('No existe precio asociado al producto verifique la lista de precios....', '3500', 'center');
                        break;
                    case (3):

                        this.native.mensaje('El almacén seleccionado no contiene este producto seleccione otro.', '3500', 'top');
                        break;
                }
            } else {
                let c = data.data;
                item.bonificacion = c.bonificacion;
                console.log("CONSOLA: CONSULTA COMBOS 681");
                let combosx: any = await this.combos.findAll(item.ItemCode);

                console.log("CONSOLA: CARGA LOS DATOS RETORNADOS 684");
                let pedisox: any = {
                    dataproducto: item,
                    WhsCode: c.almacen,
                    unidadID: c.unidad,
                    cantidad: c.cantidad,
                    descuento: c.descuento,
                    descuentoporsentaje: c.descuentoporsentaje,
                    descuentototal: c.descuentototal,
                    presio: c.unidad.Price,
                    icete: c.icete,
                    icetp: c.icetp,
                    icett: c.presio,
                    ICEt: c.ICEt,
                    ICEp: c.ICEp,
                    ICEe: c.ICEe,
                    bonificacion: c.bonificacion,
                    combos: combosx[0].total,
                    lotesarr: c.lotesarr,
                    seriesarr: c.seriesarr,
                    BaseId: c.unidad.BaseQty,
                    bonificacionesUsadas: c.bonificacionesUsadas,
                    IdBonfAut: this.grupoBonificacion,//ojo
                    GroupName: c.GroupName

                };

                if (this.grupoBonificacion == 1) {
                    this.dataexport.databoni.opcional = '';
                }

                console.log("CONSOLA: LLAMA A LA FUNCION addDetalle 715");
                this.addDetalle(pedisox);
            }
        });
        return await modalventa.present();
    }

    public async findItem(item: any) {

        console.log("SELECCIONADO",item);
        console.log("this.dataUser",this.dataUser);
        console.log("this.dataUser",this.dataUser[0].ctrl_lineasduplicadas);
        if(this.dataUser[0].ctrl_lineasduplicadas == '1'){
            console.log("items selecionados",GlobalConstants.DetalleDoc);
            let aux = 0;
            for await (let data of GlobalConstants.DetalleDoc) {
                if(item.ItemCode == data.ItemCode){
                    aux ++;
                }
            }
            if(aux > 0){
                this.native.mensaje('El producto seleccionado ya se encuentra agregado.', '3500', 'center');
            }else{
                this.estadoSelect = true;
                this.addProducto(item);
            }
        }else{
            this.estadoSelect = true;
            this.addProducto(item);
        }
    }

}
