import { Clientes } from "../models/clientes";
/*import { Calculo } from "./calculo";
import { Bolivia } from "./bolivia";*/
//rafael
import { Clientespercepciones } from "../models/clientespercepciones";
import { Productos } from "../models/productos";
import { Productosalmacenes } from "../models/productosalmacenes";
import { Almacenes } from "../models/almacenes";
import { Almacenespercepciones } from "../models/almacenespercepciones";
import { Servicioventas } from "../models/servicioventas";
import { Grupospercepciones } from "../models/grupospercepciones";
import { Clientessucursales } from "../models/clientessucursales";
import { Gestionsap } from "../models/gestionsap";

export class Calculopercepciones {
  private ClientePercepcionModel = new Clientespercepciones();
  private serviciosVentaModel = new Servicioventas();
  private grupoPercepcionesModel = new Grupospercepciones();

  private productModel = new Productos();
  private almacenesPercepcionesModel = new Almacenespercepciones();
  private clientesSucursalesModel = new Clientessucursales();
  private gestionSap = new Gestionsap();

  constructor() {}

  public async bf_GrupoPercepcion(
    Code_1: string,
    GrupoManual: string,
    TotalConMoneda: string,
    Warehouse: string,
    Dispensador: string,
    CardCode_1: string,
    TaxOnly: string,
    Cantidad: number,
    GrupoPercepcion: string,
    PorImp: number,
    Impuesto: string,
    TipoDoc: string
  ): Promise<any> {
    console.log(
      "data calc percep->",
      Code_1,
      GrupoManual,
      TotalConMoneda,
      Warehouse,
      Dispensador,
      CardCode_1,
      TaxOnly,
      Cantidad,
      GrupoPercepcion,
      PorImp,
      Impuesto,
      TipoDoc
    );
    let iCardType: string;
    let iU_EXX_TIPOPERS: string;
    let iAgeRet: string;
    let iAgePerVI, iSociedadAgePerVI, iSociedadAgePerCO: string;
    let iAgePerCO: string;
    let iPersNatSinNeg: string;
    let iPersNatConNeg: string;
    let iEntExcPer: string;
    let contador: number;
    let igrupoPercepcion: string;
    let iesGLP: string;
    let imontominimo: number;
    let ialmacenPERDGH: string;
    let iImpuesto, iTotal: number;
    let iCantidad: number;
    let iU_EXX_PERCOM: string;  //persona combustible
    let iU_EXX_PERCDI: string; //consumidor directo
    

    iCantidad = Cantidad;
    // seleccionado socio de negocios
    let findClientPercepcion: any = await this.ClientePercepcionModel.find(
      CardCode_1
    );

    console.log("clienteUser--selected", findClientPercepcion);

    console.log("tipo doc->", TipoDoc);

    if (findClientPercepcion.length > 0) {
      iCardType = findClientPercepcion[0].CardType;
      iU_EXX_TIPOPERS = findClientPercepcion[0].U_EXX_TIPOPERS;
      iAgeRet = findClientPercepcion[0].QryGroup2;
      iAgePerVI = findClientPercepcion[0].QryGroup3;
      iAgePerCO = findClientPercepcion[0].QryGroup4;
      iPersNatSinNeg = findClientPercepcion[0].QryGroup6;
      iPersNatConNeg = findClientPercepcion[0].QryGroup7;
      iEntExcPer = findClientPercepcion[0].QryGroup8;
      iU_EXX_PERCOM = findClientPercepcion[0].U_EXX_PERCOM;
      iU_EXX_PERCDI = findClientPercepcion[0].iU_EXX_PERCDI?findClientPercepcion[0].iU_EXX_PERCDI:'';
      iSociedadAgePerVI = findClientPercepcion[0].QryGroup3;
      iSociedadAgePerCO = findClientPercepcion[0].QryGroup4;
      // sociedad a implementar
      let clienteGestionSap: any = await this.ClientePercepcionModel.vlidateGestionSap();
      console.log("clienteGestionSap validate", clienteGestionSap);
      if (clienteGestionSap.length > 0) {
        iSociedadAgePerVI = clienteGestionSap[0].QryGroup3;
        iSociedadAgePerCO = clienteGestionSap[0].QryGroup4;
      }
      if (TipoDoc == "I") {
        const products: any = await this.productModel.validateGroupPer(Code_1);
        console.log("validacion de productos grupo percepcion->", products);
        if (products.length > 0) {
          igrupoPercepcion = "0000";
          iesGLP = "";
          imontominimo = 0;
        } else {
          const pGroupPer: any = await this.productModel.selectProductGroupPer(
            Code_1
          );

          igrupoPercepcion = "0000";
          iesGLP = "N";
          imontominimo = 0;

          console.log(Code_1 + "producto grupo percepciones->", pGroupPer);
         // if (pGroupPer.length > 0) {
            igrupoPercepcion = pGroupPer && pGroupPer[0] && pGroupPer[0].Code?pGroupPer[0].Code:'0000';
            iesGLP = pGroupPer && pGroupPer[0] && pGroupPer[0].U_EXX_GLP?pGroupPer[0].U_EXX_GLP:'N';
            imontominimo = pGroupPer && pGroupPer[0] && pGroupPer[0].U_EXX_MONMIN?pGroupPer[0].U_EXX_MONMIN:0;
        //}
        }
        const almacenes: any = await this.almacenesPercepcionesModel.findByWhsCode(
          Warehouse
        );
        console.log("almacen encontrado->", almacenes);
        if (almacenes.length > 0) {
          ialmacenPERDGH =
          almacenes && almacenes[0] && almacenes[0].U_EXX_PERDGH == "" ||
            almacenes[0].U_EXX_PERDGH == "NULL"
              ? "N"
              : almacenes[0].U_EXX_PERDGH;
         /*  ialmacenPERDGH = almacenes && almacenes[0] && almacenes[0].U_EXX_PERDGH != "" ||
            almacenes[0].U_EXX_PERDGH != "NULL"
            ? almacenes[0].U_EXX_PERDGH
            : "N"; */
          console.log("ialmacenPERDGH->", ialmacenPERDGH);
        }
      } else {
        let servicioVenta: any = await this.serviciosVentaModel.validateServicioVentaGrupoPer(
          Code_1
        );
        console.log("servicioVenta percepcion-->", servicioVenta);
        //if (servicioVenta.length > 0) {
          igrupoPercepcion = servicioVenta && servicioVenta[0] && servicioVenta[0].Code?servicioVenta[0].Code:'0000';
          iesGLP = servicioVenta && servicioVenta[0] &&  servicioVenta[0].U_EXX_GLP?servicioVenta[0].U_EXX_GLP:'N';
          imontominimo = servicioVenta && servicioVenta[0] && servicioVenta[0].U_EXX_MONMIN?servicioVenta[0].U_EXX_MONMIN:0;
        //}

        ialmacenPERDGH = "";
      }

      // Calculo del Total
      console.log("impuesto-->", Impuesto);
      let position = Impuesto.indexOf(" ");
      console.log("lasta indexof ", position);
      if (!Impuesto) {
        iImpuesto = 0;
      } else {
        iImpuesto = this.converToDecimal(Impuesto);
      }
      console.log("impuesto-->2", iImpuesto);

      if (!TotalConMoneda) {
        iTotal = 0;
      } else {
        iTotal =
          this.converToDecimal(TotalConMoneda) + (iImpuesto ? iImpuesto : 0);
      }
    }
    console.log("TotalConMoneda->", TotalConMoneda, iTotal);
    console.log(
      igrupoPercepcion,
      "substrig rigth-->",
      igrupoPercepcion.substring(
        igrupoPercepcion.length - 2,
        igrupoPercepcion.length
      )
    );
    console.log(
      igrupoPercepcion,
      "substrig left-->",
      igrupoPercepcion.substring(0, 2)
    );
    console.log("GrupoManual->", GrupoManual);
    
    if ((GrupoManual ? GrupoManual : "N") == "N") {
      if (iCardType == "C" || iCardType == "L") {
       /*  if (
          iSociedadAgePerVI == "N" ||
          iAgeRet == "Y" ||
          PorImp == 0 ||
          TaxOnly == "Y" ||
          (igrupoPercepcion ? igrupoPercepcion : "0000") == "0000"
        )
        { */
          if (
            iSociedadAgePerVI == "N" ||
          //  iAgeRet == "Y" ||
            PorImp == 0 ||
            TaxOnly == "Y" ||
            (igrupoPercepcion ? igrupoPercepcion : "0000") == "0000"
          ) {
          igrupoPercepcion = "0000";
        } else {
          if (
            iAgePerVI == "Y" &&
            igrupoPercepcion.substring(0, 2) == "03" &&
            iSociedadAgePerVI == "Y"
          ) {
            igrupoPercepcion = "0400";
          } else
          {  
           /*  if (
              igrupoPercepcion.substring(0, 2) == "03" &&
              iEntExcPer == "N" &&
              iSociedadAgePerVI == "Y" &&
              (iU_EXX_TIPOPERS != "TPN" ||
                iPersNatConNeg == "Y" ||
                (TipoDoc == "I" &&
                  iCantidad > imontominimo &&
                  igrupoPercepcion.substring(
                    igrupoPercepcion.length - 2,
                    igrupoPercepcion.length
                  ) == "-Q" &&
                  iPersNatSinNeg == "Y") ||
                (iTotal > imontominimo &&
                  igrupoPercepcion.substring(
                    igrupoPercepcion.length - 2,
                    igrupoPercepcion.length
                  ) != "-Q" &&
                  iPersNatSinNeg == "Y")) &&
              ((iesGLP ? iesGLP : "N") == "N" ||
                (iesGLP == "Y" && ialmacenPERDGH == "N") ||
                (iesGLP == "Y" && ialmacenPERDGH == "Y" && Dispensador == "N"))
            ) { */
            if (
              igrupoPercepcion.substring(0, 2) == "03" &&
              iEntExcPer == "N" &&
              (iEntExcPer == "N" && iAgeRet =='N') &&
              iSociedadAgePerVI == "Y" 
              /* (iU_EXX_TIPOPERS != "TPN" ||
                iPersNatConNeg == "Y" ||
                (TipoDoc == "I" &&
                  iCantidad > imontominimo &&
                  igrupoPercepcion.substring(
                    igrupoPercepcion.length - 2,
                    igrupoPercepcion.length
                  ) == "-Q" &&
                  iPersNatSinNeg == "Y") ||
                (iTotal > imontominimo &&
                  igrupoPercepcion.substring(
                    igrupoPercepcion.length - 2,
                    igrupoPercepcion.length
                  ) != "-Q" &&
                  iPersNatSinNeg == "Y")) &&
              ((iesGLP ? iesGLP : "N") == "N" ||
                (iesGLP == "Y" && ialmacenPERDGH == "N") ||
                (iesGLP == "Y" && ialmacenPERDGH == "Y" && Dispensador == "N")) */
            ){
              if (TipoDoc == 'I' &&
                ((iPersNatConNeg != 'N' && iPersNatSinNeg != 'N') ||
                (iU_EXX_TIPOPERS == 'TPN' && iPersNatSinNeg == 'Y'))&&
                 (igrupoPercepcion.substring(
                  igrupoPercepcion.length - 2,
                  igrupoPercepcion.length)
                ) == "-Q" && iCantidad >= imontominimo
              ){
                igrupoPercepcion = igrupoPercepcion;

              } else if ( 
                TipoDoc == 'I' &&
                ((iPersNatConNeg != 'N' && iPersNatSinNeg != 'N') ||
                  (iU_EXX_TIPOPERS == 'TPJ' || iPersNatSinNeg == 'Y')) &&
                  (igrupoPercepcion.substring(
                    igrupoPercepcion.length - 2,
                    igrupoPercepcion.length)
                  ) == "-Q"
              ){
                igrupoPercepcion = igrupoPercepcion;
              } else if (
                TipoDoc == 'I' &&
                (igrupoPercepcion.substring(
                  igrupoPercepcion.length - 2,
                  igrupoPercepcion.length)
                ) != "-Q" &&
                ((iPersNatSinNeg == 'Y' && iTotal > imontominimo) || iPersNatConNeg == 'Y' || iU_EXX_TIPOPERS == 'TPJ') &&
                ((iesGLP ? iesGLP : 'N') == 'N' || (iesGLP == 'Y' && ialmacenPERDGH == 'N')) ||
                (iesGLP=='Y' && ialmacenPERDGH=='Y' && Dispensador=='N')

              ){
                igrupoPercepcion = igrupoPercepcion;
              } else{
                igrupoPercepcion    = '0000';
              }
              console.log("asignacuion 1111",iSociedadAgePerCO,iAgePerCO,iU_EXX_PERCOM,iU_EXX_PERCOM,igrupoPercepcion)
              //igrupoPercepcion = igrupoPercepcion;
            } else {
              /*if (
                iSociedadAgePerCO == "Y" &&
                iAgePerCO == "N" &&
                (iU_EXX_PERCOM ? iU_EXX_PERCOM : "").length == 1 &&
                iU_EXX_PERCOM == "Y" &&
                igrupoPercepcion.substring(0, 2) == "01"
              ) {
                console.log("asignacuion 2222",iSociedadAgePerCO,iAgePerCO,iU_EXX_PERCOM,iU_EXX_PERCOM,igrupoPercepcion)
                igrupoPercepcion = igrupoPercepcion;
              } else {
                console.log("asignacuion 2222else",iSociedadAgePerCO,iAgePerCO,iU_EXX_PERCOM,iU_EXX_PERCOM,igrupoPercepcion)
                igrupoPercepcion = "0000";
              }*/
              if (
                iSociedadAgePerCO == 'Y' &&
                (iAgePerVI == 'N' && iAgePerCO == 'N') &&
                ((iU_EXX_PERCOM ? iU_EXX_PERCOM : '').length == 1 && iU_EXX_TIPOPERS == 'Y' && iU_EXX_PERCDI == 'N') &&
                igrupoPercepcion.substring(0, 2) == "01" &&
                //((iPersNatSinNeg=='Y' && iTotal>imontominimo) || iPersNatConNeg=='Y' || iU_EXX_TIPOPERS=='TPJ')
                ((iPersNatSinNeg=='Y' && iTotal>imontominimo) || iPersNatConNeg=='Y')
              ){
                console.log("asignacuion 2222",iSociedadAgePerCO,iAgePerCO,iU_EXX_PERCOM,iU_EXX_PERCOM,igrupoPercepcion)
                igrupoPercepcion = igrupoPercepcion;
              } else{
                console.log("asignacuion 2222else",iSociedadAgePerCO,iAgePerCO,iU_EXX_PERCOM,iU_EXX_PERCOM,igrupoPercepcion)
                igrupoPercepcion = "0000";
              }
            }
          }
        }
      } else {
        if ((iAgePerVI == "Y" || iAgePerCO == "Y")&& iU_EXX_TIPOPERS=='TPJ'||(iU_EXX_PERCOM=='Y' && iU_EXX_PERCDI=='N')) {
          //igrupoPercepcion = "Combustible";
          igrupoPercepcion = "0100";

        } else {
          igrupoPercepcion = "0000";
        }
      }
    } else {
      igrupoPercepcion = "0000";
    }

    return Promise.resolve({ igrupoPercepcion: igrupoPercepcion });
    // producto_std2// grupo de percepciones
    // iAgeRet=
    /* return {
            CardCode_1 nvarchar(15),--V
            iCardType nvarchar(1), --OK
            iU_EXX_TIPOPERS nvarchar(10), --OK
            iAgeRet nvarchar(1), --OK
            iAgePerVI nvarchar(1),--OK
            iAgePerCO nvarchar(1),--OK
            iEntExcPer nvarchar(1), --OK
            iPersNatSinNeg nvarchar(1), --OK
            iPersNatConNeg nvarchar(1), --OK 
            iSociedadAgePerCO nvarchar(1),   --OK
            Code_1 nvarchar(50), --V
            TipoDoc nvarchar(1), --V
            iesGLP nvarchar(1), --O
            imontominimo numeric(19,6), --O
            GrupoPercepcion nvarchar(30), --O
            Warehouse nvarchar(30), --OK
            ialmacenPERDGH nvarchar(1),
            GrupoManual nvarchar(1),
            iTotal numeric(19,6) 
        }; */
  }
  async bf_impuestos(
    Address: string,
    CardCode: string,
    TIPO: string,
    GRUPOPER_SERVICIO: string,
    GRUPOPER_ARTICULO: string,
    TAXCODEDOC_SERVICIO: string,
    TAXCODEDOC_ARTICULO: string
  ): Promise<any> {
    let CODIGO_PERCEPCION: string;
    let taxCode: string;
    let taxCodeSN: string;
    let taxCodeDoc: string;
    let iTaxCode: string;
    console.log("data inafecto->", Address,",",
    CardCode,",",
    TIPO,",",
    GRUPOPER_SERVICIO,",",
    GRUPOPER_ARTICULO,",",
    TAXCODEDOC_SERVICIO,",",
    TAXCODEDOC_ARTICULO);

    console.log("CardCode->", CardCode, Address);
    console.log("Address->", Address);

    console.log("TIPO,GRUPOPER_SERVICIO->", TIPO, GRUPOPER_SERVICIO);
    console.log(
      "GRUPOPER_ARTICULO,TAXCODEDOC_SERVICIO->",
      GRUPOPER_ARTICULO,
      TAXCODEDOC_SERVICIO
    );
    console.log("TAXCODEDOC_ARTICULO", TAXCODEDOC_ARTICULO);

    if (Address && Address.length > 0) {
      console.log("CardCode,Address->", CardCode, Address);
      let clienteSucursal: any = await this.clientesSucursalesModel.selectDocumentByCarcode(
        CardCode,
        Address
      );
      console.log(taxCodeSN, "encontro cliente sucursal", clienteSucursal);
      if (clienteSucursal.length > 0) {
        taxCodeSN = clienteSucursal[0].TaxCode;
      }
      console.log("taxCodeSN", taxCodeSN);
      console.log("TIPO", TIPO);
      switch (TIPO) {
        case "S":
          ;
          CODIGO_PERCEPCION = GRUPOPER_SERVICIO ? GRUPOPER_SERVICIO : "0000";
          taxCodeDoc = TAXCODEDOC_SERVICIO;
          break;
        case "I":
          CODIGO_PERCEPCION = GRUPOPER_ARTICULO ? GRUPOPER_ARTICULO : "0000";
          taxCodeDoc = TAXCODEDOC_ARTICULO;
          break;
        default:
          break;
      }
      console.log("CODIGO_PERCEPCION 1->", CODIGO_PERCEPCION);
      if (CODIGO_PERCEPCION.substring(0, 1) == "0") {
        console.log("CODIGO_PERCEPCION 2->", CODIGO_PERCEPCION);

        let grupoPercepcion: any = await this.grupoPercepcionesModel.findByCode(
          CODIGO_PERCEPCION
        );
        console.log("grupoPercepcion iva->", grupoPercepcion);
        if (grupoPercepcion && grupoPercepcion.length > 0) {
          taxCode = grupoPercepcion[0].U_EXX_TaxCode;
        }
      } else {
        taxCode = "IGV";
      }

      taxCodeDoc = taxCodeDoc ? taxCodeDoc : "";
      taxCode = taxCode ? taxCode : "";
      console.log("taxCodeDoc-->", taxCodeDoc);
      console.log("taxCode-->", taxCode);

      if (
        taxCodeDoc != "EXO" &&
        taxCodeDoc != "INA" &&
        taxCodeDoc != "IGV_EXE"
      ) {
        if (taxCode == "") {
          iTaxCode = taxCodeSN;
        } else {
          iTaxCode = taxCode;
        }
      } else {
        iTaxCode = taxCodeDoc;
      }
      console.log("iTaxCode", iTaxCode);
      return Promise.resolve(iTaxCode);
    } else {
      return Promise.resolve("");
    }
  }
  public async getImpuestoDefault(CardCode, Address){
    let clienteSucursal: any = await this.clientesSucursalesModel.selectDocumentByCarcode(
      CardCode,
      Address
    );
    console.log("encontro cliente sucursal", clienteSucursal);
    let taxCode=clienteSucursal.length>0? clienteSucursal[0].TaxCode:'IGV';
    if( taxCode=='INA'){
      return Promise.resolve(taxCode);
    }else{
      return Promise.resolve('IGV');
    }
  }
  converToDecimal(cost: string) {
    let auximpuesto = cost.substring(cost.indexOf(" "), cost.length);
    return parseFloat(auximpuesto.replace(",", ""));
  }

  public initNull(valor: number): number {
    if (!valor || valor == null) {
      return 0;
    } else {
      return valor;
    }
  }
  public calcVatSum(data: {
    precio: Number;
    cantidad: Number;
    descuento: Number;
    impuesto: Number;
  }): Number {
    //let vatSum=0;
    console.log("data para el calculo vatsum", data);
    let subTotal = Number(data.precio) * Number(data.cantidad);
    let totalDescuento =
      subTotal * (Number(data.descuento ? data.descuento : 0) / 100);
    subTotal = subTotal - totalDescuento;
    subTotal = subTotal * (Number(data.impuesto) / 100);
    return subTotal ? Number(subTotal.toFixed(2)) : 0;
  }
}
