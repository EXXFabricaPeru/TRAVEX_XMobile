import { Calculo } from "./calculo";
import { Bolivia } from "./bolivia"
import { formatNumber } from "@angular/common";
import { GlobalConstants, } from "../../global";

export class Companex extends Bolivia {

    protected _cantidad: number;
    protected _precio: number;
    protected _porcentual: number;
    protected _monetario: number;
    protected tice: any;
    protected icee: number;
    protected icep: number;
    protected cantidaduni: number;
    protected usadecimales: any;

    public async calculo() {
        //pruebas
        let totalNeto = (this._cantidad * this._precio).toFixed(4);
        let descuentos: any = 0;

        if (this._porcentual > 0){
            console.log("CONSOLA: ES DESCUENTO PORCENTUAL 24");
            /*descuentos = (Calculo.porcentaje(totalNeto, this._porcentual));
            descuentos = Number(descuentos).toFixed(2);*/
            descuentos = this._porcentual;
        }else{
            console.log("CONSOLA: ES DESCUENTO MONETARIO 29");
            console.log("CONSOLA: ES DESCUENTO MONETARIO 29",this._monetario);
            descuentos = this._monetario;
        }

        console.log("this._precio ", this._precio);
        console.log("this._cantidad ", this._cantidad);
        console.log("this.cantidaduni ", this.cantidaduni);
        console.log("this.icee ", this.icee);
        console.log("this.icep ", this.icep);

        if (this.cantidaduni == 0) {
            this.cantidaduni = this._cantidad;
        }
        let total = totalNeto;
        
        console.log("CONSOLA: LLAMA FUNCION ICE_linea 44");
        let icelinea:any = await this.ICE_linea(this._precio, this._cantidad, descuentos, 0, this.tice, this.icee, this.icep, this.cantidaduni);


        if(this.usadecimales == '0' || this.usadecimales == undefined){
            console.log("NO USA REDONDEO");
            return {
                "totalNeto": total,
                "descuento": icelinea.descuento,
                "total": icelinea.total,
                "totalSinIces": icelinea.totalSinIces,
                "icet": this.tice,
                "icee": icelinea.icee,
                "icep": icelinea.icep,
                "unids": this.cantidaduni
            };
        }else{
            console.log("USA REDONDEO");

            return {
                "totalNeto": Calculo.round(total,0) ? Calculo.round(total,0) : 0,
                "descuento": Calculo.round(icelinea.descuento,0) ? Calculo.round(icelinea.descuento,0) : 0,
                "total": Calculo.round(icelinea.total,0) ? Calculo.round(icelinea.total,0) : 0, 
                "totalSinIces": Calculo.round(icelinea.totalSinIces,0) ? Calculo.round(icelinea.totalSinIces,0) : 0,
                "icet": this.tice,
                "icee": Calculo.round(icelinea.icee,0) ? Calculo.round(icelinea.icee,0) : 0,
                "icep": Calculo.round(icelinea.icep,0) ? Calculo.round(icelinea.icep,0) : 0,
                "unids": this.cantidaduni
            };
        }

    }

    public async calculaprecio(cantidad:any,precio:any,descuento:any) {
        
        console.log("CONSOLA: INICIA FUNCION calculaprecio 77");
        console.log("CONSOLA: CANTIDAD",cantidad);
        console.log("CONSOLA: PRCIO",precio);
        console.log("CONSOLA: DESCUENTO",descuento);

        let cxp = cantidad*precio;
        console.log("CANTIDAD POR PRECIO",cxp);
        let descsap = 1-(descuento/100);
        console.log("CALCULO DEL DESCUENTO",descsap);

        let totallin = cxp*descsap;
        console.log("CANTIDAD POR PRECIO POR DESCUENTO",totallin);
       

        
        totallin = Calculo.round(totallin);
        totallin =  Number(totallin.toFixed(4));
        console.log("VALOR REDONDEADO",totallin);

        let desc = cxp-totallin;
        desc =  Number(desc.toFixed(4));

        console.log("VALOR DESCUENTO",desc);
        return {
            "totallinea": totallin,
            "descuento": desc,
            "precio":cxp
        };
    }


    public async ICE_linea($precio, $cantidad = 1, $descuentom = 0, $descuentop = 0, $ice, $icee, $icep, $cantidaduni) {
        console.log("CONSOLA: INICIA FUNCION ICE_linea 98");

        if ($cantidaduni == null || $cantidaduni == "null") {
            $cantidaduni = 1;
        }

        let precio = 0;

        let ice = $ice;
        let icep: any = parseFloat($icep);
        let icee: any = parseFloat($icee);
        let totalLinea = 0;
        let descuento =0;

        if (this._porcentual > 0){
            console.log("CONSOLA: LLAMA FUNCION calculaprecio 106");
            let resul = await this.calculaprecio($cantidad,$precio,$descuentom);
            console.log("datos retornados",resul);
            totalLinea = resul.totallinea;
            descuento = resul.descuento;
        }else{
            totalLinea =$precio * $cantidad;
            totalLinea = totalLinea - $descuentom;
            descuento = $descuentom;
        }

        let iva = totalLinea * 0.13;
        let neto = totalLinea - iva;

        let c_icep = 0;
        let c_icee = 0;

        if (ice !== "N") { //NINGUNO
            precio = $precio;
            if (ice == "E") { // ESPECIFICO
                c_icee = $cantidad * icee * $cantidaduni;
                precio = totalLinea + c_icep + c_icee;
            }
            if (ice == "P") { // PORCENT
                c_icep = (icep / 100) * neto;
                precio = totalLinea + c_icep + c_icee;
            }
            if (ice == "A") { // AMBOS
                c_icep = (icep / 100) * neto;
                c_icee = $cantidad * icee * $cantidaduni;
                precio = totalLinea + c_icep + c_icee;
            }
            if (ice == 0) {
                precio = totalLinea;
            }
        } else {
            precio = totalLinea;
        }

        let salida = {
            totalSinIces:Number(totalLinea.toFixed(4)),
            total: Number(precio.toFixed(4)), //Calculo.round(
            icep: Number(c_icep.toFixed(4)),
            icee: Number(c_icee.toFixed(4)),
            descuento: Number(descuento.toFixed(4))
        };

        console.log("localizacion salida ", salida);
        return salida;

    }

    public calculoNew(precio): any {
        console.log("from service ", precio);
    }

    public async CalculoDescuentocabecera(itm){ 
        console.log("CONSOLA: INICIA FUNCION DEL CALCULO CalculoDescuentocabecera 184");
        let porcentaje = itm.XMPORCENTAJE+itm.XMPORCENTAJECABEZERA;

        console.log("CONSOLA: LLAMA FUNCION calculaprecio 190");
        let resul = await this.calculaprecio(itm.Quantity,itm.Price,porcentaje);
        console.log("datos retornados",resul);

        let precio = resul.precio;
        let descuento = resul.descuento;

        console.log("CONSOLA: DESCUENTO ORIGINAL 197",itm.U_4DESCUENTO);
        console.log("CONSOLA: DESCUENTO CALCULADO 198",descuento);
        
        //if(itm.U_4DESCUENTO <= descuento){

            let valor = precio-descuento;
            let iva = valor*0.13;
            let neto = valor-iva;
            let valorice = neto*(itm.icetp/100);
            let icees = itm.Quantity*itm.icete*itm.BaseQty;

            if(this.usadecimales == '0' || this.usadecimales == undefined){
                return {
                    "ICEe": icees,
                    "ICEp": valorice,
                    "U_4DESCUENTO": descuento,
                    "LineTotal":  (icees+valorice+precio),
                    "LineTotalPay": (icees+valorice+valor),
                    
                };
            }else{
                return {
                    "ICEe": Calculo.round(icees,0),
                    "ICEp": Calculo.round(valorice,0),
                    "U_4DESCUENTO": Calculo.round(descuento,0),
                    "LineTotal": Calculo.round((icees+valorice+precio),0),
                    "LineTotalPay": Calculo.round((icees+valorice+valor),0),
                    
                };
            }

        //}

    }

    public async sumaTotalLocal(cabecera: any,detalle: any) {

        console.log("CONSOLA: INICIA FUNCION sumaTotalLocal 229");

        let totalNeto = 0;
        let total = 0;
        let descuentos = 0;
        let ICEes = 0;
        let ICEps = 0;
        let ICEtotales = 0;
        let tipoDescuento = 0;
        let doctotal = 0;

        for(let items of detalle){
 
            if (items.Tbonificacion == 1) {
                console.log("CONSOLA: ES BONIFICACION 243");
                let aux = (parseFloat(items.U_4DESCUENTOBoni));
                let aux2 = (parseFloat(items.Quantity)*parseFloat(items.Price));

                totalNeto += aux2;
                total += (aux2-aux)+(parseFloat(items.ICEeBoni)+parseFloat(items.ICEpBoni));
                

                descuentos += (parseFloat(items.U_4DESCUENTOBoni));
                ICEes += parseFloat(items.ICEeBoni);
                ICEps += parseFloat(items.ICEpBoni);
                ICEtotales += (parseFloat(items.ICEeBoni)+parseFloat(items.ICEpBoni));
                doctotal += totalNeto+ICEtotales;
            } else {
                console.log("CONSOLA: NO ES BONIFICACION 243",items);

                console.log("CONSOLA: LLAMA FUNCION calculaprecio 264");
                let resul = await this.calculaprecio(items.Quantity,items.Price,items.DiscPrcnt);
                console.log("datos retornados",resul);

                
                let aux2 = resul.precio;
                let aux = resul.descuento;

                console.log("CONSOLA: DESCUENTO ACTUAL 267",items.U_4DESCUENTO);
                console.log("CONSOLA: DESCUENTO RECLACULADO SAP 268",aux);

                totalNeto += aux2;
                total += (aux2-aux)+(parseFloat(items.ICEe)+parseFloat(items.ICEp));
                console.log(total);

                descuentos += (parseFloat(items.U_4DESCUENTO));
                ICEes += parseFloat(items.ICEe);
                ICEps += parseFloat(items.ICEp);
                ICEtotales += (parseFloat(items.ICEe)+parseFloat(items.ICEp));
                doctotal += totalNeto+ICEtotales;

            }
        }

        console.log("CONSOLA: DATOS DEL CALCULO totalNeto:",totalNeto);
        console.log("CONSOLA: DATOS DEL CALCULO descuentos:",descuentos);
        console.log("CONSOLA: DATOS DEL CALCULO ICEes:",ICEes);
        console.log("CONSOLA: DATOS DEL CALCULO ICEps:",ICEps);
        console.log("CONSOLA: DATOS DEL CALCULO ICEtotales:",ICEtotales);
        console.log("CONSOLA: DATOS DEL CALCULO total:",total);
        console.log("CONSOLA: DATOS DEL CALCULO doctotal:",doctotal);



        tipoDescuento = cabecera[0].tipodescuento;

        return {
            totalNeto: Calculo.round(totalNeto),
            descuentos: Calculo.round(descuentos),
            ICEes: Calculo.round(ICEes),
            ICEps: Calculo.round(ICEps),
            ICEtotales: Calculo.round(ICEtotales),
            total: Calculo.round(total),
            doctotal: Calculo.round(doctotal),
            porcentajeDescuentoCabezera: tipoDescuento
        };
    }

    public async xneto(item: any) {

        let sumLineTotalPay = 0;
        let xneto = 0;
        let totalnetox = 0;
        let totaldescuentox = 0;

        xneto = ((item.Quantity * item.Price) - item.U_4DESCUENTO) + Number(item.ICEe) + Number(item.ICEp);
        totalnetox = (item.Quantity * item.Price);
        sumLineTotalPay = xneto; 
        totaldescuentox = parseFloat(item.U_4DESCUENTO);
        

        return {
            "xneto": xneto,
            "totalnetox": totalnetox,
            "sumLineTotalPay":sumLineTotalPay,
            "totaldescuentox":totaldescuentox
        };
    }

    public bonificacioncabaecera(totalItems: any,descuentoDelTotal: any,descuentoDelTotalPorcentual: any,bono: any){

        GlobalConstants.DetalleDoc.map((item: any) => {

            if (item.bonificacion != 1 || !item.bonificacion) {

                item.U_4DESCUENTOBef_cab=item.U_4DESCUENTO; 
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
                item.ICEp = icep_aux4.toFixed(4);
                item.ICEpBoni=item.ICEp;
                console.log("resultados",item.ICEp);
                let ltp_aux1=Number(item.Quantity) * Number(item.Price);
                let ltp_aux2=ltp_aux1-Number(item.U_4DESCUENTO);
                let ltp_aux3=ltp_aux2+ Number(item.ICEe);
                let ltp_aux4=ltp_aux3+ Number(item.ICEp);
                item.LineTotalPay = ltp_aux4.toFixed(4);
                console.log("resultados",item.LineTotalPay);

                item.LineTotalPayBoni = item.LineTotalPay;
            }


            return item
        })
    }
}
