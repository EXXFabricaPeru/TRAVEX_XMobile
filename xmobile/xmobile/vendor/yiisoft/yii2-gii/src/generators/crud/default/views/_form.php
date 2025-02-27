<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?= "<?php " ?>$form = ActiveForm::begin(['id' => '<?=Inflector::camel2words(StringHelper::basename($generator->modelClass));?>-form']); ?>
    <div class="row">
<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo ' <div class="col-md-6">'; 
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>";
        echo '<span class="text-danger text-clear" id="error-'.$attribute.'"></span>';
        echo '</div>';
    }
} ?>
        </div>


    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
