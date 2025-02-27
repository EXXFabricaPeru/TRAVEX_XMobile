
export interface IBoniCabezera {
    id: string;
    Code: string;
    nombre: string;
    fecha_inicio: Date;
    fecha_fin: Date;
    cabezera_tipo: CabezeraTipo;
    id_cabecera_tipo: string;
    tipo: string; //Tipo;
    cantidad_compra: number;
    unindad_compra: string;
    monto_total: number;
    cantidad_maxima_compra: number;
    cantidad_regalo: number;
    unindad_regalo: string;
    maximo_regalo: number;
    grupo_cliente: string;
    extra_descuento: string;
    porcentaje: string;
    opcional: Opcional;
    tipo_regla_compra: TipoReglaCompra;
    detalle_especifico: DetalleEspecifico;
    id_regla_bonificacion: number;
    codigo_canal: string;
    U_observacion: string;
    TerritoryID: string;
    idUser: number;
    Description: string;
}


export enum CabezeraTipo {
    Bonificacion = "BONIFICACION",
    DescuentoDocumento = "DESCUENTO DOCUMENTO",
    DescuentoLinea = "DESCUENTO LINEA",
}

export enum DetalleEspecifico {
    PorCantidadEspecifica = "Por cantidad especifica",
    PorCantidadGlobal = "Por cantidad global",
    SimpleGlobalCABECERA = "Simple global - CABECERA",
    SimpleGlobalLINEA = "Simple global LINEA",
    SimplePorcentaje = "Simple porcentaje",
}

export enum Opcional {
    Obligatorio = "OBLIGATORIO",
    Opcional = "OPCIONAL",
}

// export enum Tipo {
//     ProductosEspecificos = "PRODUCTOS ESPECIFICOS",
// }

export enum TipoReglaCompra {
    Global = "GLOBAL",
    PorDetalle = "POR DETALLE",
}

export interface IBoniCompras {
    id: string;
    code_compra: string;
    producto_nombre_compra: string;
    id_bonificacion_cabezera: string;
    U_regla: string;
    producto_cantidad: string;
    estado: null | string;
}

export interface IBoniRegalos {
    id: string;
    code_regalo: string;
    producto_nombre_regalo: string;
    id_bonificacion_cabezera: string;
    U_regla: string;
}

