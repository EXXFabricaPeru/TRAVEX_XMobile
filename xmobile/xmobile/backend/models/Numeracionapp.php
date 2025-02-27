<?php

namespace backend\models;

class Numeracionapp extends \yii\db\ActiveRecord {

    public $datanum;
    public $tipo;
    public $cod;
    public $id;

    public function __construct(string $typo, int $id) {
        $this->tipo = $typo;
        $this->id = $id;
        $sql = 'SELECT * FROM numeracion  WHERE iduser = ' . $id;
        $this->datanum = Numeracion::findBySql($sql)->one();
    }

    public function fecha() {
        return date('y') . date('m') . date('d');
    }

    public function genCode($num) {
        $re = substr('00000', 0, -strlen($num));
        return $re . '' . $num;
    }

    public function execute() {
        $num = 0;
        switch ($this->tipo) {
            case('DOP'):
                $num = (int) $this->datanum->numdop;
                break;
            case('DOF'):
                $num = (int) $this->datanum->numdof;
                break;
            case('DFA'):
                $num = (int) $this->datanum->numdfa;
                break;
            case('DOE'):
                $num = (int) $this->datanum->numdoe;
                break;
        }
        $this->cod = $this->tipo . $this->genCode($this->id) . $this->fecha() . $this->genCode(($num + 1));
    }

    public function run() {
        $this->execute();
        return $this->cod;
    }

}
