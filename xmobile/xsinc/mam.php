<?php
//require_once "config.php"; 
class conexionlocal{
	private $conectar;
	function conexionlocal(){
		$this->conectarbd();
		//$this->seleccionar_bd();
		}
	private function conectarbd(){
		$this->conectar=mysqli_connect("127.0.0.1","root","","cardealer");	
		}
	private function seleccionar_bd(){
		mysqli_select_db("sac_cdlp");
		}
	public function ejecutar_consulta($sql){
		mysqli_set_charset($this->conectar,"utf8");
		$resultado=mysqli_query($this->conectar,$sql);
		//mysqli_close($this->conectar);
		return $resultado;
		}	
	public function campos_consulta($sql){
		$cols=$this->n_cols($sql);
		$salida=array();
		for($i=0;$i<=$cols-1;$i++){
			$campo=$this->nombre_campo($sql,$i);
			$tipo=$this->tipo_campo($sql,$i);
			$salida["campo"][]=$campo;
			$salida["tipo"][]=$tipo;
			}
		return $salida;
		}	
	public function devolver_datos_tabla($tabla,$campos,$condicion,$debug=0){
		$consulta="SELECT ".$campos." from ".$tabla." ".$condicion; 
		if($debug==1){
			echo $consulta."<br/>";
		}
		//
		$xdato=array();
		if($resultado=$this->ejecutar_consulta($consulta)){
		//print_r($resultado);
		$cols=$this->n_cols($resultado);
		$columnas=$this->campos_consulta($resultado);
		$aux = 0;
		while ($row = mysqli_fetch_row($resultado)) {
			for($i=0;$i<=$cols-1;$i++){
				$campo=$this->nombre_campo($resultado,$i);
				$xdato[$aux][$campo]=$row[$i];
			}
			$aux++;
		}
		$fecha=date('Y-m-d H:i:s');
		//session_start();
		//$usuario=$_SESSION["usuario"];
		//$log="INSERT INTO general_log (idusuario,accion,tabla,cadena,fecha) VALUES ('".$usuario."','consulta','".$tabla."','".addslashes($consulta)."','".$fecha."')";
		//$this->ejecutar_consulta($log);
		//mysqli_close($this->conectar);
		return array("resultado"=>$xdato,"campos"=>$columnas,"json"=>json_encode($xdato));
		}
		}
	public function buscar_dato_tabla($tabla,$datobusca,$condicion){
		$consulta="SELECT ".$datobusca." from ".$tabla." ".$condicion; 
		//echo $consulta."<br/>";
		if($resultado=$this->ejecutar_consulta($consulta)){
		//print_r($resultado)	;
		while ($row = mysqli_fetch_row($resultado)) {
			$xdato[]=$row[0];
			}
		$fecha=date('Y-m-d H:i:s');
		session_start();
		$usuario=$_SESSION["usuario"];
		$log="INSERT INTO general_log (idusuario,accion,tabla,cadena,fecha) VALUES ('".$usuario."','consulta','".$tabla."','".addslashes($consulta)."','".$fecha."')";
		//$this->ejecutar_consulta($log);
		return $xdato; 
		}
		}	
	public function insertar_datos_tabla($tabla,$campos,$datos,$dev=0){
		//$datos=strtoupper($datos);

		$cadena="INSERT INTO ".$tabla." (".$campos.") VALUES (".$datos.")";
		if($dev==1)
		echo $cadena."<br/>";
		$this->ejecutar_consulta($cadena);
		$id=mysqli_insert_id($this->conectar);
		$fecha=date('Y-m-d H:i:s');
		session_start();
		$usuario=$_SESSION["usuario"];
		$log="INSERT INTO general_log (idusuario,accion,tabla,cadena,fecha) VALUES ('".$usuario."','registro','".$tabla."','".addslashes($cadena)."','".$fecha."')";
		//$this->ejecutar_consulta($log);
		//mysqli_close($this->conectar);
		return $id;
		
		}
	public function actualizar_datos_tabla($tabla,$campo,$dato,$condicion){
		$cadena="UPDATE $tabla SET $campo='$dato' ".$condicion;
		//echo $cadena."<br/>";
		$this->ejecutar_consulta($cadena);
		$fecha=date('Y-m-d H:i:s');
		session_start();
		$usuario=$_SESSION["usuario"];
		$log="INSERT INTO general_log (idusuario,accion,tabla,cadena,fecha) VALUES ('".$usuario."','edicion','".$tabla."','".addslashes($cadena)."','".$fecha."')";
		//$this->ejecutar_consulta($log);
		//mysqli_close($this->conectar);
		}
	public function eliminar_datos_tabla($tabla,$campo,$dato){
		$cadena="DELETE  FROM  $tabla WHERE $campo='$dato' ";
		//echo $cadena;
		$this->ejecutar_consulta($cadena);
		$fecha=date('Y-m-d H:i:s');
		session_start();
		$usuario=$_SESSION["usuario"];
		$log="INSERT INTO general_log (idusuario,accion,tabla,cadena,fecha) VALUES ('".$usuario."','eliminar','".$tabla."','".addslashes($cadena)."','".$fecha."')";
		$this->ejecutar_consulta($log);
		//mysqli_close($this->conectar);
		}
	public function ejecutar_pa($pa,$datos,$deb=0){
			$consulta="CALL ".$pa."(".$datos.")"; 
			if($deb==1)
			echo $consulta."<br/>";
			//$xdato=array();
			if($resultado=$this->ejecutar_consulta($consulta)){
			//print_r($resultado);
			$cols=$this->n_cols($resultado);
			//$columnas=$this->campos_consulta($resultado);
			$aux = 0;
			while ($row = mysqli_fetch_row($resultado)) {
				for($i=0;$i<=$cols-1;$i++){
					$campo=$this->nombre_campo($resultado,$i);
					$xdato[$aux][$campo]=$row[$i];
				}
				$aux++;
			}
			return array("resultado"=>$xdato,"json"=>json_encode($xdato),"consulta"=>$consulta);
			}
		}
	public function n_filas($sql){
		return mysqli_num_rows($sql);
		}	
	public function n_cols($sql){
		return mysqli_num_fields($sql);
		}
	public function nombre_campo($sql,$i){
		$campo=mysqli_fetch_field_direct($sql,$i);
		return $campo->name;
		}
	public function tipo_campo($sql,$i){
		$campo=mysqli_fetch_field_direct($sql,$i);
		return $campo->type;
		}	
	public function select($tabla,$value,$option,$condicion,$div,$seleccionado=0,$tabindex=1){
		$campos=$value.','.$option;
		$datos=$this->devolver_datos_tabla($tabla,$campos,$condicion);
		$resultado=$datos["resultado"];
		//print_r($resultado);
	
		$contador_registros=count($resultado);
		$salida='<select class=" form-control input-sm" id="'.$div.'" name="'.$div.'" tabindex="'.$tabindex.'"><option value="">Seleccionar</option>';
		for($i=0;$i<=$contador_registros-1;$i++){	
		if($resultado[$i][$value]==$seleccionado){
				$salida.='<option value="'.$resultado[$i][$value].'" selected="selected" >'.$resultado[$i][$option].'</option>';	
			}else{
				$salida.='<option value="'.$resultado[$i][$value].'">'.$resultado[$i][$option].'</option>';	
			}
		}
		$salida.='</select>';
		return $salida;
		}
	public function multi_select($tabla,$value,$option,$condicion,$div,$seleccionado=array()){
		//print_r($seleccionado);
		$campos=$value.','.$option;
		$datos=$this->devolver_datos_tabla($tabla,$campos,$condicion);
		$resultado=$datos["resultado"];
		//print_r($resultado);
		$contador_registros=count($resultado);
		$salida='<select class="form-control" multiple id="'.$div.'" name="'.$div.'[]">';
		for($i=0;$i<=$contador_registros-1;$i++){	
		if( in_array($resultado[$i][$value],$seleccionado)){
				$salida.='<option value="'.$resultado[$i][$value].'" selected="selected" >'.$resultado[$i][$option].'</option>';	
			}else{
				$salida.='<option value="'.$resultado[$i][$value].'">'.$resultado[$i][$option].'</option>';	
			}
		}
		$salida.='</select>';
		return $salida;
		}
	public function toHtml($entra) {
		$traduce = array('á' => '&aacute;', 'é' => '&eacute;', 'í' => '&iacute;', 'ó' => '&oacute;', 'ú' => '&uacute;', 'ñ' => '&ntilde;', 'Á' => '&Aacute;', 'É' => '&Eacute;', 'Í' => '&Iacute;', 'Ó' => '&Oacute;', 'Ú' => '&Uacute;', '°' => '&deg;', 'º' => 'o', 'ü' => 'u', 'Ñ' => '&Ntilde;','ñ'=>'Ã±');
		$sale = strtr($entra, $traduce);
		return $sale;
		}
	public function toAcute($entra) {
		$traduce = array('&aacute;' => 'a', '&eacute;' => 'e', '&iacute;' => 'i', '&oacute;' => 'o', '&uacute;' => 'u', '&ntilde;' => 'ñ', '&Aacute;' => 'A', '&Eacute;' => 'E', '&Iacute;' => 'I', '&Oacute;' => 'O', '&Uacute;' => 'U', '&deg;' => '°', '&Ntilde;' => 'Ñ', '&AACUTE;' => 'A', '&EACUTE;' => 'E', '&IACUTE;' => 'I', '&OACUTE;' => 'O', '&UACUTE;' => 'U', '&NTILDE;' => 'Ñ');
		$sale = strtr($entra, $traduce);
		return $sale;
		}
	public function fecha($entra){
		$fecha=explode("/",$entra);
		$salida=$fecha[2].'/'.$fecha[1].'/'.$fecha[0];
		return $salida;
		}
	public function tofecha1($entra){
			$fecha=explode("/",$entra);
			$salida=$fecha[2].'-'.$fecha[1].'-01';
			return $salida;
			}	
	public function fecha2($entra){
		$fecha=explode("-",$entra);
		$salida=$fecha[2].'/'.$fecha[1].'/'.$fecha[0];
		return $salida;
		}
	public function fecha3($entra){
		$fecha=explode(" ",$entra);
		$hora=$fecha[1];
		$entra=$fecha[0];
		$fecha=explode("-",$entra);
		$salida=$fecha[2].'/'.$fecha[1].'/'.$fecha[0];
		return $salida." ".$hora;
		}
	public function valor($tabla,$campo,$condicion){
		$aux=$this->devolver_datos_tabla($tabla,$campo,$condicion);
		$aux_res=$aux["resultado"];
		$contador=count($aux_res);
		if($contador==0)
		return '';
		else
		return $aux_res[0][$campo];
		}
	public function edad($fecha1,$fecha2){
		$datetime1=new DateTime($fecha1);
		$datetime2=new DateTime($fecha2);
		$interval=$datetime2->diff($datetime1);
		$edad = $interval->format("%y");
		return $edad;
		
		}	
	

}
?>