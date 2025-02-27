


class Iobject {
    Code: String;
    Description: String;
    TerritoryID: String;
    U_observacion: String;
    cabezera_tipo: String;
    cantidad_compra: String;
    cantidad_maxima_compra: String;
    cantidad_regalo: String;
    extra_descuento: String;
    fecha_fin: String;
    fecha_inicio: String;
    grupo_cliente: String;
    idTerritorio: String;
    idUser: String;
    id_bonificacion_cabezera: String;
    id_cabezera_tipo: String;
    id_regla_bonificacion: String;
    maximo_regalo: String;
    monto_total: String;
    nombre: String;
    opcional: String;
    territorio: String;
    tipo: String;
    unindad_compra: String;
    unindad_regalo: String;
    productosCompra: [];
    codigo_canal: String;
}
const BonoFactory = {

    createBono: function (type, object: Iobject, items, dataCliente) {
        // if (object.Code == "sdds") {
        //     return new uno(type, object, items, dataCliente);
        // }

        items = items.filter((value) => {
            return value.bonificacion != 1
        })
        console.log("type ", type);

        console.log("object ", object);

        console.log("items ", items);

        console.log("dataCliente ", dataCliente);


        switch (type.toLowerCase()) {
            // case "1":
            //     return new uno(object, items, dataCliente);
            // case "2":
            //     return new dos(object, items, dataCliente);
            case "3":
                return new tres(object, items, dataCliente);
            // case "4":
            //     return new cuatro(object, items, dataCliente);
            // case "5":
            //     return new cinco(object, items, dataCliente);
            // case "6":
            //     return new seis(object, items, dataCliente);
            // case "7":
            //     return new siete(object, items, dataCliente);
            // case "8":
            //     return new ocho(object, items, dataCliente);
            // case "9":
            //     return new nueve(object, items, dataCliente);
            default:
                return null;
        }
    }
};

function tres(object: Iobject, items, client) {
    this.REGLA_BONO = "TRES";

    console.log("EL OBJETO ES ",object);
    console.log("EL cliente ES ",client);
    console.log("EL items ES ",items);


    this.items = items;

    this.client = client;
    this.bonoRefact = object;
    this.isValidGruopClient = validGrupoCliente(object.grupo_cliente, client.GroupName);
    this.isValidCodeCanal = validCanalCliente(object.codigo_canal, client.codeCanal);
    this.isValidGroupProducto = validGrupoProductos(object.tipo, items, object.productosCompra);

    console.log("resultado");

    console.log(this.isValidGruopClient);
    console.log(this.isValidCodeCanal);
    console.log(this.isValidGroupProducto);

    this.sumaItemsTotal = items.reduce((sum, value) => (sum + Number(value.LineTotalPay)), 0); //+ Number(value.ICEe) + Number(value.ICEp)
    console.log("    this.sumaItemsTotal  ", this.sumaItemsTotal)
    this.sumaItemsTotal = this.sumaItemsTotal.toFixed(2);
    this.OPCIONAL = object.opcional;
    this.cumpleMonto = Number(this.sumaItemsTotal) >= Number(object.monto_total);
    console.log(" this.cumpleMonto ", this.cumpleMonto)





    this.showModalDesc = this.isValidGruopClient && this.isValidGroupProducto && this.cumpleMonto && this.isValidCodeCanal;
    if (this.showModalDesc) {
        console.log("DEVD CUMPLE TODAS LAS CONDICIONES");
    }

}

function validGrupoCliente(BonoGrupoCliente, clientGroupName) {
    if (BonoGrupoCliente == 0) {
        return true;
    } else {
        if (BonoGrupoCliente == clientGroupName) {
            return true;
        } else {

        } return false;

    }

}
function validCanalCliente(BonoCanal, clientGroupName) {
    if (BonoCanal == 0 || BonoCanal == null || BonoCanal == undefined) {
        return true;
    } else {
        if (BonoCanal == clientGroupName) {
            return true;
        } else {

        } return false;

    }

}

function validGrupoProductos(tipo, items, productosCompra) {
    console.log("carrito()", items);
    console.log("bonoCompras()", productosCompra);
    let validData = [];
    let returnValid: any = false;
    if (tipo == "PRODUCTOS ESPECIFICOS") {

        for (let value of productosCompra) {
            console.log("CODIGO A BUSCAR", value.code_compra);
            for (let it of items) {
                if (value.code_compra == it.ItemCode) {
                    console.log(" CUMPLE ", value.code_compra);
                    returnValid = true;
                } else {
                    console.log("No CUMPLE ", value.code_compra);
    
                }
            }
            /*let filterItemValid = items.filter(x => x.ItemCode == value.code_compra);

            if (filterItemValid.length > 0) {
                console.log(" CUMPLE ", value.code_compra);
                returnValid = true;
            } else {
                console.log("No CUMPLE ", value.code_compra);

            }*/
        }
        // return validData;
    } else {//por grupo productos
        for (var valueItem of items) {
            for (var valueCompra of productosCompra) {
                if (valueItem.GroupName == valueCompra.producto_nombre_compra) {
                    validData.push(valueCompra);
                }
            }
        }
        //  return validData;


        for (let value of productosCompra) {

            for (let it of items) {
                if (value.code_compra == it.ItemCode) {
                    console.log(" CUMPLE ", value.code_compra);
                    returnValid = true;
                } else {
                    console.log("No CUMPLE ", value.code_compra);
    
                }
            }


            /*let filterItemValid = items.filter(x => x.GroupName == value.producto_nombre_compra);
            if (filterItemValid.length > 0) {
                console.log(" CUMPLE ", value.code_compra);
                returnValid = true;
            } else {
                console.log("No CUMPLE ", value.code_compra);

            }*/
        }


    }
    return returnValid;
}


export default BonoFactory;