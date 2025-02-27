import {Calculo} from "./calculo";
import {Bolivia} from "./bolivia"
import {Descuentos} from "../models/descuentos";

export class Gedesa extends Bolivia {
    public async descuentos(data: any) {
        let descuentos = new Descuentos();
        try {
            let descux: any = await descuentos.selectDescuento(data.ItemCode, data.dataexport.cliente.CardCode,
                data.dataexport.cliente.GroupCode, data.dataexport.listaPrecio.PriceListNo, descuentos.getFechaPicker());
            if (typeof descux[0] !== 'undefined') {
                switch (descux[0].tipodescuento) {
                    case('DE'):
                        console.log("DE");
                        break;
                    case('DG'):
                        console.log("DG");
                        break;
                    case('DPC'):
                        console.log("DPC");
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
}
