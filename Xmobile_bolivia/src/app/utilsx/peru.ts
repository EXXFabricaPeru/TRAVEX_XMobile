import {Calculo} from "./calculo";
import {Bolivia} from "./bolivia"

export class Peru extends Bolivia {
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
    protected manejaPercepcion : boolean;
    protected precioespecial: number;
    

    public redondeo(valor){
        let cadena  = String(valor);
        let x1= '';
        let aumeta = 0;
        let total = 0;
        let x2,x3;
        this.precioespecial = 0;
        console.log("cadena a rrecorrer", cadena);
        for (let i = 0; i < cadena.length; i++) {
            
            x1 = x1+cadena[i];
            if(cadena[i]== '.'){
                console.log("consigue el punto");
                let aux1 = cadena[i+1];
                let aux2 = cadena[i+2];
                let aux3 = cadena[i+3] ? cadena[i+3] : 0 ;

                console.log("LOS PRIMEROS 3 DECIMALES SON:" +aux1+'-'+aux2+'-'+aux3);

                if(Number(aux3) > 0){
                    console.log("TERCER DECIMAL ES MAYOR A 0");
                    if(Number(aux2) > 5){
                        console.log("SEGUNDO DECIMAL ES MAYOR A 5");
                        x2 = String(Number(aux1)+1);
                        if(Number(x2) >= 10){
                            console.log("VALOR DA IGUAL O MAYOR A 10");
                            aumeta = 1;
                            x2 = 0
                        }
                        x3 = '0';
                    }else{
                        x2 = aux1;
                        x3 = String(Number(aux2)+1);
                        console.log("SEGUNDO DECIMAL ES MENOR A 5",x3);
                    }
                }else{
                    console.log("TERCER DECIMAL ES IGUAL A 0");
                    x2 = aux1;
                    x3 = aux2;
                }
               
                i = cadena.length;
            }
        }

        if(aumeta == 1){
            console.log("SE AUMENTA 1 A LOS ENTEROS");
            total = parseFloat(x1)+1;
            total = parseFloat(total.toString()+'.'+x2+x3);
        }else{
            console.log("ss");
            total = parseFloat(x1+x2+x3);
        }

        return total;
    }

    public calculo(): any{ 
        console.group();
        console.log("calculando impuesto");

        let impuesto=0;
        let aux_total_bruto=0;
        let aux_iva=0;
        let hoy=new Date();
        let decimales=6;
        //let decimalesPrecios=2;

      
        let iva=0;
        let descuentos = 0;
        let impuesto2=0;
        console.log("esta es la moneda--> para el recalculo",this.moneda,"precioespecial-->",this.precioespecial,this._cantidad,"recalculo-->",this._precio,this._cantidad,"--->",this.icee,"TICE",this.tice);
        /* if(this.moneda!="GS"){
            console.log("entro para 6 decimales",this.moneda);
            decimales=6; 
        } */
        
        /* if(this.precioespecial == 1){
            this.icee = 0;
        } */
        aux_total_bruto = this._precio;
        console.log("el precio que llega",aux_total_bruto);
       
        descuentos=descuentos?descuentos:0;
        // let aux_total= aux_total_bruto-descuentos;
        let aux_total= aux_total_bruto;
        aux_total=aux_total;
        let totalNeto=0;
        let total = 0;
        let precioBruto = 0;
        if(aux_total>0){
            if (this.tice=="tNO"){
                let totalAux = (this._precio * this._cantidad); // antes del descuento
                let precioSinImpusto = 0;
                console.log("_precio", this._precio);
                console.log("icee", this.icee);

                console.log("calculo",(Number(this.icee) / 100))

                let impuestoUnitario = this._precio * (Number(this.icee) / 100);

                console.log("_precio", this._precio);
                console.log("impuestoUnitario", impuestoUnitario);
                console.log("decimales", decimales);

                //precioBruto =Calculo.round(this._precio + impuestoUnitario, decimales) ;

                precioBruto = (Number(this._precio) + Number(impuestoUnitario)) ;

                console.log("broto", precioBruto);

                if((this._porcentual>0) && (this._monetario>0)){
                    descuentos =Calculo.porcentaje(totalAux, this._porcentual);
                } else{
                    if (this._porcentual>0){
                        descuentos = (Calculo.porcentaje(totalAux, this._porcentual) + this._monetario);
                         precioSinImpusto = Calculo.round((this._precio * ((100 - this._porcentual) / 100)), decimales);
                         impuestoUnitario = precioSinImpusto * (Number(this.icee) / 100);
                         precioBruto =Calculo.round(precioSinImpusto + impuestoUnitario, decimales) ;
                         console.log("broto 2", precioBruto);
                    }
                   
                }
                
                totalNeto = totalAux - descuentos; // antes del del impuesto
                impuesto=totalNeto*(Number(this.icee)/100);
                console.log("descuento aplicado-->",descuentos)
                console.log("this.tice bruto ", this.tice);
                let totalAux2 = totalNeto + impuesto; // despues del impuesto

               // totalNeto
               // descuentos
                total = totalAux2;
                iva = this.icee;
                /* aux_total = this._precio - descuentos;
                this._precio=this._precio;
                impuesto=aux_total*(Number(this.icee)/100);
                impuesto = Calculo.round((impuesto*this._cantidad),decimales);
                iva=this.icee;
                //impuesto=Calculo.round((aux_total*Number(this.icee)/100),2);
                aux_total_bruto = Calculo.round((this._precio*this._cantidad),decimales);
                aux_total_bruto = Calculo.round((this._precio * this._cantidad), decimales);
                
                aux_total= Calculo.round(aux_total_bruto-descuentos,decimales);
                impuesto=Calculo.round((this._cantidad*impuesto),decimales);
                totalNeto = Calculo.round((Number(aux_total_bruto)),decimales);
                if (totalNeto>0)
                total = Calculo.round((totalNeto + impuesto - descuentos),decimales);
                total = Calculo.round((totalNeto + impuesto - descuentos),decimales); */

                
            }
            else
            {
                console.log("this.icee  ", this.icee);
                
              //  aux_total_bruto=aux_total/(1+(Number(this.icee)/100));
               // aux_total_bruto=Calculo.round(aux_total_bruto,decimales);
                
               console.log("this.tice neto ", aux_total_bruto,this._precio,"-->",this._cantidad);
               
                this._precio=this._precio/(1+(Number(this.icee)/100));
                impuesto=(this._precio)*(Number(this.icee)/100);
                //this._precio=Calculo.round(this._precio,decimales);
                console.log("this.tice neto01", aux_total_bruto,this._precio,impuesto,"-->",this._cantidad);
                //if((this._porcentual>0) || (this._monetario>0)){
                    // con descuento
                    // descuentos =Calculo.porcentaje(aux_total_bruto, this._porcentual);
                    let precioAux=this._precio*this._cantidad;

                    /**
                     * obteneindo para el precio bruto
                     */
                    console.log(this._precio, impuesto);
                    const precioSinDescuento = Calculo.round(this._precio + impuesto, decimales);
                     precioBruto = Calculo.round((precioSinDescuento *((100-this._porcentual)/100)  ) , decimales);
                     console.log("broto 3", precioBruto);
                    
                    /**
                     * obteneindo para el para los totales
                     */
                    descuentos =(Calculo.porcentaje((precioAux), this._porcentual) ); // total de descuento 
                    
                    //nuevo total
                    let totalAux=Calculo.round(precioAux-descuentos,2);
                     //nuevo impuesto
                    let impuestoAux=(totalAux*(Number(this.icee)/100));
                    // seteo de impuesto a dos decimales
                    impuesto=Calculo.round(impuestoAux,2);
                    // seteo del total bruto
                    aux_total_bruto=totalAux+impuesto;
                    console.log("rafael",aux_total_bruto);
                    //redondeo a dos decimales el descuento
                    descuentos=Calculo.round(descuentos,2);

                    totalNeto = Calculo.round((Number(precioAux)),decimales);
                    
                    total = Calculo.round((aux_total_bruto), decimales);
                    console.log("rafael",total);
                    //  const descuentoUnitario = (Calculo.porcentaje((this._precio), this._porcentual));
                  
                   
                   
               
                /*}else{
                    // sin descuento
                     // descuentos =(Calculo.porcentaje((this._precio*this.cantidad), this._porcentual));
                            //impuesto2=impuesto
                        
                        // impuesto=Calculo.round(impuesto,decimales);
                    iva=this.icee;
                    precioBruto = Calculo.round(this._precio + impuesto, decimales);
                    console.log(aux_total_bruto);
        
                    console.log("this.tice neto2 ", aux_total_bruto,this._precio,"-->",this._cantidad);
                    aux_total_bruto = Calculo.round((this._precio*this._cantidad),decimales);
                    //descuentos = (Calculo.porcentaje(aux_total_bruto, this._porcentual) + this._monetario);
                    console.log("this.tice neto3 ", aux_total_bruto,this._precio,"-->",this._cantidad);
                    aux_total= Calculo.round(aux_total_bruto-descuentos,decimales);
                                
                    impuesto=Calculo.round((this._cantidad*impuesto),decimales);
                   
                    totalNeto = Calculo.round((Number(aux_total_bruto)),decimales);
                  
                    
                    
                    if (totalNeto>0)
                    total = Calculo.round((totalNeto + impuesto - descuentos),decimales);
                    
                }*/
               
              
               
    
            }
        }
        
        console.log("objeto calculo");
        console.log(this);
        let aux = precioBruto?precioBruto:0
        aux = aux*this._cantidad;
        if(this._cantidad > 1){
            total = this.redondeo(aux);
        }else{
            total = aux;
        }
        
        console.log(total);
        console.groupEnd();
        return {
            "precio":this._precio,
            "totalNeto": totalNeto,
            "descuento": descuentos,
            "total": total,
            "icee":Calculo.round(impuesto,decimales),//monto impuestp
            "iva": iva,
            "precioBruto":precioBruto?precioBruto:0
            
        };
    }
}
