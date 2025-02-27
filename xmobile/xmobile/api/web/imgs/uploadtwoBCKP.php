<?php 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
if(isset($_FILES['file']['name']) ){
	//$return = Array('ok'=>TRUE);
	$upload_folder = 'cli';
	
	$nombre_archivo = $_FILES['file']['name'];
	//$ext=explode(".",$nombre_archivo);

	$tipo_archivo = $_FILES['file']['type'];
	$tamano_archivo = $_FILES['file']['size'];
	$tmp_archivo = $_FILES['file']['tmp_name'];
	$archivador = $upload_folder . '/' . $nombre_archivo;
	if (!move_uploaded_file($tmp_archivo, $archivador)) {
		//$return = Array('ok' => FALSE, 'msg' => "Ocurrio un error al subir el archivo. ".$nombre_archivo." No pudo guardarse.", 'status' => 'error');
		echo 'no se guardo';
	}else{
		echo 'se guardo';
	}
}else{
	echo 'No se envio imagen';
}

 ?>