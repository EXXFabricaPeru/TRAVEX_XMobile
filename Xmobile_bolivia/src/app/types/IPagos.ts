import { ICliente } from "./IClientes";

export interface IPagos {
    nro_recibo: string,
    correlativo: number,
    usuario: number,
    documentoId: string | null,
    fecha: string,
    hora: string,
    monto_total: number,
    tipo: 'deuda' | 'factura' | 'cuenta',
    otpp: 1 | 2 | 3,
    tipo_cambio: number,
    moneda: string,
    cliente_carcode: string,
    razon_social: string,
    nit: string,
    estado: States,
    equipo: States,
    latitud: States,
    longitud: States,
    cancelado: 0 | 1 | 2 | 3,
    mediosPago: IMediosPagos[],
    facturaspago?: IFacturasPagos[],
    camposusuario: any,
}

export interface IMediosPagos {
    nro_recibo: string,
    formaPago: 'PEF' | 'PCC' | 'PBT' | 'PCH',
    monto: number,
    fecha: string,
    numCheque?: string,
    numComprobante?: string,
    numTarjeta?: string,
    bancoCode?: string,
    cambio?: number,
    monedaDolar?: number,
    monedaLocal?: number,
    centro?: string,
    baucher?: string,
    NumeroTarjeta?: string,
    NumeroID?: string,
    checkdate?: string,
    transferencedate?: string,
    CreditCard?: number,
    camposusuario: any,
    emitidoPor?: String,
    tipoCheque?: string,
    dateEmision?: string,

}

export interface IFacturasPagos {
    vendedor: string;
    nro_recibo: string,
    clienteId: string,
    documentoId: string,
    docentry: string,
    monto: number,
    CardName: string,
    saldo: number,
    nroFactura: string,
    DocTotal: number,
    cuota: number
}

export type States = 0 | 1 | 2 | 3; // 1 movil, 2 mid, 3 sap , 6 anulado, 0 error 

export interface httpResponse {
    mensaje: string,
    codigo: number,
    estado: number,
    data?: any
}
export interface relationalTable
{
    table: string,
    relationshipFieldPrin: string,
    relationshipFieldSeg: string
}

export interface IDataPagoPdf  {
    "fechahora": string,
    "tipodocumento":string,
    "iddocumento":string,
    "usuario": string,
    "equipo": string,
    "dataPago": IPagos,
    "dataCliente"?:ICliente
};