import { Injectable } from '@angular/core';
import { Descuentos } from "../models/descuentos";

@Injectable({
    providedIn: 'root'
})
export class UtilsService {

    constructor() {

    }

    sum(val1: number, val2: number) {
        return (val2 + val1);
    }

    //DescuentosSap(Cliente:String, Producto:String, ListaPrecio:number, Cantidad:number, Fecha:Date, GrupoProducto:number, Fabricante:number, Propiedades:String, GrupoCliente:number)  
    async DescuentosSap(Cliente: String, Producto: String, ListaPrecio: number, Cantidad: number, GrupoProducto: number, Fabricante: number, Propiedades: String, GrupoCliente: number) {

        console.log("IBJ - Recuperando descuentos de SAP");
        let Valor = 0.0;
        let Descuento = new Descuentos();

        //RECUPERAR GRUPO DE CLIENTE
        let datos: any = await Descuento.selectDatosDescuentosEspeciales("select GroupCode  from clientes where CardCode = '" + Cliente + "' ");
        for await (let item of datos) {
            GrupoCliente = item.GroupCode;
        }

        //RECUPERAR GRUPO DE PRODUCTO
        datos = await Descuento.selectDatosDescuentosEspeciales("select ItemsGroupCode from productos where ItemCode = '" + Producto + "' ");
        for await (let item of datos) {
            GrupoProducto = item.ItemsGroupCode;
        }

        console.log("Cliente :", Cliente);
        console.log("Producto :", Producto);
        console.log("ListaPrecio :", ListaPrecio);
        console.log("Cantidad :", Cantidad);
        console.log("GrupoProducto :", GrupoProducto);
        console.log("Fabricante :", Fabricante);
        console.log("Propiedades :", Propiedades);
        console.log("GrupoCliente :", GrupoCliente);


        /*PRECIOS ESPECIALES*/
        //PRECIO ESPECIAL - CANTIDAD
        let variable: any = await Descuento.selectDatosDescuentosEspeciales("SELECT IFNULL(DESCUENTO,0.00) DESCUENTO FROM descuentos where CardCode = '" + Cliente + "' and LISTA_PRECIO = '" + ListaPrecio + "' and ItemCode = '" + Producto + "' and CANTIDAD <= '" + Cantidad + "' AND PRIORIDAD = '1' AND LINEA = '1'  order by CANTIDAD, DESCUENTO");
        for await (let item of variable) {
            Valor = item.DESCUENTO;
        }
        //PRECIO ESPECIAL
        if (Valor == 0) {
            console.log("CONSOLE: CONSULTA DESCUENTOS ESPECIALES LINEA 2 Y 3 54");
            variable = await Descuento.selectDatosDescuentosEspeciales(" SELECT IFNULL(DESCUENTO,0.00) as DESCUENTO FROM descuentos where CardCode = '" + Cliente + "' and LISTA_PRECIO = '" + ListaPrecio + "' and ItemCode = '" + Producto + "' AND PRIORIDAD = '1' AND LINEA IN (2,3) order by CANTIDAD,  LINEA desc");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }
        /*GRUPO DE DESCUENTOS*/
        /*CLIENTE ESPECIFICO*/
        //GRUPO DE DESCUENTOS, GRUPO ARTICULOS
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales(" SELECT ifnull( DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where CardCode = '" + Cliente + "' and GRUPO_PRODUCTO = '" + GrupoCliente + "' AND PRIORIDAD = '2' AND LINEA = 1 and tipo = 1  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, GRUPO PROPIEDADES
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where CardCode = '" + Cliente + "' " + Propiedades + "  AND PRIORIDAD = '2' AND LINEA = 1 and tipo = 2  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, GRUPO FABRICANTE
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull( DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where CardCode = '" + Cliente + "' and FABRICANTE = '" + Fabricante + "'  AND PRIORIDAD = '2' AND LINEA = 1 and tipo = 3  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, ARTICULOS
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where CardCode = '" + Cliente + "' and ItemCode = '" + Producto + "' AND PRIORIDAD = '2' AND LINEA = 1 and tipo = 4  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        /*GRPO DE CLIENTES*/
        //GRUPO DE DESCUENTOS, GRUPO ARTICULOS
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull( DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where GRUPO_CLIENTE = '" + GrupoCliente + "' and GRUPO_PRODUCTO = '" + GrupoProducto + "' AND PRIORIDAD = '2' AND LINEA = 3 and tipo = 1 order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, GRUPO PROPIEDADES
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where GRUPO_CLIENTE = '" + GrupoCliente + "' " + Propiedades + "  AND PRIORIDAD = '2' AND LINEA = 3 and tipo = 2  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, GRUPO FABRICANTE
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where GRUPO_CLIENTE = '" + GrupoCliente + "' and FABRICANTE = '" + Fabricante + "'  AND PRIORIDAD = '2' AND LINEA = 3 and tipo = 3  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, ARTICULOS
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE FROM descuentos where GRUPO_CLIENTE = '" + GrupoCliente + "' and ItemCode = '" + Producto + "' AND PRIORIDAD = '2' AND LINEA = 3 and tipo = 4  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        /*TODOS LA OCRD*/
        //GRUPO DE DESCUENTOS, GRUPO ARTICULOS
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where GRUPO_PRODUCTO = '" + GrupoProducto + "' AND PRIORIDAD = '2' AND LINEA = 4 and tipo = 1  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, GRUPO PROPIEDADES
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where   PRIORIDAD = '2' " + Propiedades + " AND LINEA = 4 and tipo = 2  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, GRUPO FABRICANTE
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where  FABRICANTE = '" + Fabricante + "'  AND PRIORIDAD = '2' AND LINEA = 4 and tipo = 3  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //GRUPO DE DESCUENTOS, ARTICULOS
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO , CANTIDAD, CANTIDAD_LIBRE, MAXIMO_LIBRE  FROM descuentos where  ItemCode = '" + Producto + "' AND PRIORIDAD = '2' AND LINEA = 4 and tipo = 4  order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        /*DESCUENTOS POR PERIODO Y CANTIDAD*/
        //DESCUENTOS POR PERIODO Y CANTIDAD, CANTIDAD
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO  FROM descuentos where LISTA_PRECIO = '" + ListaPrecio + "' and ItemCode = '" + Producto + "' and CANTIDAD <= '" + Cantidad + "' AND PRIORIDAD = '3' AND LINEA = '1' order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }

        //DESCUENTOS POR PERIODO Y CANTIDAD
        if (Valor == 0) {
            variable = await Descuento.selectDatosDescuentosEspeciales("SELECT ifnull(DESCUENTO,0.00) as DESCUENTO  FROM descuentos where LISTA_PRECIO = '" + ListaPrecio + "' and ItemCode = '" + Producto + "' AND PRIORIDAD = '3' AND LINEA IN (2) order by CANTIDAD,  DESCUENTO");
            for await (let item of variable) {
                Valor = item.DESCUENTO;
            }
        }


        console.log("IBJ, DESCUENTO: " + Valor);
        return Valor;
    }

}