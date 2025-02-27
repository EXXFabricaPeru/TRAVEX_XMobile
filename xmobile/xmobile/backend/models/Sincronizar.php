<?php

namespace backend\models;

use Cerbero\JsonObjects\JsonObjects;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use GuzzleHttp\Client;

class Sincronizar extends Model {


    public $actiondir;
    public $id;
    private  $cliente;
    private $conf;

    public function __construct()
    {
      $this->conf = new Configlayer();
      $this->cliente = new Client([
        'base_uri' =>"http://localhost:1433/xmobile/xsinc/",
        'timeout' => 3000,
        'verify' => false,
        'cookies' => true
      ]);
    }

    
    public function executex_sn($data){
        $url="http://localhost:1433/xmobile/xsinc/modelo.php";
        $data_url = http_build_query ($data);
        $data_len = strlen ($data_url);
    
        $respuesta=array ('content'=>file_get_contents ($url, false, stream_context_create (array ('http'=>array ('method'=>'POST'
                , 'header'=>"Connection: close\r\nContent-Length: $data_len\r\n"
                , 'content'=>$data_url
                ))))
            , 'headers'=>$http_response_header
            );
      return $respuesta["content"];
    }

     /* Implementación de método para limpiar caracteres especiales al comienzo y al final */
     public function emptyCharacters($cadena)
     {   if($cadena[0]!='['):
             $pos=strpos($cadena,'[',0);                                // Si la cadena en la posición 0 es diferente
             $cadena=substr($cadena,$pos);                              // Devuleve la posicón del caracter '[' desde el inicio.
         endif;
 
         if(substr($cadena,-1,1)!=']'):
             $pos=strrpos($cadena,']',0)+1;
             //$pos=strrpos($respuesta,']',0) ? strrpos($respuesta,']',0)+1 : strlen($cadena)-1);     // Si la cadena en la posición 0 es diferente
             $cadena=substr($cadena,0,strrpos($cadena,']',0)+1);                              // Devuleve la posicón del caracter '[' desde el inicio.
         endif;
         return $cadena;
     }
    
    public function executex($data){
      
        $ch = curl_init(); 
        // definimos la URL a la que hacemos la petición
        curl_setopt($ch, CURLOPT_URL,"http://localhost:1433/xmobile/xsinc/modelo.php");
        // indicamos el tipo de petición: POST
        curl_setopt($ch, CURLOPT_POST, TRUE);
        // definimos cada uno de los parámetros
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);        
        // recibimos la respuesta y la guardamos en una variable
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $remote_server_output = curl_exec ($ch);        
        // cerramos la sesión cURL
        curl_close ($ch);        
        // hacemos lo que queramos con los datos recibidos
        // por ejemplo, los mostramos
        $remote_server_output=$this->emptyCharacters($remote_server_output);
        return($remote_server_output);

    }
}
