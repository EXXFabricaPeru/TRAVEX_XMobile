<?php

namespace backend\models;

use Cerbero\JsonObjects\JsonObjects;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use GuzzleHttp\Client;

class Servislayer extends Model {

    public $actiondir;
    public $id;
    private $cliente;
    private $conf;

    public function __construct() {
        $this->conf = new Configlayer();
        $this->cliente = new Client([
            'base_uri' => $this->conf->path,
            'timeout' => 0,
            'verify' => false,
            'cookies' => true
        ]);
    }

    private function loginSL() {
        try {
            $dataLogin = $this->conf->dataHana;
            $response = $this->cliente->request('POST', 'Login',
                    [
                        'json' => $dataLogin
            ]);
            if ($response->getStatusCode() == 200) {
                $session = json_decode($response->getBody()->getContents());
                $cookies = $response->getHeaders();
                if (!isset($session->SessionId)) {
                    Yii::error("FALLO-LOGIN: {$response->getStatusCode()}");
                    throw new Exception("Error en login Service Layer");
                }
                return true;
            } else {
                Yii::error("FALLO-LOGIN: {$response->getStatusCode()}");
            }
        } catch (GuzzleException $e) {
            Yii::error("LOGIN_SAP: {$e->getMessage()}");
            throw new Exception("Error en el servicio login Service Layer");
        }
        throw new Exception("Error general en el servicio de Login Service Layer");
    }

    private function logoutSL() {
        try {
            $response = $this->cliente->request('POST', 'Logout');
            if ($response->getStatusCode() == 204) {
                return true;
            } else {
                Yii::error("FALLO-LOGOUT: {$response->getStatusCode()}");
                throw new Exception("Error en el servicio de Logout Service Layer");
            }
        } catch (GuzzleException $e) {
            Yii::error("LOGOUT_SAP: {$e->getMessage()}");
            throw new Exception("Error en el servicio de Logout Service Layer");
        }
    }

    public function executex($maxPageSize = 0) {
        ini_set('memory_limit', '10G');
        $maxPageSize = $this->countRows($maxPageSize) != 0 ? $this->countRows($maxPageSize) : 20;
        try {
            if ($this->loginSL()) {
                $response = $this->cliente->request('GET', $this->actiondir, [
                    'headers' => ["Prefer" => "odata.maxpagesize={$maxPageSize}"]
                ]);
                if ($response->getStatusCode() == 200) {
                    $result = json_decode($response->getBody()->getContents());

                    Yii::error(json_encode($result));

                    if (!isset($result->value)) {
                        //Yii::error("FALLO-SERVICIO: {$response->getBody()->getContents()}");
                        Yii::error("FALLO-SERVICIO: {$response->getBody()}");
                    }
                    
                    //$this->insertLog(addslashes($this->actiondir),addslashes($parametros),'success');
                    if ($this->logoutSL()) {
                        return $result;
                    }
                } else {
                    $error = json_decode($response->getBody()->getContents());
                    if (isset($error->error->message) && isset($error->error->message->value)) {
                        Yii::error("Service Layer 1: {$error->error->message->value}");
                        $this->insertLog($this->actiondir,$parametros,$error->error->message->value);
                    } else {
                        Yii::error("Service Layer 2: {$response->getBody()->getContents()}");
                        $this->insertLog($this->actiondir,$parametros,$response->getBody()->getContents());
                    }
                }
            }
        } catch (GuzzleException $e) {
            Yii::error("Service Layer 3: {$e->getMessage()}");
            return false;
        } catch (Exception $ex) {
            Yii::error("Servicio Service Layer : {$ex->getMessage()}");
        }
        return false;
    }

    //IBJ
    public function executex2() {
        ini_set('memory_limit', '10G');
        //$maxPageSize = $this->countRows($maxPageSize) != 0 ? $this->countRows($maxPageSize) : 20;
        try {
            if ($this->loginSL()) {
                $response = $this->cliente->request('GET', $this->actiondir, [
                    'headers' => ["Prefer" => "odata.maxpagesize={20}"]
                ]);
                if ($response->getStatusCode() == 200) {
                    $result = json_decode($response->getBody()->getContents());
                    if ($this->logoutSL()) {
                        return $result;
                    }
                } else {
                    $error = json_decode($response->getBody()->getContents());
                    if (isset($error->error->message) && isset($error->error->message->value)) {
                        Yii::error("Service Layer: {$error->error->message->value}");
                        $this->insertLog($this->actiondir,$parametros,$error->error->message->value);
                    } else {
                        Yii::error("Service Layer: {$response->getBody()->getContents()}");
                        $this->insertLog($this->actiondir,$parametros,$response->getBody()->getContents());
                    }
                }
            }
        } catch (GuzzleException $e) {
            Yii::error("Service Layer: {$e->getMessage()}");
            return false;
        } catch (Exception $ex) {
            Yii::error("Servicio Service Layer : {$ex->getMessage()}");
        }
        return false;
    }



    public function executePost($parametros) {
        ini_set('memory_limit', '1024M');

        try {
            if ($this->loginSL()) {
                if (count($parametros)) {
                    $response = $this->cliente->request('POST', $this->actiondir, [
                        'json' => $parametros
                    ]);
                } else {
                    $response = $this->cliente->request('POST', $this->actiondir);
                }
                //$this->insertLog($this->actiondir,$parametros,'succes');
                return $this->responseServiceLayer($response);
            }
        } catch ( \GuzzleHttp\Exception\ClientException $e) {
            //throw new ApiException('Api error: ' . $e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
            $auxerror=$e->getResponse()->getBody()->getContents();
            //Yii::error("Service Layer (Guzzle a0): {$e->getResponse()->getBody()->getContents()}");
            $error = json_decode($auxerror);

            if (isset($error->error)) {
                //$this->insertLog($this->actiondir,$parametros,$e->getMessage()->error->message->value);
                //Yii::error("Service Layer (Guzzle a): {$error->error->message->value}");
                return $error;
            } else {
                //$this->insertLog($this->actiondir,$parametros,$e->getMessage());
                Yii::error("Service Layer (Guzzle b): {$e->getMessage()}");
               return $e->getMessage();
               
            }            
        } catch (Exception $ex) {
            //$this->insertLog($this->actiondir,$parametros,$ex->getMessage());
            Yii::error("Servicio Service Layer : {$ex->getMessage()}");
        }
        return false;
    }

    public function executePatchPut($metodo, $parametros) {
        ini_set('memory_limit', '1024M');
        try {
            if ($this->loginSL()) {
                $response = $this->cliente->request($metodo, $this->actiondir, [
                    'json' => $parametros
                ]);
                $this->insertLog($this->actiondir,$parametros,'succes');
                return $this->responseServiceLayer($response);
            }
        } catch (GuzzleException $e) {
            Yii::error("Service Layer 1 : {$e->getMessage()}");
            $this->insertLog($this->actiondir,$parametros,$e->getMessage());
           // $error = $e->getMessage();
            return false ;
        } catch (Exception $ex) {
            Yii::error("Servicio Service Layer 2: {$ex->getMessage()}");
            $this->insertLog($this->actiondir,$parametros,$ex->getMessage());
        }
        return false;
    }

    public function countRowsok($maxPageSize) {
        ini_set('memory_limit', '1024M');
        $count = 0;
        $aUrl = explode('?', $this->actiondir);
        $items = explode('/', $aUrl[0]);
        $cant = $items[0] . '/' . $items[1] . '?' . $aUrl[1];
        $json = $items[0] . '?' . $aUrl[1];
        try {
            if ($this->loginSL()) {
                $response = $this->cliente->request('GET', $cant);
                $count = json_decode($response->getBody()->getContents());
                return is_null($count) ? $response->getBody()->getContents() : $count;
            }
        } catch (Exception $ex) {
            Yii::error("Servicio Service Layer : {$ex->getMessage()}");
            return $count;
        }
    }

    public function countRows($maxPageSize) {
        ini_set('memory_limit', '1024M');
        $count = 0;
        try {
            $aUrl = explode('?', $this->actiondir);
            if ($this->loginSL()) {
                if ($maxPageSize) {
                    $count = strval($maxPageSize);
                } else {
                    $response = $this->cliente->request('GET', $aUrl[0] . '/$count');
                    if ($response->getStatusCode() == 200) {
                        $count = json_decode($response->getBody()->getContents());
                        $count = is_null($count) ? $response->getBody()->getContents() : $count;
                        $this->logoutSL();
                    } else {
                        Yii::error("Service Layer: {$response->getBody()->getContents()}");
                    }
                }
            }
            return $count;
        } catch (GuzzleException $e) {
            Yii::error("Service Layer (Guzzle): {$e->getMessage()}");
            return $count;
        } catch (Exception $ex) {
            Yii::error("Servicio Service Layer : {$ex->getMessage()}");
        }
        return $count;
    }

    private function responseServiceLayer($response) {
        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents());
            $this->logoutSL();
            return $result;
        } else if ($response->getStatusCode() == 201) {
            $result = json_decode($response->getBody()->getContents());
            if ($this->logoutSL()) {
                return $result;
            }
        } else if ($response->getStatusCode() == 204) {
            if ($this->logoutSL()) {
                return true;
            }
        } else {
            $error = json_decode($response->getBody()->getContents());
            Yii::error("Service Layer: {$error->message->value}");
            return false;
            //return $error->message->value;
        }
        return false;
    }

    private function insertLog($action,$par,$error){
        $error = addslashes($error);
        $parametro = json_encode($par);
        // $sql = "INSERT INTO log_envio(idlog, proceso, envio, respuesta, fecha, ultimo, endpoint) VALUES (DEFAULT,'','".$parametros."','".$error."','".date('Y-m-d')."','','".$action."')";
        $sql = "INSERT INTO log_envio(idlog, proceso, envio, respuesta, fecha, ultimo, endpoint) VALUES (DEFAULT,'','".$parametros."','".$error."','".date('Y-m-d')."','','SAP endPoint')";
        Yii::error("Query insert: ". $sql);
        Yii::$app->db->createCommand($sql)->execute();
    }

}
