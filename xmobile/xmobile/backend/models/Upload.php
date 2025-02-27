<?php

namespace backend\models;

use Yii;
use yii\imagine\Image;
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;

class Upload extends \yii\db\ActiveRecord {

    public function imagen() {
        echo "exxis-miguel";
    }

    public function xs($img) {
        Image::thumbnail(Yii::getAlias('@webroot') . '/upload/normal/' . $img, 240, 180)
                ->save(Yii::getAlias('@webroot') . '/upload/xs/' . $img, ['quality' => 100]);
    }

    public function sm($img) {
        Image::thumbnail(Yii::getAlias('@webroot') . '/upload/normal/' . $img, 150, 150)
                ->save(Yii::getAlias('@webroot') . '/upload/sm/' . $img, ['quality' => 100]);
    }

    public function md($img) {
        Image::thumbnail(Yii::getAlias('@webroot') . '/upload/normal/' . $img, 800, 600)
                ->save(Yii::getAlias('@webroot') . '/upload/md/' . $img, ['quality' => 100]);
    }

    public function lg($img) {
        Image::thumbnail(Yii::getAlias('@webroot') . '/upload/normal/' . $img, 400, 400)
                ->save(Yii::getAlias('@webroot') . '/upload/lg/' . $img, ['quality' => 100]);
    }

    public function xlg($img) {
        Image::thumbnail(Yii::getAlias('@webroot') . '/upload/normal/' . $img, 550, 550)
                ->save(Yii::getAlias('@webroot') . '/upload/xlg/' . $img, ['quality' => 100]);
    }

    public function crop($img) {
        Image::crop(Yii::getAlias('@webroot') . '/upload/normal/' . $img, 350, 350, [0, 0])
                ->save(Yii::getAlias('@webroot') . '/upload/crop/' . $img, ['quality' => 100]);
    }

    /*     * **************** */

    public function corte($img) {
        $resp = Image::thumbnail(Yii::getAlias('@webroot') . '/upload/' . $img, 50, 50)
                ->save(Yii::getAlias('@webroot') . '/upload/trum/' . $img, ['quality' => 50]);
    }

    public function actionEffectos() {
        $image = Image::getImagine();
        $newImage = $image->open(Yii::getAlias(Yii::getAlias('@app') . '/upload/1519158719d.jpg'));
        $newImage->effects()->grayscale();
        //$newImage->effects()->negative();
        //$newImage->effects()->gamma(0.7);
        $newImage->save(Yii::getAlias('@app') . '/upload/trum/' . rand(1, 5000) . '.jpg', ['quality' => 80]);
        echo '<pre>';
        print_r($image);
        echo '</pre>';
    }

    public function actionText() {
        $resp = Image::text(Yii::getAlias('@app') . '/upload/marca.jpg', 'MIGUEL', Yii::getAlias('@app') . '/upload/font/Respective_Slanted.ttf', [50, 50], ['color' => 000])
                ->save(Yii::getAlias('@app') . '/upload/trum/' . rand(1, 5000) . '.jpg', ['quality' => 100]);
        echo '<pre>';
        print_r($resp);
        echo '</pre>';
    }

    public function actionCrop() {
        $resp = Image::crop(Yii::getAlias('@app') . '/upload/marca.jpg', 150, 150, [20, 20])
                ->save(Yii::getAlias('@app') . '/upload/trum/' . rand(1, 5000) . '.jpg', ['quality' => 100]);
        echo '<pre>';
        print_r($resp);
        echo '</pre>';
    }

    public function actionAutorotate() {
        $resp = Image::autorotate(Yii::getAlias('@app') . '/upload/marca.jpg', '3ADF00')
                ->save(Yii::getAlias('@app') . '/upload/trum/' . rand(1, 5000) . '.jpg', ['quality' => 100]);
        echo '<pre>';
        print_r($resp);
        echo '</pre>';
    }

    public function actionFoto() {
        $resp = Image::frame(Yii::getAlias('@app') . '/upload/descarga.jpg', 5, '666', 0)
                ->rotate(-8)
                ->save(Yii::getAlias('@app') . '/upload/trum/' . rand(1, 5000) . '.jpg', ['jpeg_quality' => 100]);
        echo '<pre>';
        print_r($resp);
        echo '</pre>';
    }

    public function actionThum() {
        $resp = Image::thumbnail(Yii::getAlias('@app') . '/upload/descarga.jpg', 50, 50)
                ->save(Yii::getAlias('@app') . '/upload/trum/' . rand(1, 5000) . '.jpg', ['quality' => 50]);
        echo '<pre>';
        print_r($resp);
        echo '</pre>';
    }

    public function actionMarca() {
        $resp = Image::watermark(Yii::getAlias('@webroot') . '/upload/mk/m2.jpg', Yii::getAlias('@webroot') . '/upload/mk/m1.png', [150, 100])
                ->save(Yii::getAlias('@webroot') . '/upload/mk/' . rand(1, 5000) . '.jpg', ['quality' => 50]);
        echo '<pre>';
        print_r($resp);
        echo '</pre>';
    }

}

?>