import { Injectable } from '@angular/core';
import { HTTP } from '@ionic-native/http/ngx';
import { ConfigService } from "../models/config.service";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { Documentos } from "../models/documentos";
import { Detalle } from "../models/detalle";
import { Clientes } from "../models/clientes";
import { Contactos } from "../models/contactos";
import { Pagos } from "../models/pagos";
import { Visitas } from "../models/visitas";
import * as moment from 'moment';
import { FacturasPagos } from '../models/facturasPagos';
import { environment } from '../../environments/environment';
import { DataService } from './data.service';
import { Bonificaciones } from "../models/V2/bonificaciones";
import { Bonificaciones as Bonificacion_ca } from '../models/V2/bonificaciones';
import { bonificacion_regalos } from '../models/bonificacion_regalos';
import { bonificacion_compras } from '../models/bonificacion_compras';
import { GlobalConstants, } from "../../global";
import { Bolivia } from "../utilsx/bolivia";
import { Companex } from "../utilsx/companex";
import { Chile } from "../utilsx/chile";
import { Paraguay } from "../utilsx/paraguay";


@Injectable({
    providedIn: 'root'
})
export class BonificacionesService {
    public Bonificacion_ca = new Bonificacion_ca();
    public path: any;
    public arraux: any;
    items: any;
    territorioCliente = ''
    public localizacion_calculo: any;
    public bonificacion_regalos = new bonificacion_regalos();
    public bonificacion_compras = new bonificacion_compras();
    constructor(private http: HTTP, private configService: ConfigService, private dataService: DataService, private spinnerDialog: SpinnerDialog) {
    }

    validCartBonificaciones = async (itemProduct, clientCart) => {
        console.log("itemProduct ", itemProduct);
        console.log("clientCart ", clientCart);
        await this.validProductInBonificaion(clientCart.rutaterritorisap, clientCart.codeCanal, clientCart.GroupCode, clientCart.cliente_std1, itemProduct.ItemCode, itemProduct.GroupName)

    }

    validProductInBonificaion = async (TerritoryID, codigo_canal, grupo_cliente, grupoDosificacion, itemCode, itemGroup) => {
        console.log("validProductInBonificaion ");
        let finderModel = 0
        let modelSql = new Bonificaciones()
        const responseQuery = await modelSql.getBonificacionesDisponibles(TerritoryID, codigo_canal, grupo_cliente, grupoDosificacion, itemCode, itemGroup)
        console.log("responseQuery ", responseQuery);
        const bonificacion = new BonificacionContext(new Model1())
        // bonificacion.setStrategy(new Model1())
        console.log("RESPONSE STRATEGY", bonificacion.addDiscount([], "A"))
        return finderModel;
    }

    /**
      * BONIFICACIONEES
      */
    public async validBonificacion(productos: any, cliente: any) {
        // alert("Crear bonos ");
        //debugger;
        console.log("validBonificacion() cliente ", cliente);

        console.log("validBonificacion() productos ", productos);
        this.items = productos;
        this.Bonificacion_ca.DeleteBoniUsadas();
        console.info("DEVD showAll() ", await this.Bonificacion_ca.getCompraUsados());
        let bonificacionesUsadas: any = [];
        this.territorioCliente = cliente[0].rutaterritorisap;
        for (let j = 0; j < productos.length; j++) {

            let grupoProducto = productos[j].GroupName;
            let grupoCliente = cliente[0].GroupName;
            console.log("------- > grupoCliente ", grupoCliente);
            console.log("------- > grupoProducto ", grupoProducto);
            console.log("------- > DEVD producto a evaluar () ", productos[j]);
            let existeEnCompras: any = await this.bonificacion_compras.getIdCabezera(productos[j].ItemCode);


            let existeEnComprasGrupo: any = [];


            if (existeEnCompras.length == 0) {
                existeEnComprasGrupo = await this.bonificacion_compras.getGruposIdCabezera(grupoProducto);

                if (existeEnComprasGrupo.length > 0) {
                    existeEnCompras = existeEnComprasGrupo;
                }
            }

            let auxCodesUses: any = await this.Bonificacion_ca.getCompraUsados();
            let ItemsExist = [];

            for (let auxItem of this.items) {
                let existIn = auxCodesUses.filter(value => value.code_compra == auxItem.ItemCode);
                if (existIn.length > 0) {
                    ItemsExist.push(auxItem);
                }
            }
            for (let i = 0; i < existeEnCompras.length; i++) {
                let bonificacionVigenteCabezera: any = await this.Bonificacion_ca.getBonificacionExist(existeEnCompras[i].code_bonificacion_cabezera);

                
                if (bonificacionVigenteCabezera.length > 0) {
                    
                    if (bonificacionVigenteCabezera[0].codigo_canal != "0") {
                        
                        bonificacionVigenteCabezera = bonificacionVigenteCabezera.filter(x => cliente[0].codeCanal == x.codigo_canal);
                        console.log("BOPNIFICACION DOCUMENT dado de baja por code canal  ");
                    }

                    bonificacionVigenteCabezera = bonificacionVigenteCabezera.filter(x => cliente[0].rutaterritorisap == x.TerritoryID);
                    bonificacionVigenteCabezera = bonificacionVigenteCabezera.filter(x => cliente[0].cliente_std1 == x.id_cliente_dosificacion || x.id_cliente_dosificacion == 0);
                   
                }

                
                if (bonificacionVigenteCabezera.length > 0) {
                    
                    let masterBoniDocument: any = await this.Bonificacion_ca.getFindBonoDocument(bonificacionVigenteCabezera[0].code, this.territorioCliente);
                    if (masterBoniDocument.length > 0) {
                        
                        if (masterBoniDocument[0].id_regla_bonificacion == "9") {
                            
                            console.log("aplica bonificacion 9")
                            let cantidad = 0;
                            let code = masterBoniDocument[0].id_bonificacion_cabezera;
                            let auxValid: any = await this.bonificacion_compras.findForCabezera(code);

                            let xcantidad = 0;
                            for (let value of auxValid) {
                                let filterItemValid = productos.filter(x => value.code_compra == x.ItemCode);
                                if (filterItemValid.length > 0) {
                                    
                                    xcantidad = 0;
                                    for (let item of filterItemValid) {

                                        xcantidad = xcantidad + (item.Quantity * item.BaseQty);

                                    }
                                    cantidad = cantidad + xcantidad;

                                }


                            }


                            if (!await this.validExistBonoDocument(bonificacionVigenteCabezera, cantidad, ItemsExist, cliente)) {
                                
                                console.log("DADO DE BAJA POR NO CUMPLIR REGLA ");
                                bonificacionVigenteCabezera = [];
                            }

                        }
                        if (masterBoniDocument[0].id_regla_bonificacion == "10") {
                            
                            let cantidad = 0;
                            let code = masterBoniDocument[0].id_bonificacion_cabezera;
                            let auxValid: any = await this.bonificacion_compras.findForCabezera(code);
                            let xcantidad = 0;
                            for (let value of auxValid) {
                                let filterItemValid = productos.filter(x => value.code_compra == x.ItemCode);
                                if (filterItemValid.length > 0) {
                                    
                                    xcantidad = 0;
                                    for (let item of filterItemValid) {
                                        xcantidad = xcantidad + (item.Quantity * item.BaseQty);

                                    }
                                    cantidad = cantidad + xcantidad;
                                }
                            }
                            if (!await this.validExistBonoDocument(bonificacionVigenteCabezera, cantidad, ItemsExist, cliente)) {
                                
                                console.log("DADO DE BAJA POR NO CUMPLIR REGLA ");
                                bonificacionVigenteCabezera = [];
                            }

                        }
                        if (masterBoniDocument[0].id_regla_bonificacion == "11") {
                            
                            console.log("Productos");
                            console.log(ItemsExist);
                            if (!await this.validExistBonoDocument(bonificacionVigenteCabezera, productos[j].Quantity * productos[j].BaseQty, productos, cliente)) {
                                console.log("DADO DE BAJA POR NO CUMPLIR REGLA ");
                                bonificacionVigenteCabezera = [];
                            }

                        }
                        if (masterBoniDocument[0].id_regla_bonificacion == "12") {
                            
                            if (!await this.validExistBonoDocument(bonificacionVigenteCabezera, productos[j].Quantity * productos[j].BaseQty, productos, cliente)) {
                                console.log("DADO DE BAJA POR NO CUMPLIR REGLA ");
                                bonificacionVigenteCabezera = [];
                            }

                        }
                        if (masterBoniDocument[0].id_regla_bonificacion == "13") {
                            
                            let cantidad = 0;
                            let xca_aux = 0;
                            let xcantidad = 0;
                            let code = masterBoniDocument[0].id_bonificacion_cabezera;
                            let auxValid: any = await this.bonificacion_compras.findForCabezera(code);
                            console.log("13 DEBES CUMPLIR auxValid ", auxValid);
                            for (let value of auxValid) {

                                let filterItemValid = productos.filter(x => value.code_compra == x.ItemCode);

                                if (filterItemValid.length > 0) {
                                    xcantidad = 0;
                                    for (let item of filterItemValid) {
                                        console.log("MAM bonificacionVigenteCabezera: productos  ", item.ItemCode);
                                        console.log("MAM bonificacionVigenteCabezera: productos  ", item.Quantity);
                                        console.log("MAM bonificacionVigenteCabezera: productos  ", item.BaseQty);
                                        xcantidad = xcantidad + (item.Quantity * item.BaseQty);

                                    }
                                    cantidad = cantidad + xcantidad;
                                }
                            }
                            console.log("DEBUG 13 xcantidad", xcantidad)
                            console.log("DEBUG 13 cantidad", cantidad)
                            if (!await this.validExistBonoDocument(bonificacionVigenteCabezera, cantidad, productos, cliente)) {
                                console.log("DADO DE BAJA POR NO CUMPLIR REGLA ");
                                bonificacionVigenteCabezera = [];
                            }

                        }

                    }

                }
                
                if (bonificacionVigenteCabezera.length > 0) {
                    
                    if (bonificacionVigenteCabezera[0].grupo_cliente != "0") {
                       
                        if (bonificacionVigenteCabezera[0].grupo_cliente != grupoCliente) {
                            
                            bonificacionVigenteCabezera = [];
                        } else {
                            
                            console.log("not");
                        }
                    }
                }
                
                if (bonificacionVigenteCabezera.length > 0) {
                   
                    let productosBonificacionCompra: any = await this.bonificacion_compras.findForCabezera(bonificacionVigenteCabezera[0].code);

                    if (productosBonificacionCompra.length > 0 && bonificacionVigenteCabezera.length > 0) {
                        
                        let productoIsBonificable: any;
                        if (existeEnComprasGrupo.length > 0) {
                            
                            productoIsBonificable = await this.bonificacion_compras.validProductoInComprasGrupo(grupoProducto, bonificacionVigenteCabezera[0].code);
                        } else {
                           
                            productoIsBonificable = await this.bonificacion_compras.validProductoInCompras(productos[j].ItemCode, bonificacionVigenteCabezera[0].code);

                        }
                        console.log("DEVD logica bonificacion dispobles");

                        if (productoIsBonificable.length) {
                            
                            console.log("DEVD esta en bopnificaciones compras productoIsBonificable ", productoIsBonificable);
                            // VER SI CUMPLE REQUISITOS PARA SER PARTE DE UNA BONIFICACION
                            let validProductoUnindadGrupo: any = await this.bonificacion_compras.validProductoUnindadGrupo(bonificacionVigenteCabezera[0].unindad_compra, bonificacionVigenteCabezera[0].code);
                            if (validProductoUnindadGrupo.length > 0) {
                                
                                if (productos[j].BaseQty == "undefined") {
                                   
                                    productos[j].BaseQty = 1;
                                }

                                bonificacionesUsadas.push({
                                    code_bonificacion_cabezera: bonificacionVigenteCabezera[0].code,
                                    code_compra: productos[j].ItemCode,
                                    //cantidad: this.cantidadUI,
                                    cantidad: productos[j].Quantity * productos[j].BaseQty,
                                    unidad: bonificacionVigenteCabezera[0].unindad_compra, // this.nombreunidad,
                                    cardCode: cliente[0].CardCode,
                                    estado: "PENDIENTE",
                                    id_vendedor: 1,
                                    idDocumento: 0,
                                    idDocumentoDetalle: 0,
                                    total: productos[j].LineTotalPay
                                });
                                //this.bonificacion_compras.markeUseCardCodeCompra(this.data.ItemCode);
                                console.log("DEVD bonificacionesUsadas ", bonificacionesUsadas);

                            }
                        }
                    }
                } else {
                    console.log("DEVD Bonifdicacion no vigentge ");

                }
            }
        }

        for (let value of bonificacionesUsadas) {
            console.log("each bonificacionesUsadas insert ", value);
            await this.Bonificacion_ca.insertCompraUsados(value.code_bonificacion_cabezera, value.code_compra, value.cantidad, "nombre unidad", value.cardCode, "PENDIENTE", 1, 0, 0, value.total);
        }
    }

    /**
            * NUEVA LOGICA DE BONOS V2
            *  */

    validExistBonoDocument = async (bonoCompletoMaster, Quantity, productosCarrito = [], dataCliente) => {

        bonoCompletoMaster = await this.Bonificacion_ca.getFindBonoDocument(bonoCompletoMaster[0].code, this.territorioCliente);
        let returnSW = false;
        let code = bonoCompletoMaster[0].id_bonificacion_cabezera;
        if (bonoCompletoMaster.length > 0) {
            if (bonoCompletoMaster[0].codigo_canal != "0") {
                bonoCompletoMaster = bonoCompletoMaster.filter(x => dataCliente[0].codeCanal == x.codigo_canal);
                console.log("BOPNIFICACION DOCUMENT dado de baja por code canal  ");
            }
        }

        console.log("bonoCompletoMaster[0] ", bonoCompletoMaster[0]);
        if (bonoCompletoMaster.length > 0) {
            if ((bonoCompletoMaster[0].id_regla_bonificacion == "9" || bonoCompletoMaster[0].id_regla_bonificacion == "10") && Quantity >= Number(bonoCompletoMaster[0].cantidad_compra) && Quantity <= Number(bonoCompletoMaster[0].cantidad_maxima_compra)) {// es bono 
                for (let value of productosCarrito) {
                    let auxValid: any = await this.bonificacion_compras.findForCabezera(code);
                    if (auxValid.length == productosCarrito.length) {
                        //VALIDAR CANTIDAD MATCH 
                        //for (let item of auxValid) {
                        let filterAuxValid = auxValid.filter(x => value.ItemCode == x.code_compra);
                        if (filterAuxValid.length > 0) {
                            if (filterAuxValid[0].code_compra == value.ItemCode) {
                                console.log("CUMPLE ", value.ItemCode);
                            } else {
                                return false;
                            }
                        } else {
                            console.log("PRODUCTO PERDIDO");
                            return false;
                        }

                        //  }
                    } else {
                        console.log("CANTIDAD PRODUCTOS NO IGUALAN ");
                        return false;
                    }
                }
                returnSW = true;
            }
            //MAU  DEv  
            if (bonoCompletoMaster[0].id_regla_bonificacion == "11" || bonoCompletoMaster[0].id_regla_bonificacion == "12") {
                let cantidad = 0;
                let cantidadCompraRegla = 1;
                console.log("BOPNIFICACION INGRESA BONO 11 ", productosCarrito);
                let auxValid: any = await this.bonificacion_compras.findForCabezera(code);
                console.log("auxValid ", auxValid)
                for (let value of auxValid) {
                    let filterItemValid: any = productosCarrito.filter((x: any) => value.code_compra == x.ItemCode);
                    if (filterItemValid.length > 0) {
                        let xcantidad = 0;
                        for (let item of filterItemValid) {
                            xcantidad = xcantidad + (item.Quantity * item.BaseQty);
                            cantidad = cantidad + xcantidad;
                        }
                        if (filterItemValid[0].ItemCode == value.code_compra && xcantidad >= value.producto_cantidad) {
                            console.log("CUMPLE ", value.code_compra);
                        } else {
                            console.log("No CUMPLE ", value.code_compra);
                            cantidadCompraRegla = 0;
                        }
                    } else {
                        console.log("No CUMPLE ", value.code_compra);
                        cantidadCompraRegla = 0;
                    }
                }

                if (cantidadCompraRegla == 1 && cantidad >= Number(bonoCompletoMaster[0].cantidad_compra)) { //&& cantidad <= Number(bonoCompletoMaster[0].cantidad_maxima_compra)
                    returnSW = true;
                } else {
                    returnSW = false;
                }
            }

            //fin mau
            if (bonoCompletoMaster[0].id_regla_bonificacion == "13" && Quantity >= Number(bonoCompletoMaster[0].cantidad_compra)) {// es descuento 

                returnSW = true;
            }
        }
        return returnSW;
    }
    // descuentoICE(descuentoDelTotal, value, cod, estado, codigo_bono = 0){
    //     monto_neto 
    // }

    public async descuentoICEdocumento(descuentoDelTotal: any, descuentoDelTotalPorcentual: number, idDocumento: any, bono = 0,campa = 0) {
        return new Promise(async (resolve, reject) => {
            console.log("ES CAMPAÑA",campa);
            console.log(GlobalConstants.DetalleDoc);
            descuentoDelTotal = Number(descuentoDelTotal).toFixed(2);
            let cartAux = [...GlobalConstants.DetalleDoc.filter((item: any) => item.bonificacion != 1)]
            const totalItems = cartAux.reduce((result: any, e: any) => {
                return result + ((e.Quantity * e.Price) - e.U_4DESCUENTO)
            }, 0);

            let servi = await this.configService.getSession();

            switch (parseInt(servi[0].localizacion)) {
                case (1):
                    this.localizacion_calculo = new Bolivia();
                    break;
                case (2):
                    this.localizacion_calculo = new Companex();
                    break;
                case (3):
                    this.localizacion_calculo = new Paraguay();
                    break;
                case (4):
                    this.localizacion_calculo = new Chile();
                    break;
            }

            console.log("JS totalItems ", totalItems);
            if (descuentoDelTotal > 0) { //DESCUENTO A TODO EL DOCUMENTO

                this.localizacion_calculo.bonificacioncabaecera(totalItems,descuentoDelTotal,descuentoDelTotalPorcentual,bono);

                /*GlobalConstants.DetalleDoc.map((item: any) => {

                    if (item.bonificacion != 1 || !item.bonificacion) {

                        item.U_4DESCUENTOBef_cab=item.U_4DESCUENTO; 
                        console.log("SI111111111111");
                        console.log(Number(item.Quantity))
                        console.log(Number(item.Price))
                        console.log(Number(item.U_4DESCUENTO))
                        console.log(totalItems)
                        console.log(Number(descuentoDelTotal));
                        console.log(descuentoDelTotalPorcentual);


                        item.XMPORCENTAJEBONIFICACION = descuentoDelTotalPorcentual;
                        item.XMVALORBONIFICACION = Number(descuentoDelTotal);



                        console.log("resultados");

                        item.U_4DESCUENTO = (Number(item.U_4DESCUENTO) + (((Number(item.Quantity) * (Number(item.Price)) * 1.0)* (descuentoDelTotalPorcentual/100))) ).toFixed(4)
                        
                        item.SumBoniLin = item.U_4DESCUENTO,
                        
                        console.log("resultados",item.U_4DESCUENTO);

                        item.U_4DESCUENTOBoni =item.U_4DESCUENTO;
                        console.log("resultados",item.U_4DESCUENTOBoni);

                        item.ICEe = Number(item.icete) * (Number(item.Quantity) * Number(item.BaseQty));
                        item.ICEeBoni =item.ICEe; 
                        console.log("resultados",item.ICEe);

                        item.codeBonificacionUse = bono
                        console.log("resultados",item.codeBonificacionUse);

                        item.bonificacion = '2';
                        let icep_aux1=(Number(item.Quantity) * Number(item.Price));
                        let icep_aux2=icep_aux1- Number(item.U_4DESCUENTO);
                        let icep_aux3=icep_aux2*0.87;
                        let icep_aux4=icep_aux3*(Number(item.icetp) / 100);
                        item.ICEp = icep_aux4.toFixed(2);
                        item.ICEpBoni=item.ICEp;
                        console.log("resultados",item.ICEp);
                        let ltp_aux1=Number(item.Quantity) * Number(item.Price);
                        let ltp_aux2=ltp_aux1-Number(item.U_4DESCUENTO);
                        let ltp_aux3=ltp_aux2+ Number(item.ICEe);
                        let ltp_aux4=ltp_aux3+ Number(item.ICEp);
                        item.LineTotalPay = ltp_aux4.toFixed(2);
                        console.log("resultados",item.LineTotalPay);

                        item.LineTotalPayBoni = item.LineTotalPay;
                    }


                    return item
                })*/
                console.log(GlobalConstants.DetalleDoc);
            }
            resolve(true);
        });
    }

    public descuentoCabezeraPorcentual(descuentoMonetario: number, descuentoPorcentual: number, idDocumento: string) {
        return new Promise(async (resolve, reject) => {

            let cartAux = [...GlobalConstants.DetalleDoc.filter((item: any) => item.bonificacion != 1)]
            const totalItems = cartAux.reduce((result: any, e: any) => {
                return result + ((e.Quantity * e.Price) - e.U_4DESCUENTO)
            }, 0);
            console.log("JS totalItems ", totalItems);

            if (descuentoMonetario >= 0) { //DESCUENTO A TODO EL DOCUMENTO

                // XMPORCENTAJECABEZERA pocentaje item 
                // descuento= moneda doc
                // tipodescuento

                GlobalConstants.DetalleDoc.map((item: any) => {
                    if (item.bonificacion != 1) {
                        item.XMPORCENTAJECABEZERA = descuentoPorcentual
                    }
                    return item
                })


                GlobalConstants.CabeceraDoc.map((item: any) => {
                    item.descuento = descuentoMonetario
                    item.tipodescuento = descuentoPorcentual
                    return item
                })
                console.log("SERVISE CON EL DESCUENTO CABEZERA APLICADO ", GlobalConstants.CabeceraDoc);


            }
            resolve(true);
        });
    }

}


//TODO: IMPLEMENTAR BONIFICACINOES REFACT
interface StrategyDiscount {
    addDiscount(product: any, rule: string): boolean;
}

class BonificacionContext {
    private strategy: StrategyDiscount;
    constructor(strategy: StrategyDiscount) {
        this.strategy = strategy;
    }
    setStrategy(strategy: StrategyDiscount) {
        this.strategy = strategy;
    }
    addDiscount(product: any, rule: string) {
        return this.strategy.addDiscount(product, rule);

    }
}
class Model1 implements StrategyDiscount {
    addDiscount(product: any, rule: string): boolean {
        console.log("Modelo A ");
        return true;
    }
}
class Model2 implements StrategyDiscount {
    addDiscount(product: any, rule: string): boolean {
        console.log("Modelo B ");
        return true;
    }
}
class Model3 implements StrategyDiscount {
    addDiscount(product: any, rule: string): boolean {
        console.log("Modelo C ");
        return true;
    }
}



