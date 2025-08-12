<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $model \humhub\modules\requestSupport\models\SupportRequest */
/* @var $categories array */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h4><?= Yii::t('RequestSupportModule.base', 'Create Support Request') ?></h4>
        </div>
    </div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'support-request-form']); ?>

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => Yii::t('RequestSupportModule.base', 'Select a category')]) ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 6, 'placeholder' => Yii::t('RequestSupportModule.base', 'Please describe your issue in detail...')]) ?>

        <div class="form-group">
            <?= Html::submitButton(
                '<i class="fa fa-send"></i> ' . Yii::t('RequestSupportModule.base', 'Submit Request'),
                ['class' => 'btn btn-primary']
            ) ?>

            <?= Html::a(
                '<i class="fa fa-times"></i> ' . Yii::t('RequestSupportModule.base', 'Cancel'),
                ['/requestSupport/default/index', 'contentContainer' => $contentContainer],
                ['class' => 'btn btn-default']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
