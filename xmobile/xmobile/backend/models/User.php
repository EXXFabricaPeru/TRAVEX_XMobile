<?php

namespace backend\models;

use yii\db\Query;
use Yii;
use api\traits\Respuestas;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $verification_token
 * @property string $access_token
 * @property int $idPersona
 * @property int $estadoUsuario
 * @property string $fechaUMUsuario
 * @property string $plataformaUsuario
 * @property string $plataformaPlataforma
 * @property string $plataformaEmei
 * @property int $reset
 *
 * @property Cabeceradocumentos[] $cabeceradocumentos
 * @property Usuariopersona $persona
 * @property Usuarioconfiguracion[] $usuarioconfiguracions
 */
class User extends \yii\db\ActiveRecord {

    use Respuestas;

    public $password;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['username', 'auth_key', 'password_hash', 'created_at', 'updated_at', 'access_token', 'idPersona', 'estadoUsuario', 'plataformaUsuario', 'plataformaPlataforma', 'plataformaEmei', 'reset'], 'required'],
            [['status', 'created_at', 'updated_at', 'idPersona', 'estadoUsuario', 'reset'], 'integer'],
            [['access_token'], 'string'],
            [['fechaUMUsuario'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'verification_token', 'plataformaEmei'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['plataformaUsuario'], 'string', 'max' => 1],
            [['plataformaPlataforma'], 'string', 'max' => 50],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['idPersona'], 'exist', 'skipOnError' => true, 'targetClass' => Usuariopersona::className(), 'targetAttribute' => ['idPersona' => 'idPersona']],
        ];
    }

    public function attributeLabels() {
        return [
            'username' => 'Usuario',
            'created_at' => 'Registrado',
            'idPersona' => 'Persona',
        ];
    }

    /*     * *******START LOGIN************ */

    public function login($data) {
        $sql = "SELECT * FROM vi_m_login_a WHERE nombreUsuario = '" . $data['usuarioNombreUsuario'] . "' AND UUID = '" . $data['plataformaEmei'] . "'";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function docificacion($id) {
        $sql = "SELECT * FROM vi_m_login_b WHERE equipoId = " . $id;
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function cuentasContables($id) {
        $sql = "SELECT * FROM equipoxcuentascontables WHERE equipoxId = " . $id;
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function accesos($id) {
        $sql = "SELECT * from vi_m_login_d WHERE iduser = " . $id;
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function config($id) {
        $sql = "SELECT * FROM vi_m_login_ex WHERE idUser = " . $id;
        return Yii::$app->db->createCommand($sql)->queryAll();
    }    
	public function listapreciosx($id) {
        $sql = "SELECT * FROM `vi_m_login_h` WHERE idUser = " . $id;
       $resultado = Yii::$app->db->createCommand($sql)->queryAll();
        
       /*$resultado = Yii::$app->db->createCommand("CALL pa_obtenerListaPrecios(:usuario)")
        ->bindValue(':usuario',$id)        
        ->queryAll();*/
        if (count($resultado) > 0) {
            return $resultado;
        }
        return $this->correcto([], "No se encontro Datos", 201);
        
    } 
	public function camposdinamicosCliente() {
        $sql = "SELECT parametro as name, valor4 as estado, valor3 as label FROM `configuracion` where parametro in('cliente_std1','cliente_std2','cliente_std3','cliente_std4','cliente_std5','cliente_std6','cliente_std7','cliente_std8','cliente_std9','cliente_std10') AND estado = 1";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function almacenes($idSucursal) {
        $sql = "SELECT * FROM vi_m_login_f WHERE id = " . $idSucursal;
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function condicionespago($id) {
        $sqpl=" select valor from configuracion where parametro='condicionPago'";
        $respuesta=Yii::$app->db->createCommand($sqpl)->queryScalar();
        
        if ($respuesta==1){
            $sql = "SELECT * FROM vi_m_login_g  WHERE idUser = " . $id;
            return Yii::$app->db->createCommand($sql)->queryAll();
        }else if($respuesta==0){
            $sql = "SELECT  * from condicionespagos where GroupNumber='-1'";
            return Yii::$app->db->createCommand($sql)->queryAll();
        }else if($respuesta==2){
            $sql = "SELECT  * from condicionespagos";
            return Yii::$app->db->createCommand($sql)->queryAll();
        }
        
    }
    public function condicionespago2() {
        $sql = "SELECT * FROM vi_m_login_g  WHERE GroupNumber = '-1' group by GroupNumber ";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }
    /*     * *******END LOGIN************ */

    public function createEvent($id) {
        $sql = "CREATE EVENT event_$id ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL (60*24) MINUTE DO 
				UPDATE user SET access_token = '' WHERE id = $id;";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function eventExist($eventparam) {
        $sql = "SHOW EVENTS";
        $event = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($event as $key => $val) {
            if ($eventparam == $val["Name"]) {
                $sqlx = "DROP EVENT $eventparam";
                Yii::$app->db->createCommand($sqlx)->execute();
                break;
            }
        }
    }

    public function evenIni($id) {
        $ev = "event_" . $id;
        $this->eventExist($ev);
        $this->createEvent($id);
    }

    public function updateToken($id) {
        $model = User::findOne($id);
        $model->access_token = $this->getUniqueAccessToken();
        if ($model->save()) {
            return $model->access_token;
        }
    }

    private function getUniqueAccessToken() {
        $resultado = bin2hex(Yii::$app->security->generateRandomString() . date('U') . '_' . time());
        $identity = $this->findIdentityByAccessToken($resultado);
        if ($identity) {
            $resultado = $this->getUniqueAccessToken();
        }
        return $resultado;
    }

    public function findIdentityByAccessToken($token) {
        return User::findOne(['access_token' => $token]);
    }

    public function resetPass($id, $pass, $t) {
        $rem = Yii::$app->security->generatePasswordHash($pass);
        $key = Yii::$app->security->generateRandomString();
        $sql = "UPDATE user SET password_hash = '$rem', auth_key = '$key', reset = $t WHERE id = $id";
        return Yii::$app->db->createCommand($sql)->execute();
    }
	
	
    public function resetSolicitud($user) {
        $sql = "UPDATE user SET reset = 1 WHERE username = '{$user}'";
        return Yii::$app->db->createCommand($sql)->execute();
    }
	
	

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCabeceradocumentos() {
        return $this->hasMany(Cabeceradocumentos::className(), ['idUser' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersona() {
        return $this->hasOne(Usuariopersona::className(), ['idPersona' => 'idPersona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioconfiguracion() {
        return $this->hasMany(Usuarioconfiguracion::className(), ['idUser' => 'id']);
    }

    public function findByDocumentoIdentidadPersona($docId) {
        $sql = "select * from `usuariopersona` where `documentoIdentidadPersona` = '$docId'";
        $objectPersona = Yii::$app->db->createCommand($sql)->queryAll();
        return $objectPersona;
    }

    public function paInsertSolicitudRegistro($data) {

        $username = $data["username"];
        $passwordHash = Yii::$app->getSecurity()->generatePasswordHash($data["passwordHash"]);
        $plataformaUsuario = "m";
        $plataformaPlataforma = $data["plataformaPlataforma"];
        $plataformaEmei = $data["plataformaEmei"];
        $idPersona = $data["idPersona"];
        $estadoUsuario = $data["estadoUsuario"];
        $reset = $data["reset"];
        $query = new Query;
        $oUser = $query->select('id')
                ->from('user')
                ->where(array('username' => $username, 'plataformaUsuario' => 'm'))
                ->one();
        if ($oUser) {
            return $this->correcto([], 'El usuario ya existe', 202);
        }
        $sql = "CALL pa_insert_solicitud_registro('$username','$passwordHash','$plataformaUsuario','$plataformaPlataforma','$plataformaEmei','$idPersona','$estadoUsuario','$reset')";
        $data = Yii::$app->db->createCommand($sql)->execute();
        if ($data == 1) {
            return $this->correcto([], 'Registro Correcto', 200);
        }
        return $this->error('Error al Registrar los datos');
    }

}
