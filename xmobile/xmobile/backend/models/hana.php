<?php
namespace backend\models;
use Cerbero\JsonObjects\JsonObjects;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use GuzzleHttp\Client;

class hana extends Model {
    public  $dbsrv;
    public  $base;
    private $username;
    private $password;
    // no se pueden hacer inserts ,updates,deletes a HANA
    public function __construct(){     
        Yii::$app->hana->createCommand("SET SCHEMA \"SBO_TRAVEX_PROD21\"; ")->execute();    
        
    }
    public function ejecutarconsultaAll($sql){
     
        $resultado= Yii::$app->hana->createCommand($sql)->queryAll();
        return $resultado;
    }
    public function ejecutarconsultaOne($sql){
       
        $resultado= Yii::$app->hana->createCommand($sql)->queryOne();
        return $resultado;
    }
    public function ejecutar($sql){
       
        $resultado= Yii::$app->hana->createCommand($sql)->execute();
        return $resultado;
    }
}