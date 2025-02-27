import { Injectable } from '@angular/core';
import { HTTP } from '@ionic-native/http/ngx';
import { ConfigService } from "../models/config.service";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { Documentos } from "../models/documentos";
import { Detalle } from "../models/detalle";
import { Clientes } from "../models/clientes";
import { Contactos } from "../models/contactos";
import { Pagos } from "../models/pagos";
import { Visitas } from "../models/visitas";
import * as moment from 'moment';
import { FacturasPagos } from '../models/facturasPagos';
import { environment } from '../../environments/environment';
import { DataService } from './data.service';


@Injectable({
    providedIn: 'root'
})
export class DocumentService {
    public path: any;
    public arraux: any;

    constructor(private http: HTTP, private configService: ConfigService, private dataService: DataService, private spinnerDialog: SpinnerDialog) {
    }

    async downloadDeudasCliente(CardCode) {
        this.spinnerDialog.show(undefined, undefined, true);
        try {
            let dataDocsClient: any = await this.dataService.servisDownloadDocsSap(CardCode);
            // console.log("dataDocsClient ", dataDocsClient);
            let xJsondocs = JSON.parse(dataDocsClient.data);
            //console.log("sincronizacion prod");
            console.log("xJson.respuesta", xJsondocs);
            console.table(xJsondocs.respuesta);

            let documentos: any = new Documentos()
            if (xJsondocs.respuesta) {

                let res = await documentos.deletePagoscuota(CardCode);
                console.log(res);

                for await (let documento of xJsondocs.respuesta) {
                    if (documento.DocType === "DFA") {
                        let dataInsert: any = [];
                        let newData: any = {};
                        let idLast = 0;
                        newData.origen = 'outer';
                        newData.clone = '0';
                        // console.log("newData ", newData)
                        newData.cod = documento.DocNum;
                        newData.DocEntry = documento.DocEntry;
                        newData.DocNum = documento.DocNum;
                        newData.DocType = documento.DocType;
                        newData.DocDate = documento.DocDate;
                        newData.DocDueDate = documento.DocDueDate;
                        newData.CardCode = documento.CardCode;
                        newData.CardName = documento.CardName;
                        newData.DocTotal = documento.DocTotal;
                        newData.PaidtoDate = documento.PaidtoDate;
                        newData.Currency = documento.Currency;

                        newData.DateUpdate = documento.DateUpdate;
                        newData.U_4NIT = "";
                        newData.U_4RAZON_SOCIAL = "";
                        newData.PriceListNum = documento.PriceListNum;

                        newData.DateUpdate = documento.DateUpdate;
                        newData.descuento = documento.descuento;

                        newData.tipodescuento = 0;

                        newData.ReserveInvoice = documento.ReserveInvoice;
                        newData.Saldo = documento.Saldo;
                        newData.Pendiente = documento.Pendiente;
                        newData.centrocosto = documento.centrocosto;
                        newData.unidadnegocio = documento.unidadnegocio;
                        newData.reimpresiones = '0';
                        newData.U_XMB_AUX1 = documento.U_XMB_AUX1;
                        newData.grupoproductodocificacion = documento.grupoproductodocificacion;
                        newData.DocEntry = documento.DocEntry;
                        newData.Cuota = documento.Cuota;
                        newData.vendedor = documento.vendedor;

                        dataInsert.push(newData);
                        let rx: any = await documentos.existeDocumento(newData.cod);
                        console.log("rx existe? ", newData.cod)
                        // if (rx[0].exits > 0) {
                        //     //TODO: NO ELIMINAR POR QUE NO SE ACTUALIZA LO PAGADO ANTERIORMENTE
                        //     // let returnD = await documentos.deleteDocumento(newData.cod);
                        //     // console.log("return ", returnD)
                        // } else {
                        //     // if (rx[0].exits == 0) {
                        //     console.log("dataInsert NO EXISTE ", newData.cod, dataInsert);

                        idLast = await documentos.insertAllSap(dataInsert[0]);
                        // }
                    }

                }
                this.spinnerDialog.hide();
            } else {
                console.log("cliente no encontrado en sap")
                this.spinnerDialog.hide();
            }



        } catch (error) {
            console.log("error try ", error);

            this.spinnerDialog.hide();
        }
    }
}