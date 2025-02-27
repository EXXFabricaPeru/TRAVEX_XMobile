import { Injectable } from '@angular/core';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import { File } from '@ionic-native/file/ngx';
import { FileOpener } from '@ionic-native/file-opener/ngx';
import { ConfigService } from "../models/config.service";
import { Documentos } from "../models/documentos";
import { Detalle } from '../models/detalle';
import { Pagos }  from "../models/pagos";
import { CurrencyPipe, formatDate, formatNumber } from '@angular/common';
import { Calculo } from "../utilsx/calculo";
import { SpinnerDialog } from '@ionic-native/spinner-dialog/ngx';
import { Productosalmacenes } from "../models/productosalmacenes";
import { Tiempo } from "../models/tiempo";
import { bonificacionesDocCabezera } from '../models/bonificacionDocCabezera.';
import { DataService } from "../services/data.service";
import { Network } from "@ionic-native/network/ngx";
import * as moment  from 'moment'
import { ConfigLayautService } from './config-layaut.service';
import { Almacenes } from '../models/almacenes';
import { Productos } from '../models/productos';

@Injectable({
  providedIn: 'root'
})
export class ReporteDiarioStyleoneService {
  public layout;
  public pdfObj = null;
  public objPDF: any;
  public metadata: any;


  constructor(private file: File, private fileOpener: FileOpener,
    private configService: ConfigService, private spinnerDialog: SpinnerDialog,
    private _configLayaut:ConfigLayautService,
    private dataService: DataService,
    private network: Network
  )
  {
  }

  public async generateEXE(name: string) {
      this.pdfObj = pdfMake.createPdf(this.objPDF, this.layout);
      let archivoPDF = `${name}.pdf`;
      this.pdfObj.getBuffer(async (buffer) => {
          let blob = new Blob([buffer], { type: 'application/pdf' });
          await this.file.writeFile(this.file.externalApplicationStorageDirectory, archivoPDF, blob, { replace: true });
          try {
              this.fileOpener.open(this.file.externalApplicationStorageDirectory + archivoPDF, 'application/pdf');
          } catch (e) {
              console.log("ERRRORORORORO ", e);
          }
      });
  }

  public async inventarioreport() {
      let datauser: any = await this.configService.getSession();
      let productosalmacenes = new Productosalmacenes();
    console.log(datauser);
    let dataConfigLayaut = await this._configLayaut.getConfig();
        dataConfigLayaut = dataConfigLayaut.find(e=>e.type_document=='RIN');
    console.log("data para configuracion", dataConfigLayaut);
      let dataresp: any;
      if(datauser[0].validaciondisponible == 1){
           dataresp = await productosalmacenes.findAllReportDis();
      }else{
          dataresp = await productosalmacenes.findAllReport();
      }
      console.log("dataresp ", dataresp);
      let arrxprox = [];
      arrxprox.push([{
          text: 'PRODUCTO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'COMPROME',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'STOCK',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'DISPONIBLE',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ALMACEN',
          style: 'tableHeader',
          alignment: 'center'
      }]);
      let header: any;
      let cuerpodetalle = [];
      for (let prox of dataresp) {
          arrxprox.push([prox.ItemName, prox.Committed, prox.InStock, (prox.InStock - prox.Committed), prox.WarehouseCode]);


          /**
              * header detalle
              */
          header = { columns: [{ text: ` \n  ${prox.ItemCode} - ${prox.ItemName}  \n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalle.push(header);
          /**
          * cuerpo detalle
          */

          header = {
              columns: [
                  {
                      text: `\n COMPROMETIDO `, style: ['small'], width: '50%'
                  },
                  {
                      text: `\n ${formatNumber((prox.Committed), 'en-US', '1.0-0')}`, style: ['small'], alignment: 'right',
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: `\n STOCK `, style: ['small'], width: '50%'
                  },
                  {
                      text: `\n ${formatNumber((prox.InStock), 'en-US', '1.0-0')}`, style: ['small'], alignment: 'right',
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: `\n DISPONIBLE `, style: ['small'], width: '50%'
                  },
                  {
                      text: `\n ${formatNumber((prox.InStock - prox.Committed), 'en-US', '1.0-0')} `, style: ['small'], alignment: 'right',
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: `\n ALMACEN `, style: ['small'], width: '50%'
                  },
                  {
                      text: `\n ${prox.WarehouseCode}`, style: ['small'], alignment: 'right',
                  }
              ],
          };
          cuerpodetalle.push(header);

      }
      let tabelarr = { body: arrxprox };
      return new Promise((resolve, reject) => {
          this.objPDF = {
              pageSize: { width: dataConfigLayaut?dataConfigLayaut.papel_width:80, height: 'auto' },
              pageMargins: [ dataConfigLayaut?dataConfigLayaut.margin_rigth:6, dataConfigLayaut?dataConfigLayaut.margin_up:3, dataConfigLayaut?dataConfigLayaut.margin_left:3,dataConfigLayaut?dataConfigLayaut.margin_down:10],
              content: [{
                  text: 'RESUMEN DE INVENTARIO',
                  alignment: 'center',
                  style: 'header'
              }, /*'\n', {
                  text: 'Fecha: ' + formatDate(Tiempo.fecha(), 'dd/MM/yyyy', 'en-US'), style: 'subheader'
              },*/ '\n', {
                  text: 'Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona, style: 'subheader'
              }, '\n',

                  cuerpodetalle
              ],
              styles: {
                  header:dataConfigLayaut  ?JSON.parse(dataConfigLayaut.title): {
                      fontSize: 5,
                      bold: true
                  },
                  subheader:dataConfigLayaut?JSON.parse(dataConfigLayaut.sub_title): {
                      fontSize: 4,
                      bold: true
                  },
                  small: dataConfigLayaut?JSON.parse(dataConfigLayaut.description):{
                      fontSize: 3
                  },
                  xsmall:dataConfigLayaut ?JSON.parse(dataConfigLayaut.xsmall): {
                      fontSize: 2.5
                  },
                  tableExample:{
                      margin: [0, 5, 0, 15]
                  },
                  tableHeader: dataConfigLayaut ?JSON.parse(dataConfigLayaut.table): {
                      fontSize: 4,
                      bold: true
                  },

              },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }

  public async construcpdfCieereDiario(fechaData: string, fechamax: string) {
      this.spinnerDialog.show('', 'Cargando...', true);
      let datauser: any;
      let monedalocal: any;
      let arrxdocumentos = [];
      let resxpagos: any;
      let documenpspagox: any;
      let padosxx = [];
      let montousd = 0;
      let montolocal = 0;
      let cuenta: any;
      try {
          datauser = await this.configService.getSession();
          monedalocal = datauser[0].config[0].moneda;
          let pagos = new Pagos();
          cuenta = await pagos.pagoscuentaexxis(fechaData, fechamax);
          resxpagos = await pagos.pagosCieerediario(fechaData, fechamax);
          documenpspagox = await pagos.pagosPagosrealizados(fechaData, fechamax);
          let documentos = new Documentos();
          let resx: any = await documentos.docxAnulados(fechaData, fechamax);
          console.log(resx);
          for (let itmx of documenpspagox) {
              arrxdocumentos.push({
                  columns: [
                      {
                          text: itmx.formaPago,
                          style: 'subheader'
                      },
                      {
                          text: itmx.codigo,
                          style: 'subheader'
                      },
                      {
                          text: itmx.bancoCode,
                          alignment: 'right',
                          style: 'subheader'
                      },
                      {
                          text: itmx.total,
                          alignment: 'right',
                          style: 'subheader'
                      },
                  ]
              });
          }
          for (let pagox of resxpagos) {
              let tipeformx = '';
              switch (pagox.formaPago) {
                  case ('PEF'):
                      tipeformx = 'Efectivo';
                      break;
                  case ('PEFX'):
                      tipeformx = 'Dolares';
                      break;
                  case ('PCC'):
                      tipeformx = 'Tarjeta';
                      break;
                  case ('PBT'):
                      tipeformx = 'Tranferencia';
                      break;
                  case ('PCH'):
                      tipeformx = 'Cheque';
                      break;
              }
              montousd += pagox.monedaDolar;
              montolocal += pagox.monto;
              padosxx.push({
                  columns: [{
                      text: tipeformx,
                      style: 'subheader'
                  }, {
                      text: Calculo.formatMoney(pagox.monedaDolar),
                      alignment: 'right',
                  }, {
                      text: Calculo.formatMoney(pagox.monto),
                      alignment: 'right',
                  }]
              });
          }
          this.spinnerDialog.hide();
      } catch (e) {
          this.spinnerDialog.hide();
      }
      this.spinnerDialog.hide();
      return new Promise((resolve, reject) => {
          this.objPDF = {
              content: [
                  {
                      text: 'CIERRE DIARIO',
                      alignment: 'center',
                      style: 'header'
                  },
                  '\n',
                  '\n',
                  {
                      text: 'Fecha: ' + formatDate(fechaData, 'dd/MM/yyyy', 'en-US'),
                  },
                  '\n',
                  {
                      text: 'Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona,
                  },
                  '\n',
                  {
                      columns: [
                          {
                              text: 'CIERRE',
                              style: 'subheader'
                          },
                          {
                              text: 'MONTO (USD)',
                              alignment: 'right',
                              style: 'subheader'
                          },
                          {
                              text: 'MONTO (' + monedalocal + ')',
                              alignment: 'right',
                              style: 'subheader'
                          },
                      ]
                  },

                  '\n',
                  padosxx,
                  '\n',
                  {
                      columns: [
                          {
                              text: cuenta[0].contador + ' PAGOS A CUENTA',
                              style: 'subheader'
                          },
                          {
                              text: '',
                              alignment: 'right',
                              style: 'subheader'
                          },
                          {
                              text: monedalocal + " " + cuenta[0].total,
                              alignment: 'right',
                              style: 'subheader'
                          },
                      ]
                  },
                  {
                      text: '...................................................................................................................................................................'
                  },
                  {
                      columns: [
                          {
                              text: 'TOTAL',
                              style: 'subheader',
                          },
                          {
                              text: Calculo.formatMoney(montousd),

                              alignment: 'right',
                          },
                          {
                              text: Calculo.formatMoney(montolocal + parseFloat(cuenta[0].total)),
                              alignment: 'right',
                          },
                      ]
                  },
                  '\n',
                  {
                      text: '...................................................................................................................................................................'
                  },
                  '\n',
                  '\n',
                  {
                      text: 'DETALLE',
                      style: 'subheader',
                  },
                  '\n',
                  {
                      columns: [
                          {
                              text: 'TIPO',
                              style: 'subheader'
                          },
                          {
                              text: 'COD ',
                              style: 'subheader'
                          },
                          {
                              text: 'BANCO ',
                              alignment: 'right',
                              style: 'subheader'
                          },
                          {
                              text: 'MONTO ',
                              alignment: 'right',
                              style: 'subheader'
                          },
                      ]
                  },
                  '\n',
                  arrxdocumentos
              ],
              styles: {
                  header: {
                      fontSize: 18,
                      bold: true
                  },
                  subheader: {
                      fontSize: 14,
                      bold: true
                  },
                  bigger: {
                      fontSize: 15,
                      italics: true
                  },
                  tableExample: {
                      margin: [0, 5, 0, 15]
                  },
              },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }

  public async construcpdf(fechaData: string, fechamax: string) {
      this.spinnerDialog.show('', 'Cargando...', true);
      let datauser: any = await this.configService.getSession();
      let monedalocal = datauser[0].config[0].moneda;
      let documentos = new Documentos();
      let resx: any = await documentos.cantidadDocx(fechaData, fechamax);
      let pagos = new Pagos();
      let resxpagos: any = await pagos.findAllPagosexxis(fechaData, fechamax);
      let pagosrecibidos = resxpagos.reduce((sum, datax) => {
          return sum + datax.monto;
      }, 0);
      let contado = await pagos.pagoscontadoexxis(fechaData, fechamax);
      let credito = await pagos.pagoscreaditoexxis(fechaData, fechamax);
      let cuenta = await pagos.pagoscuentaexxis(fechaData, fechamax);
      let arrdocumentos = [];
      let namefacx = '';
      let contxe = 0;
      let cantidadDocumentos = 0;
      let totalDocumentos = 0;
      for (let facx of resx) {
          switch (facx.DocType) {
              case ('DFA'):
                  if (facx.tipoestado == 'cerrado') {
                      namefacx = 'FACTURAS';
                      contxe = 2;
                  } else {
                      namefacx = 'FACTURAS ANULADAS';
                      contxe = 6;
                  }
                  break;
              case ('DOF'):
                  if (facx.tipoestado == 'cerrado') {
                      namefacx = 'OFERTAS';
                      contxe = 0;
                  } else {
                      namefacx = 'OFERTAS ANULADAS';
                      contxe = 4;
                  }
                  break;
              case ('DOP'):
                  if (facx.tipoestado == 'cerrado') {
                      namefacx = 'PEDIDOS';
                      contxe = 1;
                  } else {
                      namefacx = 'PEDIDOS ANULADOS';
                      contxe = 5;
                  }
                  break;
              case ('DOE'):
                  if (facx.tipoestado == 'cerrado') {
                      namefacx = 'ENTREGAS';
                      contxe = 3;
                  } else {
                      namefacx = 'ENTREGAS ANULADOS';
                      contxe = 7;
                  }
                  break;
          }
          cantidadDocumentos += facx.cantx;
          totalDocumentos += parseFloat(facx.total);
          arrdocumentos[contxe] = {
              columns: [
                  {
                      text: namefacx,
                      style: 'subheader'
                  },
                  {
                      text: facx.cantx,
                      alignment: 'right',
                  },
                  {
                      text: facx.currency + ' ' + Calculo.formatMoney(facx.total),
                      alignment: 'right',
                  }
              ]
          };
      }
      this.spinnerDialog.hide();
      return new Promise((resolve, reject) => {
          this.objPDF = {
              content: [{
                  text: 'RESUMEN DE VENTAS',
                  alignment: 'center',
                  style: 'header'
              }, '\n', {
                  text: 'Fecha: ' + formatDate(fechaData, 'dd/MM/yyyy', 'en-US'),
              }, '\n', {
                  text: 'Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoMPersona + ' ' + datauser[0].apellidoPPersona,
              }, '\n', {
                  columns: [{
                      text: 'TIPO',
                      style: 'subheader'
                  }, {
                      text: 'CANTIDAD',
                      alignment: 'right',
                      style: 'subheader'
                  }, {
                      text: 'MONTO',
                      alignment: 'right',
                      style: 'subheader'
                  }]
              }, '\n',
                  arrdocumentos,
                  '\n', {
                  columns: [
                      {
                          text: 'TOTAL DOCUMENTOS',
                          style: 'subheader'
                      },
                      {
                          text: cantidadDocumentos,
                          alignment: 'right',
                          style: 'subheader'
                      },
                      {
                          text: monedalocal + ' ' + Calculo.formatMoney(totalDocumentos),
                          alignment: 'right',
                          style: 'subheader'
                      },
                  ]
              },
                  '\n',
                  '\n',
                  '\n',
              {
                  text: 'FACTURAS: _____________________________________________________________________',
                  style: 'subheader'
              },
                  '\n',
              {
                  columns: [
                      {
                          text: 'FACTURAS CONTADO',
                          style: 'subheader'
                      },
                      {
                          text: contado[0].cantidad,
                          style: 'subheader',
                          alignment: 'right',
                      },
                      {
                          text: monedalocal + ' ' + (typeof contado[0].contado != undefined) ? Calculo.formatMoney(contado[0].contado) : 0,
                          alignment: 'right',
                      },
                  ]
              },
              {
                  columns: [
                      {
                          text: 'FACTURAS CREDITO',
                          style: 'subheader'
                      },
                      {
                          text: credito[0].cantidad,
                          style: 'subheader',
                          alignment: 'right',
                      },
                      {
                          text: monedalocal + ' ' + credito[0].total,
                          alignment: 'right',
                      },
                  ]
              },
                  '\n',
              {
                  columns: [
                      {
                          text: 'TOTAL FACTURAS',
                          style: 'subheader'
                      },
                      {
                          text: (contado[0].cantidad + credito[0].cantidad),
                          alignment: 'right',
                          style: 'subheader'
                      },
                      {
                          text: monedalocal + ' ' + formatNumber((contado[0].contado + credito[0].total), 'en-US', '1.2-2'),
                          alignment: 'right',
                          style: 'subheader'
                      },
                  ]
              },
                  '\n',
                  '\n',
                  '\n',
              {
                  text: 'COBROS RECIBIDOS: ____________________________________________________________',
                  style: 'subheader'
              },
                  '\n',
              {
                  columns: [
                      {
                          text: 'FACTURAS ',
                          style: 'subheader'
                      },
                      {
                          text: (contado[0].cantidad + credito[0].cantidad),
                          alignment: 'right',
                      },
                      {
                          text: monedalocal + ' ' + formatNumber((contado[0].contado + credito[0].total), 'en-US', '1.2-2'),
                          alignment: 'right',
                      },
                  ]
              }, {
                  columns: [
                      {
                          text: 'A CUENTA ',
                          style: 'subheader'
                      },
                      {
                          text: cuenta[0].contador,
                          alignment: 'right',
                      },
                      {
                          text: monedalocal + ' ' + cuenta[0].total,
                          alignment: 'right',
                      },
                  ]
              },
                  '\n',
              {
                  columns: [
                      {
                          text: 'CAJA TOTAL',
                          style: 'subheader'
                      },
                      {
                          text: ((contado[0].cantidad + credito[0].cantidad) + cuenta[0].contador),
                          alignment: 'right',
                          style: 'subheader'
                      },
                      {
                          text: monedalocal + ' ' + Calculo.formatMoney(((contado[0].contado + credito[0].total) + cuenta[0].total)),
                          alignment: 'right',
                          style: 'subheader'
                      },
                  ]
              }

              ],
              styles: {
                  header: {
                      fontSize: 18,
                      bold: true
                  },
                  subheader: {
                      fontSize: 14,
                      bold: true
                  },
                  bigger: {
                      fontSize: 15,
                      italics: true
                  },
                  tableExample: {
                      margin: [0, 5, 0, 15]
                  },
              },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }

  public async reportResumenCajaa(date) {
      let documentos = new Documentos();
      let resx: any = await documentos.selectAllResumenCaja(date, "factura");
      return new Promise((resolve, reject) => {

          resolve(resx);
      });
  }

  public async reportResumenAll() {
      let documentos = new Documentos();
      let resx: any = await documentos.selectAll();
      return new Promise((resolve, reject) => {

          resolve(resx);
      });
  }


  public async reportResumenCaja(date) {
       
      let documentos = new Documentos();
      let dataResumenCaja: any = await documentos.selectAllResumenCaja(date, "factura");
      console.log("dataResumenCaja ", dataResumenCaja);
      let dataBs = dataResumenCaja.filter((item) => {
          return item.moneda == 'BS';
      });
      let dataUsd = dataResumenCaja.filter((item) => {
          return item.moneda == 'USD';
      });


      let datauser: any = await this.configService.getSession();
      console.log("datauser ", datauser);
      // let productosalmacenes = new Productosalmacenes();
      //   let dataresp: any = await productosalmacenes.findAllReport();
      let arrxproxBS = [];
      arrxproxBS.push([{
          text: 'TIPO DE PAGO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ACTIVO BS.',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'PAGO EN USD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'USD EN BS',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ANULADO BS.',
          style: 'tableHeader',
          alignment: 'center'

      }]);
      let arrxproxUSD = [];
      arrxproxUSD.push([{
          text: 'TIPO DE PAGO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ACTIVO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ANULADO',
          style: 'tableHeader',
          alignment: 'center'
      }]);
      let dataBsAux = dataBs;
      let efectivoBs = dataBsAux.filter(value => value.formaPagoText == "Efectivo");

      if (efectivoBs.length == 0) {

          dataBs.push({
              formaPagoText: "Efectivo",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0,
              acumuladoActivoUSD: 0
              , tipoCambioDolar: 0

          });

      }

      let tarjetaBs = dataBsAux.filter(value => value.formaPagoText == "Tarjeta");
      if (tarjetaBs.length == 0) {
          dataBs.push({
              formaPagoText: "Tarjeta",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0,
              acumuladoActivoUSD: 0
              , tipoCambioDolar: 0
          });
      }

      let transferenciaBs = dataBsAux.filter(value => value.formaPagoText == "Transferencia");
      if (transferenciaBs.length == 0) {
          dataBs.push({
              formaPagoText: "Transferencia",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0,
              acumuladoActivoUSD: 0
              , tipoCambioDolar: 0
          });
      }

      let chequeBs = dataBsAux.filter(value => value.formaPagoText == "Cheque");
      if (chequeBs.length == 0) {
          dataBs.push({
              formaPagoText: "Cheque",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0,
              acumuladoActivoUSD: 0
              , tipoCambioDolar: 0
          });
      }
      let TotalUSDGlobal = 0;

      for (let value of dataBs) {
          // console.log("value ", value);
          let pagoBs = 0;

          if (value.tipoCambioDolar > 0 && value.formaPagoText == 'Efectivo') {
              pagoBs = Math.round(value.acumuladoActivoUSD * value.tipoCambioDolar);
              // value.acumuladoActivo=value.acumuladoActivo-pagoBs;
              //  console.log("pagos realizados->",value,value.acumuladoActivoUSD)
              if (value.acumuladoActivoUSD > 0) TotalUSDGlobal = value.acumuladoActivoUSD;
          }
          arrxproxBS.push([value.formaPagoText, value.totalDocumentosActivos, formatNumber((value.acumuladoActivo - pagoBs), 'en-US', '1.2-2'), formatNumber((value.acumuladoActivoUSD), 'en-US', '1.2-2'), formatNumber((pagoBs), 'en-US', '1.2-2'), value.totalDocumentosInactivos, formatNumber((value.acumuladoInactivo), 'en-US', '1.2-2')]);
      }

      let totalFacturasBs = dataBs.reduce((sum, datax) => {
          return sum + datax.acumuladoActivo;
      }, 0);
      console.log("totalFacturasBs ", totalFacturasBs);
      let totalFacturasBsanulado = dataBs.reduce((sum, datax) => {
          return sum + datax.acumuladoInactivo;
      }, 0);
      let tabelarrBS = { body: arrxproxBS };





      /**
       * DOLARES
       */
      let dataUSDAux = dataUsd;
      let efectivoUSD = dataUSDAux.filter(value => value.formaPagoText == "Efectivo");

      if (efectivoUSD.length == 0) {

          dataUsd.push({
              formaPagoText: "Efectivo",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0
          });

      }

      let tarjetaUSD = dataUSDAux.filter(value => value.formaPagoText == "Tarjeta");
      if (tarjetaUSD.length == 0) {
          dataUsd.push({
              formaPagoText: "Tarjeta",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0
          });
      }

      let transferenciaUSD = dataUSDAux.filter(value => value.formaPagoText == "Transferencia");
      if (transferenciaUSD.length == 0) {
          dataUsd.push({
              formaPagoText: "Transferencia",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0
          });
      }

      let chequeUSD = dataUSDAux.filter(value => value.formaPagoText == "Cheque");
      if (chequeUSD.length == 0) {
          dataUsd.push({
              formaPagoText: "Cheque",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0
          });
      }

      for (let value of dataUsd) arrxproxUSD.push([value.formaPagoText, value.totalDocumentosActivos, formatNumber((value.acumuladoActivo), 'en-US', '1.2-2'), value.totalDocumentosInactivos, formatNumber((value.acumuladoInactivo), 'en-US', '1.2-2')]);
      let tabelarrUSD = { body: arrxproxUSD };

      let totalFacturasUSD = dataUsd.reduce((sum, datax) => {
          return sum + datax.acumuladoActivo;
      }, 0);

      let totalFacturasUSDanulado = dataUsd.reduce((sum, datax) => {
          return sum + datax.acumuladoActivo;
      }, 0);
      console.log("totalFacturasBs ", totalFacturasBs);

      dataResumenCaja = await documentos.selectAllResumenCaja(date, "cuenta");
      dataBs = [];
      dataUsd = [];

      dataBs = dataResumenCaja.filter((item) => {
          return item.moneda == 'BS';
      });
      dataUsd = dataResumenCaja.filter((item) => {
          return item.moneda == 'USD';
      });
      console.log("data cuentas ", dataResumenCaja);


      /**
       * PAGOS A CUENTA
       */

      let arrxproxCuentaBS = [];
      arrxproxCuentaBS.push([{
          text: 'TIPO DE PAGO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ACTIVO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'PAGO EN USD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'USD EN BS',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ANULADO',
          style: 'tableHeader',
          alignment: 'center'

      }]);
      let arrxproxCuentaUSD = [];
      arrxproxCuentaUSD.push([{
          text: 'TIPO DE PAGO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ACTIVO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ANULADO',
          style: 'tableHeader',
          alignment: 'center'
      }]);
      //dataBs=[];
      let dataBsCuentaAux = dataBs;
      arrxproxBS = [];
      efectivoBs = dataBsCuentaAux.filter(value => value.formaPagoText == "Efectivo");

      if (efectivoBs.length == 0) {

          dataBs.push({
              formaPagoText: "Efectivo",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0,
              acumuladoActivoUSD: 0
              , tipoCambioDolar: 0
          });

      }
      console.log("dataBs ", dataBs);
      tarjetaBs = [];
      tarjetaBs = dataBsCuentaAux.filter(value => value.formaPagoText == "Tarjeta");
      if (tarjetaBs.length == 0) {
          dataBs.push({
              formaPagoText: "Tarjeta",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0,
              acumuladoActivoUSD: 0
              , tipoCambioDolar: 0
          });
      }
      transferenciaBs = [];
      transferenciaBs = dataBsCuentaAux.filter(value => value.formaPagoText == "Transferencia");
      if (transferenciaBs.length == 0) {
          dataBs.push({
              formaPagoText: "Transferencia",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0,
              acumuladoActivoUSD: 0
              , tipoCambioDolar: 0
          });
      }
      chequeBs = [];
      chequeBs = dataBsCuentaAux.filter(value => value.formaPagoText == "Cheque");
      if (chequeBs.length == 0) {
          dataBs.push({
              formaPagoText: "Cheque",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0,
              acumuladoActivoUSD: 0
              , tipoCambioDolar: 0
          });
      }

      let TotalUSDGlobalCuenta = 0;
      console.log("dataBs fin ", dataBs);
      //arrxproxBS=[];
      for (let value of dataBs) {
          console.log("value ", value);
          let pagoBs = 0;
          if (value.tipoCambioDolar > 0 && value.formaPagoText == 'Efectivo') {
              pagoBs = Math.round(value.acumuladoActivoUSD * value.tipoCambioDolar);
              // value.acumuladoActivo=pagoBs-value.totalDocumentosActivos;
              TotalUSDGlobalCuenta = value.acumuladoActivoUSD;
          }
          arrxproxCuentaBS.push([value.formaPagoText, value.totalDocumentosActivos, formatNumber((value.acumuladoActivo), 'en-US', '1.2-2'), formatNumber((value.acumuladoActivoUSD), 'en-US', '1.2-2'), formatNumber((pagoBs), 'en-US', '1.2-2'), value.totalDocumentosInactivos, formatNumber((value.acumuladoInactivo), 'en-US', '1.2-2')]);
      }

      let tabelarrCuentaBS = { body: arrxproxCuentaBS };

      let totalFacturasBsCuenta = dataBs.reduce((sum, datax) => {
          return sum + datax.acumuladoActivo;
      }, 0);
      console.log("totalFacturasBs ", totalFacturasBs);
      let totalFacturasBsanuladoCuenta = dataBs.reduce((sum, datax) => {
          return sum + datax.acumuladoInactivo;
      }, 0);

      /**
       * DOLARES
       */
      /*
      text: monedalocal + ' ' + formatNumber((contado[0].contado + credito[0].total), 'en-US', '1.2-2'),
      alignment: 'right',
      */
      //dataUsd=[];
      dataUSDAux = dataUsd;
      efectivoUSD = dataUSDAux.filter(value => value.formaPagoText == "Efectivo");

      if (efectivoUSD.length == 0) {

          dataUsd.push({
              formaPagoText: "Efectivo",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0
          });

      }

      tarjetaUSD = dataUSDAux.filter(value => value.formaPagoText == "Tarjeta");
      if (tarjetaUSD.length == 0) {
          dataUsd.push({
              formaPagoText: "Tarjeta",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0
          });
      }

      transferenciaUSD = dataUSDAux.filter(value => value.formaPagoText == "Transferencia");
      if (transferenciaUSD.length == 0) {
          dataUsd.push({
              formaPagoText: "Transferencia",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0
          });
      }

      chequeUSD = dataUSDAux.filter(value => value.formaPagoText == "Cheque");
      if (chequeUSD.length == 0) {
          dataUsd.push({
              formaPagoText: "Cheque",
              totalDocumentosActivos: 0,
              acumuladoActivo: 0,
              totalDocumentosInactivos: 0,
              acumuladoInactivo: 0
          });
      }

      for (let value of dataUsd) arrxproxCuentaUSD.push([value.formaPagoText, value.totalDocumentosActivos, formatNumber((value.acumuladoActivo), 'en-US', '1.2-2'), value.totalDocumentosInactivos, formatNumber((value.acumuladoInactivo), 'en-US', '1.2-2')]);
      let tabelarrCuentaUSD = { body: arrxproxCuentaUSD };

      let totalFacturasUSDCuenta = dataUsd.reduce((sum, datax) => {
          return sum + datax.acumuladoActivo;
      }, 0);

      let totalFacturasUSDanuladoCuenta = dataUsd.reduce((sum, datax) => {
          return sum + datax.acumuladoActivo;
      }, 0);

      /**
       * END
       */

      return new Promise((resolve, reject) => {
          this.objPDF = {
              content: [
                  {
                      text: 'RESUMEN DE CAJA',
                      alignment: 'center',
                      style: 'header'
                  }, '\n', {
                      text: 'Fecha: ' + formatDate(date, 'dd/MM/yyyy', 'en-US'), alignment: 'right'
                  }, '\n', {
                      text: 'Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona,
                  }, '\n', {
                      text: 'I) Pagos de Facturas ',//en Moneda Local (BS.)
                  }, '\n', {
                      table: tabelarrBS
                  }, '', {
                      text: 'Total Bs. ' + totalFacturasBs,
                  }, '', {
                      text: 'Total USD. ' + TotalUSDGlobal,
                  }/*, '\n', {
                      text: 'II) Pagos de Facturas en Moneda Extrangera (USD.)',
                  }, '\n', {
                      table: tabelarrUSD
                  }, '', {
                      text: 'Total USD. ' + formatNumber((totalFacturasUSD), 'en-US', '1.2-2'),
                  }, '', {
                      text: 'Total Anulado USD. ' + formatNumber((totalFacturasUSDanulado), 'en-US', '1.2-2'),
                  }*/, '\n', {
                      text: 'II) Pagos a cuenta', // en Moneda Local (BS.)
                  }, '\n', {
                      table: tabelarrCuentaBS
                  },
                  , '', {
                      text: 'Total Bs. ' + totalFacturasBsCuenta,
                  }, '', {
                      text: 'Total USD. ' + TotalUSDGlobalCuenta,
                  },/* '\n', {
                      text: 'IV) Pagos a cuenta en Moneda Extrangera (USD.)',
                  }, '\n', {
                      table: tabelarrCuentaUSD
                  }, '', {
                      text: 'Total USD. ' + formatNumber((totalFacturasUSDCuenta), 'en-US', '1.2-2'),
                  }, '', {
                      text: 'Total Anulado USD. ' + formatNumber((totalFacturasUSDanuladoCuenta), 'en-US', '1.2-2'),
                  }*/
              ],


              styles: {
                  header: {
                      fontSize: 18,
                      bold: true
                  },
                  subheader: {
                      fontSize: 14,
                      bold: true
                  },
                  bigger: {
                      fontSize: 15,
                      italics: true
                  },
                  tableExample: {
                      margin: [0, 5, 0, 15]
                  },
              },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }

  /**
   * DETALLE DE CAJA
   */

  public async reportResumenCajaDetalle(date) {

      let documentos = new Documentos();

      let dataResumenCaja: any = await documentos.selectAllResumenCajaDetalle(date, "factura");
      console.log("dataResumenCaja detalle ", dataResumenCaja);

      let dataBs = dataResumenCaja.filter((item) => {
          return item.moneda == 'BS';
      });
      let dataUsd = dataResumenCaja.filter((item) => {
          return item.moneda == 'USD';
          // return Number(item.monedaDolar) > 0;

      });

      let datauser: any = await this.configService.getSession();
      let arrxproxBS = [];
      arrxproxBS.push([{
          text: 'RECIBO DE PAGO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'TIPO DE PAGO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'FECHA',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'Nro TRANSACCION',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'BANCO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'MONTO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'PAGO EN USD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'USD EN BS',
          style: 'tableHeader',
          alignment: 'center'

      }]);

      let TotalUSDGlobal = 0;
      for (let value of dataBs) {
          console.log("value BS ", value);
          let comprobante = "";
          if (value.formaPago == "PCH") {
              comprobante = value.numCheque;
          }

          if (value.formaPago == "PBT") {
              comprobante = value.boucher;
          }
          if (value.formaPago == "PCC") {
              comprobante = value.baucher;
          }
          let pagoBs = 0;
          if (value.tipoCambioDolar > 0 && value.formaPagoText == 'Efectivo') {
              pagoBs = Math.round(value.monedaDolar * value.tipoCambioDolar);
              // value.acumuladoActivo=pagoBs-value.totalDocumentosActivos;

              TotalUSDGlobal = TotalUSDGlobal + value.monto;
              //  console.log("reporte en dolares", TotalUSDGlobal, value.acumuladoActivoUSD);
          }

          arrxproxBS.push([value.documentoPagoId, value.formaPagoText, value.fecha, comprobante, value.bancoCode, formatNumber((value.monto), 'en-US', '1.2-2'), value.monedaDolar, formatNumber((pagoBs), 'en-US', '1.2-2')]);
      }

      let totalBs = dataBs.reduce((sum, datax) => {
          return sum + datax.monto;
      }, 0);
      console.log("DEVD totalFacturasBs ", arrxproxBS);

      let tabelarrBS = { body: arrxproxBS };



      console.log("arrxproxBS ", arrxproxBS);
      /**
       * DOLARES
       */

      let xuxdataDolar = dataResumenCaja.filter((item) => {
          //  return item.moneda == 'USD';
          return Number(item.monedaDolar) > 0;

      });
      console.log("dolar itmes-->", xuxdataDolar);
      // let totalUSD = dataUsd.reduce((sum, datax) => {
      let totalUSD = xuxdataDolar.reduce((sum, datax) => {

          return sum + datax.monedaDolar;
      }, 0);


      //console.log("totalUSDanulado ", totalUSDanulado);

      dataResumenCaja = await documentos.selectAllResumenCajaDetalleAnulados(date, "1");
      console.log("data anulados ", dataResumenCaja);
      dataBs = [];
      dataUsd = [];

      dataBs = dataResumenCaja.filter((item) => {
          return item.moneda == 'BS';
      });
      dataUsd = dataResumenCaja.filter((item) => {
          return item.moneda == 'USD';
      });
      console.log("data cuentas ", dataResumenCaja);


      let arrxproxBSanulados = [];
      arrxproxBSanulados.push([{
          text: 'RECIBO DE PAGO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'TIPO DE PAGO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'FECHA',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'Nro TRANSACCION',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'BANCO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'MONTO',
          style: 'tableHeader',
          alignment: 'center'
      }]);

      let TotalUSDGlobalCuenta = 0;
      for (let value of dataBs) {
          let comprobante = "";
          if (value.formaPago == "PCH") {
              comprobante = value.numCheque;
          }

          if (value.formaPago == "PBT") {
              comprobante = value.boucher;
          }
          if (value.formaPago == "PCC") {
              comprobante = value.baucher;
          }
          let pagoBs = 0;
          if (value.tipoCambioDolar > 0 && value.formaPagoText == 'Efectivo') {
              pagoBs = value.monedaDolar * value.tipoCambioDolar;
              // value.acumuladoActivo=pagoBs-value.totalDocumentosActivos;
              TotalUSDGlobalCuenta = TotalUSDGlobalCuenta + value.monedaDolar;
          }

          // arrxproxBSanulados.push([0, 0, 0, 0, 0, 0, 0, 0]);
          arrxproxBSanulados.push([value.documentoPagoId, value.formaPagoText, value.fecha, comprobante, value.bancoCode, formatNumber((value.monto), 'en-US', '1.2-2')]);
      }
      let totalBsanulado = dataBs.reduce((sum, datax) => {
          return sum + datax.monto;
      }, 0);
      let tabelarrBSAnulado = { body: arrxproxBSanulados };

      let totalUSDanulado = dataUsd.reduce((sum, datax) => {
          return sum + datax.monto;
      }, 0);
      return new Promise((resolve, reject) => {
          this.objPDF = {
              content: [
                  {
                      text: 'DETALLE DE CAJA',
                      alignment: 'center',
                      style: 'header'
                  }, '\n', {
                      text: 'Fecha: ' + formatDate(date, 'dd/MM/yyyy', 'en-US'), alignment: 'right'
                  }, '\n', {
                      text: 'Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoMPersona + ' ' + datauser[0].apellidoPPersona,
                  }, '\n', {
                      text: 'I) Pagos Activos en Moneda Local (BS.)',
                  }, '\n', {
                      table: tabelarrBS
                  }, '', {
                      text: 'Total Bs. ' + totalBs,
                  }, '\n', {

                  }, '', {
                      text: 'Total USD. ' + formatNumber((totalUSD), 'en-US', '1.2-2'),
                  }

                  , '\n', {
                      text: 'I) Pagos Activos anulados en Moneda Local (BS.)',
                  }, '\n', {
                      table: tabelarrBSAnulado
                  }, '', {
                      text: 'Total Bs. ' + totalBsanulado,

                  }, '', {
                      text: 'Total USD. ' + formatNumber((TotalUSDGlobalCuenta), 'en-US', '1.2-2'),
                  }


              ],


              styles: {
                  header: {
                      fontSize: 18,
                      bold: true
                  },
                  subheader: {
                      fontSize: 14,
                      bold: true
                  },
                  bigger: {
                      fontSize: 15,
                      italics: true
                  },
                  tableExample: {
                      margin: [0, 5, 0, 15]
                  },
              },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }


  /**
   * DETALLE DE VENTAS
   */


  public async reportResumenVentas(date) {
      let documentos = new Documentos();
      let dataResumenCaja: any = await documentos.selectAllResumenVentas(date, "factura");
      console.log("dataResumenCaja ", dataResumenCaja);

      let arrxproxBS = [];
      let datauser: any = await this.configService.getSession();
      arrxproxBS.push([{
          text: 'TIPO DOCUMENTO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'MONEDA',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ACTIVO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ANULADO',
          style: 'tableHeader',
          alignment: 'center'
      }]);


      let MontoTotal = 0.0;
      let MontoTotalAnulado = 0.0;

      for (let value of dataResumenCaja) {

          arrxproxBS.push([value.DocType, value.DocCur, value.CantidadActivo, formatNumber((value.MontoActivo), 'en-US', '1.2-2'), value.CantidadAnulado, formatNumber((value.MontoAnulado), 'en-US', '1.2-2')]);

          if (value.tipoestado == 'anulado') {
              MontoTotalAnulado = MontoTotalAnulado + parseFloat(value.MontoAnulado);
          }
          else {
              MontoTotal = MontoTotal + parseFloat(value.MontoActivo);
          }

      }
      let TablaDocumentos = { body: arrxproxBS };


      return new Promise((resolve, reject) => {
          this.objPDF = {
              content: [
                  {
                      text: 'RESUMEN DE DOCUMENTOS',
                      alignment: 'center',
                      style: 'header'
                  }, '\n', {
                      text: 'Fecha: ' + formatDate(date, 'dd/MM/yyyy', 'en-US'), alignment: 'right'
                  }, '\n', {
                      text: 'Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona,
                  }, '\n', {
                      text: 'I) Documentos ',
                  }, '\n', {
                      table: TablaDocumentos
                  }, '', {
                      text: 'Total Bs. ' + formatNumber((MontoTotal), 'en-US', '1.2-2'),
                  }, '', {
                      text: 'Total Bs. Anulado ' + formatNumber((MontoTotalAnulado), 'en-US', '1.2-2'),
                  }
              ],
              styles: {
                  header: {
                      fontSize: 18,
                      bold: true
                  },
                  subheader: {
                      fontSize: 14,
                      bold: true
                  },
                  bigger: {
                      fontSize: 15,
                      italics: true
                  },
                  tableExample: {
                      margin: [0, 5, 0, 15]
                  },
              },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }


  public async selectAllResumenDocOfertas(date) {
      let datauser: any = await this.configService.getSession();
      let documentos = new Documentos();
      let dataService: any = await documentos.selectAllResumenDocOfertas(date, "", 0);



      let MontoTotalOferta = 0.0;
      let MontoTotalOfertaAnulado = 0.0;
      let dataBs = dataService.filter((item) => { return item.DocType == 'DOF'; });
      let arrxproxBS = [];
      arrxproxBS.push([{ text: 'NRO DOCUMENTO', style: 'tableHeader', alignment: 'center' },
      { text: 'CLIENTE', style: 'tableHeader', alignment: 'center' },
      { text: 'FECHA', style: 'tableHeader', alignment: 'center' },
      { text: 'ESTADO', style: 'tableHeader', alignment: 'center' },
      { text: 'MONEDA', style: 'tableHeader', alignment: 'center' },
      { text: 'TOTAL', style: 'tableHeader', alignment: 'center' }
      ]);
      for (let value of dataBs) {
          arrxproxBS.push([value.cod, value.CardName, value.DocDate, value.tipoestado, value.DocCur, formatNumber((value.monto), 'en-US', '1.2-2')]);
          if (value.tipoestado == 'anulado') {
              MontoTotalOfertaAnulado = MontoTotalOfertaAnulado + parseFloat(value.monto);
          }
          else {
              MontoTotalOferta = MontoTotalOferta + parseFloat(value.monto);
          }
      }
      let TablaOfertas = { body: arrxproxBS };

      let MontoTotalPedido = 0.0;
      let MontoTotalPedidoAnulado = 0.0;
      dataBs = dataService.filter((item) => { return item.DocType == 'DOP'; });
      arrxproxBS = [];
      arrxproxBS.push([{ text: 'NRO DOCUMENTO', style: 'tableHeader', alignment: 'center' },
      { text: 'CLIENTE', style: 'tableHeader', alignment: 'center' },
      { text: 'FECHA', style: 'tableHeader', alignment: 'center' },
      { text: 'ESTADO', style: 'tableHeader', alignment: 'center' },
      { text: 'MONEDA', style: 'tableHeader', alignment: 'center' },
      { text: 'TOTAL', style: 'tableHeader', alignment: 'center' }
      ]);
      for (let value of dataBs) {
          arrxproxBS.push([value.cod, value.CardName, value.DocDate, value.tipoestado, value.DocCur, formatNumber((value.monto), 'en-US', '1.2-2')]);
          if (value.tipoestado == 'anulado') {
              MontoTotalPedidoAnulado = MontoTotalPedidoAnulado + parseFloat(value.monto);
          }
          else {
              MontoTotalPedido = MontoTotalPedido + parseFloat(value.monto);
          }
      }
      let TablaPedidos = { body: arrxproxBS };

      let MontoTotalFactura = 0.0;
      let MontoTotalFacturaAnulado = 0.0;
      dataBs = dataService.filter((item) => { return item.DocType == 'DFA'; });
      arrxproxBS = [];
      arrxproxBS.push([{ text: 'NRO DOCUMENTO', style: 'tableHeader', alignment: 'center' },
      { text: 'CLIENTE', style: 'tableHeader', alignment: 'center' },
      { text: 'FECHA', style: 'tableHeader', alignment: 'center' },
      { text: 'ESTADO', style: 'tableHeader', alignment: 'center' },
      { text: 'MONEDA', style: 'tableHeader', alignment: 'center' },
      { text: 'TOTAL', style: 'tableHeader', alignment: 'center' }
      ]);
      for (let value of dataBs) {
          arrxproxBS.push([value.cod, value.CardName, value.DocDate, value.tipoestado, value.DocCur, formatNumber((value.monto), 'en-US', '1.2-2')]);
          if (value.tipoestado == 'anulado') {
              MontoTotalFacturaAnulado = MontoTotalFacturaAnulado + parseFloat(value.monto);
          }
          else {
              MontoTotalFactura = MontoTotalFactura + parseFloat(value.monto);
          }
      }
      let TablaFacturas = { body: arrxproxBS };

      let MontoTotalEntrega = 0.0;
      let MontoTotalEntregaAnulado = 0.0;
      dataBs = dataService.filter((item) => { return item.DocType == 'DOE'; });
      arrxproxBS = [];
      arrxproxBS.push([{ text: 'NRO DOCUMENTO', style: 'tableHeader', alignment: 'center' },
      { text: 'CLIENTE', style: 'tableHeader', alignment: 'center' },
      { text: 'FECHA', style: 'tableHeader', alignment: 'center' },
      { text: 'ESTADO', style: 'tableHeader', alignment: 'center' },
      { text: 'MONEDA', style: 'tableHeader', alignment: 'center' },
      { text: 'TOTAL', style: 'tableHeader', alignment: 'center' }
      ]);
      for (let value of dataBs) {
          arrxproxBS.push([value.cod, value.CardName, value.DocDate, value.tipoestado, value.DocCur, formatNumber((value.monto), 'en-US', '1.2-2')]);
          if (value.tipoestado == 'anulado') {
              MontoTotalEntregaAnulado = MontoTotalEntregaAnulado + parseFloat(value.monto);
          }
          else {
              MontoTotalEntrega = MontoTotalEntrega + parseFloat(value.monto);
          }
      }
      let TablaEntregas = { body: arrxproxBS };

      return new Promise((resolve, reject) => {
          this.objPDF = {
              content: [
                  {
                      text: 'DETALLE DE VENTAS',
                      alignment: 'center',
                      style: 'header'
                  }, '\n', {
                      text: 'Fecha: ' + formatDate(date, 'dd/MM/yyyy', 'en-US'), alignment: 'right'
                  }, '\n', {
                      text: 'Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona,
                  }, '\n', {
                      text: 'I) Ofertas',
                  }, '\n', {
                      table: TablaOfertas
                  }, '', {
                      text: 'Total Bs. ' + formatNumber((MontoTotalOferta), 'en-US', '1.2-2'),
                  }, {
                      text: 'Total Anulado: ' + formatNumber((MontoTotalOfertaAnulado), 'en-US', '1.2-2'),
                  }

                  , '\n', {
                      text: 'III) Pedidos',
                  }, '\n', {
                      table: TablaPedidos
                  }, '', {
                      text: 'Total Bs. ' + formatNumber((MontoTotalPedido), 'en-US', '1.2-2'),
                  }, {
                      text: 'Total Anulado: ' + formatNumber((MontoTotalPedidoAnulado), 'en-US', '1.2-2'),
                  }

                  , '\n', {
                      text: 'V) Facturas',
                  }, '\n', {
                      table: TablaFacturas
                  }, '', {
                      text: 'Total Bs. ' + formatNumber((MontoTotalFactura), 'en-US', '1.2-2'),
                  }, {
                      text: 'Total Anulado: ' + formatNumber((MontoTotalFacturaAnulado), 'en-US', '1.2-2'),
                  }

                  , '\n', {
                      text: 'IX) Entregas',
                  }, '\n', {
                      table: TablaEntregas
                  }, '', {
                      text: 'Total Bs. ' + formatNumber((MontoTotalEntrega), 'en-US', '1.2-2'),
                  }, {
                      text: 'Total Anulado: ' + formatNumber((MontoTotalEntregaAnulado), 'en-US', '1.2-2'),
                  }


              ],


              styles: {
                  header: {
                      fontSize: 18,
                      bold: true
                  },
                  subheader: {
                      fontSize: 14,
                      bold: true
                  },
                  bigger: {
                      fontSize: 15,
                      italics: true
                  },
                  tableExample: {
                      margin: [0, 5, 0, 15]
                  },
              },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }

  /**
   * REPORTE DE CAJA EN ROLLO
   */


  public async reportResumenCajaRollo(date) {


    let dataConfigLayaut = await this._configLayaut.getConfig();
    dataConfigLayaut = dataConfigLayaut.find(e=>e.type_document=='DCR');
      

      let pago= new Pagos();
      let dataresumencabecera: any=await pago.resumenCaja(date);
      let dataresumencuerpo: any=await pago.resumenCajamedios(date);
      let dataresumentotal: any=await pago.resumenCajatotal(date);

      console.log("mau : dataresumencabecera");
      console.log(dataresumencabecera);
      console.log(dataresumencuerpo);
      console.log(dataresumentotal);

      
      let valueInterlineado = 0.6; 
      let datauser: any = await this.configService.getSession();
      let TotalUSDGlobal = 0;
      let acumuladoBSfromUSD = 0;
      let header: any;
      let cuerpodetalle1= [];
      let cuerpodetalle2 = [];  
      let cuerpodetalle3 = [];      
      
      /* for (let value of dataresumencabecera) {
          
          let tipopago="";
          if(value.estado==0){tipopago="Transacciones confirmar \n"}
          if(value.estado==1){tipopago="Transacciones fallidas \n"}
          if(value.estado==2){tipopago="Transacciones fallidas \n"}
          if(value.estado==3){tipopago=""}
          if(value.cancelado==3){tipopago="Transacciones Canceladas \n"}
          if(value.otpp==1){tipopago+="Ventas al contado"}
          if(value.otpp==2){tipopago+="Cobros de Facturas"}
          if(value.otpp==3){tipopago+="Anticipos"}            
          
          let linea={
              columns: [{
                  text:tipopago,
                  style: 'small' ,
                  width: '50%'
              },{
                  text:value.cantidad,
                  style: 'small',
                  width: '10%'
              },{
                  text:Calculo.formatMoney(value.total)  +" "+value.moneda,
                  style: 'small',
                  width: '40%',
                  alignment: 'right',
              }]
          }      
              
          cuerpodetalle1.push(linea);
      } */
      for (let value of dataresumencuerpo) {
          let tipopago="";
          /* if(value.estado==0){tipopago="Transacciones confirmar \n"}
          if(value.estado==1){tipopago="Transacciones fallidas \n"}
          if(value.estado==2){tipopago="Transacciones fallidas \n"}
          if(value.estado==3){tipopago=""}
          if(value.cancelado==3){tipopago="Transacciones Canceladas \n"} */
          if(value.formaPago=="PEF"){tipopago+="Efectivo"}
          if(value.formaPago=="PBT"){tipopago+="Transferencia"}
          if(value.formaPago=="PCH"){tipopago+="Cheque"}            
          if(value.formaPago=="PCC"){tipopago+="Tarjeta"}  
          let linea = [
              {
                  columns: [{
                      text:tipopago,
                      //style: 'small',
                      style: 'subheader',
                      width: '50%'
                  },{
                      text:value.cantidad,
                      style: 'small',
                      width: '50%',
                      alignment: 'right',
                      }]
                  
              },
              {
              columns: [{
                  text:'Monto',
                  style: 'small' ,
                  width: '50%'
              },{
                  text:Calculo.formatMoney(value.total)+" "+value.moneda,
                  style: 'small',
                  width: '50%',
                  alignment: 'right',
                  }]
              
          }]      
              
          cuerpodetalle2.push(linea);
      }
      for (let value of dataresumentotal) {
          
          let tipopago="";
          tipopago = "Total "
          console.log("value total", value.total);
          let valor = '';
          if(value.total > 0){
              valor = `${Calculo.formatMoney(value.total)}  ${value.moneda}`;
          }else{
              valor = '0';
          }



          let linea={
              columns: [{
                  text:tipopago,
                  style: 'subheader' ,
                  width: '50%'
              },{
                  text: valor,
                  style: 'subheader',
                  width: '50%',
                  alignment: 'right',
              }]
          }      
              
          cuerpodetalle3.push(linea);
      }

      return new Promise((resolve, reject) => {
          this.objPDF = {
              /* pageSize: { width: 100, height: 'auto' },
              pageMargins: [6, 5, 5, 10], */
              pageSize: { width: dataConfigLayaut?dataConfigLayaut.papel_width:80, height: 'auto' },
              pageMargins: [ dataConfigLayaut?dataConfigLayaut.margin_rigth:6, dataConfigLayaut?dataConfigLayaut.margin_up:3, dataConfigLayaut?dataConfigLayaut.margin_left:3,dataConfigLayaut?dataConfigLayaut.margin_down:10],
              
              
              content: [
                  {
                      text: 'RESUMEN DE CAJA',
                      alignment: 'center',
                      style: 'header'
                  }, {
                      text: '\n  Fecha: ' + formatDate(date, 'dd/MM/yyyy', 'en-US'), alignment: 'right', style: 'subheader', lineHeight: 1
                  }, {
                      text: '\n  Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona, style: 'subheader'
                  }, 
                  
                 /*  {
                      text: '\n  Transacciones ' , style: 'subheader'
                  }, */
                  // cuerpodetalle1,
                  , 
                  
                  {
                      text: '\n  Formas de Pago \n ' , style: 'subheader'
                  },
                  cuerpodetalle2,
                  
                  {
                      text: '\n ' , style: 'subheader'
                  },
                  cuerpodetalle3,

              ],


              styles: {
                header:dataConfigLayaut  ?JSON.parse(dataConfigLayaut.title): {
                    fontSize: 5,
                    bold: true
                },
                subheader:dataConfigLayaut?JSON.parse(dataConfigLayaut.sub_title): {
                    fontSize: 4,
                    bold: true
                },
                small: dataConfigLayaut?JSON.parse(dataConfigLayaut.description):{
                    fontSize: 3
                },
                xsmall:dataConfigLayaut ?JSON.parse(dataConfigLayaut.xsmall): {
                    fontSize: 2.5
                },
                tableExample:{
                    margin: [0, 5, 0, 15]
                },
                tableHeader: dataConfigLayaut ?JSON.parse(dataConfigLayaut.table): {
                    fontSize: 4,
                    bold: true
                },

            },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }

  /**
   * DETALLE DE CAJA ROLLO
   */

  public async reportResumenCajaDetalleRollo(date) {
      let pago= new Pagos();
      let contado: any=await pago.detalleCajaFacturas(date);
      let deuda: any=await pago.detalleCajaDeuda(date);
      let anticipo: any=await pago.detalleCajaAnticipo(date);
      console.log("mau : datadetallecabecera");
      console.log(contado);
      console.log(deuda);
      console.log(anticipo);
      //detalles cabezera
      let datalleCabezera: any = await pago.headerDetail();
      console.log(datalleCabezera);
          
      let valueInterlineado = 0.6; 
      let datauser: any = await this.configService.getSession();
      let TotalUSDGlobal = 0;
      let acumuladoBSfromUSD = 0;
      let header: any;
      let cuerpodetalle1= [];
      let cuerpodetalle2 = [];  
      let cuerpodetalle1fall= [];
      let cuerpodetalle2fall = [];  

      let cuerpodetalle3 = [];
      let sumcuerpodetalle1=0;
      let sumcuerpodetalle2=0;
      let sumcuerpodetalle3=0;      
      
     
      let totalMonedaLocal = 0;
      let totalMonedaUsd = 0;
      let totalMonedaLocalfall = 0;
      let totalMonedaUsdfall = 0;


      let typeMethodPayments = [{ formaPago: 'PEF', total: 0,titlePago:"EFECTIVO" }, { formaPago: 'PBT',titlePago:"TRANSFERENCIA" }, { formaPago: 'PCH', titlePago:"CHEQUE" }, { formaPago: 'PCC',titlePago:"TARJETA" }];
     

      for (let val of typeMethodPayments)
      {
          let titlePago = "";
          let dataPaymentBs: any;
          let dataPaymentUsd: any;

          let dataPaymentBsfall: any;
          let dataPaymentUsdfall: any;
          
          
          
         
          dataPaymentBs = await pago.paymentMethodDetail(val.formaPago,moment(date).format('YYYY-MM-DD'));
          dataPaymentUsd = await pago.paymentMethodDetailUsd(val.formaPago,date);

          console.log("data documentos", dataPaymentBs);
       
        
         
          let subTotalPayment = 0;
          let subTotalPaymentType = 0;
          let subTotalPaymentTypeUsd = 0;

          dataPaymentBs.forEach(value =>{
              if (value.monedaDolar==0 || value.monedaDolar=='' || value.monedaDolar=='null' || value.monedaDolar==null){
                  subTotalPaymentType += value.estado == 3 ? value.monto : 0;
              } else{
                  subTotalPaymentType += value.estado == 3 ? value.monedaLocal : 0;  
              }
              
          });

          let lineat1 = {    
             
              columns: [
                {
                  text:""+val.titlePago,
                  style: 'subheader' ,
                  width: '60%'
              },{
                  text:""+formatNumber((subTotalPaymentType), 'en-US', '1.2-2'),//Calculo.formatMoney(sumcuerpodetalle1)+" BS",
                  style: 'subheader',
                  width: '40%',
                  alignment: 'right',
              }]
          } 
          let lineat0 = {                                
              columns: [
                  {
                      text:"------------------------------------------------------------------------------- \n",
                      style: 'small' ,
                      width: '100%'
                  }]
          } 

           
          cuerpodetalle1.push(lineat1);
          cuerpodetalle1.push(lineat0);


          dataPaymentBsfall = await pago.paymentMethodDetailFall(val.formaPago,moment(date).format('YYYY-MM-DD'));
          dataPaymentUsdfall = await pago.paymentMethodDetailUsdfall(val.formaPago,date);

          console.log("data documentos fall", dataPaymentBsfall);
       
          let subTotalPaymentfall = 0;
          let subTotalPaymentTypefall = 0;
          let subTotalPaymentTypeUsdfall = 0;

          dataPaymentBsfall.forEach(value =>{
              if (value.monedaDolar==0 || value.monedaDolar=='' || value.monedaDolar=='null' || value.monedaDolar==null){
                  subTotalPaymentTypefall += value.estado != 3 ? value.subTotal : 0;
              } else{
                  subTotalPaymentTypefall += value.estado != 3 ? value.monedaLocal : 0;  
              }
          });

          let lineat1fall = {    
             
              columns: [
                {
                  text:""+val.titlePago,
                  style: 'subheader' ,
                  width: '60%'
              },{
                  text:""+formatNumber((subTotalPaymentTypefall), 'en-US', '1.2-2'),//Calculo.formatMoney(sumcuerpodetalle1)+" BS",
                  style: 'subheader',
                  width: '40%',
                  alignment: 'right',
              }]
          } 
          let lineat0fall = {                                
              columns: [
                  {
                      text:"------------------------------------------------------------------------------- \n",
                      style: 'small' ,
                      width: '100%'
                  }]
          } 

           
          cuerpodetalle1fall.push(lineat1fall);
          cuerpodetalle1fall.push(lineat0fall);

          for (let dataValue of dataPaymentBs){
              //subTotalPayment = 0;
              let tipoOperacion = "";
             
              switch (dataValue.otpp) {
                  case 1:
                      tipoOperacion="Contado";
                      break;
                  case 2:
                      tipoOperacion="Pago credito";
                      break;
                  case 3:
                      tipoOperacion="Anticipo";
                      break; 
                  default:
                      break;
              }
              let montoSubTotal = 0;

              


              montoSubTotal = dataValue.monedaDolar==0 || dataValue.monedaDolar=='' || dataValue.monedaDolar=='null' || dataValue.monedaDolar==null?dataValue.subTotal:dataValue.monedaLocal-dataValue.cambio;
              totalMonedaLocal += Number(dataValue.cancelado != 3 ?montoSubTotal:0);
              subTotalPayment += Number(dataValue.cancelado != 3 ?montoSubTotal:0);


             let detailPayment={
                 columns: [
                  
                  {
                      text:[ {
                          // auto-sized columns have their widths based on their content
                        
                          text: 'ID DOCUMENTO:',
                          style:"small",
                          bold:true
                        },
                          `${dataValue.otpp && dataValue.otpp == 2 ? dataValue.docId : (dataValue.codDocumento && dataValue.codDocumento != 'null' ? dataValue.codDocumento : '')}\n`,
                          {
                              // auto-sized columns have their widths based on their content
                            
                              text: 'TIPO:',
                              style:"small",
                              bold:true
                            }, 
                          `${tipoOperacion}\n`,
                          {
                              // auto-sized columns have their widths based on their content
                            
                              text: 'FECHA:',
                              style:"small",
                              bold:true
                          },
                          `${moment(dataValue.fecha).format('DD/MM/YYYY') }\n`,
                          {
                          // auto-sized columns have their widths based on their content
                          width: 'auto',
                          text: 'CLIENTE:',
                          style: "small",
                          bold:true
                      }, `${dataValue.cliente_carcode}`,
                       
                         
                          
                            ],
                      style: 'small' ,
                      width: '100%'
                  },
                  
                  
                 ]              
              } 
              
              cuerpodetalle1.push(detailPayment);
              
              cuerpodetalle1.push({
                  columns: [
                      {
                          text:"Total",
                          style: 'small',
                          bold:true
                         // alignment: 'right',
                          //width: '50%',
                          
                      },
                      {
                          text:""+formatNumber((dataValue.cancelado!=3?montoSubTotal:0), 'en-US', '1.2-2')+'',
                          style: 'small',
                          alignment: 'right',
                          bold:true
                          //idth: '50%',
                          
                      },
              ]
                  
              });
              cuerpodetalle1.push( {
                  text:"\n",
                  style: 'verySmall',
              });
              //colocalndo el total por forma de pago en Bs
            
          }


          for (let dataValue of dataPaymentBsfall){
              //subTotalPayment = 0;
              let tipoOperacion = "";
             
              switch (dataValue.otpp) {
                  case 1:
                      tipoOperacion="Contado";
                      break;
                  case 2:
                      tipoOperacion="Pago credito";
                      break;
                  case 3:
                      tipoOperacion="Anticipo";
                      break; 
                  default:
                      break;
              }
              let montoSubTotal = 0;
              montoSubTotal = dataValue.monedaDolar==0 || dataValue.monedaDolar=='' || dataValue.monedaDolar=='null' || dataValue.monedaDolar==null?dataValue.subTotal:dataValue.monedaLocal-dataValue.cambio;
              totalMonedaLocalfall += Number(dataValue.cancelado != 3 ?montoSubTotal:0);
              subTotalPaymentfall += Number(dataValue.cancelado != 3 ?montoSubTotal:0);


             let detailPayment={
                 columns: [
                  
                  {
                      text:[ {
                          // auto-sized columns have their widths based on their content
                        
                          text: 'ID DOCUMENTO:',
                          style:"small",
                          bold:true
                        },
                          `${dataValue.otpp && dataValue.otpp == 2 ? dataValue.docId : (dataValue.codDocumento && dataValue.codDocumento != 'null' ? dataValue.codDocumento : '')}\n`,
                          {
                              // auto-sized columns have their widths based on their content
                            
                              text: 'TIPO:',
                              style:"small",
                              bold:true
                            }, 
                          `${tipoOperacion}\n`,
                          {
                              // auto-sized columns have their widths based on their content
                            
                              text: 'FECHA:',
                              style:"small",
                              bold:true
                          },
                          `${moment(dataValue.fecha).format('DD/MM/YYYY') }\n`,
                          {
                          // auto-sized columns have their widths based on their content
                          width: 'auto',
                          text: 'CLIENTE:',
                          style: "small",
                          bold:true
                      }, `${dataValue.cliente_carcode}`,
                       
                         
                          
                            ],
                      style: 'small' ,
                      width: '100%'
                  },
                  
                  
                 ]              
              } 
              
              cuerpodetalle1fall.push(detailPayment);
              
              cuerpodetalle1fall.push({
                  columns: [
                      {
                          text:"Total",
                          style: 'small',
                          bold:true
                         // alignment: 'right',
                          //width: '50%',
                          
                      },
                      {
                          text:""+formatNumber((dataValue.cancelado!=3?montoSubTotal:0), 'en-US', '1.2-2')+'',
                          style: 'small',
                          alignment: 'right',
                          bold:true
                          //idth: '50%',
                          
                      },
              ]
                  
              });
              cuerpodetalle1fall.push( {
                  text:"\n",
                  style: 'verySmall',
              });
              //colocalndo el total por forma de pago en Bs
            
          }
        
          /**/

           subTotalPaymentTypeUsd = 0;

          dataPaymentUsd.forEach(value =>{
              
             /* if (value.monedaDolar==0 || value.monedaDolar=='' || value.monedaDolar=='null' || value.monedaDolar==null){
                  subTotalPaymentTypeUsd+=value.subTotal
              } else{
                  subTotalPaymentTypeUsd+=value.monedaLocal  
              }*/
              subTotalPaymentTypeUsd += value.cancelado != 3 ? value.monedaDolar : 0;  
              
          })
        
  
          cuerpodetalle2.push({    
             
              columns: [
                {
                  text:""+val.titlePago,
                  style: 'subheader' ,
                  width: '60%'
              },{
                  text:""+formatNumber((subTotalPaymentTypeUsd), 'en-US', '1.2-2'),//Calculo.formatMoney(sumcuerpodetalle1)+" BS",
                  style: 'subheader',
                  width: '40%',
                  alignment: 'right',
              }]
          });
          cuerpodetalle2.push( {                                
              columns: [
                  {
                      text:"-------------------------------------------------------------------------------  \n",
                      style: 'small' ,
                      width: '100%'
                  }]
          } );

          subTotalPayment = 0;
          for (let dataValue of dataPaymentUsd){
              //subTotalPayment = 0;
              let tipoOperacion = "";
             
              switch (dataValue.otpp) {
                  case 1:
                      tipoOperacion="C";
                      break;
                  case 2:
                      tipoOperacion="D";
                      break;
                  case 3:
                      tipoOperacion="A";
                      break; 
                  default:
                      break;
              }
              let montoSubTotal = 0;
              montoSubTotal = dataValue.cancelado!=3?dataValue.monedaDolar:0;
              totalMonedaUsd += Number(montoSubTotal);
              subTotalPayment += Number(montoSubTotal);


             let detailPayment={
                 columns: [
                  
                  {
                      text:[ {
                          // auto-sized columns have their widths based on their content
                        
                          text: 'ID DOCUMENTO:',
                          style:"small",
                          bold:true
                        },
                          `${dataValue.otpp && dataValue.otpp == 2 ? dataValue.docId : (dataValue.codDocumento && dataValue.codDocumento != 'null' ? dataValue.codDocumento : '')}\n`,
                          {
                              // auto-sized columns have their widths based on their content
                            
                              text: 'TIPO:',
                              style:"small",
                              bold:true
                            }, 
                          `${tipoOperacion}\n`,
                          {
                              // auto-sized columns have their widths based on their content
                            
                              text: 'FECHA:',
                              style:"small",
                              bold:true
                          },
                          `${moment(dataValue.fecha).format('DD/MM/YYYY') }\n`,
                          {
                          // auto-sized columns have their widths based on their content
                          width: 'auto',
                          text: 'CLIENTE:',
                          style: "small",
                          bold:true
                          }, `${dataValue.cliente_carcode}`,
                          
                           ],
                      style: 'small' ,
                      width: '80%'
                  },
                  /* {
                      text:""+formatNumber((montoSubTotal), 'en-US', '1.2-2')+'',
                      style: 'small',
                      alignment: 'right',
                      width: '20%',
                      
                  }, */
                  
                 ]              
             } 
              cuerpodetalle2.push(detailPayment);
              cuerpodetalle2.push({
                  columns: [
                      {
                          text:"Total",
                          style: 'small',
                          bold:true
                         // alignment: 'right',
                          //width: '50%',
                          
                      },
                      {
                          text:""+formatNumber((montoSubTotal), 'en-US', '1.2-2')+'',
                          style: 'small',
                          alignment: 'right',
                          bold:true
                          //idth: '50%',
                          
                      },
              ]
                  
              });
              cuerpodetalle2.push( {
                  text:"\n",
                  style: 'verySmall',
              });
              //colocalndo el total por forma de pago en USD
            
          }
        


           subTotalPaymentTypeUsdfall = 0;
          console.log("DOLARES",dataPaymentUsdfall);

           dataPaymentUsdfall.forEach(value =>{
              
              /* if (value.monedaDolar==0 || value.monedaDolar=='' || value.monedaDolar=='null' || value.monedaDolar==null){
                   subTotalPaymentTypeUsd+=value.subTotal
               } else{
                   subTotalPaymentTypeUsd+=value.monedaLocal  
               }*/
               subTotalPaymentTypeUsdfall += value.cancelado != 3 ? value.monedaDolar : 0;  
               
           })
         
   
           cuerpodetalle2fall.push({    
              
               columns: [
                 {
                   text:""+val.titlePago,
                   style: 'subheader' ,
                   width: '60%'
               },{
                   text:""+formatNumber((subTotalPaymentTypeUsdfall), 'en-US', '1.2-2'),//Calculo.formatMoney(sumcuerpodetalle1)+" BS",
                   style: 'subheader',
                   width: '40%',
                   alignment: 'right',
               }]
           });
           cuerpodetalle2fall.push( {                                
               columns: [
                   {
                       text:"-------------------------------------------------------------------------------  \n",
                       style: 'small' ,
                       width: '100%'
                   }]
           } );
           subTotalPaymentfall = 0;
           for (let dataValue of dataPaymentUsdfall){
               //subTotalPayment = 0;
               let tipoOperacion = "";
              
               switch (dataValue.otpp) {
                   case 1:
                       tipoOperacion="C";
                       break;
                   case 2:
                       tipoOperacion="D";
                       break;
                   case 3:
                       tipoOperacion="A";
                       break; 
                   default:
                       break;
               }
               let montoSubTotal = 0;
               montoSubTotal = dataValue.cancelado!=3?dataValue.monedaDolar:0;
               totalMonedaUsdfall += Number(montoSubTotal);
               subTotalPaymentfall += Number(montoSubTotal);


              let detailPayment={
                  columns: [
                   
                   {
                       text:[ {
                           // auto-sized columns have their widths based on their content
                         
                           text: 'ID DOCUMENTO:',
                           style:"small",
                           bold:true
                         },
                           `${dataValue.otpp && dataValue.otpp == 2 ? dataValue.docId : (dataValue.codDocumento && dataValue.codDocumento != 'null' ? dataValue.codDocumento : '')}\n`,
                           {
                               // auto-sized columns have their widths based on their content
                             
                               text: 'TIPO:',
                               style:"small",
                               bold:true
                             }, 
                           `${tipoOperacion}\n`,
                           {
                               // auto-sized columns have their widths based on their content
                             
                               text: 'FECHA:',
                               style:"small",
                               bold:true
                           },
                           `${moment(dataValue.fecha).format('DD/MM/YYYY') }\n`,
                           {
                           // auto-sized columns have their widths based on their content
                           width: 'auto',
                           text: 'CLIENTE:',
                           style: "small",
                           bold:true
                           }, `${dataValue.cliente_carcode}`,
                           
                            ],
                       style: 'small' ,
                       width: '80%'
                   },
                   /* {
                       text:""+formatNumber((montoSubTotal), 'en-US', '1.2-2')+'',
                       style: 'small',
                       alignment: 'right',
                       width: '20%',
                       
                   }, */
                   
                  ]              
              } 
              cuerpodetalle2fall.push(detailPayment);
              cuerpodetalle2fall.push({
                   columns: [
                       {
                           text:"Total",
                           style: 'small',
                           bold:true
                          // alignment: 'right',
                           //width: '50%',
                           
                       },
                       {
                           text:""+formatNumber((montoSubTotal), 'en-US', '1.2-2')+'',
                           style: 'small',
                           alignment: 'right',
                           bold:true
                           //idth: '50%',
                           
                       },
               ]
                   
               });
               cuerpodetalle2fall.push( {
                   text:"\n",
                   style: 'verySmall',
               });
               //colocalndo el total por forma de pago en USD
             
           }
  
      }



      cuerpodetalle1.push({
          columns: [
              {
                  text:"--------------------------------------------------------------------- \n",
                  style: 'small',
                  bold:true
                 // alignment: 'right',
                  //width: '50%',
                  
              },
             
      ]
          
      });
      let lineTotalMonedaLocal = {    
             
          columns: [
            {
              text:"TOTAL Bs.",
              style: 'subheader' ,
              width: '50%'
          },{
              text:""+formatNumber((totalMonedaLocal), 'en-US', '1.2-2')+"",//Calculo.formatMoney(sumcuerpodetalle1)+" BS",
              style: 'subheader',
              width: '50%',
              alignment: 'right',
          }]
      } 

      cuerpodetalle1.push(lineTotalMonedaLocal);
      cuerpodetalle1.push({
          columns: [
              {
                 
                  text:"==================================================================== \n",
                  style: 'small',
                  bold:true
                 // alignment: 'right',
                  //width: '50%',
                  
              },
             
      ]
          
      });
      cuerpodetalle2.push({
          columns: [
              {
                  text:"--------------------------------------------------------------------- \n",
                  style: 'small',
                  bold:true
                 // alignment: 'right',
                  //width: '50%',
                  
              },
             
      ]
          
      });
      let lineTotalMonedaUs = {    
             
          columns: [
            {
              text:"Total en USD",
              style: 'subheader' ,
              width: '50%'
          },{
              text:""+formatNumber((totalMonedaUsd), 'en-US', '1.2-2')+"",//Calculo.formatMoney(sumcuerpodetalle1)+" BS",
              style: 'subheader',
              width: '50%',
              alignment: 'right',
          }]
      } 

      cuerpodetalle2.push(lineTotalMonedaUs);
      cuerpodetalle2.push({
          columns: [
              {
                 
                  text:"==================================================================== \n",
                  style: 'small',
                  bold:true
                 // alignment: 'right',
                  //width: '50%',
                  
              },
             
      ]
          
      });



      cuerpodetalle1fall.push({
          columns: [
              {
                  text:"--------------------------------------------------------------------- \n",
                  style: 'small',
                  bold:true
                 // alignment: 'right',
                  //width: '50%',
                  
              },
             
      ]
          
      });
      let lineTotalMonedaLocalfall = {    
             
          columns: [
            {
              text:"TOTAL Bs.",
              style: 'subheader' ,
              width: '50%'
          },{
              text:""+formatNumber((totalMonedaLocalfall), 'en-US', '1.2-2')+"",//Calculo.formatMoney(sumcuerpodetalle1)+" BS",
              style: 'subheader',
              width: '50%',
              alignment: 'right',
          }]
      } 

      cuerpodetalle1fall.push(lineTotalMonedaLocalfall);
      cuerpodetalle1fall.push({
          columns: [
              {
                 
                  text:"==================================================================== \n",
                  style: 'small',
                  bold:true
                 // alignment: 'right',
                  //width: '50%',
                  
              },
             
      ]
          
      });


      cuerpodetalle2fall.push({
          columns: [
              {
                  text:"--------------------------------------------------------------------- \n",
                  style: 'small',
                  bold:true
                 // alignment: 'right',
                  //width: '50%',
                  
              },
             
      ]
          
      });
      let lineTotalMonedaUsfall = {    
             
          columns: [
            {
              text:"Total en USD",
              style: 'subheader' ,
              width: '50%'
          },{
              text:""+formatNumber((totalMonedaUsdfall), 'en-US', '1.2-2')+"",//Calculo.formatMoney(sumcuerpodetalle1)+" BS",
              style: 'subheader',
              width: '50%',
              alignment: 'right',
          }]
      } 

      cuerpodetalle2fall.push(lineTotalMonedaUsfall);
      cuerpodetalle2fall.push({
          columns: [
              {
                 
                  text:"==================================================================== \n",
                  style: 'small',
                  bold:true
                 // alignment: 'right',
                  //width: '50%',
                  
              },
             
      ]
          
      });
      
      return new Promise((resolve, reject) => {
          this.objPDF = {
              pageSize: { width: 100, height: 'auto' },
              pageMargins: [6, 5, 5, 10],
              content: [
                  {
                      text: 'DETALLE DE CAJA',
                      alignment: 'center',
                      style: 'header'
                  }, {
                      text: '\n  Fecha: ' + formatDate(date, 'dd/MM/yyyy', 'en-US'), alignment: 'right', style: 'subheader', lineHeight: 1
                  }, {
                      text: '\n  Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona, style: 'subheader'
                  },
                 
                  {
                      text: '\n  Cobro en Bs. ' , style: 'subheader'
                  },
                  cuerpodetalle1,
                  {
                      text: '\n  Cobro en USD ' , style: 'subheader'
                  },
                  cuerpodetalle2,

                  {
                      text: 'TRANSACCIONES FALLIDAS' , style: 'subheader2'
                  },
                  
                  {
                      text: '\n  Cobro en Bs. ' , style: 'subheader'
                  },
                  cuerpodetalle1fall,
                  {
                      text: '\n  Cobro en USD ' , style: 'subheader'
                  },
                  cuerpodetalle2fall,
                 
                 
                  
                  /* {
                      text: '\n  Anticipos ' , style: 'subheader'
                  },
                  cuerpodetalle3,*/

              ],


              styles: {
                  header: {
                      fontSize: 5,
                      bold: true
                  },
                  subheader: {
                      fontSize: 4,
                      bold: true
                  },
                  subheader2: {
                      fontSize: 5,
                      bold: true,
                      alignment: 'center'
                  },
                  small: {
                      fontSize: 4
                  },
                  verySmall: {
                      fontSize: 3
                  },
                  bigger: {
                      fontSize: 15,
                      italics: true
                  },
                  tableExample: {
                      margin: [0, 5, 0, 15]
                  },
                  tableHeader: {
                      fontSize: 4,
                      bold: true
                  },

              },
              defaultStyle: {
                  columnGap: 20
              }
          };
          resolve(true);
      });
  }

  

  generaDetalleRolloCajaDetalle = (dataParam) => {
      let valueInterlineado = 0.8;
      //let TotalUSDGlobal = 0;
      let header: any;
      let cuerpodetalle = [];
      for (let value of dataParam) {
          console.log("value BS ", value);
          let comprobante = "";
          if (value.formaPago == "PCH") {
              comprobante = value.numCheque;
          }

          if (value.formaPago == "PBT") {
              comprobante = value.boucher;
          }
          if (value.formaPago == "PCC") {
              comprobante = value.baucher;
          }
          let pagoBs: any = 0;
          if (value.tipoCambioDolar > 0 && value.formaPagoText == 'Efectivo') {
              pagoBs = (value.monedaDolar * value.tipoCambioDolar).toFixed(2);
              //value.acumuladoActivo=pagoBs-value.totalDocumentosActivos;

              //   TotalUSDGlobal = TotalUSDGlobal + value.monto;
              //  console.log("reporte en dolares", TotalUSDGlobal, value.acumuladoActivoUSD);
          }

          //  arrxproxBS.push([value.documentoPagoId, value.formaPagoText, value.fecha, comprobante, value.bancoCode, formatNumber((value.monto), 'en-US', '1.2-2'), value.monedaDolar, formatNumber((pagoBs), 'en-US', '1.2-2')]);
          /**
                * header detalle
                */
          header = { columns: [{ text: `\n  RECIBO ${value.documentoPagoId}  \n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalle.push(header);
          /**
          * cuerpo detalle
          */

          header = {
              columns: [
                  {
                      text: `\n TIPO DE PAGO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.formaPagoText}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);
          
          header = {
              columns: [
                  {
                      text: `\n NRO TRANSACCION `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${comprobante}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: ` \n BANCO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.bancoCode} `, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: ` \n MONTO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${formatNumber((value.monto), 'en-US', '1.2-2')}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: ` \n PAGO USD `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${formatNumber((value.monedaDolar), 'en-US', '1.2-2')} (BS. ${formatNumber((pagoBs), 'en-US', '1.2-2')})`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);

      }
      return cuerpodetalle;
  }
  /* generaDetalleRolloCajaDetalle = (dataParam) => {
       let header: any;
       let cuerpodetalle = [];
       let TotalUSDGlobalCuenta = 0;
       for (let value of dataParam) {
           console.log("exh dataParam ", value)
           let comprobante = "";
           if (value.formaPago == "PCH") {
               comprobante = value.numCheque;
           }

           if (value.formaPago == "PBT") {
               comprobante = value.boucher;
           }
           if (value.formaPago == "PCC") {
               comprobante = value.baucher;
           }
           let pagoBs: any = 0;
           if (value.tipoCambioDolar > 0 && value.formaPagoText == 'Efectivo') {
               //   pagoBs = value.monedaDolar * value.tipoCambioDolar;
               pagoBs = (value.monedaDolar * value.tipoCambioDolar).toFixed(2);
               
               // value.acumuladoActivo=pagoBs-value.totalDocumentosActivos;
               TotalUSDGlobalCuenta = TotalUSDGlobalCuenta + value.monedaDolar;
           }

           // arrxproxBSanulados.push([0, 0, 0, 0, 0, 0, 0, 0]);
           //  arrxproxBSanulados.push([value.documentoPagoId, value.formaPagoText, value.fecha, comprobante, value.bancoCode, formatNumber((value.monto), 'en-US', '1.2-2')]);

        
           header = { columns: [{ text: ` \n RECIBO ${value.documentoPagoId}  \n`, style: ['tableHeader'], width: '90%' }], };
           cuerpodetalle.push(header);
   

           header = {
               columns: [
                   {
                       text: `\n TIPO DE PAGO `, style: ['small'], width: '50%'
                   },
                   {
                       text: `\n ${value.formaPagoText}`, style: ['small'], alignment: 'right',
                   }
               ],
           };

           cuerpodetalle.push(header);
           header = {
               columns: [
                   {
                       text: `\n NRO TRANSACCION `, style: ['small'], width: '50%'
                   },
                   {
                       text: `\n ${comprobante}`, style: ['small'], alignment: 'right',
                   }
               ],
           };
           cuerpodetalle.push(header);
           header = {
               columns: [
                   {
                       text: ` \n BANCO `, style: ['small'], width: '50%'
                   },
                   {
                       text: `\n ${value.bancoCode} `, style: ['small'], alignment: 'right',
                   }
               ],
           };
           cuerpodetalle.push(header);
           header = {
               columns: [
                   {
                       text: ` \n MONTO `, style: ['small'], width: '50%'
                   },
                   {
                       text: `\n ${formatNumber((value.monto), 'en-US', '1.2-2')}`, style: ['small'], alignment: 'right',
                   }
               ],
           };
           cuerpodetalle.push(header);

           header = {
               columns: [
                   {
                       text: ` \n PAGO USD `, style: ['small'], width: '50%'
                   },
                   {
                       text: `\n ${formatNumber((value.monedaDolar), 'en-US', '1.2-2')} (BS. ${formatNumber((pagoBs), 'en-US', '1.2-2')})`, style: ['small'], alignment: 'right',
                   }
               ],
           };
           cuerpodetalle.push(header);

      
           // cuerpodetalle.push(header);


       }
       return cuerpodetalle;
   }

*/
  /**
   * DETALLE DE VENTAS EN ROLLO 
   */

  public async reportResumenVentasRollo(date) {

      let valueInterlineado = 0.8;
    let documentos = new Documentos();
    let dataConfigLayaut = await this._configLayaut.getConfig();
        dataConfigLayaut = dataConfigLayaut.find(e=>e.type_document=='RDV');

      console.log("DEVD v_documentoview9 ", await documentos.selectAllVista());
      let dataResumenCaja: any = await documentos.selectAllResumenVentas(date, "factura");
      console.log("DEVD dataResumenCaja ", dataResumenCaja);

      let arrxproxBS = [];
      let datauser: any = await this.configService.getSession();
      arrxproxBS.push([{
          text: 'TIPO DOCUMENTO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'MONEDA',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ACTIVO',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'CANTIDAD',
          style: 'tableHeader',
          alignment: 'center'
      }, {
          text: 'ANULADO',
          style: 'tableHeader',
          alignment: 'center'
      }]);


      let MontoTotal = 0.0;
      let MontoTotalAnulado = 0;
      let header: any;
      let cuerpodetalle = [];

      for (let value of dataResumenCaja) {
          if (!value.CantidadAnulado) value.CantidadAnulado = 0;
          if (!value.MontoAnulado) value.MontoAnulado = 0;
          if (!value.CantidadActivo) value.CantidadActivo = 0;
          if (!value.MontoActivo) value.MontoActivo = 0;
          //   if (!value.CantidadAnulado) value.CantidadAnulado = 0;
          arrxproxBS.push([value.DocType, value.DocCur, value.CantidadActivo, formatNumber((value.MontoActivo), 'en-US', '1.2-2'), value.CantidadAnulado, formatNumber((value.MontoAnulado), 'en-US', '1.2-2')]);

          // if (value.tipoestado == 'anulado') {
          //  if (!value.MontoAnulado) value.MontoAnulado = 0;

          //   }
          //  else {

          //   }

          /**
           * header detalle
           */
          header = { columns: [{ text: `\n  ${value.DocType}  \n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalle.push(header);
          /**
          * cuerpo detalle
          */

          header = {
              columns: [
                  {
                      text: `\n CANTIDAD `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.CantidadActivo}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: `\n ACTIVO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${formatNumber((value.MontoActivo), 'en-US', '1.2-2')}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: `\n  CANTIDAD ANULADO`, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.CantidadAnulado}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);
          header = {
              columns: [
                  {
                      text: ` \n MONTO ANULADO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${formatNumber((value.MontoAnulado), 'en-US', '1.2-2')} `, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalle.push(header);
          console.log("DEVD value ", value);
          MontoTotal = MontoTotal + parseFloat(value.MontoActivo);
          MontoTotalAnulado = MontoTotalAnulado + parseFloat(value.MontoAnulado);

      }
      // let TablaDocumentos = { body: arrxproxBS };


      return new Promise((resolve, reject) => {
          this.objPDF = {
              //pageSize: { width: 100, height: 'auto' },
              //pageMargins: [6, 5, 5, 10],
              pageSize: { width: dataConfigLayaut?dataConfigLayaut.papel_width:80, height: 'auto' },
              pageMargins: [ dataConfigLayaut?dataConfigLayaut.margin_rigth:6, dataConfigLayaut?dataConfigLayaut.margin_up:3, dataConfigLayaut?dataConfigLayaut.margin_left:3,dataConfigLayaut?dataConfigLayaut.margin_down:10],
              
              content: [
                  {
                      text: 'RESUMEN DE DOCUMENTOS',
                      alignment: 'center',
                      style: 'header'
                  }, {
                      text: '\n Fecha: ' + formatDate(date, 'dd/MM/yyyy', 'en-US'), alignment: 'right', style: 'subheader'
                  }, {
                      text: '\n Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona, style: 'subheader'
                  }, {
                      text: '\n I) Documentos ', style: 'subheader'
                  },
                  cuerpodetalle,
                  {
                      text: '\n Total Bs. ' + formatNumber((MontoTotal), 'en-US', '1.2-2'), style: 'subheader'
                  }, '', {
                      text: 'Total Bs. Anulado ' + formatNumber((MontoTotalAnulado), 'en-US', '1.2-2'), style: 'subheader'
                  }
              ],

              styles: {
                  
                header:dataConfigLayaut  ?JSON.parse(dataConfigLayaut.title): {
                  fontSize: 5,
                  bold: true
              },
              subheader:dataConfigLayaut?JSON.parse(dataConfigLayaut.sub_title): {
                  fontSize: 4,
                  bold: true
              },
              small: dataConfigLayaut?JSON.parse(dataConfigLayaut.description):{
                  fontSize: 3
              },
              xsmall:dataConfigLayaut ?JSON.parse(dataConfigLayaut.xsmall): {
                  fontSize: 2.5
              },
              tableExample:{
                  margin: [0, 5, 0, 15]
              },
              tableHeader: dataConfigLayaut ?JSON.parse(dataConfigLayaut.table): {
                  fontSize: 4,
                  bold: true
              },


              },
              defaultStyle: {
                  columnGap: 5
              }
          };
          resolve(true);
      });
  }


  /**
   * DETALLE DE VENTAS EN ROLLO
   * @param date 
   * @returns 
   */

  public async reportResumenVentasDetalleRollo(date){

      let dataConfigLayaut = await this._configLayaut.getConfig();
      dataConfigLayaut = dataConfigLayaut.find(e=>e.type_document=='DVR');
      let valueInterlineado = 0.8;
      let datauser: any = await this.configService.getSession();
      let documentos = new Documentos();
      let dataService: any = await documentos.selectAllResumenDocOfertas(date, "", 0);

      console.log("dataService ", dataService);

      let MontoTotalOferta = 0.0;
      let MontoTotalOfertaAnulado = 0.0;
      let dataOfertas = dataService.filter((item) => { return item.DocType == 'DOF'; });
      console.log("primer ofertas filter ", dataOfertas);

      let arrxproxBS = [];
      arrxproxBS.push([{ text: 'NRO DOCUMENTO', style: 'tableHeader', alignment: 'center' },
      { text: 'CLIENTE', style: 'tableHeader', alignment: 'center' },
      { text: 'FECHA', style: 'tableHeader', alignment: 'center' },
      { text: 'ESTADO', style: 'tableHeader', alignment: 'center' },
      { text: 'MONEDA', style: 'tableHeader', alignment: 'center' },
      { text: 'TOTAL', style: 'tableHeader', alignment: 'center' }
      ]);
      let headerO: any;
      let cuerpodetalleO = [];
      if (dataOfertas.length == 0) cuerpodetalleO = [];
      for (let value of dataOfertas) {
          arrxproxBS.push([value.cod, value.CardName, value.DocDate, value.tipoestado, value.DocCur, formatNumber((value.monto), 'en-US', '1.2-2')]);
          if (value.canceled == '3') {
              MontoTotalOfertaAnulado = MontoTotalOfertaAnulado + parseFloat(value.monto);
          }
          else {
              MontoTotalOferta = MontoTotalOferta + parseFloat(value.monto);
          }
          /**
        * header detalle
        */
          headerO = { columns: [{ text: `\n  ${value.cod}  ${(value.canceled == '3') ? '(ANULADO)' : ''} \n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalleO.push(headerO);
          /**
          * cuerpo detalle
          */

          headerO = {
              columns: [
                  {
                      text: `\n CLIENTE `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.CardName}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleO.push(headerO);
          headerO = {
              columns: [
                  {
                      text: `\n FECHA `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.DocDate}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleO.push(headerO);
          headerO = {
              columns: [
                  {
                      text: `\n  ESTADO`, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.tipoestado}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleO.push(headerO);
          headerO = {
              columns: [
                  {
                      text: ` \n MONTO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${formatNumber((value.monto), 'en-US', '1.2-2')} `, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleO.push(headerO);
      }
      let TablaOfertas = cuerpodetalleO;

      let MontoTotalPedido = 0.0;
      let MontoTotalPedidoAnulado = 0.0;
      let dataPedido = dataService.filter((item) => { return item.DocType == 'DOP'; });

      arrxproxBS = [];
      arrxproxBS.push([{ text: 'NRO DOCUMENTO', style: 'tableHeader', alignment: 'center' },
      { text: 'CLIENTE', style: 'tableHeader', alignment: 'center' },
      { text: 'FECHA', style: 'tableHeader', alignment: 'center' },
      { text: 'ESTADO', style: 'tableHeader', alignment: 'center' },
      { text: 'MONEDA', style: 'tableHeader', alignment: 'center' },
      { text: 'TOTAL', style: 'tableHeader', alignment: 'center' }
      ]);
      let headerP: any;
      let cuerpodetalleP = [];

      console.log("DATOS",dataPedido);

      for (let value of dataPedido) {

          arrxproxBS.push([value.cod, value.CardName, value.DocDate, value.tipoestado, value.DocCur, formatNumber((value.monto), 'en-US', '1.2-2')]);
         
          if (value.canceled == '3') {
              MontoTotalPedidoAnulado = MontoTotalPedidoAnulado + parseFloat(value.monto);
          }
          else {
              MontoTotalPedido = MontoTotalPedido + parseFloat(value.monto);
          }

          /**
     * header detalle
     */
          headerP = { columns: [{ text: `\n  ${value.cod}  ${(value.canceled == '3') ? '(ANULADO)' : ''}  \n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalleP.push(headerP);
          /**
          * cuerpo detalle
          */

          headerP = {
              columns: [
                  {
                      text: `\n CLIENTE `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.CardName}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleP.push(headerP);
          headerP = {
              columns: [
                  {
                      text: `\n FECHA `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.DocDate}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleP.push(headerP);
          headerP = {
              columns: [
                  {
                      text: `\n  ESTADO`, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.tipoestado}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleP.push(headerP);
          headerP = {
              columns: [
                  {
                      text: ` \n MONTO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${formatNumber((value.monto), 'en-US', '1.2-2')} `, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleP.push(headerP);
      }
      let TablaPedidos = cuerpodetalleP;

      let MontoTotalFactura = 0.0;
      let MontoTotalFacturaAnulado = 0.0;

      let dataFacturas = dataService.filter((item) => { return item.DocType == 'DFA'; });
      console.log("dataBs filter dataFacturas ", dataFacturas);
      arrxproxBS = [];
      arrxproxBS.push([{ text: 'NRO DOCUMENTO', style: 'tableHeader', alignment: 'center' },
      { text: 'CLIENTE', style: 'tableHeader', alignment: 'center' },
      { text: 'FECHA', style: 'tableHeader', alignment: 'center' },
      { text: 'ESTADO', style: 'tableHeader', alignment: 'center' },
      { text: 'MONEDA', style: 'tableHeader', alignment: 'center' },
      { text: 'TOTAL', style: 'tableHeader', alignment: 'center' }
      ]);
      let headerF: any;
      let cuerpodetalleF = [];
      // if (dataFacturas.length == 0) cuerpodetalle = [];
      for (let value of dataFacturas) {
          arrxproxBS.push([value.cod, value.CardName, value.DocDate, value.tipoestado, value.DocCur, formatNumber((value.monto), 'en-US', '1.2-2')]);
          if (value.canceled == '3') {
              MontoTotalFacturaAnulado = MontoTotalFacturaAnulado + parseFloat(value.monto);
          }
          else {
              MontoTotalFactura = MontoTotalFactura + parseFloat(value.monto);
          }
          /**
     * header detalle
     */
          headerF = { columns: [{ text: `\n  ${value.cod} ${(value.canceled == '3') ? '(ANULADO)' : ''}  \n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalleF.push(headerF);
          /**
          * cuerpo detalle
          */

          headerF = {
              columns: [
                  {
                      text: `\n CLIENTE `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.CardName}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleF.push(headerF);
          headerF = {
              columns: [
                  {
                      text: `\n FECHA `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.DocDate}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleF.push(headerF);
          headerF = {
              columns: [
                  {
                      text: `\n  ESTADO`, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.tipoestado}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleF.push(headerF);
          headerF = {
              columns: [
                  {
                      text: ` \n MONTO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${formatNumber((value.monto), 'en-US', '1.2-2')} `, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleF.push(headerF);
      }
      let TablaFacturas = cuerpodetalleF;
      console.log("dataBs filter TablaFacturas ", TablaFacturas);
      let MontoTotalEntrega = 0.0;
      let MontoTotalEntregaAnulado = 0.0;

      let dataEntregas = dataService.filter((item) => { return item.DocType == 'DOE'; });
      arrxproxBS = [];
      arrxproxBS.push([{ text: 'NRO DOCUMENTO', style: 'tableHeader', alignment: 'center' },
      { text: 'CLIENTE', style: 'tableHeader', alignment: 'center' },
      { text: 'FECHA', style: 'tableHeader', alignment: 'center' },
      { text: 'ESTADO', style: 'tableHeader', alignment: 'center' },
      { text: 'MONEDA', style: 'tableHeader', alignment: 'center' },
      { text: 'TOTAL', style: 'tableHeader', alignment: 'center' }
      ]);
      let headerE: any;
      let cuerpodetalleE = [];
      for (let value of dataEntregas) {
          arrxproxBS.push([value.cod, value.CardName, value.DocDate, value.tipoestado, value.DocCur, formatNumber((value.monto), 'en-US', '1.2-2')]);
          if (value.canceled == '3') {
              MontoTotalEntregaAnulado = MontoTotalEntregaAnulado + parseFloat(value.monto);
          }
          else {
              MontoTotalEntrega = MontoTotalEntrega + parseFloat(value.monto);
          }
          /**
     * header detalle
     */
          headerE = { columns: [{ text: `\n  ${value.cod}  ${(value.canceled == '3') ? '(ANULADO)' : ''}\n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalleE.push(headerE);
          /**
          * cuerpo detalle
          */

          headerE = {
              columns: [
                  {
                      text: `\n CLIENTE `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.CardName}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleE.push(headerE);
          headerE = {
              columns: [
                  {
                      text: `\n FECHA `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.DocDate}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleE.push(headerE);
          headerE = {
              columns: [
                  {
                      text: `\n  ESTADO`, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${value.tipoestado}`, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleE.push(headerE);
          headerE = {
              columns: [
                  {
                      text: ` \n MONTO `, style: ['small'], width: '50%', lineHeight: valueInterlineado
                  },
                  {
                      text: `\n ${formatNumber((value.monto), 'en-US', '1.2-2')} `, style: ['small'], alignment: 'right', lineHeight: valueInterlineado
                  }
              ],
          };
          cuerpodetalleE.push(headerE);
      }
      let TablaEntregas = cuerpodetalleE;





      return new Promise((resolve, reject) => {
          this.objPDF = {
              //pageSize: { width: 100, height: 'auto' },
              //pageMargins: [6, 5, 5, 10],
              pageSize: { width: dataConfigLayaut?dataConfigLayaut.papel_width:80, height: 'auto' },
              pageMargins: [ dataConfigLayaut?dataConfigLayaut.margin_rigth:6, dataConfigLayaut?dataConfigLayaut.margin_up:3, dataConfigLayaut?dataConfigLayaut.margin_left:3,dataConfigLayaut?dataConfigLayaut.margin_down:10],
              
              content: [
                  {
                      text: 'DETALLE DE VENTAS',
                      alignment: 'center',
                      style: 'header'
                  }, {
                      text: '\n Fecha: ' + formatDate(date, 'dd/MM/yyyy', 'en-US'), alignment: 'right', style: 'subheader'
                  }, {
                      text: '\n Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona, style: 'subheader'
                  }, {
                      text: '\n I) Ofertas', style: 'subheader'
                  },
                  TablaOfertas
                  , {
                      text: '\n Total Bs. ' + formatNumber((MontoTotalOferta), 'en-US', '1.2-2'), style: 'subheader'
                  }, {
                      text: 'Total Anulado: ' + formatNumber((MontoTotalOfertaAnulado), 'en-US', '1.2-2'), style: 'subheader'
                  }

                  , {
                      text: '\n II) Pedidos', style: 'subheader'
                  },
                  TablaPedidos
                  , {
                      text: '\n Total Bs. ' + formatNumber((MontoTotalPedido), 'en-US', '1.2-2'), style: 'subheader'
                  }, {
                      text: '\n Total Anulado: ' + formatNumber((MontoTotalPedidoAnulado), 'en-US', '1.2-2'), style: 'subheader'
                  }

                  , {
                      text: '\n IV) Facturas', style: 'subheader'
                  },
                  TablaFacturas
                  , {
                      text: '\n Total Bs. ' + formatNumber((MontoTotalFactura), 'en-US', '1.2-2'), style: 'subheader'
                  }, {
                      text: 'Total Anulado: ' + formatNumber((MontoTotalFacturaAnulado), 'en-US', '1.2-2'), style: 'subheader'
                  }

                  , {
                      text: '\n V) Entregas', style: 'subheader'
                  },
                  TablaEntregas
                  , {
                      text: '\n Total Bs. ' + formatNumber((MontoTotalEntrega), 'en-US', '1.2-2'), style: 'subheader'
                  }, {
                      text: 'Total Anulado: ' + formatNumber((MontoTotalEntregaAnulado), 'en-US', '1.2-2'), style: 'subheader'
                  }


              ],
              styles: {
                header:dataConfigLayaut  ?JSON.parse(dataConfigLayaut.title): {
                    fontSize: 5,
                    bold: true
                },
                subheader:dataConfigLayaut?JSON.parse(dataConfigLayaut.sub_title): {
                    fontSize: 4,
                    bold: true
                },
                small: dataConfigLayaut?JSON.parse(dataConfigLayaut.description):{
                    fontSize: 3
                },
                xsmall:dataConfigLayaut ?JSON.parse(dataConfigLayaut.xsmall): {
                    fontSize: 2.5
                },
                tableExample:{
                    margin: [0, 5, 0, 15]
                },
                tableHeader: dataConfigLayaut ?JSON.parse(dataConfigLayaut.table): {
                    fontSize: 4,
                    bold: true
                },

              },
              defaultStyle: {
                  columnGap: 5
              }
          };
          resolve(true);
      });
  }

/*
* DETALLE BONIFICAIONES Y DESCUENTOS VIGENTES
*/

  public async reportBonificaciones() {
      console.log("****** reportBonificaciones() ");
    let documentos = new Documentos();
    let dataConfigLayaut = await this._configLayaut.getConfig();
        dataConfigLayaut = dataConfigLayaut.find(e=>e.type_document=='DBD');
      // console.log(" await documentos.selectBonificaiones ", await documentos.selectBonificaiones());
      let dataSelect: any = await documentos.selectBonificaionesDescuentos("BONIFICACION");
      console.log("dataSelect --->", dataSelect);

      let showAllCompras: any = await documentos.showAllCompras();
      console.log("showAllCompras --->", showAllCompras); // code_bonificacion_cabezera 

      let showAllRegalos: any = await documentos.showAllRegalos();
      console.log("showAllRegalos ---> ", showAllRegalos); // code_bonificacion_cabezera 


      let datauser: any = await this.configService.getSession();

      let cuerpodetalle = [];
      let cuerpodetalle2 = [];
      let header: any;
      let header2: any;

      //Mau
      let Aux_modelBonos = new bonificacionesDocCabezera();
      let Aux_dataBonos: any = [];

      Aux_dataBonos = await Aux_modelBonos.selectOne();
      console.log("BONIFICACION Aux_dataBonos ---> ", Aux_dataBonos);
      //
      dataSelect.forEach(element => {
          console.log("element --->", element);
          let dertalleCompras = showAllCompras.filter((item) => {
              return item.code_bonificacion_cabezera == element.code;
          });
          if (element.grupo_cliente == "0") {
              element.grupo_cliente = "TODOS";
          }

          let dataDoc = Aux_dataBonos.filter(data => data.id_bonificacion_cabezera == element.code);
          console.log("BONIFICACION CABEZERA ", dataDoc);

          console.log("dertalleCompras ", dertalleCompras);
          //header
          header = { columns: [{ text: `\n  ${element.nombre} (${element.grupo_cliente}) \n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalle.push(header);
          header = { columns: [{ text: `\n  Cantidad compra: ${element.cantidad_compra} \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle.push(header);

          if (element.id_regla_bonificacion == "9" && dataDoc.length > 0) {
              header = { columns: [{ text: `\n  Máximo compra:  ${dataDoc[0].cantidad_maxima_compra}  \n`, style: ['subheader'], width: '90%' }], };
              cuerpodetalle.push(header);
          }


          header = { columns: [{ text: `\n   Cantidad Regalo: ${element.cantidad_regalo} \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle.push(header);
          // maximo_regalo: 4

          if (element.id_regla_bonificacion == "13" && dataDoc.length > 0) {
              header = { columns: [{ text: `\n  Porcentaje Bonificación:  ${dataDoc[0].porcentaje}  \n`, style: ['subheader'], width: '90%' }], };
              cuerpodetalle.push(header);
          }


          header = { columns: [{ text: `\n  Tipo: ${element.opcional} \n`, style: ['subheader'], width: '90%' }], };

          header = { columns: [{ text: `\n  Fecha inicio: ${formatDate(element.fecha_inicio, 'dd/MM/yyyy', 'en-US')}, Fecha fin: ${formatDate(element.fecha_fin, 'dd/MM/yyyy', 'en-US')} \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle.push(header);

          header = { columns: [{ text: `\n  Máximo regalo: ${element.maximo_regalo}  \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle.push(header);

          //detalle
          let strAux = "";
          for (let value of dertalleCompras) {
              strAux = "";
              if (element.id_regla_bonificacion == "11") {
                  strAux = `\n CANT. ESP.: ${value.producto_cantidad} `

              }

              header = {
                  columns: [
                      {
                          text: `\n PRODUCTO COMPRA ${strAux}`, style: ['small'], width: '35%'
                      },
                      {
                          text: `\n ${value.code_compra} - ${value.producto_nombre_compra}`, style: ['small'], alignment: 'right',
                      }
                  ],
              };
              cuerpodetalle.push(header);
          }

          let detalleRegalos = showAllRegalos.filter((item) => {
              return item.code_bonificacion_cabezera == element.code;
          });


          console.log("detalleRegalos ", detalleRegalos);

          //detalle
          for (let value of detalleRegalos) {
              header = {
                  columns: [
                      {
                          text: `\n PRODUCTO REGALO `, style: ['small'], width: '35%'
                      },
                      {
                          text: `\n ${value.code_regalo} - ${value.producto_nombre_regalo}`, style: ['small'], alignment: 'right',
                      }
                  ],
              };
              cuerpodetalle.push(header);
          }




      });

      let dataSelect2: any = await documentos.selectBonificaionesDescuentos("DESCUENTO");
      console.log("******* dataSelect2 ", dataSelect2);

      console.log("******* dataSelect2 BonifDocsCab: ", Aux_dataBonos);

      dataSelect2.forEach(element => {
          console.log("element ", element);
          let dertalleCompras = showAllCompras.filter((item) => {
              return item.code_bonificacion_cabezera == element.code;
          });
          if (element.grupo_cliente == "0") {
              element.grupo_cliente = "TODOS";
          }

          console.log("dertalleCompras ", dertalleCompras);
          //header
          header2 = { columns: [{ text: `\n ${element.nombre} (${element.grupo_cliente}) \n`, style: ['tableHeader'], width: '90%' }], };
          cuerpodetalle2.push(header2);
          // header = { columns: [{ text: `\n  Máximo regalo: ${element.maximo_regalo}  \n`, style: ['subheader'], width: '90%' }], };
          // cuerpodetalle2.push(header);
          ///MAU
         
          if (Number(element.cantidad_compra) > 0) {
              header2 = { columns: [{ text: `\n  Cantidad compra: ${element.cantidad_compra}   \n`, style: ['subheader'], width: '90%' }], };

          } else {
              let cantidad = Aux_dataBonos.filter(data => data.id_bonificacion_cabezera == element.code);
              console.log("cantidad ", cantidad);
              if(cantidad.length > 0){
                  header2 = { columns: [{ text: `\n  Objetivo de Venta (Bs.) : ${cantidad[0].monto_total}   \n`, style: ['subheader'], width: '90%' }], };
              }
             
          }
          // header2 = { columns: [{ text: `\n  Cantidad compra: ${element.cantidad_compra}   \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle2.push(header2);

          if (element.id_regla_bonificacion == "10") {
              let maximo_compra_descuentos = Aux_dataBonos.filter(data => data.id_bonificacion_cabezera == element.code);
              console.log("maximo compra descuentos ", maximo_compra_descuentos);
              if (maximo_compra_descuentos.length > 0) {
                  header2 = { columns: [{ text: `\n  Máximo compra:  ${maximo_compra_descuentos[0].cantidad_maxima_compra}  \n`, style: ['subheader'], width: '90%' }], };
                  cuerpodetalle2.push(header2);
              }

          }

          header2 = { columns: [{ text: `\n  Porcentaje descuento: ${element.cantidad_regalo}% \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle2.push(header2);

          header2 = { columns: [{ text: `\n  Descuento extra:  ${element.extra_descuento}% \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle2.push(header2);

          header2 = { columns: [{ text: `\n  Tipo: ${element.opcional} \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle2.push(header2);


          header2 = { columns: [{ text: `\n  Fecha inicio: ${formatDate(element.fecha_inicio, 'dd/MM/yyyy', 'en-US')}, Fecha fin: ${formatDate(element.fecha_fin, 'dd/MM/yyyy', 'en-US')} \n`, style: ['subheader'], width: '90%' }], };
          cuerpodetalle2.push(header2);

          //detalle
          let strAux;
          let strAuxDesc;
          for (let value of dertalleCompras) {
              strAux = "";
              strAuxDesc = "";
              if (element.id_regla_bonificacion == "12") {
                  strAux = `\n CANT. ESP.: ${value.producto_cantidad} `
                  if (value.estado == 1) {
                      strAuxDesc = `(APLICA DESC)`
                  }
              }

              header2 = {
                  columns: [
                      {
                          text: `\n PRODUCTO COMPRA ${strAux} ${strAuxDesc} `, style: ['small'], width: '35%'
                      },
                      {
                          text: `\n ${value.code_compra} - ${value.producto_nombre_compra}`, style: ['small'], alignment: 'right',
                      }
                  ],
              };
              cuerpodetalle2.push(header2);
          }
      });
      //for (let value of dataSelect) {
      //  console.log("value ", value);
      /*
                  let dertalleCompras = showAllCompras.filter((item) => {
                      return item.code_bonificacion_cabezera == value.code;
                  });
      
      
                  console.log("dertalleCompras ", dertalleCompras);
                  //  arrxproxBS.push([value.DocType, value.DocCur, value.CantidadActivo, formatNumber((value.MontoActivo), 'en-US', '1.2-2'), value.CantidadAnulado, formatNumber((value.MontoAnulado), 'en-US', '1.2-2')]);
      
      
      
                  //header
                  header = { columns: [{ text: `\n  ${value.nombre} (${value.grupo_cliente}) \n`, style: ['tableHeader'], width: '90%' }], };
                  cuerpodetalle.push(header);
      
                  //detalle
                  for (let item of dertalleCompras) {
                      header = {
                          columns: [
                              {
                                  text: `\n PRODUCTO COMPRA `, style: ['small'], width: '35%'
                              },
                              {
                                  text: `\n ${item.producto_nombre_compra}`, style: ['small'], alignment: 'right',
                              }
                          ],
                      };
                      cuerpodetalle.push(header);
                  }
      
      */

      //  }
      // let TablaDocumentos = { body: arrxproxBS };


      return new Promise((resolve, reject) => {
          this.objPDF = {
              //pageSize: { width: 100, height: 'auto' },
              //pageMargins: [6, 5, 5, 10],}
              pageSize: { width: dataConfigLayaut?dataConfigLayaut.papel_width:80, height: 'auto' },
              pageMargins: [ dataConfigLayaut?dataConfigLayaut.margin_rigth:6, dataConfigLayaut?dataConfigLayaut.margin_up:3, dataConfigLayaut?dataConfigLayaut.margin_left:3,dataConfigLayaut?dataConfigLayaut.margin_down:10],
              
              content: [
                  {
                      text: 'BONIFICACIONES Y DESCUENTOS',
                      alignment: 'center',
                      style: 'tableHeader'
                  }, '\n', {
                      text: '\n Usuario: ' + datauser[0].nombrePersona + ' ' + datauser[0].apellidoPPersona + ' ' + datauser[0].apellidoMPersona, style: 'subheader'
                  }, {
                      text: '\n I) BONIFICACIONES ', style: 'tableHeader'
                  },
                  cuerpodetalle,
                  '\n', {
                      text: 'I) DESCUENTOS ', style: 'tableHeader'
                  },
                  cuerpodetalle2,
                  '\n',

              ],

              styles: {
                  header:dataConfigLayaut  ?JSON.parse(dataConfigLayaut.title): {
                      fontSize: 5,
                      bold: true
                  },
                  subheader:dataConfigLayaut?JSON.parse(dataConfigLayaut.sub_title): {
                      fontSize: 3,
                      bold: true
                  },
                  small:dataConfigLayaut?JSON.parse(dataConfigLayaut.description): {
                      fontSize: 3
                  },
                    xsmall:dataConfigLayaut ?JSON.parse(dataConfigLayaut.xsmall): {
                      fontSize: 2.5
                  },
                  tableExample: {
                      margin: [0, 5, 0, 15]
                  },
                  tableHeader:dataConfigLayaut ?JSON.parse(dataConfigLayaut.table): {
                      fontSize: 4,
                      bold: true
                  },

              },
              defaultStyle: {
                  columnGap: 5
              }
          };
          resolve(true);
      });
  }

  public async cierrecaja(date){


    let dataConfigLayaut = await this._configLayaut.getConfig();
    dataConfigLayaut = dataConfigLayaut.find(e=>e.type_document=='CDC');

    const fecha = new Date();
    let fecha_actual =  await this.formatoFecha(fecha, 'dd/mm/yy');
    var hora = fecha.toLocaleTimeString()
    let datauser: any = await this.configService.getSession();

    let empresa = datauser[0].empresa[0].nombre;

    let nombre_equipo = datauser[0].equipo;

    let documentos = new Documentos();
    let resx: any = await documentos.Factporfecha(date);
    
    console.log("DATOS",resx);

    let cantidad_fact = resx.length;
    let total = '';
    let totalD = '';
    let totalA = '';
    let sum = 0;
    let sumD = 0;

    for await (let doc of resx) {
        sum += doc.DocumentTotalPay;
        if(doc.tipoestado != 'anulado'){
            console.log(doc.cod);
            console.log(doc.DocumentTotalPay)
            sumD += doc.DocumentTotalPay;
        }
    }
    
    total = formatNumber(sum, 'en-US', '1.2-2');
    totalD = formatNumber(sumD, 'en-US', '1.2-2');
    totalA = formatNumber(sum-sumD, 'en-US', '1.2-2');


    let factInicial = '';
    let factFinal = '';

    this.spinnerDialog.show();

    if (this.network.type == 'none') {
        console.log("sin internet");

       
        factInicial = resx[0].cod.substring(resx[0].cod.length - 5);
        factFinal = resx[cantidad_fact-1].cod.substring(resx[cantidad_fact-1].cod.length - 5);

        let band = 0;
        let auxiliar = '';
        for (let i = 0; i < factInicial.length; i++) {
            if(factInicial[i] != '0' && band == 0){
                auxiliar = factInicial.substring(i, factInicial.length);
                band = 1;
            }
        }
        factInicial = auxiliar;
        auxiliar = '';
        band = 0;

        for (let i = 0; i < factFinal.length; i++) {
            if(factFinal[i] != '0' && band == 0){
                auxiliar = factFinal.substring(i, factFinal.length);
                band = 1;
            }
        }

        factFinal = auxiliar;


        console.log(factInicial);
        console.log(factFinal);


    }else{

        factInicial = resx[0].cod;
        factFinal = resx[cantidad_fact-1].cod;

        let dat1: any = {
            "iddoc": factInicial,
        };
        let dat2: any = {
            "iddoc": factFinal,
        };

        let xData: any = await this.dataService.servisReportConsultaNumfacPost(dat1);
        let xJson = JSON.parse(xData.data);
        console.log("xJson.respuesta", xJson.respuesta[0]);
        factInicial = xJson.respuesta[0].U_LB_NumeroFactura;

        let xData2: any = await this.dataService.servisReportConsultaNumfacPost(dat2);
        let xJson2 = JSON.parse(xData2.data);
        console.log("xJson.respuesta", xJson2.respuesta[0]);
        factFinal = xJson2.respuesta[0].U_LB_NumeroFactura

    }
    this.spinnerDialog.hide();

    return new Promise((resolve, reject) => {
        try {
            this.objPDF = {
                pageSize: { width: dataConfigLayaut?dataConfigLayaut.papel_width:80, height: 'auto' },
                pageMargins: [ dataConfigLayaut?dataConfigLayaut.margin_rigth:6, dataConfigLayaut?dataConfigLayaut.margin_up:3, dataConfigLayaut?dataConfigLayaut.margin_left:3,dataConfigLayaut?dataConfigLayaut.margin_down:10],

                content: [

                    {
                        text: empresa,
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    },

                    {
                        text: 'FECHA DE IMPRESION',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: fecha_actual,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: 'HORA DE IMPRESION',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: hora,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: '.',
                        style: ['header'],
                        width: '100%',
                        alignment: 'letf',  
                        color: '#ffffff'                      
                    }, 
                    {
                        columns: [
                            {
                                text: 'Factura Inicial: ',
                                style: ['subheader'],
                                alignment: 'letf',
                                width: '40%',
                            },
                            {
                                text: `${factInicial}`,
                                style: ['subheader'],
                                alignment: 'letf',
                                width: '50%',
                                bold: true
                            }
                        ]
                    }, {
                        columns: [
                            {
                                text: 'Factura Final: ',
                                style: ['subheader'],
                                alignment: 'letf',
                                width: '40%',
                            },
                            {
                                text: `${factFinal}`,
                                style: ['subheader'],
                                alignment: 'letf',
                                width: '50%',
                                bold: true
                            }
                        ]
                    }, 
                    {
                        text: '.',
                        style: ['subheader'],
                        width: '100%',
                        alignment: 'letf',  
                        color: '#ffffff'                      
                    }, 
                    {
                        text: 'Cierre de Caja',
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    }, 
                    {
                        text: nombre_equipo,
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: '--------------------------------',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        columns: [
                            {
                                text: cantidad_fact,
                                style: ['small'],
                                width: '10%',
                                alignment: 'letf',
                                bold: true
                            },
                            {
                                text: 'ventas por Sist',
                                style: ['small'],
                                alignment: 'letf',
                                width: '40%'
                            },
                            {
                                text: total,
                                style: ['small'],
                                alignment: 'letf',
                                width: '30%',
                                bold: true
                            }
                        ]
                    },

                    {
                        text: '.',
                        style: ['small'],
                        width: '100%',
                        alignment: 'letf',  
                        color: '#ffffff'                      
                    }, 
                    {
                        text: 'Total Ventas: '+total ,
                        style: ['small'],
                        width: '100%',
                        alignment: 'letf'                        
                    }, 
                    {
                        text: 'Cantidad de Documentos: '+cantidad_fact,
                        style: ['small'],
                        width: '100%',
                        alignment: 'letf'
                    },
                    {
                        text: '--------------------------------',
                        alignment: 'center',
                        style: ['small']
                    },
                    {
                        text: 'Declaracion ',
                        style: ['small']
                    },{
                        columns: [
                            {
                                text: 'Declarado Bs ',
                                style: ['small'],
                                alignment: 'letf',
                                width: '40%',
                            },
                            {
                                text: totalD,
                                style: ['small'],
                                alignment: 'letf',
                                width: '50%',
                                bold: true
                            }
                        ]
                    },{
                        columns: [
                            {
                                text: 'Total Declarado',
                                style: ['small'],
                                alignment: 'letf',
                                width: '40%',
                            },
                            {
                                text: totalA,
                                style: ['small'],
                                alignment: 'letf',
                                width: '50%',
                                bold: true
                            }
                        ]
                    },
                    {
                        text: '--------------------------------',
                        bold: true,
                        alignment: 'center',
                        style: ['small']
                    },
                    {   
                        columns: [
                            {
                                text: 'Total Facturado',
                                style: ['small'],
                                alignment: 'letf',
                                width: '40%',
                            },
                            {
                                text: total,
                                style: ['small'],
                                alignment: 'letf',
                                width: '50%',
                                bold: true
                            }
                        ]
                    },{
                        columns: [
                            {
                                text: 'Total Declarado',
                                style: ['small'],
                                alignment: 'letf',
                                width: '40%',
                            },
                            {
                                text: totalD,
                                style: ['small'],
                                alignment: 'letf',
                                width: '50%',
                                bold: true
                            }
                        ]
                    },{
                        columns: [
                            {
                                text: 'Diferencia',
                                style: ['small'],
                                alignment: 'letf',
                                width: '40%',
                            },
                            {
                                text: totalA,
                                style: ['small'],
                                alignment: 'letf',
                                width: '50%',
                                bold: true
                            }
                        ]
                    },
                    {
                        text: '--------------------------------',
                        bold: true,
                        alignment: 'center',
                        style: ['subheader']
                    },
                    {
                        text: ` \n UNA VEZ DECLARADO SU CIERRE DE CAJA \n NO SE PODRA ADICIONAR MAS DINERO \n PARA COMPLETAR SU DIFERENCIA`,
                        alignment: 'center',
                        style: ['xsmall']
                    },
                    '\n',

                    {
                        text: 'Firma Cajero',
                        alignment: 'center',
                        style: ['subheader']
                    },
                    '\n',
                    {
                        text: 'Firma Responsable',
                        alignment: 'center',
                        style: ['subheader']
                    },
                    {
                        text: '.',
                        style: ['small'],
                        width: '100%',
                        alignment: 'letf',  
                        color: '#ffffff'                      
                    },
                    {
                        text: 'Observaciones:',
                        alignment: 'letf',
                        style: ['subheader']
                    },
                    {
                        text: '-------------------------------------------------------------------',
                        bold: true,
                        alignment: 'letf',
                        style: ['small']
                    },
                    {
                        text: '-------------------------------------------------------------------',
                        bold: true,
                        alignment: 'letf',
                        style: ['small']
                    },
                    {
                        text: '-------------------------------------------------------------------',
                        bold: true,
                        alignment: 'letf',
                        style: ['small']
                    },
                ],
                styles: {
                    header:dataConfigLayaut  ?JSON.parse(dataConfigLayaut.title): {
                        fontSize: 5,
                        bold: true
                    },
                    subheader:dataConfigLayaut?JSON.parse(dataConfigLayaut.sub_title): {
                        fontSize: 4,
                        bold: true
                    },
                    small: dataConfigLayaut?JSON.parse(dataConfigLayaut.description):{
                        fontSize: 3
                    },
                    xsmall:dataConfigLayaut ?JSON.parse(dataConfigLayaut.xsmall): {
                        fontSize: 2.5
                    },
                    tableExample:{
                        margin: [0, 5, 0, 15]
                    },
                    tableHeader: dataConfigLayaut ?JSON.parse(dataConfigLayaut.table): {
                        fontSize: 4,
                        bold: true
                    },
    
                },
                defaultStyle: {
                    columnGap: 5
                }
            };
            resolve(true);
        } catch (e) {
            reject(e);
        }
    });
  }

  public async detalledeinventario(date){

    let dataConfigLayaut = await this._configLayaut.getConfig();
    dataConfigLayaut = dataConfigLayaut.find(e=>e.type_document=='DDI');
    const fecha = new Date();
    let fecha_actual =  await this.formatoFecha(fecha, 'dd/mm/yy');
    var hora = fecha.toLocaleTimeString()
    let datauser: any = await this.configService.getSession();
    let empresa = datauser[0].empresa[0].nombre;
    let nombre_equipo = datauser[0].equipo;

    let prodx =  new Productos();
    let auxx: any =  await prodx.selectpro('1-03-01-0001');

    console.log(auxx);

    let productos = new Productosalmacenes()
    let almacenes: any = await productos.almacenes();
    let prod: any = await productos.findAllReport();
    console.log("productos..",prod);
    let datos =  [];
    let detalles =  [];

    for await (const almac of almacenes) {
        for await (const items of prod) {
            if(almac.WarehouseCode == items.WarehouseCode){
                datos.push({
                    descripcion: items.ItemName,
                    cantidad: Number.parseFloat(items.InStock.toString()).toFixed(2),
                    unidad: items.SalesUnit
                });

            }
        }

        detalles.push({    
            columns: [
            {
                text: `Almacen :`,
                style: ['small'],
                width: '30%',
                alignment: 'left'
            },
            {
                text: almac.WarehouseCode,
                style: ['small'],
                width: '50%',
                alignment: 'left',
                bold: true,
            }]
        });
        detalles.push({    
            columns: [
            {
                text: '--------------------------------',
                style: ['small'],
                width: '100%',
                alignment: 'center',
                color: '#ffffff'   
            }]
        });


        for (let dato of datos){

            detalles.push({    
                columns: [
                {
                    text: `${dato.descripcion}`,
                    style: ['xsmall'],
                    width: '70%',
                    alignment: 'left'
                },{
                    text:`${dato.cantidad}`,
                    width: '15%',
                    style: ['xsmall'],
                    alignment: 'left',
                },{
                    text:`${dato.unidad}`,
                    width: '15%',
                    style: ['xsmall'],
                    alignment: 'left',
                }]
            });

        }
    }


    return new Promise((resolve, reject) => {
        try {
            this.objPDF = {
                pageSize: { width: dataConfigLayaut?dataConfigLayaut.papel_width:80, height: 'auto' },
                pageMargins: [ dataConfigLayaut?dataConfigLayaut.margin_rigth:6, dataConfigLayaut?dataConfigLayaut.margin_up:3, dataConfigLayaut?dataConfigLayaut.margin_left:3,dataConfigLayaut?dataConfigLayaut.margin_down:10],
                content: [

                    {
                        text: empresa,
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    },

                    {
                        text: 'FECHA DE IMPRESION',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: `${fecha_actual}`,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: 'HORA DE IMPRESION',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: `${hora}`,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: 'Saldos de Productos',
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    }, 
                    {
                        text: nombre_equipo,
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: '--------------------------------',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        columns: [
                            {
                                text: 'Descripcion ',
                                style: ['subheader'],
                                width: '30%',
                                alignment: 'center',
                                bold: true
                            },
                            {
                                text: 'Saldo',
                                style: ['subheader'],
                                alignment: 'center',
                                width: '40%',
                                bold: true
                            },
                            {
                                text: 'Unidad',
                                style: ['subheader'],
                                alignment: 'center',
                                width: '30%',
                                bold: true
                            }
                        ]
                    },

                    {
                        text: '--------------------------------',
                        alignment: 'center',
                        style: ['header']
                    },
                    detalles,
                    {
                        text: '--------------------------------',
                        alignment: 'center',
                        style: ['header']
                    },
                ],
                styles: {
                    header:dataConfigLayaut  ?JSON.parse(dataConfigLayaut.title): {
                        fontSize: 5,
                        bold: true
                    },
                    subheader:dataConfigLayaut?JSON.parse(dataConfigLayaut.sub_title): {
                        fontSize: 4,
                        bold: true
                    },
                    small: dataConfigLayaut?JSON.parse(dataConfigLayaut.description):{
                        fontSize: 3
                    },
                    xsmall:dataConfigLayaut ?JSON.parse(dataConfigLayaut.xsmall): {
                        fontSize: 2.5
                    },
                    tableExample:{
                        margin: [0, 5, 0, 15]
                    },
                    tableHeader: dataConfigLayaut ?JSON.parse(dataConfigLayaut.table): {
                        fontSize: 4,
                        bold: true
                    },
    
                },
                defaultStyle: {
                    columnGap: 5
                }
            };
            resolve(true);
        } catch (e) {
            reject(e);
        }
    });
  }

  public async ventasproductos(date){

    let dataConfigLayaut = await this._configLayaut.getConfig();
    dataConfigLayaut = dataConfigLayaut.find(e=>e.type_document=='VPP');

    const fecha = new Date();
    let fecha_actual =  await this.formatoFecha(fecha, 'dd/mm/yy');
    var hora = fecha.toLocaleTimeString()
    let datauser: any = await this.configService.getSession();
    let nombre_equipo = datauser[0].equipo;

    let documentos = new Documentos();
    let resx: any = await documentos.docxporfecha(date);
    let detalle = new Detalle();
    console.log("DATOS",resx);
    let conc = '';
    for (let val of resx){
        conc += "'"+val.cod+"',"; 
    }
    conc = conc.slice(0, -1);
    console.log(conc);

    let resd: any = await detalle.findAll4(conc);

    console.log("DATOS DETALLE",resd);

    let datos =[];
    for (let val of resd){
        /*valida si existe el dato en el objeto final*/
        let validador = 0;

        for (let dat of datos){
            if(dat.codigo == val.ItemCode){
                validador = 1;
            }
        }
        if(validador == 0){
            let descp = '';
            let canti = 0;
            let cod = '';

            for (let val2 of resd){
                if(val2.ItemCode == val.ItemCode){
                    descp = val2.Dscription;
                    canti += val2.Quantity;
                    cod = val2.ItemCode;
                }
            }

            datos.push({
                descripcion: descp,
                cantidad: Number.parseFloat(canti.toString()).toFixed(2),
                codigo: cod
            });
        }
    }

    console.log("DATOS DETALLE",datos);


    //var detalles = new Array(1);
    var detalles =[];
    let x = 1;
    for (let dato of datos){

        detalles.push({    
            columns: [
              /*{
                text: `${dato.codigo}`,
                style: ['xsmall'],
                width: '25%',
                
                alignment: 'left'
            },*/{
                text:`${dato.descripcion}`,
                width: '80%',
                style: ['xsmall'],
                alignment: 'left',
            },{
                text:`${dato.cantidad}`,
                width: '20%',
                style: ['xsmall'],
                alignment: 'left',
            }]
        });
    }
    console.log(detalles);

    return new Promise((resolve, reject) => {
        try {
            this.objPDF = {
                pageSize: { width: dataConfigLayaut?dataConfigLayaut.papel_width:80, height: 'auto' },
                pageMargins: [ dataConfigLayaut?dataConfigLayaut.margin_rigth:6, dataConfigLayaut?dataConfigLayaut.margin_up:3, dataConfigLayaut?dataConfigLayaut.margin_left:3,dataConfigLayaut?dataConfigLayaut.margin_down:10],
                content: [

                    {
                        text: 'EMPRESA',
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    },

                    {
                        text: 'FECHA DE IMPRESION',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: `${fecha_actual}`,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: 'HORA DE IMPRESION',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: `${hora}`,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: 'Ventas por Productos',
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    }, 
                    {
                        text: nombre_equipo,
                        bold: true,
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        text: '--------------------------------',
                        alignment: 'center',
                        style: ['header']
                    },
                    {
                        columns: [
                            /*{
                                text: 'Codigo ',
                                style: ['subheader'],
                                width: '30%',
                                alignment: 'center',
                                bold: true
                            },*/
                            {
                                text: 'Descripcion',
                                style: ['subheader'],
                                alignment: 'center',
                                width: '70%',
                                bold: true
                            },
                            {
                                text: 'Cantidad',
                                style: ['subheader'],
                                alignment: 'center',
                                width: '30%',
                                bold: true
                            }
                        ]
                    },

                    {
                        text: '--------------------------------',
                        alignment: 'center',
                        style: ['header']
                    },
                    detalles,
                    {
                        text: '--------------------------------',
                        alignment: 'center',
                        style: ['header']
                    },
                ],
                styles: {
                    header:dataConfigLayaut  ?JSON.parse(dataConfigLayaut.title): {
                        fontSize: 5,
                        bold: true
                    },
                    subheader:dataConfigLayaut?JSON.parse(dataConfigLayaut.sub_title): {
                        fontSize: 4,
                        bold: true
                    },
                    small: dataConfigLayaut?JSON.parse(dataConfigLayaut.description):{
                        fontSize: 3
                    },
                    xsmall:dataConfigLayaut ?JSON.parse(dataConfigLayaut.xsmall): {
                        fontSize: 2.5
                    },
                    tableExample:{
                        margin: [0, 5, 0, 15]
                    },
                    tableHeader: dataConfigLayaut ?JSON.parse(dataConfigLayaut.table): {
                        fontSize: 4,
                        bold: true
                    },
    
                },
                defaultStyle: {
                    columnGap: 5
                }
            };
            resolve(true);
        } catch (e) {
            reject(e);
        }
    });
  }

  public async formatoFecha(fecha, formato) {

    let dia = '';
    if(fecha.getDate() < 10){
        dia = '0'+fecha.getDate();
    }else{
        dia =fecha.getDate();
    }

    let mes = '';
    if((fecha.getMonth() + 1) < 10){
        mes = '0'+(fecha.getMonth() + 1);
    }else{
        mes = fecha.getMonth() + 1;
    }

    const map = {
        dd: dia,
        mm: mes,
        yy: fecha.getFullYear(),
        yyyy: fecha.getFullYear()
    }

    return formato.replace(/dd|mm|yy|yyy/gi, matched => map[matched])
  }

}
