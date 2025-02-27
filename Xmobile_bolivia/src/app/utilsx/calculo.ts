export class Calculo {

    public static porcentaje(cantidad: any, porciento: number): number {
        return cantidad * porciento / 100;
    }

    public static round(num: any, decimales = 2): number {
        console.log("LLAMA A REDONDEAR DE CALCULO ", num);
        let signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0)
            return signo * Math.round(num);
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        num = num.toString().split('e');
        num = (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
        return (signo * num);
    }

    public static validaNumeroPago(numero: number): boolean {
        if (Calculo.validaEntero(numero) && numero > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static formatMoney(numx: any) {
        console.log("numx ", numx);
        let num = parseFloat(numx);
        let p = num.toFixed(2).split(".");
        return p[0].split("").reverse().reduce(function (acc, num, i, orig) {
            return num == "-" ? acc : num + (i && !(i % 3) ? "," : "") + acc;
        }, "") + "." + p[1];
    }

    public static generaCodeRecibo(user: any, total: any, pais: any): string {
        let u = '0000';
        let i = '00000';
        pais=pais+'01'
        let ux = u.slice(0, -user.length);
        let ix = i.slice(0, -total.length);
        return `${pais}${ux}${user}${ix}${total}`;
    }

    public static validaEntero(numero: number): boolean {
        if (Math.sign(numero) === 1 || numero == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static validaFloat(numero: number): boolean {
        if ((Math.sign(numero) == 1 || numero == 0) || Calculo.isFloat(numero)) {
            return true;
        } else {
            return false;
        }
    }

    public static isFloat(n) {
        return Number(n) === n && n % 1 !== 0;
    }

    public static isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    public static ICEHEADER($cabecera: any, $cuerpo: any) {
        let $dp = $cabecera["TotalDiscPrcnt"];
        let $dm = parseFloat($cabecera["TotalDiscMonetary"]);
        let $total1 = 0;
        let $total2 = 0;
        for (let i = 0; i < $cuerpo.length; i++)
            $total1 += parseFloat($cuerpo[i]["LineTotal"]);
        if ($dp > 0) {
            $dm = Calculo.round((($dp * $total1) / 100));
        }
        for (let i = 0; i < $cuerpo.length; i++) {
            $cuerpo[i]["ponderado"] = Calculo.round(parseFloat($cuerpo[i]["LineTotal"]) * 100 / $total1);
            $cuerpo[i]["descuento"] = Calculo.round((parseFloat($cuerpo[i]["ponderado"]) / 100) * $dm);
            let $ldp = parseFloat($cuerpo[i]["DiscTotalPrcnt"]);
            let $ldm = parseFloat($cuerpo[i]["DiscTotalMonetary"]) + parseFloat($cuerpo[i]["descuento"]);
            console.log("CUERPO");
            console.log($cuerpo[i]);
            let $nuevalinea: any = Calculo.ICELINEA($cuerpo[i], $cuerpo[i]["Price"], $cuerpo[i]["Quantity"], $ldm, $ldp, true);
            console.log($nuevalinea);
            $cuerpo[i]["nuevototal"] = $nuevalinea.total;
            $cuerpo[i]["icetp"] = $nuevalinea.icetp;
            $cuerpo[i]["icete"] = $nuevalinea.icete;
            $cuerpo[i]["ICEt"] = $nuevalinea.ICEt;
            $cuerpo[i]["ICEe"] = $nuevalinea.ICEe;
            $cuerpo[i]["ICEp"] = $nuevalinea.ICEp;
            $total2 = ($nuevalinea.total) + ($total2);
        }
        $cabecera["dp"] = $cabecera["TotalDiscPrcnt"];
        $cabecera["dm"] = $cabecera["TotalDiscMonetary"];
        $cabecera["TotalDiscMonetary"] = 0;
        $cabecera["TotalDiscPrcnt"] = 0;
        $cabecera["nuevoTotal"] = $total2;
        return { cabecera: $cabecera, cuerpo: $cuerpo }
    }

    public static ICELINEA($producto, $precio, $cantidad = 1, $descuentom = 0, $descuentop = 0, x = false) {
        let precio = 0;
        let ice = $producto["ICEt"];
      
        let icep = $producto["ICEp"];
        let icee = $producto["ICEe"];
        let totalLinea = $precio * $cantidad;
        if ($descuentop > 0 && $descuentop != 0) {
            $descuentom = 0;
        }
        totalLinea = totalLinea - $descuentom;
        totalLinea = totalLinea - (totalLinea * $descuentop / 100);
        let iva = Calculo.round(totalLinea * 0.13);
        let neto = Calculo.round(totalLinea) - Calculo.round(iva);
        
        let c_icep = 0;
        let c_icee = 0;
        if (ice !== "N") {
            precio = Calculo.round($precio);
            if (ice == "E") {
                c_icee = $cantidad * icee;
                precio = Calculo.round(totalLinea) + Calculo.round(c_icep) + Calculo.round(c_icee);
            } else if (ice == "P") {
                
                c_icep = icep * neto;
                precio = Calculo.round(totalLinea) + Calculo.round(c_icep) + Calculo.round(c_icee);
            } else {
                
                c_icep = icep * neto;
                c_icee = $cantidad * icee;
                precio = Calculo.round(totalLinea) + Calculo.round(c_icep) + Calculo.round(c_icee);
            }
        } else {
            precio = Calculo.round(totalLinea);
        }
        let salida = {
            total: Calculo.round(precio),
            icetp: Calculo.round(c_icep),
            icete: Calculo.round(c_icee),
            ICEt: ice,
            ICEp: Calculo.round(icep),
            ICEe: Calculo.round(icee)
        };
        return salida;
    }

    public static total(cantidad: number, presio: number) {
        return (cantidad * presio);
    }

    public static resumenTotal(cantidad: number, presio: number, descuento = 0) {
        let total = (cantidad * presio);
        let totalDescuento = total - descuento;
        return Calculo.round(totalDescuento);
    }
}

