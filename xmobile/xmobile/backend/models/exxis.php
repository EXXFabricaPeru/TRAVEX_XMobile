<?php
include("dbo.php");
class conexion{
    private $conectar;
    private $motor;
    public $db;
    function conexion($xmotor){
        $this->motor=$xmotor;
		$this->conectarbd();

		//$this->seleccionar_bd();
        }
    private function conectarbd(){       
            if($this->motor=="hana") {

                $username = "SYSTEM";
                $password = "Passw0rd";
                
                $dsn = 'odbc:driver={HDBODBC32};SERVERNODE=192.168.50.71:30015;DATABASE=SBO_INKAFERRO_PROD;UID=SYSTEM;PWD=3XX1s19.;charset=utf8mb4';
                $this->db = new dbo(
                    $dsn,
                    $username,
                    $password
                );

                 $this->db->query(' SET SCHEMA "SBO_INKAFERRO_PROD";');
                 

            }
            else if ($this->motor=="sql") {
                
            }
    }   
    public function devolver_datos_tabla($tabla,$campos,$condicion,$debug=0){ 
        $salida=[];

        $consulta='SELECT '.$campos.' from "'.$tabla.'" '.$condicion;
        
        //echo $consulta;
        $resultado=$this->db->getAll($consulta);       
        if($debug==1){
            echo $consulta;
             //var_dump($resultado);       
         }       

        if($resultado){ 
            if (empty($resultado)){
                $resultado=0;
            }         
            $salida=array("estado"=>200,"consulta"=>$consulta,"resultado"=>$resultado,"json"=>$this->utf8_converter($resultado));
            
        }else{
             $salida= array("json"=>json_encode(array("estado"=>400)));
        }

        return $salida;
   
    }
    public function devolver_datos_tabla2($tabla,$campos,$condicion,$debug=0){ 
        $salida=[];

        $consulta='SELECT '.$campos.' from "'.$tabla.'" '.$condicion;
        if($debug==1){
            echo $consulta;
             //var_dump($resultado);       
         } 
        //echo $consulta;
        $resultado=$this->db->getAll($consulta);       
             

        if($resultado){ 
            if (empty($resultado)){
                $resultado=0;
            }         
            $salida=array("estado"=>200,"consulta"=>$consulta,"resultado"=>$resultado,"json"=>$this->utf8_converter($resultado));
            
        }else{
             $salida= array("estado"=>400);
        }

        return $salida;
   
    }
    public function insertar_datos_tabla($tabla,$campos,$valores,$debug=0){ 
         $salida=[];

        $consulta='INSERT  "'.$tabla.'" ('.$campos.')  '.$valores ;
         if($debug==1){
            echo $consulta;
             //var_dump($resultado);       
         }
        $resultado=$this->db->query($consulta);
       
       
        $salida= array("json"=>json_encode(array("estado"=>200)));
        return $salida;
   
    }

    public function actualizar_datos_tabla($tabla,$campo,$valor,$condicion,$debug=0){ 
         $salida=[];

        $consulta='UPDATE "'.$tabla.'" SET "'.$campo.'" = '.$valor.' '.$condicion;
         if($debug==1){
            echo $consulta;
             //var_dump($resultado);       
         }
        $resultado=$this->db->query($consulta);
       
       
        $salida= array("json"=>json_encode(array("estado"=>200)));
        return $salida;
   
    }
    
    public function utf8_converter($array){
      array_walk_recursive($array, function(&$item){
          $item = utf8_encode( $item ); 
      });
      return json_encode( $array );
    }
    public function idLastCode($table) {
        $db = $this->db;
        $sql = 'SELECT right( \'00000000\' || CAST(IFNULL(max(CAST("Code" AS int)), 0) AS varchar(8)), 8) "Code" FROM "'.$table.'";';
        $stmt = $db->prepare($sql);
        $stmt -> execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($result["Code"])?$result["Code"]:"";
    }
}

?>