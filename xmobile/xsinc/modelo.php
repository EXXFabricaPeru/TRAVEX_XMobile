<?php
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

include("exxis.php");
require ('libs/env/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$accion= new conexion("hana","10.10.10.2","SBO_TRAVEX_PROD21",getenv('DB_USR',''),getenv('DB_PASSWORD',''));

if(!file_get_contents("php://input")){
 $tipo_evento=52; 
 $objDatos->limite=1000;
 $objDatos->salto=0;
 $objDatos->vendedor=10;
  $objDatos->Item="03PAM0012";
  $objDatos->almacen="L4017CEA";
  $objDatos->empresafex=6;
//$tipo_evento=18; 
}else{
$objDatos = json_decode(file_get_contents("php://input"));
$tipo_evento=$objDatos->accion;
}
switch($tipo_evento){
  case 0://prueba
    echo json_encode(array("estado"=>600,"error"=>"Coneccion ok ".$tipo_evento));
   break;
  case 1://facturas
    $salto=$objDatos->salto; 
		$respuesta=" ";         
		$tabla="EXX_XM_FacturasSap";
        $campos="*";
		$condicion=" ORDER BY \"DocEntry\" desc  limit 1000 offset ".$salto;
    //$$condicion=" ORDER BY \"DocEntry\" desc  ";
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]),JSON_UNESCAPED_UNICODE);
         //var_dump($respuesta["resultado"]);
		    }
    break;
    case 2://facturas Detalle
    $salto=$objDatos->salto;
        $respuesta=" ";         
        $tabla="EXX_XM_FacturasDetalleSap";
        $campos=" * ";
        $condicion=" ORDER BY \"DocEntry\"  limit 1000 offset ".$salto;
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
         //var_dump($respuesta["resultado"]);
        }
    break;
    case 3://pedidos Cabecera
    $salto=$objDatos->salto;
    $Repartidor=$objDatos->Repartidor;
        $respuesta=" ";         
        $tabla="EXX_XM_PedidoSapGeo";//*REVISAR RNP
        $campos=" * ";
        $condicion=" where \"Repartidor\"=".$Repartidor."  ORDER BY \"DocEntry\"  desc limit 1000 offset ".$salto;
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
         //var_dump($respuesta["resultado"]);
        }
    break;
    case 4:// pedido detalle
    $salto=$objDatos->salto;
    $Repartidor=$objDatos->Repartidor;
        $respuesta=" ";         
        $tabla="EXX_XM_pedidosDetalleSap";
        $campos=" * ";
        $condicion="  where \"Repartidor\"=".$Repartidor." ORDER BY \"DocEntry\" desc limit 1000 offset ".$salto;
       // $condicion="";
    //  echo "************ hola ";
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
         //var_dump($respuesta["resultado"]);
        }
    break;
  case 5:// productos alterativos
    $respuesta=" ";         
    $tabla="EXX_XM_ProductosAlt";
        $campos="*";
        $condicion=" ";
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
         //var_dump($respuesta["resultado"]);
     }
    break;
    case 6:// lotes de productos
      $respuesta=" ";         
      $tabla="EXX_XM_LotesProductos";
          $campos="*";
          $condicion=" ";
          $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
          if($respuesta["json"]==''){          
           echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
          }else{
           echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
           //var_dump($respuesta["resultado"]);
       }
      break;
      case 7:// series usadas
        $respuesta=" ";         
        $tabla="EXX_XM_SeriesUsadas";
            $campos="\"ItemCode\",\"DistNumber\"";
            $condicion=" ";
            $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      $contador=count($respuesta["resultado"]);
      if(($respuesta["json"]=='')or($contador==0)){          
             echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{
             echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
             //var_dump($respuesta["resultado"]);
         }
        break;
        case 8:// pagos a cuenta de clientes
          $respuesta=" ";         
          $tabla="EXX_XM_PagosCuenta";
              $campos="*";
              $condicion=" ";
              $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
              if($respuesta["json"]==''){          
               echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
              }else{
               echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
               //var_dump($respuesta["resultado"]);
           }
          break;
          case 9:// centros de costo
            $respuesta=" ";         
            $tabla="OPRC";
                $campos="\"PrcCode\",\"PrcName\"";
                $condicion="  WHERE \"DimCode\" ='2' AND  \"Active\" ='Y' ";
                $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
                if($respuesta["json"]==''){          
                 echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
                }else{
                 echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
                 //var_dump($respuesta["resultado"]);
             }
            break;
            case 10:// plazos cuotas
              $respuesta=" ";         
              $tabla="EXX_XM_CUOTASPAGO";
                  $campos=" * ";
                  $condicion="  ";
                  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
                  if($respuesta["json"]==''){          
                   echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
                  }else{
                   echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
                   //var_dump($respuesta["resultado"]);
               }
              break;
            case 11:// cuotas facturas
                $respuesta=" ";         
                $tabla="EXX_XM_CUOTASFACTURA";
                    $campos=" * ";
                    $condicion="  ";
                    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
                    if($respuesta["json"]==''){          
                     echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
                    }else{
                     echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
                     //var_dump($respuesta["resultado"]);
                 }
            break;
            case 12:// almacen serie
              $respuesta=" ";         
              $tabla="EXX_XM_AlmacenSerie";
                  $campos=" * ";
                  $condicion="  ";
                  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
                  if($respuesta["json"]==''){          
                   echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
                  }else{
                   echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
                   //var_dump($respuesta["resultado"]);
               }
          break;
      case 13:// bancos con cuenta contable
              $respuesta=" ";         
              $tabla="DSC1";
                  $campos=" \"BankCode\",\"GLAccount\",\"AcctName\" ";
                  $condicion=" where \"UsrNumber2\"='POS' ";
                  //$condicion=" ";
                  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
                  if($respuesta["json"]==''){          
                   echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
                  }else{
                   echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
                   //var_dump($respuesta["resultado"]);
               }
          break;
/***********************New Lines MC****************************/          
      case 14:// ofertas de venta cabecera
            $salto=$objDatos->salto;
            $respuesta=" ";         
              $tabla="EXX_XM_OfertasSap";
                  $campos=" * ";
                  $condicion=" ORDER BY \"DocEntry\"  limit 1000 offset ".$salto;
                  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
                  if($respuesta["json"]==''){          
                   echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
                  }else{
                   echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
                   //var_dump($respuesta["resultado"]);
               }
        break;
      case 15:// detalle de ofertas de venta. Lineas de documento
          $salto=$objDatos->salto;
            $respuesta=" ";         
              $tabla="EXX_XM_OfertasDetallesSap";
                  $campos=" * ";
                  $condicion=" ORDER BY \"DocEntry\"  limit 1000 offset ".$salto;
                  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
                  if($respuesta["json"]==''){          
                   echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
                  }else{
                   echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
                   //var_dump($respuesta["resultado"]);
               }
      break;    
      case 16:// Entregas de venta, cabecera
          $salto=$objDatos->salto;
        $respuesta=" ";         
        $tabla="EXX_XM_EntregasSap";
        $campos=" * ";
        $condicion=" ORDER BY \"DocEntry\"  limit 1000 offset ".$salto;
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
         //var_dump($respuesta["resultado"]);
        }
      break; 
      case 17:// detalle de entregas de venta. Lineas de documento
         $salto=$objDatos->salto;
        $respuesta=" ";         
        $tabla="EXX_XM_EntregasDetalleSap";
        $campos=" * ";
        $condicion=" ORDER BY \"DocEntry\"  limit 1000 offset ".$salto;
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
         //var_dump($respuesta["resultado"]);
        }
      break;
      case 18:// notas de credito de venta, cabecera
        $respuesta=" ";         
          $tabla="EXX_XM_NotasCreditoSap";
              $campos=" * ";
              $condicion="  ";
              $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
              if($respuesta["json"]==''){          
              echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
              }else{
              echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
              //var_dump($respuesta["resultado"]);
          }
    break; 
    case 19:// detalle de notas de credito. Lineas de documento
        $respuesta=" ";         
          $tabla="EXX_XM_NotasCreditoDetalleSap";
              $campos=" * ";
              $condicion="  ";
              $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
              if($respuesta["json"]==''){          
              echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
              }else{
              echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
              //var_dump($respuesta["resultado"]);
          }
    break;
   
  case 25:// reporte SALDOXVENCIMIENTOCLIENTES
            $respuesta=" ";         
            $fecha=$objDatos->fecha;
      $consulta="CALL EXX_XM_PA_SALDOXVENCIMIENTOCLIENTES('".$fecha."')";
            $respuesta=$accion->ejecutarConsulta($consulta);
      //var_dump($respuesta);
      
            if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{      
          echo $respuesta["json"];
            }
      
    break;
  case 26:// reporte 
            $respuesta=" ";  
      //$item="ITM-0000070";
            $item=$objDatos->item;
      $consulta="CALL EXX_XM_PA_ITEMKARDEX('".$item."')";
            $respuesta=$accion->ejecutarConsulta($consulta);
      //var_dump($respuesta);
      
            if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{      
          echo $respuesta["json"];
      //print_r($respuesta["resultado"]);
            }
      
    break;
  case 27:// reporte 
            $respuesta=" ";         
            $fecha=$objDatos->fecha;
      $consulta="CALL EXX_XM_PA_SALDOXVENCIMIENTOCLIENTESRESUMEN('".$fecha."')";
            $respuesta=$accion->ejecutarConsulta($consulta);
      //var_dump($respuesta);
      
            if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{      
          echo $respuesta["json"];
            }
      
    break;
  case 30: //productosAlmacenes on demand     
            $item = $objDatos->ItemCode;
      $respuesta=" ";         
            $tabla="OITW";            
            $campos = '"ItemCode","WhsCode","OnHand","IsCommited","Locked","OnOrder"';
            $condicion ="  WHERE \"ItemCode\"='".$item."' and  \"OnHand\" > 0 ";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if($respuesta["json"]==''){          
            echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{ 
            echo $respuesta["json"];
      } 
        
  break;
  case 31: //empleados de venta       
            $respuesta=" ";         
            $tabla="OSLP";            
            $campos = '"SlpCode","SlpName","Fax"';
            $condicion =" ";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if($respuesta["json"]==''){          
            echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{ 
            echo $respuesta["json"];
      } 
        
  break;
  case 32: // series productos
    $limite = $objDatos->limite;
    $offset = $objDatos->inicio;
    $respuesta=" ";         
    $tabla="OSRN";            
    $campos = '"ItemCode","SysNumber","DistNumber","InDate","Status","UserSign","AbsEntry"';
    $condicion =" ";
    if (isset($limite) && isset($offset)) {
      $condicion .= " limit " . $limite . " offset " . $offset . " ";
    }
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;
  case 33: // contador de series productos
    $respuesta=" ";         
    $tabla="OSRN";            
    $campos = 'COUNT(*) as Cantidad';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;
  case 34: // lotes productos
    $respuesta=" ";
    $limite = $objDatos->limite;
    $offset = $objDatos->inicio;
    // $tabla="OBTN"; // NO USAR
    // $tabla="OIBT";
    $tabla="EXX_XM_LotesProductos";
    $campos = '*';

    $condicion =" ";
    if (isset($limite) && isset($offset)) {
      $condicion .= " limit " . $limite . " offset " . $offset . " ";
    }
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;
  case 35: // contador de lotes productos
    $respuesta=" ";         
    $tabla="EXX_XM_LotesProductos";            
    $campos = 'COUNT(*) as Cantidad';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;
  case 40: //unidades de medida relaciones     
           
            $respuesta=" ";         
            $tabla="UGP1";            
            $campos = '*';
            $condicion =" ";
            $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
            if($respuesta["json"]==''){          
                  echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{ 
                  echo $respuesta["json"];
            } 
        
  break;

  case 41: // contador productos
    // filter=   TreeType ne \'iNotATree\'  or (QuantityOnStock gt 0) or (InventoryItem eq \'tNO\') and (SalesItem eq \'tYES\')';
    $respuesta=" ";         
    $tabla="OITM";            
    $campos = 'Count("ItemCode") as cantidad'; //"TreeType", "OnHand", "InvntItem", "SellItem"
    $condicion =" where \"TreeType\"='N' or \"OnHand\">0 or \"InvntItem\"='N' and \"SellItem\"='Y' "; //and \"InvntItem\"='N'????????
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
    echo $respuesta["json"];      
    }
    break;
  case 42: // sincronizar productos maestro ODBC
    $camposStd=$objDatos->std;
    // $camposStd=',"U_XM_ICEtipo","U_XM_ICEPorcentual","U_XM_ICEEspecifico","U_XM_Actividad","validFor","U_Marca"';
    $salto=$objDatos->salto;
    $respuesta=" ";         
    $tabla="OITM";            
    $campos = ' "ItemCode",
    "ItemName",
    "ItmsGrpCod" as "ItemsGroupCode",
    "FrgnName" as "ForeignName",
    "CstGrpCode" as "CustomsGroupCode",
    "CodeBars" as "BarCode",
    "PrchseItem" as "PurchaseItem",
    "SellItem" as "SalesItem",
    "InvntItem" as "InventoryItem",
    "SerialNum",
    "OnHand" as "QuantityOnStock",
    "OnOrder" as "QuantityOrderedFromVendors",
    "IsCommited" as "QuantityOrderedByCustomers",    
    "ManSerNum" as "ManageSerialNumbers",
    "ManBtchNum" as "ManageBatchNumbers",
    "SalUnitMsr" as "SalesUnit",
    "SLength1" as "SalesUnitLength",
    "SWidth1" as "SalesUnitWidth",
    "BHeight1" as "SalesUnitHeight",
    "SVolume" as "SalesUnitVolume",
    "BuyUnitMsr" as "PurchaseUnit",
    "DfltWH" as "DefaultWarehouse",
    "ByWh" as "ManageStockByWarehouse",
    "EnAstSeri" as "ForceSelectionOfSerialNumber",
    "Series",
    "UgpEntry" as "UoMGroupEntry",
    "SUoMEntry" as "DefaultSalesUoMEntry",
    \'0\' as "User",
    \'0\' as "Status",
    to_date("UpdateDate") as "DateUpdate",
    "FirmCode" as "Manufacturer",
    "NoDiscount" as "NoDiscounts",
    to_date("CreateDate") as "created_at",
    to_date("UpdateDate") as "updated_at",
    "TreeType" as "combo"
    ';
    $campos=$campos.$camposStd.',\'0\' AS "SalesPersonCode", "UserText"';
    // $condicion =" WHERE \"TreeType\"='N' or \"OnHand\">0 or \"InvntItem\"='N' and \"SellItem\"='Y' " . "limit 1000 offset ".$salto;
    //$condicion =" WHERE \"TreeType\"='N' or \"InvntItem\"='N' and \"SellItem\"='Y' " . "limit 1000 offset ".$salto;
      // $condicion =" WHERE \"TreeType\"='N' or \"InvntItem\"='N' and \"SellItem\"='Y' " . " limit 1000 offset ".$salto;    // $condicion = "";
      $condicion =" WHERE \"SellItem\"='Y' AND \"validFor\" = 'Y' " . " limit 1000 offset ".$salto;    // $condicion = "";
      // echo " SELECT " .$campos." FROM \"".$tabla."\" " .$condicion;

    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion,1);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;





case 50://sincronizar clientes maestro  
    $camposStd=$objDatos->std;
    //$camposStd=',"VatStatus"';
    $salto=$objDatos->salto;
    //$salto=1000;
    $respuesta=" ";         
    $tabla="OCRD";            
    $campos = '
    "CardCode",
    "CardName",
    "CreditLine" as "CreditLimit",
    "DebtLine" as "MaxCommitment",
    "Discount" as "DiscountPercent",
    "ListNum" as "PriceListNum",
    "SlpCode" as "SalesPersonCode",
    "Currency",
    "County",
    "Country",
    "Balance" as "CurrentAccountBalance",
    \'0\' as "NoDiscounts",
    "PriceMode",
    "PriceMode",
    "LicTradNum" as "FederalTaxID",
    "Phone1",
    \'0\' as "ContactPerson",
    "GroupNum" as "PayTermsGrpCode",
    "GroupCode",
    \'0\' as "BPAddresses",
    "Territory",
    "CardType",
    \'0\' as "DiscountRelations",   
    "Phone1",
    "Phone2",
    \'0\' as "ContactPerson",
    \'0\' as  "MailAddress",
    "E_Mail" as  "EmailAddress",
    "Address",      
    "CardFName" as "CardForeignName",
    "QryGroup1" as "Properties1",
    "QryGroup2" as "Properties2",
    "QryGroup3" as "Properties3",
    "QryGroup4" as "Properties4",
    "QryGroup5" as "Properties5",
    "QryGroup6" as "Properties6",
    "QryGroup7" as "Properties7",
    "Cellular",
    \'0\' as "ContactEmployees",
    "U_XM_Longitud",
    "U_XM_Latitud",
    \'0\' as "U_XM_Canal",
    \'0\' as "U_XM_Subcanal",
    \'0\' as "U_XM_TipoTienda",
    \'0\' as "U_XM_Cadena",
    \'0\' as "U_XM_CadenaDesc",
    CASE 
      when "OCRD"."validFor"=\'Y\' then 
        case 
          when CURRENT_DATE between to_date("OCRD"."validFrom") and to_date("OCRD"."validTo") then \'Y\'
          when "OCRD"."validFrom" is null then \'Y\'
          when "OCRD"."validTo" is null then \'Y\'
        else \'N\'
        end
      when "OCRD"."frozenFor"=\'Y\' then 
        case 
          when CURRENT_DATE between to_date("OCRD"."frozenFrom") and to_date("OCRD"."frozenTo") then \'N\'
          when "OCRD"."frozenFrom" is null then \'N\'
          when "OCRD"."frozenTo" is null then \'N\'
        else \'Y\'
      end
      else \'Y\'  
    
    end as "activo"

    ';
    $campos=$campos.$camposStd.',"IndustryC" as "Industry","Free_Text" as "FreeText"';
    $condicion =" where \"CardType\"='C' order by \"CardCode\" limit 1000 offset ".$salto;
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;
  case 51://sincronizar CONTACTOS de clientes 
    $respuesta=" ";  
    $salto=$objDatos->salto;       
      $tabla="OCPR";            
      $campos = '
        "Name",
        "CardCode", 
        "Address",
        "Tel1" AS "Phone1",
        "Tel2" AS "Phone2",
        "Cellolar" AS "MobilePhone",
        "Title",
        "UserSign" AS "User",
        "Notes1" AS "Comment",
        "E_MailL" AS "Mail",
        "CntctCode" AS "InternalCode",
        "CntctCode"
      ';
      $condicion =" order by \"CardCode\" limit 1000 offset ".$salto;
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
      } else {
      echo $respuesta["json"];      
  }
  break;
  case 52://sincronizar SUCURSAL de clientes  
    $salto=$objDatos->salto;
    $respuesta=" ";         
    $tabla="CRD1";            
    $campos = '
    "LineNum",
	  "Block",
    "Address" AS "AddressName",
    "Street",
    "Country",
    "City",
    "State",
    "LineNum" AS "Code",
    "LicTradNum" AS "FederalTaxID",
    \'0\' AS "CreditLimit",
    "TaxCode",
    "CardCode",
    "UserSign",
    "AdresType",
    \'0\' AS "U_Territorio",
    \'0\' AS "U_XM_Latitud",
    \'0\' AS "U_XM_Longitud"
    ';

    $condicion =" order by \"CardCode\" limit 1000 offset ".$salto;
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;
  case 53://contar clientes maestro 
    $respuesta=" ";         
    $tabla="OCRD";            
    $campos = 'Count("CardCode") as cantidad  ';
    $condicion ="  where \"CardType\"='C' ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
    echo $respuesta["json"];      
    }
  break;
  case 54://contar clientes sucursal  
    $respuesta=" ";         
    $tabla="CRD1";            
    $campos = 'Count("CardCode") as cantidad  ';
    $condicion ="  ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
    echo $respuesta["json"];      
    }
  break;
  case 55://contar clientes contactos 
    $respuesta=" ";         
    $tabla="OCPR";            
    $campos = 'Count("CardCode") as cantidad  ';
    $condicion ="  ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
    echo $respuesta["json"];      
    }
  break;

  case 56://sincronizar SUCURSAL de clientes  
    //$salto=$objDatos->salto;
    $CardCode=$objDatos->CardCode;
    
    $respuesta=" ";         
    $tabla="CRD1";            
    $campos = '
    "LineNum",
    "Address" AS "AddressName",
    "Street",
    "Country",
    "City",
    "State",
    "LineNum" AS "Code",
    "LicTradNum" AS "FederalTaxID",
    \'0\' AS "CreditLimit",
    "TaxCode",
    "CardCode",
    "UserSign",
    "AdresType",
    "U_Territorio",
    "U_XM_Latitud",
    "U_XM_Longitud"
    ';
    $condicion =" where \"CardCode\" = '".$CardCode."' ";
    //$condicion =" where \"CardCode\" = '1002500009' ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;


  case 20:// productos /**!**/
          $respuesta=" ";         
          $tabla="OITM";
          //$campos=" * ";
          //`"ItemCode","ItemName","ItmsGrpCod","FrgnName","CstGrpCode","CodeBars","PrchseItem","SellItem","InvntItem","UserText","SERIALNUMBER","OnHand","IsCommited",`.
          //`"OnOrder","ManSerNum","ManBtchNum","SalUnitMsr","SLength1","SWidth1","BHeight1","Svolume","BuyUnitMsr","DfltWH","ByWh",""EnAstSeri","Series","UgpEntry",`.
          //`"SUoMEntry","FirmCode","NoDiscount","CreateDate","UpdateDate"`;
        $campos = ' "ItemCode",
        "ItemName",
        "ItmsGrpCod" as "ItemsGroupCode",
        "FrgnName" as "ForeignName",
        "CstGrpCode" as "CustomsGroupCode",
        "CodeBars" as "BarCode",
        "PrchseItem" as "PurchaseItem",
        "SellItem" as "SalesItem",
        "InvntItem" as "InventoryItem",
        "UserText" as "UserText",
        "SerialNum",
        "OnHand" as "QuantityOnStock",
        "OnOrder" as "QuantityOrderedFromVendors",
        "IsCommited" as "QuantityOrderedByCustomers",
        "ManSerNum" as "ManageSerialNumbers",
        "ManBtchNum" as "ManageBatchNumbers",
        "SalUnitMsr" as "SalesUnit",
        "SLength1" as "SalesUnitLength",
        "SWidth1" as "SalesUnitWidth",
        "BHeight1" as "SalesUnitHeight",
        "SVolume" as "SalesUnitVolume",
        "BuyUnitMsr" as "PurchaseUnit",
        "DfltWH" as "DefaultWarehouse",
        "ByWh" as "ManageStockByWarehouse",
        "EnAstSeri" as "ForceSelectionOfSerialNumber",
        "Series",
        "UgpEntry" as "UoMGroupEntry",
        "SUoMEntry" as "DefaultSalesUoMEntry",
        1 as "User",
        1 as "Status",
        "UpdateDate" as "DateUpdate",
        "FirmCode" as "Manufacturer",
        "NoDiscount" as "NoDiscounts",
        "CreateDate" as "created_at",
        "UpdateDate" as "updated_at",
        "TreeType" as "combo" 
        ';
        //SERIALNUMBER
            $condicion="  WHERE \"SellItem\" = 'Y' ORDER BY \"ItemCode\" ";
        //$condicion="  ";    
echo " SELECT " .$campos." FROM \"".$tabla."\" " .$condicion;

            $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
            if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{      
          echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
          }
    break;
    /*case 21: //productosAlmacenes     
            $limite = $objDatos->limite;      
            $offset = $objDatos->inicio;      
            $respuesta=" ";         
            $tabla="OITW";            
            //$campos=" * ";
            //"ItemCode","WhsCode","OnHand","IsCommited","Locked","OnOrder";
            $campos = '"ItemCode","WhsCode","OnHand","IsCommited","Locked","OnOrder"';
            $condicion ="  order by  \"ItemCode\",\"WhsCode\" limit ".$limite." offset ".$offset. " ";
            //$condicion ="  WHERE \"OnHand\" > 0 limit ".$limite." offset ".$offset. " ";
      // echo $condicion;
            $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
            if($respuesta["json"]==''){          
            echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{
      echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
      }
        
    break;
    */
    case 21: //productosAlmacenes     SELECT  "ItemCode","WhsCode","OnHand","IsCommited","Locked","OnOrder"  FROM OITW T0 where ( T0."ItemCode",T0."WhsCode")  in( SELECT DISTINCT  T0."ItemCode", T0."Warehouse" FROM OINM T0)and T0."ItemCode"='ITE-0004154'  order by T0."ItemCode", T0."WhsCode"  ;
      $limite = $objDatos->limite;
      $offset = $objDatos->inicio;
      //$limite = "1000";
      //$offset = "0";
      $respuesta=" ";
      $tabla="OITW";
      //$campos=' count(*) as contador ';
      //"ItemCode","WhsCode","OnHand","IsCommited","Locked","OnOrder";
      $campos = '"ItemCode","WhsCode",("OnHand"-"IsCommited") as "OnHand" ,0 as "IsCommited","Locked","OnOrder"';
      //  $condicion ="  WHERE \"OnHand\" > 0 order by  \"ItemCode\",\"WhsCode\" limit 1000 offset ".$offset. " ";
      $condicion ="WHERE ( \"ItemCode\",\"WhsCode\")  in( SELECT DISTINCT  \"ItemCode\",\"Warehouse\" FROM OINM )order by  \"ItemCode\",\"WhsCode\" limit 1000 offset ".$offset. " ";
      // $condicion ="   order by  \"ItemCode\",\"WhsCode\" limit 1000 offset ".$offset. " ";
      //echo $condicion;
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if($respuesta["json"]==''){
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
      }else{
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
      }
    break;
    case 22: //productosPrecios
      $limite = $objDatos->limite;
      $offset = $objDatos->inicio;
      $respuesta=" ";         
      $tabla="EXX_XM_ProductosPreciosSap";
      //$tabla="ITM1";
      //$campos=" * ";
      //`"ItemCode","PriceList","UomEntry","Price","Currency"`;
      $campos = '*';
        $condicion="  WHERE \"Price\" > 0  ";
      if (isset($limite) && isset($offset)) {
        $condicion .= " limit " . $limite . " offset " . $offset . " ";
      }
      //$condicion="  ";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if($respuesta["json"]==''){          
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
      }else{
      echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
      }
    break;
    case 23:// cantidad de productos
          $respuesta=" ";         
          $tabla="OITM";          
          $campos = " COUNT(*) as Cantidad ";
          $condicion="  WHERE \"SellItem\" = 'Y'  ";
          $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
          if($respuesta["json"]==''){          
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
          }else{      
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
          }
    break;
    /*case 24:// cantidad de productosalmacenes COMPANEX
            $respuesta=" ";         
            $tabla="OITW";
            $campos = " COUNT(*) as Cantidad ";
            $condicion ="  ";
            //$condicion ="  WHERE \"OnHand\" > 0 ";
            $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
            if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
            }else{      
          echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
            }
    break;*/
    case 24:
        $respuesta="";
        $tabla="OINM";
        $campos = 'COUNT(distinct "ItemCode","Warehouse") As Cantidad ';
        $condicion ="";
        //$condicion =" ";
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
        }else{
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
        }
      break;
    case 28: // cantidad productos precios
        $respuesta=" ";         
                $tabla="EXX_XM_ProductosPreciosSap";
                $campos = "COUNT(*) as Cantidad";
                $condicion ="  WHERE \"Price\" > 0 ";
                $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
                if($respuesta["json"]==''){          
              echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
                }else{      
              echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
                }
    break;
case 101://contador de facturas    
      $respuesta=" ";         
      $tabla="EXX_XM_FacturasSap";
          $campos=" count(\"DocEntry\") as \"REGISTROS\" ";
      $condicion=" ";
          $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
          if($respuesta["json"]==''){          
           echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
          }else{
           echo json_encode(utf8_converter($respuesta["resultado"]),JSON_UNESCAPED_UNICODE);
           //var_dump($respuesta["resultado"]);
       }
break;
case 102://contador de ofertas    
  $respuesta=" ";         
  $tabla="EXX_XM_OfertasSap";
      $campos=" count(\"DocEntry\") as \"REGISTROS\" ";
  $condicion=" ";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if($respuesta["json"]==''){          
       echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
      }else{
       echo json_encode(utf8_converter($respuesta["resultado"]),JSON_UNESCAPED_UNICODE);
       //var_dump($respuesta["resultado"]);
   }
break;
case 103://contador de pedidos
  $Repartidor=$objDatos->Repartidor;
	$respuesta=" ";         
	$tabla="EXX_XM_PedidoSapGeo";//*REVISAR RNP
    $campos=" count(\"DocEntry\") as \"REGISTROS\" ";
    $condicion=" where \"Repartidor\"=".$Repartidor;
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
        //var_dump($respuesta["resultado"]);
	}
break;
case 104://contador de entregas
	$respuesta=" ";         
	$tabla="EXX_XM_EntregasSap";
    $campos=" count(\"DocEntry\") as \"REGISTROS\" ";
    $condicion=" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
        //var_dump($respuesta["resultado"]);
	}
break;
case 105://contador de detalle de facturas
	$respuesta=" ";         
	$tabla="EXX_XM_FacturasDetalleSap";
    $campos=" count(\"DocEntry\") as \"REGISTROS\" ";
    $condicion=" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
        //var_dump($respuesta["resultado"]);
	}
break;
case 106://contador de detalle de ofertas
	$respuesta=" ";         
	$tabla="EXX_XM_OfertasDetallesSap";
    $campos=" count(\"DocEntry\") as \"REGISTROS\" ";
    $condicion=" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
        //var_dump($respuesta["resultado"]);
	}
break;
case 107://contador de detalle de pedidos
  $Repartidor=$objDatos->Repartidor;
	$respuesta=" ";         
	$tabla="EXX_XM_pedidosDetalleSap";
    $campos=" count(\"DocEntry\") as \"REGISTROS\" ";
    $condicion=" where \"Repartidor\"=".$Repartidor;
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
        //var_dump($respuesta["resultado"]);
	}
break;
case 108://contador de detalle de entregas
	$respuesta=" ";         
	$tabla="EXX_XM_EntregasDetalleSap";
    $campos=" count(\"DocEntry\") as \"REGISTROS\" ";
    $condicion=" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{
        echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
        //var_dump($respuesta["resultado"]);
	}
break;



  case 200: // bonificaciones semiautomaticas
    $respuesta=" ";         
    $tabla="@BONIFICACION_CA";            
    $campos = '*';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{ 
          echo $respuesta["json"];
    } 

  break;
case 201: // bonificaciones semiautomaticas
  $respuesta=" ";         
  $tabla="@BONIFICACION_DE1";            
  $campos = '*';
  $condicion =" ";
  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
  if($respuesta["json"]==''){          
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
  }else{ 
        echo $respuesta["json"];
  } 

  break;
case 202: // bonificaciones semiautomaticas
  $respuesta=" ";         
  $tabla="@BONIFICACION_DE2";            
  $campos = '*';
  $condicion =" ";
  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
  if($respuesta["json"]==''){          
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
  }else{ 
        echo $respuesta["json"];
  } 

  break;
case 203: // actividad de cliente
  $respuesta=" ";         
  $tabla="@ACTIVIDADECLIENTE";            
  $campos = '*';
  $condicion =" ";
  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
  if($respuesta["json"]==''){          
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
  }else{ 
        echo $respuesta["json"];
  } 

  break;
case 204: // actividad de producto
  $respuesta=" ";         
  $tabla="@ACTIVIDADEPRODUCTO";            
  $campos = '*';
  $condicion =" ";
  $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
  if($respuesta["json"]==''){          
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
  }else{ 
        echo $respuesta["json"];
  } 

  break;
  /// tablas de companex
  case 300: //canal
    $respuesta=" ";         
    $tabla="@CANAL";            
    $campos = '*';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{ 
          echo $respuesta["json"];
    } 
  break;
  case 301: //subcanal
    $respuesta=" ";         
    $tabla="@SUB_CANAL";            
    $campos = '*';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{ 
          echo $respuesta["json"];
    } 
  break;
  case 302: //tipotienda
    $respuesta=" ";         
    $tabla="@TIPO_TIENDA";            
    $campos = '*';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{ 
          echo $respuesta["json"];
    } 
  break;
  case 303: //cadena
    $respuesta=" ";         
    $tabla="@CADENA";            
    $campos = '*';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{ 
          echo $respuesta["json"];
    } 
  break;
  case 304: //cadena
    $respuesta=" ";         
    $tabla="@SOCIO_CONSOLIDADOR";            
    $campos = '*';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){          
          echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    }else{ 
          echo $respuesta["json"];
    } 
  break;
  case 305://sincronizar CONTACTOS de cliente especifico
    $respuesta=" ";
    $cliente=$objDatos->cliente;
          
      $tabla="OCPR";            
      $campos = '
        "Name",
        "CardCode", 
        "Address",
        "Tel1" AS "Phone1",
        "Tel2" AS "Phone2",
        "Cellolar" AS "MobilePhone",
        "Position" AS "Title",
        "UserSign" AS "User",
        "Notes1" AS "Comment",
        "E_MailL" AS "Mail",
        "CntctCode" AS "InternalCode",
        "CntctCode"
      ';
      $condicion =" where \"CardCode\" ='".$cliente."'";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
      } else {
      echo $respuesta["json"];      
      }
  break;
  case 306://sincronizar PROMOCIONES
      $salto=$objDatos->salto;         
      $respuesta=" ";         
      $tabla="EXX_XM_Campanas";            
      $campos = '*';
      $condicion =" ";
       $condicion =" order by \"CpnNo\",\"BpCode\" limit 1000 offset ".$salto;
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if($respuesta["json"]==''){          
            echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
      }else{ 
            echo $respuesta["json"];
      } 
     
  break;
  case 307://sincronizar PROMOCIONES         
      $respuesta=" ";         
      $tabla="EXX_XM_Campanas";            
      $campos = "COUNT(*) as Cantidad";
      $condicion =" ";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if($respuesta["json"]==''){          
            echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
      }else{ 
            echo $respuesta["json"];
      } 
     
  break;
  case 308: // lotes de un producto en un almacen
    $respuesta=" ";
    $item = $objDatos->Item;
    $almacen = $objDatos->almacen;
    // $tabla="OBTN"; // NO USAR
    // $tabla="OIBT";
    $tabla="EXX_XM_LotesProductos";
    $campos = '*';

    $condicion =" ";
    $condicion .= " where \"ItemCode\"='" . $item . "' and \"WhsCode\"='" . $almacen. "' ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
    } else {
      echo $respuesta["json"];      
    }
  break;
  case 400: // consultas en linea Centro de costo CC 3
      $respuesta=" ";
      $codesubcanal = $objDatos->codeSubCanal;     
      $tabla="@SUB_CANAL";
      $campos = '*';
      $condicion =" ";
      $condicion .= " where \"Code\"='" . $codesubcanal. "' ";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if ($respuesta["json"]=='') {
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
      } else {
        //echo json_encode(utf8_converter($respuesta["json"]),JSON_UNESCAPED_UNICODE);
        echo $respuesta["json"];      
      }
  break;
  case 401: // consultas en linea Centro de costo CC 4
      $respuesta=" ";
      $codeMarca = $objDatos->marca;     
      $tabla="@MARCA";
      $campos = '*';
      $condicion =" ";
      $condicion .= " where \"Code\"='" . $codeMarca. "' ";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if ($respuesta["json"]=='') {
        echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
      } else {
         //echo json_encode(utf8_converter($respuesta["json"]),JSON_UNESCAPED_UNICODE);
        echo $respuesta["json"];      
      }
  break;
  /*case 1113://saldo del cliente
      $respuesta=" ";
      $codigo=$objDatos->codigo;
      $tabla="OCRD";
      $campos = '"OCRD"."Balance","OCRD"."CreditLine","OCRD"."CardCode"';
      $condicion ="  where  \"CardCode\"='".$codigo."' ";
      $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
      if ($respuesta["json"]=='') {
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
      } else {
      echo $respuesta["json"];
      }
    break;
*/
    case 1114://saldo del cliente
    $respuesta="";
    $codigo=$objDatos->codigo;
    $tabla="EXX_XM_ObtenerClientes";
    $campos='*';
    $condicion ="  where  \"CardCode\"='".$codigo."' ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if($respuesta["json"]==''){
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    }else{
      //echo json_encode(utf8_converter($respuesta["resultado"]), JSON_PRETTY_PRINT);
      echo $respuesta["json"];
    }
  
    break;
  
  case 1115:// productos por almacen /**!**/
    $respuesta=" ";
    $codigo=$objDatos->codigo;
    $almacen=$objDatos->almacen;
    $tabla="OITW";
    $campos = '*';
    $condicion="  WHERE \"ItemCode\" = '".$codigo."' AND \"WhsCode\" = '".$almacen."'";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);

    if($respuesta["json"]==''){
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    }else{
    echo $respuesta["json"];
    }

  break;

  case 1116://contar clientes maestro
    $respuesta=" ";
    $rut=$objDatos->rut;
    $CardCode=$objDatos->CardCode;
    $tabla="OCRD";
    $campos = 'Count("CardCode") as cantidad  ';
    $condicion ="  where  \"LicTradNum\"='".$rut."' OR \"CardCode\"='".$CardCode."' ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
    echo $respuesta["json"];
    }
  break;
  case 1117://Pedido
    $respuesta=" ";
    $codigo=$objDatos->codigo;
    $tabla="ORDR";
    $campos = ' "U_xMOB_Codigo","DocStatus","InvntSttus" ';
    $condicion ="  where  \"DocEntry\"='".$codigo."' ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
    echo $respuesta["json"];
    }
  break;
  case 1118://Factura
    $respuesta=" ";
    $codigo=$objDatos->codigo;
    $tabla="OINV";
    $campos = ' "U_xMOB_Codigo","DocStatus","InvntSttus" ';
    $condicion ="  where  \"DocEntry\"='".$codigo."' ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
    echo $respuesta["json"];
    }
  break;
  case 1119://Oferta
    $respuesta=" ";
    $codigo=$objDatos->codigo;
    $tabla="OQUT";
    $campos = ' "U_xMOB_Codigo","DocStatus","InvntSttus" ';
    $condicion ="  where  \"DocEntry\"='".$codigo."' ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
    echo $respuesta["json"];
    }
  break;
  case 1120://sincronizar clientes maestro movil sap
    $vendedor=$objDatos->vendedor;
    $salto=$objDatos->salto;
    $respuesta=" ";
    $tabla="OCRD";
    $campos = '
    "CardCode",
    "CardName",
    "CreditLine" as "CreditLimit",
    "DebtLine" as "MaxCommitment",
    "Discount" as "DiscountPercent",
    "ListNum" as "PriceListNum",
    "SlpCode" as "SalesPersonCode",
    "Currency",
    "County",
    "Country",
    "Balance" as "CurrentAccountBalance",
    \'0\' as "NoDiscounts",
    "PriceMode",
    "PriceMode",
    "LicTradNum" as "FederalTaxID",
    "Phone1",
    \'0\' as "ContactPerson",
    "GroupNum" as "PayTermsGrpCode",
    "GroupCode",
    \'0\' as "BPAddresses",
    "Territory",
    "CardType",
    \'0\' as "DiscountRelations",
    "Phone1",
    "Phone2",
    \'0\' as "ContactPerson",
    \'0\' as  "MailAddress",
    \'0\' as  "EmailAddress",
     "Address",
    "CardFName" as "CardForeignName",
    "QryGroup1" as "Properties1",
    "QryGroup1" as "Properties2",
    "QryGroup1" as "Properties3",
    "QryGroup1" as "Properties4",
    "QryGroup1" as "Properties5",
    "QryGroup1" as "Properties6",
    "QryGroup1" as "Properties7",
    "Cellular",
    \'0\' as "ContactEmployees",
    "U_XM_Longitud",
    "U_XM_Latitud"
    ';
    $campos=$campos.$camposStd.',"IndustryC" as "Industry","Free_Text" as "FreeText"';
    $condicion =" where \"SlpCode\"='".$vendedor."' and   \"CardType\"='C' order by \"CardCode\" limit 1000 offset ".$salto;
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
      echo $respuesta["json"];
    }
  break;
  case 1121://sincronizar clientes maestro movil sap cantdad
    $vendedor=$objDatos->vendedor;
    $salto=$objDatos->salto;
    $respuesta=" ";
    $tabla="OCRD";
    $campos = 'Count("CardCode") as cantidad ';
    $campos=$campos.$camposStd.',"IndustryC" as "Industry","Free_Text" as "FreeText"';
    $condicion =" where \"SlpCode\"='".$vendedor."' and   \"CardType\"='C' order by \"CardCode\" limit 1000 offset ".$salto;
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
      echo $respuesta["json"];
    }
  break;
  /// documentos online MAU
  case 2000:// contador de documentos en base a un vendedor
    $vendedor=$objDatos->vendedor;
    $respuesta=" ";
    $tabla="EXX_XM_DocsImportados";
    $campos = 'Count("CardCode") as cantidad';
    $condicion =" where \"U_XMB_repartidor\"='".$vendedor."'  ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
      echo $respuesta["json"];
    }
  break;
  case 2001: // cabecera de docuementos  en base a un vendedor
    $vendedor=$objDatos->vendedor;
    $salto=$objDatos->salto;
    $respuesta=" ";
    $tabla="EXX_XM_DocsImportados";
    $campos = '*';
    $condicion =" where \"U_XMB_repartidor\"='".$vendedor."' limit 1000 offset ".$salto;
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
      echo $respuesta["json"];
    }
  break;
  case 2002: // cabecera de docuementos  de un cliente
    $cliente=$objDatos->cliente;
    $respuesta=" ";
    $tabla="EXX_XM_DocsImportados";
    $campos = '*';
    $condicion =" where \"CardCode\"='".$cliente."'";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
      echo $respuesta["json"];
    }
  break;
  case 2003:// contador de documentos detalle en base a un vendedor
    $vendedor=$objDatos->vendedor;
    $respuesta=" ";
    $tabla="EXX_XM_DocsImportadosDetalle";
    $campos = 'Count("CardCode") as cantidad ';
    $condicion =" where \"U_XMB_repartidor\"='".$vendedor."'  ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
      echo $respuesta["json"];
    }
  break;
  case 2004: // detalle de docuementos  en base a un vendedor
    $vendedor=$objDatos->vendedor;
    $salto=$objDatos->salto;
    $respuesta=" ";
    $tabla="EXX_XM_DocsImportadosDetalle";
    $campos = '*';
    $condicion =" where \"U_XMB_repartidor\"='".$vendedor."' limit 1000 offset ".$salto;  
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
      echo $respuesta["json"];
    }
  break;
  case 2005: // detalle de docuementos  de un cliente
    $cliente=$objDatos->cliente;
    $respuesta=" ";
    $tabla="EXX_XM_DocsImportadosDetalle";
    $campos = '*';
    $condicion =" where \"CardCode\"='".$cliente."'  ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
      echo $respuesta["json"];
    }
  break;
  case 2006: // case dinamico
		$respuesta=" ";
		//$tabla="OPRC";
		//$campos = ' "PrcCode" As code, "PrcName" As name ';
		//$condicion =" where \"DimCode\"='1'  ";
		$tabla=$objDatos->tabla;
		$campos = $objDatos->campos;
		$condicion = $objDatos->condicion;

		$respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
		if ($respuesta["json"]=='') {
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
		} else {
		  echo $respuesta["json"];
		}
	break;
  // facturacion electronica
  case 1300: // obtencion de CUF
		$empresaFex=$objDatos->empresafex;
		//$empresaFex=1;
		$fechaFex=date("Y/m/d");
		//$fechaFex=$objDatos->fecha;
		//$sucursal=$objDatos->sucursal;
		//$puntoventa=$objDatos->puntoventa;
		$respuesta=" ";         
		$tabla="\"FEX_BO\".\"FEX_BO_CUFD\"";
        $campos="*";
		//$condicion=" where \"IdFexCompany\"= ".$empresaFex."  and  \"FechaVigencia\" like '".$fechaFex."%'";		
		$condicion=" where \"IdFexCompany\"= ".$empresaFex."  and  \"FechaCreacion\" >timestamp '".$fechaFex."' ";		
        $respuesta=$accion->devolver_datos_tabla3($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]),JSON_UNESCAPED_UNICODE);
         //var_dump($respuesta["resultado"]);
		 }
	break;
	case 1301: // obtencion de Puntos de venta
		$empresaFex=$objDatos->empresafex;
		$respuesta=" ";         
		//$tabla="@EXX_FE_BO_PTOVENTA";
		$tabla="EXX_XM_FEX";
        $campos="*";
		//$condicion=" where \"IdFexCompany\"= ".$empresaFex;	
		$condicion=" ";		
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]),JSON_UNESCAPED_UNICODE);
		 }
	break;
	case 1302: // obtencion de Puntos de venta
		$empresaFex=$objDatos->empresafex;
		//$fechaFex=$objDatos->fecha;
		//$sucursal=$objDatos->sucursal;
		//$puntoventa=$objDatos->puntoventa;
		$respuesta=" ";         
		//$tabla="@EXX_FE_BO_SUCURSAL";
		$tabla="EXX_XM_FEX";
        $campos="*";
		$condicion="  ";		
        $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
        if($respuesta["json"]==''){          
         echo json_encode(array("estado"=>600,"error"=>"Problema de encode json ")); 
        }else{
         echo json_encode(utf8_converter($respuesta["resultado"]),JSON_UNESCAPED_UNICODE);
         //var_dump($respuesta["resultado"]);
		 }
	break;  
  case 1400://  facturacion electronica/**!**/
   
		$respuesta=" ";
		$iddoc=$objDatos->iddoc;
		$nit=$objDatos->nit;
		$acc=$objDatos->acc;

		$tabla="OINV";
		if($acc == "0"){
			$campos = 'Count(*) as CANTIDAD';
		}else{
			$campos = '"DocDate","CardName","U_LB_RazonSocial","U_LB_NIT","DocTotal",
			case 
			when "U_EXX_FE_Cuf" is null then \'0\' 
			WHEN "U_EXX_FE_Estado" <>\'AUT\' then \'0\'
			when "U_LB_NumeroFactura" is null then \'0\' 
			else "U_EXX_FE_Cuf"
			end as "U_EXX_FE_Cuf","U_LB_NumeroFactura"';
		}
		$condicion="  WHERE \"DocEntry\" = '".$iddoc."' ";
		$respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
		
		if($respuesta["json"]==''){
		  echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
		}else{
		  echo $respuesta["json"];
		}
	break;
  case 1401://TIPO TARJETA
    $respuesta=" ";
    $codigo=$objDatos->codigo;
    $tabla="OCRC";
    $campos = '"OCRC"."CreditCard","OCRC"."CardName","OCRC"."AcctCode"';
    $condicion =" ";
    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    if ($respuesta["json"]=='') {
    echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    } else {
    echo $respuesta["json"];
    }
  break;
  case 1402:// NUMERO DE FACTURA/**!**/
   
    $respuesta=" ";
    $iddoc=$objDatos->iddoc;

    $tabla="OINV";
    $campos = '"DocNum","U_LB_NumeroFactura"';
    $condicion="  WHERE \"U_xMOB_Codigo\" = '".$iddoc."' ";

    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    
    if($respuesta["json"]==''){
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    }else{
      echo $respuesta["json"];
    }
  break;
  case 1403:/* tipo de impuesto */
   
    $respuesta=" ";

    $tabla="OSTC a ";
    $campos = 'a."Code", a."Rate",	b."Line_ID" AS "RowNumber", b."STCCode", b."STACode", b."EfctivRate" AS "EffectiveRate", ';
    $campos = $campos . "'1' AS \"User\", '1' AS \"Status\", TO_VARCHAR(CURRENT_DATE,'DD/MM/YYYY') AS \"DateUpdate\" ";
    $condicion='INNER JOIN STC1 b ON a."Code" = b."STCCode" ';

    $respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
    
    if($respuesta["json"]==''){
      echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
    }else{
      echo $respuesta["json"];
    }
  break;

}
function utf8_converter($array)
{
array_walk_recursive($array, function(&$item, $key){
  if(!mb_detect_encoding($item, 'utf-8', true)){
    $item = utf8_encode($item);
  }
  });

return $array;
}
/*
CREATE VIEW "EXX_XM_FacturasSap"  AS SELECT
   "DocEntry",
   "DocNum",
   TO_DATE("DocDate") as "DocDate",
   "CANCELED",
   TO_DATE("DocDueDate") AS "DocDueDate",
   "CardCode",
   "CardName",
   "DocCur" AS "DocCurrency",
   "JrnlMemo" AS "JournalMemo",
   "GroupNum" AS "PaymentGroupCode",
   "Series",
   TO_DATE("TaxDate") AS "TaxDate",
   TO_DATE("CreateDate") AS "CreationDate",
   TO_DATE("UpdateDate") AS "UpdateDate",
   "FinncPriod" AS "FinancialPeriod",
   TO_DATE("UpdateDate") AS "UpdateTime",
   "U_LB_NumeroFactura",
   "U_LB_NumeroAutorizac",
   "U_LB_FechaLimiteEmis",
   "U_LB_CodigoControl",
   "U_LB_EstadoFactura",
   "U_LB_RazonSocial",
   "U_LB_TipoFactura",
   "ReqName" AS "User",
   "DocStatus" AS "Status",
   "InvntSttus" as "pedienteEntrega",
   "DocTime",
   "ImportEnt" AS "NumPedido",
   "isIns" AS "ReserveInvoice",
   "SlpCode" AS "SalesPersonCode",
   "PaidSys",
   "DocTotal",
   ROUND ("PaidToDate",
   2) AS "PaidToDate",
   ROUND (("DocTotal"-"PaidToDate"),
   2)AS "Saldo" 
FROM "OINV" 
where "DocStatus"='O' 
OR "InvntSttus"='O'

Create VIEW "EXX_XM_FacturasDetalleSap" as SELECT 
   "OINV"."DocNum",
   "INV1"."DocEntry",
   "INV1"."LineNum",
   "INV1"."ItemCode",
   "INV1"."Dscription" AS "ItemDescription",
   "INV1"."PriceAfVAT" AS "PriceAfterVAT",
   "INV1"."Currency",
   "INV1"."Rate",
   "INV1"."TotInclTax" AS "TaxTotal",
   "INV1"."PriceBefDi" AS "UnitPrice",
   "INV1"."Quantity",
   "INV1"."Price",
   "INV1"."LineTotal",
   "INV1"."OpenQty",
   ("INV1"."Quantity"-"INV1"."OpenQty") AS "Entregado",
   "OINV"."CANCELED",
   "INV1"."WhsCode" 
FROM "INV1" JOIN "OINV" ON "OINV"."DocEntry" = "INV1"."DocEntry" 
where "OINV"."DocStatus"='O' OR "OINV"."InvntSttus"='O'

 Create  VIEW "EXX_XM_PedidoSap"  AS SELECT
   "DocEntry",
   "DocNum",
   TO_DATE("DocDate") as "DocDate",
   "CANCELED",
   TO_DATE("DocDueDate") AS "DocDueDate",
   "CardCode",
   "CardName",
   "DocCur" AS "DocCurrency",
   "JrnlMemo" AS "JournalMemo",
   "GroupNum" AS "PaymentGroupCode",
   "Series",
   TO_DATE("TaxDate") AS "TaxDate",
   TO_DATE("CreateDate") AS "CreationDate",
   TO_DATE("UpdateDate") AS "UpdateDate",
   "FinncPriod" AS "FinancialPeriod",
   TO_DATE("UpdateDate") AS "UpdateTime",
   "U_LB_NumeroFactura",
   "U_LB_NumeroAutorizac",
   "U_LB_FechaLimiteEmis",
   "U_LB_CodigoControl",
   "U_LB_EstadoFactura",
   "U_LB_RazonSocial",
   "U_LB_TipoFactura",
   "ReqName" AS "User",
   "DocStatus" AS "Status",
   "DocTime",
   "ImportEnt" AS "NumPedido",
   "isIns" AS "ReserveInvoice",
   "SlpCode" AS "SalesPersonCode",
   "PaidSys",
   "DocTotal",
   "InvntSttus" as "pedienteEntrega"
FROM "ORDR"
where "DocStatus"='O' 
OR "InvntSttus"='O'

Create VIEW "EXX_XM_pedidosDetalleSap" as SELECT 
   "ORDR"."DocNum",
   "RDR1"."DocEntry",
   "RDR1"."LineNum",
   "RDR1"."ItemCode",
   "RDR1"."Dscription" AS "ItemDescription",
   "RDR1"."PriceAfVAT" AS "PriceAfterVAT",
   "RDR1"."Currency",
   "RDR1"."Rate",
   "RDR1"."TotInclTax" AS "TaxTotal",
   "RDR1"."PriceBefDi" AS "UnitPrice",
   "RDR1"."Quantity",
   "RDR1"."Price",
   "RDR1"."LineTotal",
   "RDR1"."OpenQty",
   ("RDR1"."Quantity"-"RDR1"."OpenQty") AS "Entregado",
   "ORDR"."CANCELED" ,
   "RDR1"."WhsCode" 
FROM "RDR1" JOIN "ORDR" ON "ORDR"."DocEntry" = "RDR1"."DocEntry" 
where "ORDR"."DocStatus"='O' OR "ORDR"."InvntSttus"='O'

CREATE VIEW "EXX_XM_ProductosAlt" as
SELECT 
"ITT1"."Father" as ComboCode,
"ITT1"."Code" as ItemCode,
"@AALTERNATIVO"."U_AlternativeCode" as ItemCodeAlternative,
"ITT1"."ChildNum",
"ITT1"."Currency",
"ITT1"."Quantity",
"ITT1"."Warehouse",
"ITT1"."Price",
"ITT1"."PriceList",
"OITM"."CodeBars",
"OITM"."ItemName"
 FROM "ITT1"
 JOIN "@AALTERNATIVO" ON "@AALTERNATIVO"."U_ItemCode" = "ITT1"."Code"
 JOIN "OITM" ON "OITM"."ItemCode" = "@AALTERNATIVO"."U_ItemCode";

CREATE VIEW "EXX_XM_LotesProductos" as
SELECT 
"ItemCode",
"BatchNum",
"WhsCode",
("Quantity"-"IsCommited")as "Quantity" ,
TO_DATE(TO_VARCHAR ("InDate",'YYYY-MM-DD')) as "Ingreso",
TO_DATE(TO_VARCHAR ("ExpDate",'YYYY-MM-DD')) as "Expira",
"BaseType",
"BaseEntry",
"BaseNum",
"BaseLinNum",
"Transfered",
"DataSource"
from 
"OIBT"
WHERE 
("Quantity"-"IsCommited")>0;

Create VIEW "EXX_XM_SeriesUsadas" as
SELECT 
T0."ItemCode",
T0."DistNumber",
T0."MnfSerial",
T0."InDate",
T0."Location",
T0."Notes" 
FROM "OSRN" T0 
inner join "ITL1" T1 on T1."SysNumber" = T0."SysNumber" and T1."ItemCode" = T0."ItemCode" 
inner join "OITL" T2 on T1."LogEntry" = T2."LogEntry" 
where T2."DocType" in (15,13);

CREATE VIEW "EXX_XM_PagosCuenta" as
SELECT "DocEntry",
  "DocNum" ,
  "DocType" ,
  "DocDate" ,
  "DocDueDate" ,
  "CardCode" ,
  "CardName",
  "CashAcct" ,
  "CashSum" ,
  "CashSumFC" ,
  "CreditSum" ,
  "CredSumFC" ,
  "CheckAcct" ,
  "CheckSum" ,
  "CheckSumFC" ,
  "TrsfrAcct" ,
  "TrsfrSum" ,
  "TrsfrSumFC" ,
  "TrsfrDate" ,
  "DocCurr" ,
  "DocRate" ,
  "DocTotal" ,
  "DocTotalFC" ,
  "Ref1",
  "JrnlMemo" ,
  "TransId" 
   from "ORCT" where "PayNoDoc"='Y';

CREATE VIEW "EXX_XM_CUOTASPAGO" AS
SELECT 
T0."GroupNum",
T0."PymntGroup",
T1."CTGCode", 
T1."IntsNo", 
T1."InstMonth", 
T1."InstDays", 
T1."InstPrcnt" 
FROM OCTG T0  INNER JOIN CTG1 T1 ON T0."GroupNum" = T1."CTGCode";

CREATE VIEW "EXX_XM_CUOTASFACTURA"
as SELECT 
T0."DocNum",
T0."DocType",
T0."CardName",
T1."InsTotal",
TO_DATE(T1."DueDate") AS "DueDate",
T1."InstlmntID",
T1."InstPrcnt",
T1."Paid",
(T1."InsTotal"-T1."Paid") as Saldo
FROM OINV T0  
INNER JOIN INV6 T1 ON T0."DocEntry" = T1."DocEntry"
WHERE (T1."InsTotal"-T1."Paid")>0
ORDER BY T0."DocNum",T1."InstlmntID";

CREATE VIEW "EXX_XM_AlmacenSerie" as
SELECT
   T0."ItemCode",
   T0."SysNumber",
   T0."DistNumber", 
   T2."LocCode" 
FROM "OSRN" T0 
inner join "ITL1" T1 on T1."SysNumber" = T0."SysNumber" 
and T1."ItemCode" = T0."ItemCode" 
inner join "OITL" T2 on T1."LogEntry" = T2."LogEntry" 

-------------------------------------------------------------------------
CREATE VIEW "EXX_XM_OfertasSap" as select 
"DocEntry",
   "DocNum",
   "DocDate",
   "DocDueDate",
   "CardCode",
   "CardName",
   "DocTotal",
   -- "DocCurrency", 
-- "JournalMemo", 
-- "PaymentGroupCode", 
"DocTime",
   "Series",
   "TaxDate",
   -- "CreationDate", 
"UpdateDate",
   -- "FinancialPeriod", 
-- "UpdateTime", 
"U_LB_NumeroFactura",
   "U_LB_NumeroAutorizac",
   "U_LB_FechaLimiteEmis",
   "U_LB_CodigoControl",
   "U_LB_EstadoFactura",
   "U_LB_RazonSocial",
   "U_LB_TipoFactura",
   "ReqName" AS "User",
   "DocStatus" AS "Status",
   "InvntSttus" as "pedienteEntrega",
   "isIns" AS "ReserveInvoice",
   "SlpCode" AS "SalesPersonCode",
   ROUND ("PaidToDate",
   2) AS "PaidToDate",
   ROUND (("DocTotal"-"PaidToDate"),
   2)AS "Saldo" 
FROM "OQUT" 
where  "CANCELED" = 'N' and ("DocStatus" = 'O' OR "InvntSttus"='O')


CREATE VIEW "EXX_XM_OfertasDetallesSap" as select   
   R."DocEntry",
   R."LineNum",
   R."ItemCode",
   R."Dscription" as "ItemDescription",
   R."Price",
   R."Quantity",
   R."Currency",
   R."Rate",
   R."LineTotal",
   R."OpenQty",
   R."UomCode",
   R."PriceAfVAT",
   R."WhsCode",
   R."GTotal",
   R."LineStatus" 
from "QUT1" R 
inner join "OQUT" O on (R."DocEntry" = O."DocEntry") 
where O."DocStatus"='O' OR O."InvntSttus"='O'

CREATE VIEW EXX_XM_EntregasSap" as select 
"DocEntry",
   "DocNum",
   "DocDate",
   "DocDueDate",
   "CardCode",
   "CardName",
   "DocTotal",
   -- "DocCurrency", 
-- "JournalMemo", 
-- "PaymentGroupCode", 
"DocTime",
   "Series",
   "TaxDate",
   "CreateDate",
   "UpdateDate",
   -- "FinancialPeriod", 
-- "UpdateTime", 
"U_LB_NumeroFactura",
   "U_LB_NumeroAutorizac",
   "U_LB_FechaLimiteEmis",
   "U_LB_CodigoControl",
   "U_LB_EstadoFactura",
   "U_LB_RazonSocial",
   "U_LB_TipoFactura",
   "ReqName" AS "User",
   "DocStatus" AS "Status",
   "InvntSttus" as "pedienteEntrega",
   "isIns" AS "ReserveInvoice",
   "SlpCode" AS "SalesPersonCode",
   ROUND ("PaidToDate",
   2) AS "PaidToDate",
   ROUND (("DocTotal"-"PaidToDate"),
   2)AS "Saldo" 
from "ODLN" 
where "CANCELED" = 'N' and ("DocStatus" = 'O' OR "InvntSttus"='O')

CREATE VIEW "EXX_XM_EntregasDetalleSap" as select 
   -- R."DocNum",
 R."DocEntry",
   R."LineNum",
   R."ItemCode",
   --  R."ItemDescription",
 --R."PriceAfterVAT",
 R."Currency",
   R."Rate",
   -- R."TaxTotal",
 -- R."UnitPrice",
 R."Quantity",
   R."Price",
   R."LineTotal",
   R."OpenQty",
   R."UomCode",
   R."PriceAfVAT",
   R."WhsCode",
   R."GTotal",
   R."LineStatus" --R."Entregado",
 --R."CANCELED"
 
from "DLN1" R 
inner join "ODLN" O on (R."DocEntry" = O."DocEntry" 
  and O."CANCELED" = 'N') 
where O."DocStatus" = 'O' OR O."InvntSttus"='O'


Create VIEW "EXX_XM_NotasCreditoSap" as
SELECT 
"DocEntry",
"DocNum",
TO_DATE("DocDate") as "DocDate",
"CANCELED",
TO_DATE("DocDueDate") AS "DocDueDate",
"CardCode",
"CardName",
"DocCur" AS "DocCurrency",
"JrnlMemo" AS "JournalMemo",
"GroupNum" AS "PaymentGroupCode",
"Series",
TO_DATE("TaxDate") AS "TaxDate",
TO_DATE("CreateDate") AS "CreationDate",
TO_DATE("UpdateDate") AS "UpdateDate",
"FinncPriod" AS "FinancialPeriod",
TO_DATE("UpdateDate")  AS "UpdateTime",
"U_LB_NumeroFactura",
"U_LB_NumeroAutorizac",
"U_LB_FechaLimiteEmis",
"U_LB_CodigoControl",
"U_LB_EstadoFactura",
"U_LB_RazonSocial",
"U_LB_TipoFactura",
"ReqName" AS "User",
"DocStatus" AS "Status",
"DocTime",
"ImportEnt" AS "NumPedido",
"isIns" AS "ReserveInvoice",
"SlpCode" AS "SalesPersonCode",
"PaidSys",
"DocTotal",
ROUND ("PaidToDate", 2) AS "PaidToDate",
ROUND (("DocTotal"-"PaidToDate"), 2)AS "Saldo"  FROM "ORIN";

Create VIEW "EXX_XM_NotasCreditoDetalleSap" as
SELECT 
O."DocNum",
R."DocEntry",
R."LineNum",
R."ItemCode",
R."Dscription" AS "ItemDescription",
R."PriceAfVAT" AS "PriceAfterVAT",
R."Currency",
R."Rate",
R."TotInclTax" AS "TaxTotal",
R."PriceBefDi" AS "UnitPrice",
R."Quantity",
R."Price",
R."LineTotal",
R."OpenQty",
(R."Quantity"-R."OpenQty") AS "Entregado"
--R."CANCELED"
 FROM "RIN1" R JOIN "ORIN" O ON O."DocEntry" = R."DocEntry";

 Create VIEW "EXX_XM_ProductosSap" as 
select  
"ItemCode",
 "ItemName",
 "ItmsGrpCod",
 "FrgnName",
 "CstGrpCode",
 "CodeBars",
 "PrchseItem",
 "SellItem",
 "InvntItem",
 "UserText",
 "OnHand",
 "IsCommited",
 "OnOrder",
 "ManSerNum",
 "ManBtchNum",
 "SalUnitMsr",
 "SLength1",
 "SWidth1",
 "BHeight1",
 "SVolume",
 "BuyUnitMsr",
 "DfltWH",
 "ByWh",
 "EnAstSeri",
 "Series",
 "UgpEntry",
 "SUoMEntry",
 "FirmCode",
 "NoDiscount"  from "OITM" order by "ItemCode";


 Create VIEW "EXX_XM_ProductosAlmacenesSap" as 
 select "ItemCode", "WhsCode", "OnHand", "IsCommited", "Locked", "OnOrder" 
 from "OITW" order by "ItemCode";

   Create VIEW "EXX_XM_ProductosPreciosSap" as 
 select "ItemCode", "PriceList", "UomEntry", "Price", "Currency"  
 from "PRUEBAS_2020_EB"."ITM1" order by "ItemCode";

*/
?>