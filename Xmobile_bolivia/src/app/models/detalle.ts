import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import { Combos } from "./combos";
import { Calculo } from "../utilsx/calculo";
import { Lotes } from "./lotes";
import { Seriesproductos } from "./seriesproductos";
import { Productosalmacenes } from "./productosalmacenes";
import { Productos } from "./productos";
import { Lotesproductos } from "./lotesproductos";
import { GlobalConstants } from "../../global";
import { from } from "rxjs";
//import { Console } from "console";

export class Detalle extends Databaseconf {
    public configService: ConfigService;

    public async detalledoAddstockCommited(id: any) {
        let sqlresp = await this.showTable(id);
        let productosalmacenes = new Productosalmacenes();
        return await productosalmacenes.updateCompometidoProdutosalmacenes(sqlresp);
    }

    public detalledocremovestockCommited(id: any) {
        return new Promise(async (resolve, reject) => {
            let sqlresp: any = await this.showTable(id);
            for (let sqlrx of sqlresp) {
                let productosalmacenes = new Productosalmacenes();
                await productosalmacenes.addUpdateCompometidoProdutosalmacenes(sqlrx);
            }
            resolve(true);
        });
    }

    public async detalledocremovestock(id: any) {
        let sqlresp = await this.showTable(id);
        let productosalmacenes = new Productosalmacenes();
        return await productosalmacenes.updateprodcualmacenes(sqlresp);
    }

    public detalledocAddstock(id: any) {
        return new Promise(async (resolve, reject) => {
            let sqlresp: any = await this.showTable(id);
            for (let sqlrx of sqlresp) {
                let productosalmacenes = new Productosalmacenes();
                await productosalmacenes.addUpdateprodcualmacenes(sqlrx);
                let lote = new Lotes();
                await lote.deleteLote(sqlrx.id);
            }
            resolve(true);
        });
    }

    public async showTable(id: any) {
        let sql = `SELECT * FROM detalle WHERE idDocumento = '${id}' ;`;//AND bonificacion = 0
        console.log(sql);
        return await this.queryAll(sql);
    }

    public async showTable2(id: any) {
        let sql = `SELECT * FROM detalle;`;//AND bonificacion = 0
        console.log(sql);
        return await this.queryAll(sql);
    }

    public async Deletedetalleinicial(id: any) {
        let sql = `Delete from detalle WHERE idDocumento = '${id}' ;`;
        console.log(sql);
        return await this.queryAll(sql);
    }

    /***********/
    public async find(id: string) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT d.*, round(((d.Quantity * d.Price) - d.U_4DESCUENTO + d.ICEe + d.ICEp ),2) AS totalPagar FROM detalle d WHERE d.idDocumento = '${id}';`;
            console.log("consulta", sql);

            this.executeSQL(sql).then(async (data: any) => {
                let arr = [];
                let lotes = new Lotes();
                let series = new Seriesproductos();
                for (let i = 0; i < data.rows.length; i++) {
                    let o = data.rows.item(i);
                    console.log("datos ", o);
                    o.lotes = await lotes.selectAll(o.id);
                    o.LineTotalPay = o.totalPagar;
                    o.LineTotal = o.totalPagar;
                    o.series = await series.selectserie(o.id);
                    arr.push(o);
                }
                console.log("sql ", sql);
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public estrucData(data: any, contador: number) {
        console.log("DEVD estrucData() ", data);
        return {
            BaseId: data.BaseId,
            ItemCode: data.ItemCode,
            ItemName: data.Dscription,
            cantidad: data.Quantity,
            price: data.Price,
            Currency: data.Currency,
            LineTotal: parseFloat(data.LineTotal),
            WhsCode: data.WhsCode,
            unidadID: data.unidadid,
            descuento: data.U_4DESCUENTO,
            DocEntry: '',
            DocNum: '',
            LineNum: parseInt(data.LineNum), //contador,
            BaseType: data.BaseType,
            BaseEntry: data.BaseEntry,
            combos: data.combos,
            BaseLine: parseInt(data.LineNum),
            LineStatus: '',
            GrossBase: '',
            OpenQty: data.OpenQty,
            DiscPrcnt: parseFloat(data.DiscPrcnt),
            CodeBars: '',
            PriceAfVAT: '',
            TaxCode: '',
            U_4LOTE: '',
            tc: '',
            idProductoPrecio: '',
            ProductoPrecio: '',
            porcentajedata: parseFloat(data.DiscTotalPrcnt),
            LineTotalPay: parseFloat(data.LineTotalPay),
            DiscTotalPrcnt: parseFloat(data.DiscTotalPrcnt),
            DiscTotalMonetary: parseFloat(data.DiscTotalMonetary),
            icett: data.icett,
            icete: data.icete,
            icetp: data.icetp,
            ICEp: data.ICEp,
            ICEe: data.ICEe,
            U_4DESCUENTO: parseFloat(data.DiscTotalMonetary),
            ICEt: data.ICEt,
            bonificacion: data.bonificacion,
            IdBonfAut: data.IdBonfAut,
            GroupName: data.GroupName,
            codeBonificacionUse: data.codeBonificacionUse,
            codeMid: data.codeBonificacionUse,
            XMPORCENTAJECABEZERA: data.XMPORCENTAJECABEZERA,
            XMPROMOCIONCABEZERA: data.XMPROMOCIONCABEZERA,

            BaseQty: data.BaseQty
        };
    }

    public clonar(id: any, idnuevo: any, clon: any, doctype: string, factype: number) {
        console.log("DEVD id ", id);
        console.log("DEVD idnuevo ", idnuevo);
        let doctypeAfter = id.substring(0, 3);
        console.log("DEVD doctypeAfter ", doctypeAfter);
        console.log("DEVD clon ", clon);
        console.log("DEVD doctype ", doctype);
        console.log("DEVD factype ", factype);
        return new Promise(async (resolve, reject) => {
            let pedidos: any = await this.findAll(id);
            console.log("DEVD pedidos ", pedidos);

            let contador = pedidos.length;
            for await (let data of pedidos) {
                console.log("---> a clonar ", data);
                if (clon == false) {
                    let copiados: any = await this.findAllToClone(id, data.ItemCode, data.LineNum);
                    console.log("DEVD each copiados  ", copiados);
                    if (copiados.length > 0) {
                        data.Quantity = data.Quantity - copiados[0]["copiado"];
                        if (data.Quantity > 0) {
                            data.LineTotal = data.Quantity * data.Price;
                            data.LineTotalPay = data.Quantity * data.Price;
                            data.total = data.Quantity * data.Price;
                            data.totalneto = data.Quantity * data.Price;
                            data.preciouni = data.Quantity * data.Price;
                            data.combos = data.combos;
                            data.IdBonfAut = data.IdBonfAut;
                        }
                    }
                }
                if (data.Quantity > 0) {
                    let det = this.estrucData(data, contador);
                    console.log("DEVD data return   ", data);
                    console.log("DEVD det return estrucData  ", det);
                    if (doctype == doctypeAfter) {
                        console.log("DEVD logica de reset desc");
                        if (det.codeBonificacionUse != "0" && det.codeMid != "0" && det.porcentajedata > 0) {
                            console.log("DEVD ingresa a logica BONO desc");
                            det.codeBonificacionUse = "0";
                            det.codeMid = "0";
                        }
                        if (data.bonificacion != 1) {
                            //  if (data.bonificacion == 2) {

                            det.DiscTotalMonetary = 0;
                            det.DiscTotalPrcnt = 0;
                            det.descuento = 0;
                            det.DiscTotalMonetary = 0;
                            det.XMPORCENTAJECABEZERA = 0;
                            det.XMPROMOCIONCABEZERA = 0;
                            det.bonificacion = 0;
                            det.DiscPrcnt = 0;
                            det.porcentajedata = 0;

                            if (det.porcentajedata > 0) {
                                console.log("DEVD ingresa a logica BONO desc 2 ");
                            }
                            if (Number(det.icete) > 0) {
                                console.log("DEVD modificar ICEe ", det.ICEe);
                            }
                            if (Number(det.ICEp) > 0) {
                                console.log("DEVD modificar ICEp por el descuento ", det.ICEp);

                                det.ICEp = ((det.cantidad * Number(det.price)) * 0.87 * Number(det.icetp) / 100).toFixed(2);
                                //  ICEp=ROUND(((Quantity * Price)-U_4DESCUENTO) * 0.87 * icetp /100, 1) 
                            }
                            //  }
                            /**
                            * CAMBIOS PARA PEDIDO DUPLICADO */
                            det.BaseType = 0;
                            det.BaseEntry = 0;
                            det.BaseLine = 0;
                            if (doctype == 'DFA'){
                                await this.insertLocal(det, idnuevo, 0, doctype, factype);
                                contador--;
                            }else{
                                if(det.IdBonfAut == 0){
                                    det.LineTotalPay = det.LineTotal;
                                    await this.insertLocal(det, idnuevo, 0, doctype, factype);
                                    contador--;
                                }
                            } 
                        }
                    } else {
                        console.log("DEVD logica normal");
                        await this.insertLocal(det, idnuevo, 1, doctype, factype);
                        contador--;
                    }
                }
            }
            resolve(true);
        });
    }

    public async insert(objeto: any, id: number, x = 0, doctype: string, factype: number) {
        console.log("insert object ", objeto);
        let unidad = '';
        if (objeto.combos == 0 || objeto.combos == "0") unidad = objeto.unidadID;
        let xd: number;
        console.log("DEVD x", x);
        if (x == 1) {
            xd = objeto.LineNum;
        } else {
            let iteracion: any = await this.docCount(id);
            console.log("DEVD docCount ", iteracion);
            xd = iteracion.length;
        }
        console.log("DEVD xd insert line num ", xd)
        if (!objeto.XMPORCENTAJECABEZERA) {
            objeto.XMPORCENTAJECABEZERA = 0;
        }
        if (!objeto.XMPROMOCIONCABEZERA) {
            objeto.XMPROMOCIONCABEZERA = 0;
        }
        if (objeto.BaseQty == "NaN" || objeto.BaseQty == "undefined") {
            objeto.BaseQty = 1;
        }
        if (!objeto.descuento) {
            objeto.descuento = objeto.U_4DESCUENTO;
        }

        let fecha = this.timeStamp();
        console.log("guardar Number(objeto.LineTotalPay).toFixed(2) ", Number(objeto.LineTotalPay).toFixed(2));
        let sql = `INSERT INTO detalle VALUES (NULL, '${objeto.DocEntry}', '${objeto.DocNum}', '${xd}', '${objeto.BaseType}', '${objeto.BaseEntry}', 
            '${objeto.BaseLine}', '${objeto.LineStatus}', '${objeto.ItemCode}', '${objeto.ItemName}', '${objeto.cantidad}', '${objeto.OpenQty}', '${objeto.price}', '${objeto.Currency}',
             ${objeto.DiscPrcnt}, ${objeto.LineTotal}, '${objeto.WhsCode}', 
            '${objeto.CodeBars}', '${objeto.PriceAfVAT}', '${objeto.TaxCode}', '${objeto.descuento ? objeto.descuento : 0}','${objeto.porcentajedata}', '${objeto.U_4LOTE}', '${objeto.GrossBase}', '${id}',
            '${fecha}', '${unidad}', '${objeto.tc}', '${id}', '${objeto.idProductoPrecio}', '${objeto.ProductoPrecio}', ${Number(objeto.LineTotalPay).toFixed(2)}, ${objeto.DiscTotalPrcnt}, 
            ${objeto.DiscTotalMonetary}, '${objeto.icett}', '${objeto.icete}', '${objeto.icetp}', '${objeto.ICEt}', '${objeto.ICEe}', '${objeto.ICEp}', 
            ${objeto.bonificacion},'${objeto.combos}','','','','','','','','','${objeto.BaseId}','${objeto.IdBonfAut}', '${objeto.GroupName}', '${objeto.codeMid}', ${objeto.XMPORCENTAJECABEZERA}, ${objeto.XMPROMOCIONCABEZERA},'${objeto.BaseQty}', 0,'','');`;
        console.log("sql detalle ", sql);

        let r = await this.executeRaw(sql);
        console.log("respuesta",r);
        let productoalmacenes = new Productosalmacenes();
        if ((doctype == 'DFA' && factype == 0) || doctype == 'DOE') await productoalmacenes.updateprodcualmacenes(objeto);
        if (doctype == 'DOP') await productoalmacenes.updateCompometidoProdutosalmacenes(objeto);
        return r;
    }


    public async insertDoc(objeto: any) {
        let sql = 'INSERT INTO detalle VALUES ';
        for await (let item of objeto) {
            sql += `(
            NULL,
            '${item.DocEntry}',
            '${item.DocNum}',
            '${item.LineNum}',
            '${item.BaseType}',
            '${item.BaseEntry}',
            '${item.BaseLine}',
            '${item.LineStatus}',
            '${item.ItemCode}',
            '${item.Dscription}',
            '${item.Quantity}',
            '${item.OpenQty}',
            '${item.Price}',
            '${item.Currency}',
            '${item.DiscPrcnt}',
            '${item.LineTotal}',
            '${item.WhsCode}',
            '${item.CodeBars}',
            '${item.PriceAfVAT}',
            '${item.TaxCode}',
            '${item.U_4DESCUENTO}',
            '${item.XMPORCENTAJE}',
            '${item.U_4LOTE}',
            '${item.GrossBase}',
            '${item.idDocumento}',
            '${item.fechaAdd}',
            '${item.unidadid}',
            '${item.tc}',
            '${item.idCabecera}',
            '${item.idProductoPrecio}',
            '${item.ProductoPrecio}',
            '${item.LineTotalPay}',
            '${item.DiscTotalPrcnt}',
            '${item.DiscTotalMonetary}',
            '${item.icett}',
            '${item.icete}',
            '${item.icetp}',
            '${item.ICEt}',
            '${item.ICEe}',
            '${item.ICEp}',
            '${item.bonificacion}',
            '${item.combos}',
            '${item.PriceAfterVAT}',
            '${item.Rate}',
            '${item.TaxTotal}',
            '${item.User}',
            '${item.Status}',
            '${item.DateUpdate}',
            '${item.Entregado}',
            '${item.Serie}',
            '${item.BaseId}',
            '${item.IdBonfAut}',
            '${item.GroupName}',
            '${item.codeBonificacionUse}',
            '${item.XMPORCENTAJECABEZERA}',
            '${item.XMPROMOCIONCABEZERA}',
            '${item.BaseQty}',
            '${item.grupoproductodocificacion}',
            '${item.SumBoniLin}',''),`;
        }
        console.log("sqlx  ", sql);
        if (objeto.length > 0) {
            let sqlx = sql.substring(0, sql.length - 1);
            console.log("******* QUERY DETALLE ", sqlx);
            let f = sqlx + ';';
            return this.executeSQL(f);
        } 
        let productoalmacenes = new Productosalmacenes();
        for await (let item of objeto) {
            if ((item.doctype == 'DFA' && item.factype == 0) || item.doctype == 'DOE') await productoalmacenes.updateprodcualmacenes(item);
            if (item.doctype == 'DOP') await productoalmacenes.updateCompometidoProdutosalmacenes(item);
        }

    }

    public async insertLocal(objeto: any, id: number, x = 0, doctype: string, factype: number) {

        console.log("CONSOLA: INICIA insertLocal 375",objeto);

        for (let x = 0; x < GlobalConstants.DetalleDoc.length; x++) {
            const element = JSON.stringify(GlobalConstants.DetalleDoc[x]);
        }

        
        let unidad = '';
        if (objeto.combos == 0 || objeto.combos == "0") unidad = objeto.unidadID;
        let xd: number;
        
        if (x == 1) {
            xd = objeto.LineNum;
        } else {
            let iteracion: any = await this.docCount(id);
            xd = iteracion.length;
        }
 
        if (!objeto.XMPORCENTAJECABEZERA) {
            objeto.XMPORCENTAJECABEZERA = 0;
        }
        if (!objeto.XMPROMOCIONCABEZERA) {
            objeto.XMPROMOCIONCABEZERA = 0;
        }
        if (objeto.BaseQty == "NaN" || objeto.BaseQty == "undefined") {
            objeto.BaseQty = 1;
        }
        if (!objeto.descuento) {
            objeto.descuento = objeto.U_4DESCUENTO;
        }

        if (objeto.descuento == "NaN" || objeto.descuento == "undefined"|| objeto.descuento == undefined) {
            objeto.descuento = 0;
        }
        let fecha = this.timeStamp();
        let tipoitems = 0;

        console.log("CONSOLA: VALIDA SI ES BONIFICACION 412");
        if(objeto.bonificacion){
            
            for await (let data of GlobalConstants.DetalleDoc) {
                console.log(data);
                console.log(objeto);
                if(data.ItemCode == objeto.ItemCode && data.unidadid == objeto.unidadID && data.bonificacion == true){
                    console.log("CONSOLA: EL PRODUCTO ES BONIFICACION 419");
                    tipoitems = 1;
                    data.Quantity = objeto.cantidad + data.Quantity;
                    let calculo = data.Price*data.Quantity;
                    console.log(calculo);
                    data.DiscTotalMonetary = calculo;
                    data.LineTotal = calculo;
                    data.U_4DESCUENTO = calculo;
                    data.icett = calculo;
                    
                }
            }
        }

        let LineNum = 0;
        for await (let data of GlobalConstants.DetalleDoc) {

            data.id = LineNum;
            data.LineNum = LineNum;
            LineNum ++;
        }

        GlobalConstants.numitems = LineNum;
        let bonii = 0;
        if(objeto.bonificacion){
            bonii = 1;
        }
        if(!objeto.bonificacion){
            bonii = 0;
        }
        if(objeto.bonificacion > 1){
            bonii = objeto.bonificacion;
        }

        console.log("CONSOLA: DATOS ANTES DE INSERTAR EN DETALLE 453",objeto);

        if(tipoitems == 0){
            GlobalConstants.DetalleDoc.push({
                    id:GlobalConstants.numitems,
                    DocEntry:objeto.DocEntry, 
                    DocNum:objeto.DocNum, 
                    LineNum:GlobalConstants.numitems, 
                    BaseType:objeto.BaseType, 
                    BaseEntry:objeto.BaseEntry, 
                    BaseLine:GlobalConstants.numitems, 
                    LineStatus:objeto.LineStatus, 
                    ItemCode:objeto.ItemCode, 
                    Dscription:objeto.ItemName, 
                    Quantity:objeto.cantidad, 
                    OpenQty:objeto.OpenQty, 
                    Price:objeto.price, 
                    Currency:objeto.Currency,
                    DiscPrcnt:objeto.DiscPrcnt, 
                    LineTotal:objeto.LineTotal, 
                    WhsCode:objeto.WhsCode, 
                    CodeBars:objeto.CodeBars, 
                    PriceAfVAT:objeto.PriceAfVAT, 
                    TaxCode:objeto.TaxCode, 
                    U_4DESCUENTO: Number(objeto.descuento).toFixed(2),
                    XMPORCENTAJE:objeto.porcentajedata, 
                    U_4LOTE:objeto.U_4LOTE, 
                    GrossBase:objeto.GrossBase, 
                    idDocumento:id,
                    fechaAdd:fecha, 
                    unidadid:unidad, 
                    tc:objeto.tc, 
                    idCabecera:id, 
                    idProductoPrecio:objeto.idProductoPrecio, 
                    ProductoPrecio:objeto.ProductoPrecio, 
                    LineTotalPay:Number(objeto.LineTotalPay).toFixed(2), 
                    DiscTotalPrcnt:objeto.DiscTotalPrcnt, 
                    DiscTotalMonetary:objeto.DiscTotalMonetary, 
                    icett:objeto.icett, 
                    icete:objeto.icete, 
                    icetp:objeto.icetp, 
                    ICEt:objeto.ICEt, 
                    ICEe:objeto.ICEe, 
                    ICEp:objeto.ICEp, 
                    bonificacion:bonii,
                    combos:objeto.combos,
                    PriceAfterVAT:'',
                    Rate:'',
                    TaxTotal:'',
                    User:'',
                    Status:'',
                    DateUpdate:'',
                    Entregado:'',
                    Serie:'',
                    BaseId:objeto.BaseId,
                    IdBonfAut:objeto.IdBonfAut, 
                    GroupName:objeto.GroupName, 
                    codeBonificacionUse:objeto.codeMid, 
                    XMPORCENTAJECABEZERA:objeto.XMPORCENTAJECABEZERA, 
                    XMPROMOCIONCABEZERA:objeto.XMPROMOCIONCABEZERA,
                    BaseQty:objeto.BaseQty, 
                    grupoproductodocificacion:0,
                    doctype:doctype,
                    factype:factype,
                    Tbonificacion:0,
                    lotes:objeto.lotes,
                    SumBoniLin: '' ,
                    SumBoniCab: '',
                    XMPORCENTAJEBONIFICACION: '',
                    XMVALORBONIFICACION: '',
                    
            });
            
        }
        console.log("CONSOLA: DATOS INSERTADOS EN DETALLE 453",GlobalConstants.DetalleDoc);

    }

    public async update(objeto: any, id: number, doctype: string, factype: number) {
        console.log("DEVD ************ UPDATE update objeto", objeto);
        let productoalmacenes = new Productosalmacenes();
        if ((doctype == 'DFA' && factype == 0) || doctype == 'DOE') {
            let obsaux = await this.itemaeliminar(id);
            await productoalmacenes.addUpdateprodcualmacenes(obsaux[0]);
        }
        if (doctype == 'DOP') {
            let obsaux = await this.itemaeliminar(id);
            await productoalmacenes.addUpdateCompometidoProdutosalmacenes(obsaux[0]);
        }
        let sql = `UPDATE detalle SET Quantity = '${objeto.cantidad}', U_4DESCUENTO = '${objeto.descuento}', DiscTotalMonetary = '${objeto.descuento}', Price = ${objeto.presio},
                          XMPORCENTAJE = '${objeto.porcentajedata}', icett = '${objeto.icett}', LineTotalPay = ${objeto.TotalPay},
                          icete = '${objeto.icete}', icetp = '${objeto.icetp}', bonificacion = ${objeto.bonificacion}, unidadid = '${objeto.unidadid}',
                          ICEp = '${objeto.ICEp}', ICEe = '${objeto.ICEe}', ICEt = '${objeto.ICEt}', LineTotal = '${objeto.icett}'  , BaseQty = '${objeto.BaseQty}' 
                   WHERE id = ${id}`;
        let r = this.executeRaw(sql);
        if ((doctype == 'DFA' && factype == 0) || doctype == 'DOE') await productoalmacenes.updateprodcualmacenes(objeto);
        if (doctype == 'DOP') await productoalmacenes.updateCompometidoProdutosalmacenes(objeto);
        return r;
    }

    public async updateItemsLocal(objeto: any, id: number, doctype: string, factype: number) {
        console.log(GlobalConstants);//> y <? (mayor y menor)
        let indexUpdate=GlobalConstants.DetalleDoc.findIndex(e=>e.id==id);
        GlobalConstants.DetalleDoc[indexUpdate].Quantity = objeto.cantidad;
        GlobalConstants.DetalleDoc[indexUpdate].U_4DESCUENTO = objeto.descuento;
        GlobalConstants.DetalleDoc[indexUpdate].DiscPrcnt = objeto.porcentajedata;;
        GlobalConstants.DetalleDoc[indexUpdate].DiscTotalPrcnt = objeto.porcentajedata;;
        GlobalConstants.DetalleDoc[indexUpdate].DiscTotalMonetary = objeto.descuento;
        GlobalConstants.DetalleDoc[indexUpdate].Price = objeto.presio;
        GlobalConstants.DetalleDoc[indexUpdate].XMPORCENTAJE = objeto.porcentajedata;
        GlobalConstants.DetalleDoc[indexUpdate].icett = objeto.icett;
        GlobalConstants.DetalleDoc[indexUpdate].LineTotalPay = objeto.TotalPay;
        GlobalConstants.DetalleDoc[indexUpdate].icete = objeto.icete;
        GlobalConstants.DetalleDoc[indexUpdate].icetp = objeto.icetp;
        GlobalConstants.DetalleDoc[indexUpdate].bonificacion = objeto.bonificacion;
        GlobalConstants.DetalleDoc[indexUpdate].unidadid = objeto.unidadid;
        GlobalConstants.DetalleDoc[indexUpdate].ICEp = objeto.ICEp;
        GlobalConstants.DetalleDoc[indexUpdate].ICEe = objeto.ICEe;
        GlobalConstants.DetalleDoc[indexUpdate].ICEt = objeto.ICEt;
        GlobalConstants.DetalleDoc[indexUpdate].LineTotal = objeto.icett;
        GlobalConstants.DetalleDoc[indexUpdate].BaseQty = objeto.BaseQty;
        GlobalConstants.DetalleDoc[indexUpdate].lotes = objeto.lotesarr;
        GlobalConstants.DetalleDoc[indexUpdate].lotesUsados = objeto.lotesarrAux;
    }


    public async actualizaiddocumento(cod: any,cod_and: any) {
        console.log("DEVD actualizaiddocumento()");
        let sql = `UPDATE detalle SET idCabecera='${cod}',idDocumento ='${cod}' WHERE idCabecera = '${cod_and}' `;
        console.log("sql update actualizacion", sql);
        return await this.executeSQL(sql);
    }



    public async itemaeliminar(id: number) {
        let sql = `SELECT * FROM detalle WHERE id =  ${id} LIMIT 1;`;
        console.log("select item a eliminar sql ", sql);
        return await this.queryAll(sql);
    }

    public async eliminar(id: number, doctype: string, factype: number) {//, idPedido = 0, data:any
        console.log(GlobalConstants.DetalleDoc);
        console.log("El id es",id);
        let aux= [];
        for await (let data of GlobalConstants.DetalleDoc) {
            console.log("IGUALES"+data.id+" = "+id)
            if(data.id != id){
                aux.push(data);
            }
        }
        GlobalConstants.DetalleDoc = [];
        GlobalConstants.DetalleDoc = aux;
        console.log("datos restantes",GlobalConstants.DetalleDoc);
    }

    resetLineNumOrder = async (id, idDocumento) => {

        let sqlSelect = `SELECT * FROM detalle WHERE idDocumento = '${idDocumento}' ORDER BY LineNum DESC`;
        let dataItem: any = await this.queryAll(sqlSelect);
        /**
         * verificar si hay BaseLine
         */
        var BaseLineExist = dataItem.reduce(function (totalActual, value) {
            return Number(value.BaseLine) + totalActual;
        }, 0); // El 0 será la cantidad inicial con la que comenzará el totalActual
        console.log("DEVD BaseLineExist ", BaseLineExist);
        let numAux = 0;
        for (var value of dataItem) {

            if (value.id != id) {
                console.log("DEVD value to delete ", value);


                if (Number(BaseLineExist) > 0) {
                    let sql = `UPDATE detalle SET LineNum = '${numAux}',BaseLine='${numAux}' WHERE id=${value.id};`;
                    console.log(" sql ", sql);
                    await this.executeRaw(sql);
                } else {
                    let sql = `UPDATE detalle SET LineNum = '${numAux}' WHERE id=${value.id};`;
                    console.log(" sql ", sql);
                    await this.executeRaw(sql);
                }

                numAux = numAux + 1;

            }

        }
        return true;
        //  let sql = `UPDATE detalle SET DocEntry = '${objeto.DocEntry}';`;
        //  console.log(" sql ", sql);
        // return await this.executeRaw(sql);
    }
    public DocTotal(id: number) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT round((SUM(((LineTotal) - U_4DESCUENTO +(ICEe)+(ICEP)))),2) as total FROM detalle WHERE idDocumento = ' + id;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public sumaBonificacion(id: number) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT (CASE WHEN SUM(LineTotal) IS NULL THEN 0 ELSE SUM(LineTotal) END) AS totalbonificacion FROM detalle WHERE idDocumento = ${id} AND bonificacion = 1`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }



    public async sumaTotal(id: number) {
        let sql = `SELECT 
        ROUND((SUM((Quantity * Price) - (U_4DESCUENTO)+(ICEe)+(ICEP))- descuento),2) AS total,
         SUM(Quantity * Price) AS totalNeto, 
         (SUM(U_4DESCUENTO)+descuento) AS descuentos, 
         SUM(ICEe) AS ICEes,
          SUM(ICEp) AS ICEps, 
          SUM(ICEp + ICEe) AS ICEtotales , 
          documentos.tipoDescuento AS tipoDescuento

        FROM detalle 
        left join documentos on  documentos.cod=detalle.idDocumento
        WHERE idDocumento = '${id}'`;
        let totalneto = await this.queryAll(sql);
        console.log("data sql descuentos", sql);
        return {
            totalNeto: Calculo.round(totalneto[0].totalNeto),
            descuentos: Calculo.round(totalneto[0].descuentos),
            ICEes: Calculo.round(totalneto[0].ICEes),
            ICEps: Calculo.round(totalneto[0].ICEps),
            ICEtotales: Calculo.round(totalneto[0].ICEtotales),
            total: Calculo.round(totalneto[0].total),
            porcentajeDescuentoCabezera: totalneto[0].tipoDescuento

        };
    }

    public async sumaTotalLocal(cabecera: any,detalle: any) {

        console.log("control item detalle local",cabecera);
        console.log("control item detale detalle nlocal",detalle);

        //let descuento = cabecera[0].descuento;
        //let descuento =0;
        let totalNeto = 0;
        let total = 0;
        let descuentos = 0;
        let ICEes = 0;
        let ICEps = 0;
        let ICEtotales = 0;
        let tipoDescuento = 0;

        for(let items of detalle){
            console.log("Quantity",items.Quantity);
            console.log("Price",items.Price);
            console.log("U_4DESCUENTO",items.U_4DESCUENTO);
            console.log("ICEe",items.ICEe);
            console.log("ICEp",items.ICEp);

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
       // descuentos +=descuento;
       // total=total-descuento;



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



    public async bonificacionAuth(id: any, idb: any) {
        let sql = `SELECT IFNULL(SUM(CAST(Quantity as decimal)),0) AS total FROM detalle WHERE idDocumento = '${id}' AND IdBonfAut = ${idb};`;
        return await this.queryAll(sql);
    }

    public async showAllTable(id: any) {
        let sql = `SELECT * FROM detalle WHERE idDocumento = '${id}';`;
        return await this.queryAll(sql);
    }

    public async docCount(id: any) {
        let sql = `SELECT * FROM detalle WHERE idDocumento = '${id}' ORDER BY LineNum DESC`;
        console.log(sql);
        return await this.queryAll(sql);
    }

    public async docDetalle(arrx: string) {
        let sql = `SELECT SUM(Quantity) AS Quantity FROM detalle WHERE idDocumento  IN (${arrx})`;
        return await this.queryAll(sql);
    }

    public async itemsGroup(id: any) {// ojo BaseQty
        let sql = `SELECT id, ItemCode, SUM((Quantity * BaseQty)) AS cantidad, BaseId, WhsCode FROM detalle WHERE idDocumento = '${id}' GROUP BY ItemCode, WhsCode;`;
        return await this.queryAll(sql);
    }
    public async itemsToDetalle(id: any) {
        let sql = `SELECT id, ItemCode, Quantity, BaseId, WhsCode FROM detalle WHERE idDocumento = '${id}' ;`;
        return await this.queryAll(sql);
    }

    public async detallesDocumentosunion(arrx: string) {
        let sql = `SELECT dx.ItemCode, dx.Dscription, SUM(Quantity) AS cantEntre FROM detalle dx WHERE dx.idDocumento  IN (${arrx}) GROUP BY LineNum `;
        return await this.queryAll(sql);
    }

    public async updateSinc(objeto: any, track = 0) {
        console.log("objeto ", objeto);
        let dll = '';
        if (track == 0) {
            dll = `WHERE BaseId = '${objeto.id}'`;
        } else {
            dll = `WHERE id = '${objeto.id}'`;
        }
        let sql = `UPDATE detalle SET DocEntry = '${objeto.DocEntry}', DocNum = '${objeto.DocNum}', LineNum = '${objeto.LineNum}', ItemCode = '${objeto.ItemCode}', 
        Dscription = '${objeto.ItemDescription}', Quantity = '${objeto.Quantity}', OpenQty = '${objeto.OpenQty}', price = '${objeto.Price}', Currency = '${objeto.Currency}',
        LineTotal = ${objeto.LineTotal}, icett = ${objeto.LineTotal}, ProductoPrecio = '${objeto.UnitPrice}', LineTotalPay = ${objeto.LineTotal}, PriceAfterVAT = '${objeto.PriceAfterVAT}',
        Rate = '${objeto.Rate}', TaxTotal = '${objeto.TaxTotal}', User = '${objeto.User}', Status = '${objeto.Status}', 
        DateUpdate = '${objeto.DateUpdate}', Entregado = '${objeto.Entregado}' ${dll};`;
        console.log(" sql ", sql);
        return await this.executeRaw(sql);
    }

    public async insertSinc(objeto: any, id: number) {
        let fecha = this.timeStamp();
        let sql = `INSERT INTO detalle VALUES (NULL, '${objeto.DocEntry}', '${objeto.DocNum}', '${objeto.LineNum}', '${objeto.DocBase}', 
        '${objeto.DocEntry}', '${objeto.LineNum}',  '', '${objeto.ItemCode}', '${objeto.ItemDescription}', '${objeto.pendiente}',
        '${objeto.pendiente}', '${objeto.Price}', '${objeto.Currency}', 0, ${objeto.LineTotal}, '${objeto.WhsCode}', 
        '', '', '', '','', '', '', '${id}', '${fecha}', '', '', '${id}', '${objeto.Price}', '${objeto.Price}', 
        ${objeto.LineTotal}, 0, 0, '${objeto.LineTotal}', '${objeto.LineTotal}', '0', '0', '0', '0', 0,'0','',
        '','','','${objeto.Status}','','','serieIMPR','${objeto.id}',0, '${objeto.GroupName}', 0, 0, 0, 0,'','');`;
        return await this.executeRaw(sql);
    }


    public async insertAll(data: any, id: any, contador = 0) {
        if (contador == 0) {
            // let sql = 'DELETE FROM detalle;';
            await this.clearimpot();
            //  await this.exe(sql);
        }
        //  let producto = new Productos();
        //
        let datax = JSON.parse(data.data);
        console.log("***** items a limpiar datax ", datax);
        let sql = 'INSERT INTO detalle VALUES ';
        for (let d of datax.respuesta) {

            d.LineTotal = parseFloat(d.Quantity) * parseFloat(d.Price);
            //let descuentoMonetario: any = 0;
            d.descuentoMonetario = 0;
            d.LineTotalPay = d.LineTotal;

            d.descuentoMonetario = (Calculo.porcentaje(d.LineTotal, d.descuento)).toFixed(2);

            d.LineTotalPay = (parseFloat(d.LineTotal) - parseFloat(d.descuentoMonetario));
            d.ICEEspecifico = d.ICEEspecifico ? d.ICEEspecifico : 0;
            d.ICEPorcentual = d.ICEPorcentual ? d.ICEPorcentual : 0;
            d.LineTotalPay = (parseFloat(d.LineTotalPay) + (parseFloat(d.ICEEspecifico) + parseFloat(d.ICEPorcentual))).toFixed(2);

            let codebonif = 0;
            if(d.codebonif == undefined){
                codebonif = 0;
            }else{
                codebonif = d.codebonif
            }

            let grupoproductodocificacion = 0;
            if(d.grupoproductodocificacion == undefined){
                grupoproductodocificacion = 0;
            }else{
                grupoproductodocificacion = d.grupoproductodocificacion
            }

            let GroupName = 0;
            if(d.GroupName == undefined){
                GroupName = 0;
            }else{
                GroupName = d.GroupName
            }

            let d_id = 0;
            if(d.id == undefined){
                d_id = 0;
            }else{
                d_id = d.id
            }

            let Status = 0;
            if(d.Status == undefined){
                Status = 0;
            }else{
                Status = d.Status
            }

            let bonif = 0;
            if(d.bonif == undefined){
                bonif = 0;
            }else{
                bonif = d.bonif
            }

            let iceorpor = 0;
            if(d.iceorpor == undefined){
                iceorpor = 0;
            }else{
                iceorpor = d.iceorpor
            }

            let iceoresp = 0;
            if(d.iceoresp == undefined){
                iceoresp = 0;
            }else{
                iceoresp = d.iceoresp
            }

            let ICETIPO = 0;
            if(d.ICETIPO == undefined){
                ICETIPO = 0;
            }else{
                ICETIPO = d.ICETIPO
            }

            let descuento = 0;
            if(d.descuento == undefined){
                descuento = 0;
            }else{
                descuento = d.descuento
            }

            let unityCode = 0;
            if(d.unityCode == undefined){
                unityCode = 0;
            }else{
                unityCode = d.unityCode
            }
            
            let DocBase = 0;
            if(d.DocBase == undefined){
                DocBase = 0;
            }else{
                DocBase = d.DocBase
            }
            
            let pendiente = 0;
            if(d.pendiente == undefined){
                pendiente = 0;
            }else{
                pendiente = d.pendiente
            }

            let BaseQty = '0';
            if(d.BaseQty == undefined || d.BaseQty == 'NaN'){
                BaseQty = '0';
            }else{
                BaseQty = d.BaseQty
            }

            let descuentoMonetario = '0';
            if(d.descuentoMonetario == undefined || d.descuentoMonetario == 'NaN'){
                descuentoMonetario = '0';
            }else{
                descuentoMonetario = d.descuentoMonetario
            }

            let LineTotalPay = '0';
            if(d.LineTotalPay == undefined || d.LineTotalPay == 'NaN'){
                LineTotalPay = '0';
            }else{
                LineTotalPay = d.LineTotalPay
            }

            let fecha = this.timeStamp();
            sql += `(NULL, 
                '${d.DocEntry}', 
                '${d.DocNum}', 
                '${d.LineNum}', 
                '${DocBase}',
                '${d.DocEntry}', 
                '${d.LineNum}',  
                '', 
                '${d.ItemCode}', 
                '${d.ItemDescription}', 
                '${d.Quantity}',
                '${pendiente}', 
                '${d.Price}', 
                '${d.Currency}', 
                ${descuento}, 
                ${d.LineTotal}, 
                '${d.WhsCode}',
                '', 
                '', 
                '', 
                ${descuentoMonetario},
                ${descuento}, 
                '', 
                '', 
                '${d.DocNum}', 
                '${fecha}', 
                '${d.unityCode}', 
                '', 
                '${id}', 
                '${d.Price}', 
                '${d.Price}',
                ${LineTotalPay}, 
                ${descuento}, 
                0, 
                '${ICETIPO}', 
                '${iceoresp}', 
                '${iceorpor}', 
                '${ICETIPO}', 
                '${d.ICEEspecifico}', 
                '${d.ICEPorcentual}', 
                ${bonif},
                '0',
                '',
                '',
                '',
                '',
                '${Status}',
                '',
                '',
                'serieIMPR',
                '${d_id}',
                0, 
                '${GroupName}', 
                '${codebonif}', 
                0, 
                0,
                '${parseInt(BaseQty)}', 
                '${grupoproductodocificacion}',
                '',
                ''),`;
        }


        //console.log("sqlx  ", sql);
        if (datax.respuesta.length > 0) {
            let sqlx = sql.slice(0, -1);
            console.log("******* QUERY DETALLE ", sqlx);
            let f = sqlx + ';';
            return this.executeSQL(f);
        } else {
            return true;
        }
    }


    public async findAll(id: number) {
        let sql = `SELECT *  FROM detalle WHERE idDocumento = '${id}' ORDER BY LineNum ASC`;
        console.log("sql ", sql);

        let resp: any = await this.queryAll(sql);
        console.log("respfindAll ", resp);
        let arr = [];
        let dataLotes = new Lotesproductos();
        for await (let itm of resp) {

            console.log("DEVD EACH SELECT ITEMS itm ", itm);

            let istaLotesarr2: any = await dataLotes.select(itm.ItemCode, itm.WhsCode);
            console.log("DEVD  istaLotesarr2.  ", istaLotesarr2);

            let arrlotex = [];
            if (istaLotesarr2.length > 0) {
                for await (let lotex of istaLotesarr2) {
                    let lotes: any = new Lotes();

                    let lotesusados: any = await lotes.selectLoteProducto(itm.id, lotex.BatchNum);

                    if (lotesusados.length > 0) arrlotex.push({
                        lote: lotesusados[0].Batch,
                        cant: lotesusados[0].Stock,
                        label: lotesusados[0].ItemDescription,
                    });
                }
            }

            console.log("DDEV arrlotex ", arrlotex);
            //
            itm.lotesUsados = arrlotex;


            let seriesSlide = [];
            let seriesproducto = new Seriesproductos();
            let arrseries: any = await seriesproducto.selectserie(itm.id);
            for (let seriex of arrseries) seriesSlide.push({ serie: seriex.SerialNumber });

            itm.seriesUsados = seriesSlide;

            if (itm.combos > 0) {
                let combos = new Combos();
                let prodCombo: any = await combos.searchCombo(itm.ItemCode);
                itm.productos = prodCombo;
                arr.push(itm);
            } else {
                itm.productos = [];
                arr.push(itm);
            }
        }
        return arr;
    }


    public async findLocal(datos: any) {
        let dataLotes = new Lotesproductos();
        let arr = [];

        console.log("CONSOLA: INICIA FUNCION findLocal 1092");

        console.log("CONSOLA: RECORRE EL OBJETO DE ITEMS 1094");
        for await (let itm of datos) {

            console.log("CONSOLA: BUSCA LOTES EN dataLotes.select 1097");
            let istaLotesarr2: any = await dataLotes.select(itm.ItemCode, itm.WhsCode);
           

            let arrlotex = [];
            console.log("CONSOLA: VALIDA SI LA CONSULTA RETORNO RESULTADOS 1102");
            if (istaLotesarr2.length > 0) {
                for await (let lotex of istaLotesarr2) {
                    let lotes: any = new Lotes();
                    let lotesusados: any = await lotes.selectLoteProducto(itm.id, lotex.BatchNum);
                    if (lotesusados.length > 0) arrlotex.push({
                        lote: lotesusados[0].Batch,
                        cant: lotesusados[0].Stock,
                        label: lotesusados[0].ItemDescription,
                    });
                }
            }


            itm.lotesUsados = arrlotex;
            let seriesSlide = [];
            let seriesproducto = new Seriesproductos();
            console.log("CONSOLA: BUSCA SERIES EN seriesproducto.selectserie 1119");
            let arrseries: any = await seriesproducto.selectserie(itm.id);
            console.log("CONSOLA: VALIDA SI LA CONSULTA RETORNO RESULTADOS 1121");
            for (let seriex of arrseries) seriesSlide.push({ serie: seriex.SerialNumber });
            itm.seriesUsados = seriesSlide;
    
            if (itm.combos > 0) {
                let combos = new Combos();
                let prodCombo: any = await combos.searchCombo(itm.ItemCode);
                itm.productos = prodCombo;
                arr.push(itm);
            } else {
                itm.productos = [];
                arr.push(itm);
            }
        }
        return arr;
    }

    public async findAll2(id: number) {
        let sql = `SELECT *  FROM detalle WHERE idDocumento = '${id}' ORDER BY LineNum ASC`;
        console.log("sql ", sql);

        let resp: any = await this.queryAll(sql);
        console.log("respfindAll ", resp);
        let arr = [];
        let combos = new Combos();

        for await (let itm of resp) {
            // console.log("DEVD EACH SELECT ITEMS itm ", itm);
            // let dataLotes = new Lotesproductos();
            // let istaLotesarr2: any = await dataLotes.select(itm.ItemCode, itm.WhsCode);
            // console.log("DEVD  istaLotesarr2.  ", istaLotesarr2);

            // let arrlotex = [];
            // if (istaLotesarr2.length > 0) {
            //     for await (let lotex of istaLotesarr2) {
            //         let lotes: any = new Lotes();

            //         let lotesusados: any = await lotes.selectLoteProducto(itm.id, lotex.BatchNum);

            //         if (lotesusados.length > 0) arrlotex.push({
            //             lote: lotesusados[0].Batch,
            //             cant: lotesusados[0].Stock,
            //             label: lotesusados[0].ItemDescription,
            //         });
            //     }
            // }

            // console.log("DDEV arrlotex ", arrlotex);
            // //
            // itm.lotesUsados = arrlotex;

            // let seriesSlide = [];
            // let seriesproducto = new Seriesproductos();
            // let arrseries: any = await seriesproducto.selectserie(itm.id);
            // for (let seriex of arrseries) seriesSlide.push({ serie: seriex.SerialNumber });

            // itm.seriesUsados = seriesSlide;
            if (itm.combos > 0) {
                let combos = new Combos();
                let prodCombo: any = await combos.searchCombo(itm.ItemCode);
                itm.productos = prodCombo;
                arr.push(itm);
            } else {
                itm.productos = [];
                arr.push(itm);
            }
        }
        return arr;
    }

    public async findAll3(id: number) {
        let sql = `SELECT *  FROM detalle WHERE idDocumento = '${id}' ORDER BY LineNum ASC`;
        console.log("sql ", sql);
        let resp: any = await this.queryAll(sql);
        console.log("respfindAll ", resp);
        let arr = [];
        for await (let itm of resp) {
            console.log("DEVD EACH SELECT ITEMS itm ", itm);
            /*if (itm.combos > 0) {
                let combos = new Combos();
                let prodCombo: any = await combos.searchCombo(itm.ItemCode);
                itm.productos = prodCombo;
                arr.push(itm);
            } else {
                itm.productos = [];
                arr.push(itm);
            }*/
        }
        return arr;
    }

    public async findAll4(id: any) {
        let sql = `SELECT *  FROM detalle WHERE idDocumento in (${id}) ORDER BY ItemCode ASC`;
        console.log("sql ", sql);
        let resp: any = await this.queryAll(sql);
        return resp;
    }

    public async findAll5(id: any) {
        let sql = `SELECT *  FROM detalle WHERE idDocumento = '${id}' ORDER BY ItemCode ASC`;
        console.log("sql ", sql);
        let resp: any = await this.queryAll(sql);
        return resp;
    }

    public async findAllToClone(id: number, item: any, linea: any) {
        let sql = `SELECT  SUM(dt.Quantity) as copiado FROM documentos cab INNER JOIN detalle dt  ON cab.cod=dt.idDocumento  
                     WHERE cab.clone = '${id}' and dt.BaseLine = '${linea}' and dt.ItemCode='${item}' and cab.estadosend !='7' GROUP BY dt.ItemCode,dt.BaseLine`;
        return await this.queryAll(sql);
    }


    public async eliminargrupo(id: number) {
        let sql = `DELETE FROM detalle WHERE idDocumento = '${id}'`;
        return await this.executeSQL(sql);
    }

    public async clearimpot() {
        let sql = `DELETE FROM detalle WHERE Serie = "serieIMPR"`;
        return await this.executeSQL(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS detalle`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }



    public async showAll() {
        let sql = `SELECT * FROM detalle order by id desc;`;
        return await this.queryAll(sql);
    }


    public async updateDescuentoLinea(idDocumento, descuento, id, codeMid, esExtra, LineTotal,usa_redondeo = 0) {
        let descuentoCode = 2;
        if (esExtra == 1) {
            console.log("DEVD descuento con extra en contrado ", esExtra);
            descuentoCode = 3;
        }

        let descuentoMonetario: any = (Calculo.porcentaje(LineTotal, descuento));
        console.log("DEVD LineTotal ", LineTotal);
        console.log("DEVD descuento ", descuento);
        console.log("DEVD descuentoMonetario generado para linea  ", descuentoMonetario);

        console.log("DATOS LOCALIDAD",usa_redondeo);
        usa_redondeo = Number(usa_redondeo);
        console.log("DATOS LOCALIDAD",usa_redondeo);

        if(usa_redondeo == 1){
            console.log("SIN DECIMALES");
            descuentoMonetario = Number(descuentoMonetario).toFixed(0);
        }else{
            console.log("cON DECIMALES");
            descuentoMonetario = Number(descuentoMonetario).toFixed(2);
        }
            
        console.log("DEVD  descuentoMonetario generado para linea  redondeado ", descuentoMonetario);


        for (let i = 0; i < GlobalConstants.DetalleDoc.length; i++) {
            if(GlobalConstants.DetalleDoc[i].id == id){
                GlobalConstants.CabeceraDoc[0].Tbonificacion = 1;
                console.log("el que se modifica es", GlobalConstants.DetalleDoc[i]);
                GlobalConstants.DetalleDoc[i].U_4DESCUENTOBef_line=GlobalConstants.DetalleDoc[i].U_4DESCUENTO?GlobalConstants.DetalleDoc[i].U_4DESCUENTO:0;
                let U_4DESCUENTO =Number(GlobalConstants.DetalleDoc[i].U_4DESCUENTO)+Number(descuentoMonetario);

                GlobalConstants.DetalleDoc[i].DiscTotalPrcnt = descuento;
                GlobalConstants.DetalleDoc[i].U_4DESCUENTOBoni = U_4DESCUENTO;
                GlobalConstants.DetalleDoc[i].U_4DESCUENTO=U_4DESCUENTO;

                if(GlobalConstants.DetalleDoc[i].bonificacion == 0){
                    let ICEe = (GlobalConstants.DetalleDoc[i].icete* (GlobalConstants.DetalleDoc[i].BaseQty * GlobalConstants.DetalleDoc[i].Quantity))

                    GlobalConstants.DetalleDoc[i].ICEeBoni = ICEe;

                    let ICEp= (((GlobalConstants.DetalleDoc[i].Quantity * GlobalConstants.DetalleDoc[i].Price)-GlobalConstants.DetalleDoc[i].U_4DESCUENTOBoni) * 0.87 * (GlobalConstants.DetalleDoc[i].icetp /100));
                    GlobalConstants.DetalleDoc[i].ICEpBoni = ICEp;

                    console.log("ICEe",ICEe);
                    console.log("ICEp",ICEp);
                }

                //let LineTotalPay = ((GlobalConstants.DetalleDoc[i].Quantity * GlobalConstants.DetalleDoc[i].Price)-GlobalConstants.DetalleDoc[i].U_4DESCUENTOBoni);
                let LineTotalPay = ((GlobalConstants.DetalleDoc[i].Quantity * GlobalConstants.DetalleDoc[i].Price)-GlobalConstants.DetalleDoc[i].U_4DESCUENTOBoni + GlobalConstants.DetalleDoc[i].ICEeBoni + GlobalConstants.DetalleDoc[i].ICEpBoni );

                GlobalConstants.DetalleDoc[i].LineTotalPayBoni = LineTotalPay;
                GlobalConstants.DetalleDoc[i].Tbonificacion = 1;

                console.log("U_4DESCUENTO",U_4DESCUENTO);
                console.log("LineTotalPay",LineTotalPay);
            }
        } 

        let auxtotal = 0;
        for await (let item of GlobalConstants.DetalleDoc) {
            auxtotal += (item.Price* item.Quantity) - (item.U_4DESCUENTOBoni+item.ICEeBoni+item.ICEpBoni)
        }
        GlobalConstants.CabeceraDoc[0].DocumentTotalPay = 0;
        GlobalConstants.CabeceraDoc[0].DocumentTotalPayBoni = auxtotal-GlobalConstants.CabeceraDoc[0].descuento;
        console.log("datos guardados1",JSON.stringify(GlobalConstants.CabeceraDoc));


        
        /*let sqlDesc = `UPDATE detalle SET U_4DESCUENTO=U_4DESCUENTO+${descuentoMonetario}, DiscTotalPrcnt=${descuento}
                WHERE idDocumento = '${idDocumento}' AND id='${id}'; 
                UPDATE detalle set ICEe=ROUND((icete * BaseQty),2)  where idDocumento='${idDocumento}' AND id='${id}' AND bonificacion=0;  `; // Quantity *  ojo 
        console.log("******* sqlDesc ", sqlDesc);
        await this.executeRaw(sqlDesc);


        let sqlupdateIces = `
        UPDATE detalle set ICEp=ROUND(((Quantity * Price)-U_4DESCUENTO) * 0.87 * (icetp /100), 2)  where idDocumento='${idDocumento}' AND id='${id}' AND bonificacion=0 ;

        UPDATE detalle set LineTotalPay=((Quantity * Price)-U_4DESCUENTO + ICEe + ICEp )  where idDocumento='${idDocumento}'  AND id='${id}' AND  bonificacion=0;
        `;
        console.log("DEVD sqlupdateIces ", sqlupdateIces);

        await this.executeRaw(sqlupdateIces);

        let sql = `UPDATE detalle SET codeBonificacionUse='${codeMid}' , bonificacion=${descuentoCode}
                   WHERE idDocumento = '${idDocumento}' AND id='${id}'  `;
        console.log("******* sql ", sql);
        let r = this.executeRaw(sql);*/

        //return r;
    }


    public async updateDescuentoLineaReset(idDocumento, idDetalle, montoReset, descuentoBono = 0) {
        let sql: any = ""
        if (descuentoBono == 0) {
            console.log("NO LLEEGO EL PORCENTAJE PARTICIONADO");
            sql = `UPDATE detalle SET LineTotalPay = ${montoReset}, U_4DESCUENTO=0, bonificacion=0, DiscTotalPrcnt=0, codeBonificacionUse=0
            WHERE id='${idDetalle} '`; //idDocumento = '${idDocumento}' AND 
            console.log("sql ", sql);
            await this.executeRaw(sql);
        } else {
            let descuentoMonetario: any = (Calculo.porcentaje(montoReset, descuentoBono));
            console.log("DEVD montoReset ", montoReset);
            console.log("DEVD descuentoBono ", descuentoBono);
            console.log("DEVD descuentoMonetario generado para linea  ", descuentoMonetario);

            sql = `UPDATE detalle SET U_4DESCUENTO=U_4DESCUENTO-${descuentoMonetario}, DiscTotalPrcnt=0 , bonificacion=0, ICEp=ROUND(((Quantity * Price)-0) * 0.87 * (icetp /100), 2) 
            WHERE  id='${idDetalle}' `;

            console.log("******* sql ", sql);

            await this.executeRaw(sql);
            //  await this.updateResetICEPdescuento(descuentoMonetario, idDetalle);
            // sql = `UPDATE detalle SET LineTotalPay = ${montoReset}, U_4DESCUENTO=0, bonificacion=0, DiscTotalPrcnt=0, codeBonificacionUse=0
            // WHERE id='${idDetalle} '`; 
        }

        let r = true;

        return r;
    }



    public async updateDescuentoDocumentReset(idDocumento) {
        let sql: any = ""
        console.log("DEVD ************************* updateDescuentoDocumentReset()");
        // let descuentoMonetario: any = (Calculo.porcentaje(montoReset, descuentoBono));
        // console.log("DEVD montoReset ", montoReset);
        // console.log("DEVD descuentoBono ", descuentoBono);
        // console.log("DEVD descuentoMonetario generado para linea  ", descuentoMonetario);
        // let sql_total = `SELECT SUM((Quantity * Price)-U_4DESCUENTO) as total from  detalle where idDocumento='${idDocumento}' and bonificacion<>1 `;
        // let sql_totalbonif = `SELECT SUM((Quantity * Price)-U_4DESCUENTO) as total from  detalle where idDocumento='${idDocumento}' and bonificacion=1 `;
        // let totaldoc = await this.queryAll(sql_total);
        // console.log("totaldoc ", totaldoc);

        sql = `UPDATE detalle SET XMPORCENTAJECABEZERA=0, XMPROMOCIONCABEZERA=0, U_4DESCUENTO=0,
        LineTotalPay=0,
        DiscTotalPrcnt=0,
        DiscTotalMonetary=0,
        XMPORCENTAJE=0,
        DiscPrcnt=0,
          bonificacion=0, LineTotalPay=Quantity * Price, ICEp=ROUND(((Quantity * Price)-0) * 0.87 * (icetp /100), 2) 
        WHERE  idDocumento='${idDocumento}' `;

        console.log("******* sql ", sql);

        await this.executeRaw(sql);


        let r = true;
        return r;
    }

    public async  updateBonificacionLineaReset(idDetalle) {
        let sql = `DELETE FROM detalle WHERE id='${idDetalle}'`;
        console.log("sql ", sql);

        let getSql = `SELECT *  from  detalle where id='${idDetalle}' `;
        //let getSqlbonif = `SELECT SUM(Quantity * Price) as total from  detalle where idDocumento='${id}' and bonificacion=0`;
        let dataItem = await this.queryAll(getSql);
        console.log("DEVD dataItem ", dataItem);
        let productoalmacenes = new Productosalmacenes();
        // dataItem[0].idCabecera.substring(0, 3);
        dataItem[0].cantidad = dataItem[0].Quantity;
        console.log("DEVD dataItem[0].idCabecera.substring(0,3) ", dataItem[0].idCabecera.substring(0, 3));
        if (dataItem[0].idCabecera.substring(0, 3) == 'DFA' || dataItem[0].idCabecera.substring(0, 3) == 'DOE') await productoalmacenes.addUpdateprodcualmacenes(dataItem[0]);
        if (dataItem[0].idCabecera.substring(0, 3) == 'DOP') await productoalmacenes.addUpdateCompometidoProdutosalmacenes(dataItem[0]);

        return await this.executeSQL(sql);
    }

    public async verificarStock(idDoc: any) {
        let items: any = await this.itemsGroup(idDoc);
        let contador = 0;
        let aux = true;
        for await (let item of items) {

            let sql = `SELECT DISTINCT * FROM productosalmacenes 
                        WHERE (ItemCode = '${item.ItemCode}' AND WarehouseCode = '${item.WhsCode}');`;//AND CAST(InStock as decimal) >= ${item.cantidad}
            console.log("sql verificando stock ", sql);

            let rx: any = await this.queryAll(sql);
            console.log("EACH rx.ItemCode ", rx);
            if (rx.length > 0) {
                console.log("rx[0].InStock ", Number(rx[0].InStock));
                console.log(" item.cantidad ", item.cantidad);
                if (Number(rx[0].InStock) >= item.cantidad) {

                    console.log("cumple stock ", rx);
                    contador = 1;
                } else {
                    rx = [];
                    console.log("no cumple ", rx);
                }
            }
            if (contador == 0) {
                aux = false;
            }
        }
        return aux;
    }

    public async cambiarAlmacenStock(idDoc: any, codeAlmacen) {
        let items: any = await this.itemsToDetalle(idDoc);
        let contador = 0;
        for await (let item of items) {
            let sqlrex = `
             UPDATE detalle SET WhsCode = '${codeAlmacen}'
             WHERE id='${item.id}';`;
            console.log("sqlrex cambio de almacen  ", sqlrex);
            await this.executeSQL(sqlrex);

        }
        return (true);
    }
    
    /*
    public async anulacionDocumento(idDocumento, descuento) {
        let sql = `UPDATE documentos SET DocumentTotal = '${data.conceptoAnulacion}', DocumentTotalDetallePay = 7, DocumentTotalPay = 'anulado' WHERE cod = '${cod}' `;
        return await this.executeSQL(sql);
    }
    DocumentTotal: "916.80"
DocumentTotalDetallePay: "843.12"
DocumentTotalPay: "843.12"
*/
}