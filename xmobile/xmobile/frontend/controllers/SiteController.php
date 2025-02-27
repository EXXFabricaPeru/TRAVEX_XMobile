<?php

namespace frontend\controllers;

use Yii;
use Carbon\Carbon;
use yii\web\Response;
use yii\db\Connection;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\SqlForm;
use yii\filters\AccessControl;
use backend\models\Servislayer;
use backend\models\Monedassistema;
use frontend\models\SignupForm;
/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login','logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function actionSignup(){
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    } 
	 
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function writedb() {
        $dir = \Yii::getAlias('@backend') . '/config/database.inc';
        $file = fopen($dir, "r") or exit("Unable to open file!");
        $resp = "";
        while (!feof($file))
            $resp = fgets($file);
        fclose($file);
        $x = explode("#", $resp);
        return $x[0];
    }

    public function actionVerifica() {
        $data = Yii::$app->request->post();
        if ($data['dbdata'] == "") {
            return "Nombre de la base de datos no valido.";
        }
        if ($data['usuario'] == "") {
            return "Usuario no valido.";
        }
        $dir = \Yii::getAlias('@backend') . '/config/database.inc';
        $file = fopen($dir, "w");
        fwrite($file, $data['dbdata'] . "#" . $data['usuario'] . "#" . $data['password']);
        fclose($file);
        $this->creatadb($data);
        $dirSql = \Yii::getAlias('@backend') . '/config/xm.sql';
        $dataBase = file_get_contents($dirSql);
        $dirSql = \Yii::getAlias('@backend') . '/config/xmSP.sql';
        $sp = file_get_contents($dirSql);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            set_time_limit(0);
            Yii::$app->db->createCommand($dataBase)->execute();
            Yii::$app->db->createCommand($sp)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function creatadb($data) {
        $db = new Connection([
            'dsn' => "mysql:host=localhost;dbname=mysql",
            'username' => $data["usuario"],
            'password' => $data["password"],
            'charset' => 'utf8',
        ]);
        $sql = "create database {$data["dbdata"]}";
        return $db->createCommand($sql)->execute();
    }

    public function dataControl($name) {
        $resul = false;
        $sql = 'select * from mysql.user;';
        $query = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($query as $key => $val) {
            if ($name == $val['User']) {
                $resul = true;
            }
        }
        return $resul;
    }

    public function actionIndex() {
        $dir = Yii::getAlias('@webroot') . '/xp/ini.inc';
        $file = fopen($dir, "r") or exit("Unable to open file!");
        $estado = "";
        while (!feof($file))
            $estado = fgets($file);
        fclose($file);
        if ($estado != 'false') {
            return $this->render('index');
        } else {
            return $this->render('install');
        }
    }

    public function actionAppsap() {
        $data = Yii::$app->request->post();
        $dir = Yii::getAlias('@backend') . '/config/dataparam.inc';
        $file = fopen($dir, "w");
        fwrite($file, "" . $data['hanaip'] . "#" . $data['hanauser'] . "#" . $data['password'] . "#" . $data['hanadatabase'] . "");
        fclose($file);

        $sap = new Servislayer();
        $sap->actiondir = 'Currencies?$select=Code,Name,DocumentsCode,InternationalDescription';
        $monedas = $sap->executex();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (isset($monedas->value)) {
            return $monedas->value;
        }
        return [];
    }

    public function actionMonedas()
    {
     $monedasSistema = new Monedassistema();
     $monedasSistema->CurrencyLocal = Yii::$app->request->post('moneda-local');
     $monedasSistema->CurrencySystem = Yii::$app->request->post('moneda-sistema');
     $monedasSistema->CurrecyOther = Yii::$app->request->post('moneda-otro');
     $monedasSistema->Status = 1;
     $monedasSistema->DateUpdate = Carbon::today()->format("Y-m-d");
     $monedasSistema->save();
     $dirx = Yii::getAlias('@webroot') . '/xp/ini.inc';
        $filex = fopen($dirx, "w");
        fwrite($filex, "true");
        fclose($filex);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return true;
    }
}
