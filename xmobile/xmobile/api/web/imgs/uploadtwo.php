<?php 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
if(isset($_FILES['file']['name']) ){
	$upload_folder = 'cli';
	$nombre_archivo = $_FILES['file']['name'];
	$tipo_archivo = $_FILES['file']['type'];
	$tamano_archivo = $_FILES['file']['size'];
	$tmp_archivo = $_FILES['file']['tmp_name'];
	$archivador = $upload_folder . '/' . $nombre_archivo;
	if (!move_uploaded_file($tmp_archivo, $archivador)) {
		echo json_encode(['resp' => false]);
	}else{
		echo json_encode(['resp' => true]);
	}
}else{
	echo json_encode(['resp' => false]);
}

 ?>