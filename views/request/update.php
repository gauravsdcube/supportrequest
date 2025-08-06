<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use humhub\modules\requestSupport\models\SupportRequest;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $model \humhub\modules\requestSupport\models\SupportRequest */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h4><?= Yii::t('RequestSupportModule.base', 'Edit Support Request') ?></h4>
        </div>
    </div>
    
    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'support-request-update-form']); ?>

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'status')->dropDownList(SupportRequest::getStatusOptions()) ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <div class="form-group">
            <?= Html::submitButton(
                '<i class="fa fa-save"></i> ' . Yii::t('RequestSupportModule.base', 'Update Request'),
                ['class' => 'btn btn-primary']
            ) ?>
            
            <?= Html::a(
                '<i class="fa fa-times"></i> ' . Yii::t('RequestSupportModule.base', 'Cancel'),
                ['/requestSupport/request/view', 'id' => $model->id, 'contentContainer' => $contentContainer],
                ['class' => 'btn btn-default']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div> 