import {Calculo} from "./calculo";
import {Bolivia} from "./bolivia"
import { GlobalConstants, } from "../../global";

export class Chile extends Bolivia {
    protected _cantidad: number;
    protected _precio: number;
    protected _porcentual: number;
    protected _monetario: number;
    protected tice: any;
    protected icee: number;
    protected icep: number;
    protected cantidaduni: number;
    protected turismo: any;
    protected vigencia: any;
    protected extranjero: any;
    protected usadecimales: any;

    public async calculo() {

        let impuesto=0;

        console.log("ROMERO",this.icee);
        console.log("ROMERO",this.tice);
        let totalNeto = 0;
        let descuento = 0;

        let total = this._cantidad * this._precio;

        if (this.tice=="tNO"){

            let resul = await this.calculaprecio(this._cantidad,this._precio,this._porcentual);
            totalNeto = resul.totallinea;
            descuento = resul.descuento;
            impuesto=(totalNeto*Number(this.icee)/100);
            let aux = Number(this.icee)/100;
            //let total_impuesto = totalNeto/(1-aux);
            impuesto = totalNeto*aux;
            //impuesto = total_impuesto-totalNeto;

        }else{
            let resul = await this.calculaprecio(this._cantidad,this._precio,this._porcentual);
            totalNeto = resul.totallinea;
        }

        console.log("CONSOLA: totalNeto",totalNeto);
        console.log("CONSOLA: descuento",descuento);
        console.log("CONSOLA: total",total);
        console.log("CONSOLA: icee",impuesto);
        console.log("CONSOLA: icep",impuesto);


        return {
            "totalNeto": Calculo.round(total,0),
            "descuento": Calculo.round(descuento,0),
            "total": Calculo.round(totalNeto,0),
            "icee":Calculo.round(impuesto,0),
            "icep": Calculo.round(impuesto,0)
        };
        
        
    }

    public async CalculoDescuentocabecera(itm){ 
        console.log("CONSOLA: INICIA FUNCION DEL CALCULO CalculoDescuentocabecera 184",itm);
        let porcentaje = itm.XMPORCENTAJE+itm.XMPORCENTAJECABEZERA;

        console.log("CONSOLA: LLAMA FUNCION calculaprecio 190");
        let resul = await this.calculaprecio(itm.Quantity,itm.Price,porcentaje);
        console.log("datos retornados",resul);

        let precio = resul.precio;
        let descuento = resul.descuento;
        let totallinea = resul.totallinea;

        console.log("CONSOLA: DESCUENTO ORIGINAL 197",itm.U_4DESCUENTO);
        console.log("CONSOLA: DESCUENTO CALCULADO 198",descuento);
        
        //if(itm.U_4DESCUENTO <= descuento){


            let aux = Number(itm.icetp)/100;
            let valor = precio-descuento;
            let iva = valor*aux;
            let neto = valor-iva;
            let icees = itm.Quantity*itm.icete*itm.BaseQty;
            //let impuesto=(totallinea*Number(this.icee)/100);
            //let total_impuesto = totallinea/(1-aux);
            //let valorice = neto*aux;


            return {
                "ICEe": Calculo.round(icees,0),
                "ICEp": Calculo.round(iva,0),
                "U_4DESCUENTO": Calculo.round(descuento,0),
                "LineTotal": Calculo.round((icees+iva+precio),0),
                "LineTotalPay": Calculo.round((icees+iva+valor),0),
                
            };
            

        //}

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
       

        
        totallin = Calculo.round(totallin,0);
        console.log("VALOR REDONDEADO",totallin);

        let desc = cxp-totallin;
        desc = Calculo.round(desc,0);

        console.log("VALOR DESCUENTO",desc);
        return {
            "totallinea": totallin,
            "descuento": desc,
            "precio":cxp
        };
    }

    public async sumaTotalLocal(cabecera: any,detalle: any) {

        console.log("control item detalle local",cabecera);
        console.log("control item detale detalle nlocal",detalle);

        let totalNeto = 0;
        let total = 0;
        let descuentos = 0;
        let ICEes = 0;
        let ICEps = 0;
        let ICEtotales = 0;
        let tipoDescuento = 0;

        for(let items of detalle){
 
            let aux = (parseFloat(items.U_4DESCUENTO));
            let aux2 = (parseFloat(items.Quantity)*parseFloat(items.Price));

            console.log(aux);
            console.log(aux2);

            totalNeto += aux2;
            total += (aux2-aux)+(parseFloat(items.ICEe)+parseFloat(items.ICEp));
            console.log(total);

            descuentos += (parseFloat(items.U_4DESCUENTO));
            ICEes += parseFloat(items.ICEe);
            ICEps += parseFloat(items.ICEp);
            ICEtotales += (parseFloat(items.ICEe)+parseFloat(items.ICEp));
            
        }
        tipoDescuento = cabecera[0].tipodescuento;

        return {
            totalNeto: Calculo.round(totalNeto),
            descuentos: Calculo.round(descuentos),
            ICEes: Calculo.round(ICEes),
            ICEps: Calculo.round(ICEps),
            ICEtotales: Calculo.round(ICEtotales),
            total: Calculo.round(total),
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
        })
    }
}
