import {Calculo} from "./calculo";
import {Bolivia} from "./bolivia"

export class peruSinPerc extends Bolivia {
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
    protected impuesto: any;
    protected moneda: any;
    protected turismoproducto: any;
    
    public calculo(): any {

        let impuesto=0;
        let aux_total_bruto=0;
        let aux_iva=0;
        let hoy=new Date();
        let decimales=0;
        let iva=0;
        aux_total_bruto = this._precio*this._cantidad;
        console.log("**IBJ**");
        console.log(aux_total_bruto);
        let descuentos = 0;
        if((this._porcentual>0) && (this._monetario>0)){
            descuentos =Calculo.porcentaje(aux_total_bruto, this._porcentual);
        }else{
            descuentos =(Calculo.porcentaje(aux_total_bruto, this._porcentual) + this._monetario);
        }
        
        let aux_total= aux_total_bruto-descuentos;

        decimales=2;
        if(aux_total>0){
            if (this.tice=="tNO"){
                console.log("this.tice bruto ", this.tice);
                this._precio=this._precio;
                impuesto=aux_total*(Number(this.icee)/100);
                iva=this.icee;
                //impuesto=Calculo.round((aux_total*Number(this.icee)/100),2);
            /*
                if((this.turismo=="S") && (this.turismoproducto=="SI")){
                            this.vigencia=new Date(this.vigencia);
                            if(this.vigencia>hoy){
                                impuesto=(aux_total*1.5/100);
                                iva=1.5;
                            }
                            
                }
                if(this.extranjero=="N"){
                            impuesto=0;
                            iva=0;
                } 
                */
            }else{
                console.log("this.tice neto ", this.icee);
                this.icee = 0;
                aux_total_bruto=aux_total/(1+(Number(this.icee)/100));
                impuesto=aux_total_bruto*(Number(this.icee)/100); 
                this._precio=aux_total_bruto/this.cantidad;
                iva=this.icee;
                console.log(aux_total_bruto);
    /*
                if((this.turismo=="S") && (this.turismoproducto=="SI")){
                    this.vigencia=new Date(this.vigencia);
                    if(this.vigencia>hoy){
                        aux_total_bruto=aux_total/(1+(Number(1.5)/100));
                        impuesto=aux_total_bruto*(Number(1.5)/100); 
                        this._precio=aux_total_bruto/this.cantidad; 
                        iva=1.5;
                    }
    
                }
                if(this.extranjero=="N"){
                    aux_total_bruto=aux_total;
                    impuesto=0; 
                    this._precio=aux_total_bruto/this.cantidad;
                    iva=0;
                } 
    */
                //this.precio=Calculo.round(this._precio,decimales);
                
                aux_total_bruto = Calculo.round(aux_total_bruto,decimales);
                //descuentos = (Calculo.porcentaje(aux_total_bruto, this._porcentual) + this._monetario);
                aux_total= Calculo.round(aux_total_bruto-descuentos,decimales);            
                impuesto=Calculo.round(impuesto,decimales);
    
            }
        }
        
        
        this.impuesto=aux_total;
        //this._precio=Calculo.round(this._precio,decimales);
        console.log("objeto calculo");
        console.log(this);
        let totalNeto = Calculo.round((Number(aux_total_bruto)),decimales);
       // let descuentos = (Calculo.porcentaje(totalNeto, this._porcentual) + this._monetario);
       let total =0;
       if (totalNeto>0)
       total = Calculo.round((totalNeto +impuesto- descuentos),decimales);
       
        return {
            "precio":this._precio,
            "totalNeto": totalNeto,
            "descuento": descuentos,
            "total": total,
            "icee":Calculo.round(impuesto,decimales),//monto impuestp
            "iva":iva
            
        };
    }
}
