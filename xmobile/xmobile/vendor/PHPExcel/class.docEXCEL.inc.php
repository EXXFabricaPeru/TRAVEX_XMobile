<?php 

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$GLOBALS['NOMBRE_ARCHIVO'].'_'.$GLOBALS['PIE_USUARIO'].'_'.date('d-m-Y_His').'.xlsx"');
	header('Cache-Control: max-age=0');

	require_once ('PHPExcel.php');
	$objPHPExcel = new \PHPExcel();
	
	// $GLOBALS['TITULO_REPORTE'] ACA EL TITULO;
	ini_set('max_execution_time', 9000);
	ini_set('memory_limit',"20000M");

	//Informacion del excel
	   $objPHPExcel->
         getProperties()
        ->setCreator("COMPANEXTEST")
        ->setTitle($GLOBALS['TITULO_REPORTE'])
        ->setSubject("Reporte")
        ->setDescription("Documento generado por la Companex")
        ->setKeywords("Companex  reportes")
        ->setCategory("Informes");    
 
  
	/* $objDrawing = new \PHPExcel_Worksheet_Drawing();
	 $objDrawing->setName('PHPExcel logo');
	 $objDrawing->setDescription('PHPExcel logo');
	 $objDrawing->setPath("../../Images/logo.png");
	 $objDrawing->setHeight(80);                 // sets the image height to 36px (overriding the actual image height); 
	 $objDrawing->setCoordinates('A1');    // pins the top-left corner of the image to cell D24
	 $objDrawing->setOffsetX(0);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
	 $objDrawing->setWorksheet($objPHPExcel->getActiveSheet(0));
	 */
	 // titulo de la hoja
	 $styleArray = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '2a2661'),
							'size'  => 10,
							'name'  => 'Verdana',
							'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE
    					)						
					);
	
	$objPHPExcel->getActiveSheet()->getCell('A6')->setValue($GLOBALS['TITULO_REPORTE']);
	$objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($styleArray); 
	
	// DESCRIPCION DE LA BASE DE DATOS
	
	 $styleArray = array(
    	'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '2a2661'),
        'size'  => 8,
        'name'  => 'Verdana'
    ));
	
	$styleFondoCell= array(
    	 'type' => PHPExcel_Style_Fill::FILL_SOLID,
     	 'startcolor' => array(
          'rgb' => 'D4D2D2'
    ));
	
	
	$objPHPExcel->getActiveSheet()->getCell('A1')->setValue("Fuente: COMPANEXTEST");
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray); 
	
	$objPHPExcel->getActiveSheet()->getCell('A2')->setValue("Impreso en fecha: ".date("d/m/Y H:i:s"));
	$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);	
	/*switch($_SESSION['DEPARTAMENTO'])
	{
		case 'BN': $oficina="BENI"; break;
		case 'CB': $oficina="COCHABAMBA"; break;
		case 'CH': $oficina="SUCRE"; break;
		case 'LP': $oficina="LA PAZ"; break;
		case 'OR': $oficina="ORURO"; break;
		case 'PD': $oficina="PANDO"; break;
		case 'PT': $oficina="POTOSI"; break;
		case 'SC': $oficina="SANTA CRUZ"; break;
		case 'TJ': $oficina="TARIJA"; break;
	}*/
	//$objPHPExcel->getActiveSheet()->getCell('A3')->setValue("Territorio: ".$oficina);
	//$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray); 
	
	$objPHPExcel->getActiveSheet()->getCell('A4')->setValue("Usuario: ".$GLOBALS['PIE_USUARIO']);
	$objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray); 
	
	 $styleArray = array(
						'borders' => array(
							'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
						)						
					);

	$objPHPExcel->getActiveSheet()->getStyle('A5:B5')->applyFromArray($styleArray);

	 $EstiloCabeceraCampos = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 8,
							'name'  => 'Arial'
						),
						'borders' => array(
							'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
							'bottom'=> array(
									'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
						)						
					);	

	 
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->getColor ()-> setRGB ('000000');
		
	$GLOBALS['FILA_INICIAL'] = 8;

?>