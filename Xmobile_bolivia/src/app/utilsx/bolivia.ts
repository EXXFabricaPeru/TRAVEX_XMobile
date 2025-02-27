import {Calculo} from "./calculo";
import {Descuentos} from "../models/descuentos"
import { GlobalConstants, } from "../../global";

export class Bolivia {
    protected _cantidad: number;
    protected _precio: number;
    protected _porcentual: number;
    protected _monetario: number;
    protected usadecimales: any;

    get porcentual(): number {
        return this._porcentual;
    }

    set porcentual(value: number) {
        this._porcentual = value;
    }

    get monetario(): number {
        return this._monetario;
    }

    set monetario(value: number) {
        this._monetario = value;
    }

    get cantidad(): number {
        return this._cantidad;
    }

    set cantidad(value: number) {
        this._cantidad = value;
    }

    get precio(): number {
        return this._precio;
    }

    set precio(value: number) {
        this._precio = value;
    }

    public async descuentosadicionales(data: any) {
        let descuentos = new Descuentos();
        try {
            let descux: any = await descuentos.selectDescuento(data.ItemCode, data.dataexport.cliente.CardCode,
                data.dataexport.cliente.GroupCode, data.dataexport.listaPrecio.PriceListNo, descuentos.getFechaPicker());
            if (typeof descux[0] !== 'undefined') {
                switch (descux[0].tipodescuento) {
                    case('DE'):
                        return 0;
                        break;
                    case('DG'):
                        return 0;
                        break;
                    case('DPC'):
                        return 0;
                        break;
                }
                return 0;
            } else {
                return 0;
            }
        } catch (e) {
            return 0;
        }
    }

    public calculo(): any {
        let totalNeto = Calculo.round(this._cantidad * this._precio);
        let descuentos = (Calculo.porcentaje(totalNeto, this._porcentual) + this._monetario);
        let total = Calculo.round(totalNeto - descuentos);
        return {
            "totalNeto": totalNeto,
            "descuento": descuentos,
            "total": total
        };
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
        totallin =  Number(totallin.toFixed(2));
        console.log("VALOR REDONDEADO",totallin);

        let desc = cxp-totallin;
        desc =  Number(desc.toFixed(2));

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
