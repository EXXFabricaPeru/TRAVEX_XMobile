<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class Configlayer extends Model {

    public $path = '';
    public $dataHana;

    public function __construct() {
        $dir = \Yii::getAlias('@backend') . '/config/dataparam.inc';
        $file = fopen($dir, "r") or exit("Unable to open file!");
        $resp = "";
        while (!feof($file))
            $resp = fgets($file);
        fclose($file);
        $r = explode("#", $resp);
        $this->path = $r[0];
        $this->dataHana = [
            "CompanyDB" => $r[3],
            "Password" => $r[2],
            "UserName" => $r[1]
        ];
    }

}
