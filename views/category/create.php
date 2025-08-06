<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $model \humhub\modules\requestSupport\models\SupportCategory */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h4><?= Yii::t('RequestSupportModule.base', 'Add Support Category') ?></h4>
        </div>
    </div>
    
    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'category-form']); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']) ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'is_active')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton(
                '<i class="fa fa-save"></i> ' . Yii::t('RequestSupportModule.base', 'Save Category'),
                ['class' => 'btn btn-primary']
            ) ?>
            
            <?= Html::a(
                '<i class="fa fa-times"></i> ' . Yii::t('RequestSupportModule.base', 'Cancel'),
                ['/requestSupport/category/index', 'contentContainer' => $contentContainer],
                ['class' => 'btn btn-default']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div> 