<?php
//include("dbo.php");

class conexion{
    private $conectar;
    private $motor;
    public $db;
    private $base;
    private $dbusr;
    private $dbpswd;
    private $dbsrv;

    function conexion($xmotor,$srv,$base,$usr,$pswd){
        $this->motor=$xmotor;
        $this->base=$base;
        $this->dbusr=$usr;
        $this->dbpswd=$pswd;
        $this->dbsrv=$srv;
		$this->conectarbd();

		//$this->seleccionar_bd();
        }
    private function conectarbd(){       
            if($this->motor=="hana") {

                $username =  $this->dbusr;
                $password =  $this->dbpswd;
                
                //$dsn = 'odbc:driver={HDBODBC32};SERVERNODE=192.168.50.72:30015;DATABASE=SBO_PRU;UID=SYSTEM;PWD=Exxis2019;charset=utf8mb4';
                //$dsn = 'odbc:driver={HDBODBC32};SERVERNODE='.$this->dbsrv.':30015;DATABASE='.$this->base.';UID='.$username.';PWD='.$password.';charset=utf8mb4';
                $dsn = 'odbc:driver={HDBODBC32};SERVERNODE='.$this->dbsrv.':30015;DATABASE='.$this->base.';UID='.$username.';PWD='.$password.';charset=utf8mb4';
                $this->db = new dbo(
                    $dsn,
                    $username,
                    $password
                );

                 //$this->db->query(' SET SCHEMA "SBO_PRU";');
                 $this->db->query('SET SCHEMA "SBOCAR";');

            }
            else if ($this->motor=="sql") {
                
            }
    }   
    public function devolver_datos_tabla($tabla,$campos,$condicion,$debug=0){ 
        $salida=[];

        $consulta='SELECT '.$campos.' from "'.$tabla.'" '.$condicion;
        
        
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
//include("libs/gz/Client.php");

include('libs/httpful.phar');
class servicelayer{
    
    private $sl_url;
    private $sl_dtb;
    private $sl_usr;
    private $sl_pswd;
    private $cliente;
    private $dtc;
    private $sid;
    private $nod;

    function servicelayer($sldir,$slusr,$sldtb,$slpswd){
        $this->sl_url=$sldir;
        $this->sl_dtb=$sldtb;
        $this->sl_usr=$slusr;
        $this->sl_pswd=$slpswd;        
        $this->dtc=["CompanyDB"=>$this->sl_dtb,"Password"=> $this->sl_pswd,"UserName"=> $this->sl_usr];
        $this->dtc=json_encode($this->dtc);                
            
    }
    public function login(){            
            $response = \Httpful\Request::post($this->sl_url.'Login')
            ->body($this->dtc)            
            ->send();
           
                $aux=$response->raw_headers;
                $aux_r=explode( "ROUTEID=", $aux);
                $aux_r=$aux_r[1];        
                $aux_r=explode( ";", $aux_r);
                $aux_r=$aux_r[0]; 
                
            $this->sid=$response->body->SessionId; 
            $this->nod= $aux_r;            
            //raw_headers          
    }
    public function Empleados(){
        $this->login();
        if($this->sid!=""){
            
            $response = \Httpful\Request::get($this->sl_url.'EmployeesInfo')
            ->addOnCurlOption( CURLOPT_HTTPHEADER,array("Cookie:B1SESSION=".$this->sid."; ROUTEID=".$this->nod.";") )
            ->send();
            echo json_encode($response->body);
        }
        
    }
    public function RegistrarActividad($json){
        $this->login();
        if($this->sid!=""){
            $response = \Httpful\Request::post($this->sl_url.'Activities')
            ->addOnCurlOption( CURLOPT_HTTPHEADER,array("Cookie:B1SESSION=".$this->sid."; ROUTEID=".$this->nod.";") )
            ->body($json)            
            ->send();
            return json_encode($response->body);
        }

    }
    public function RegistrarAsignacion($json){
        $this->login();
        if($this->sid!=""){
            $response = \Httpful\Request::post($this->sl_url.'EXXASIT')
            ->addOnCurlOption( CURLOPT_HTTPHEADER,array("Cookie:B1SESSION=".$this->sid."; ROUTEID=".$this->nod.";") )
            ->body($json)            
            ->send();
            return array("respuesta"=>json_encode($response->body),"id"=>$response->body->DocEntry);
        }

    }
}
?>