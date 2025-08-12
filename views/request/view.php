<?php

use humhub\modules\requestSupport\models\SupportRequest;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $model \humhub\modules\requestSupport\models\SupportRequest */
/* @var $response \humhub\modules\requestSupport\models\SupportResponse */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h4><?= Html::encode($model->subject) ?></h4>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-8">
                <h5><?= Yii::t('RequestSupportModule.base', 'Description') ?></h5>
                <div class="well">
                    <?= nl2br(Html::encode($model->description)) ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('RequestSupportModule.base', 'Request Details') ?></h5>
                    </div>
                    <div class="panel-body">
                        <p><strong><?= Yii::t('RequestSupportModule.base', 'Category') ?>:</strong>
                            <span class="label label-info"><?= Html::encode($model->category->name) ?></span>
                        </p>
                        <p><strong><?= Yii::t('RequestSupportModule.base', 'Status') ?>:</strong>
                            <?php
                            $statusClass = 'label-default';
                            switch ($model->status) {
                                case SupportRequest::STATUS_OPEN:
                                    $statusClass = 'label-warning';
                                    break;
                                case SupportRequest::STATUS_IN_PROGRESS:
                                    $statusClass = 'label-info';
                                    break;
                                case SupportRequest::STATUS_RESOLVED:
                                    $statusClass = 'label-success';
                                    break;
                                case SupportRequest::STATUS_CLOSED:
                                    $statusClass = 'label-default';
                                    break;
                            }
                            ?>
                            <span class="label <?= $statusClass ?>">
                                <?= Html::encode(SupportRequest::getStatusOptions()[$model->status]) ?>
                            </span>
                        </p>
                        <p><strong><?= Yii::t('RequestSupportModule.base', 'Requester') ?>:</strong>
                            <?= Html::encode($model->requester->displayName) ?>
                        </p>
                        <p><strong><?= Yii::t('RequestSupportModule.base', 'Created') ?>:</strong>
                            <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                        </p>

                        <?php if ($model->canManage()): ?>
                            <hr>
                            <h6><?= Yii::t('RequestSupportModule.base', 'Manage Request') ?></h6>

                            <!-- Status Change Form -->
                            <div class="form-group">
                                <label><?= Yii::t('RequestSupportModule.base', 'Change Status') ?></label>
                                <?php $statusForm = ActiveForm::begin(['id' => 'status-form', 'method' => 'post']); ?>
                                    <?= Html::dropDownList(
                                        'new_status',
                                        $model->status,
                                        SupportRequest::getStatusOptions(),
                                        [
                                            'class' => 'form-control',
                                            'style' => 'display: inline-block; width: auto; margin-right: 10px;'
                                        ]
                                    ) ?>
                                    <?= Html::submitButton(
                                        '<i class="fa fa-save"></i> ' . Yii::t('RequestSupportModule.base', 'Update Status'),
                                        [
                                            'class' => 'btn btn-warning btn-sm',
                                            'name' => 'update_status',
                                            'value' => '1'
                                        ]
                                    ) ?>
                                <?php ActiveForm::end(); ?>
                            </div>

                            <?= Html::a(
                                '<i class="fa fa-edit"></i> ' . Yii::t('RequestSupportModule.base', 'Edit Request'),
                                Url::to(['/requestSupport/request/update', 'id' => $model->id, 'contentContainer' => $contentContainer]),
                                ['class' => 'btn btn-primary btn-sm btn-block']
                            ) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h5><?= Yii::t('RequestSupportModule.base', 'Responses') ?></h5>

        <?php if (empty($model->responses)): ?>
            <div class="text-center">
                <p class="text-muted">
                    <?= Yii::t('RequestSupportModule.base', 'No responses yet.') ?>
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($model->responses as $responseItem): ?>
                <div class="media">
                    <div class="media-left">
                        <img class="media-object" src="<?= $responseItem->author->getProfileImage()->getUrl() ?>" width="40" height="40">
                    </div>
                    <div class="media-body">
                        <div class="media-heading">
                            <strong><?= Html::encode($responseItem->author->displayName) ?></strong>
                            <small class="text-muted">
                                <?= Yii::$app->formatter->asDatetime($responseItem->created_at) ?>
                            </small>
                        </div>
                        <div class="well well-sm">
                            <?= nl2br(Html::encode($responseItem->message)) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <hr>

        <?php if ($model->canAddResponse()): ?>
            <h5><?= Yii::t('RequestSupportModule.base', 'Add Response') ?></h5>
            <?php $form = ActiveForm::begin(['id' => 'response-form']); ?>

            <?= $form->field($response, 'message')->textarea([
                'rows' => 4,
                'placeholder' => Yii::t('RequestSupportModule.base', 'Type your response here...')
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton(
                    '<i class="fa fa-reply"></i> ' . Yii::t('RequestSupportModule.base', 'Add Response'),
                    ['class' => 'btn btn-success']
                ) ?>

                <?= Html::a(
                    '<i class="fa fa-arrow-left"></i> ' . Yii::t('RequestSupportModule.base', 'Back to List'),
                    ['/requestSupport/default/index', 'contentContainer' => $contentContainer],
                    ['class' => 'btn btn-default']
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <?php if ($model->status === SupportRequest::STATUS_CLOSED): ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <?= Yii::t('RequestSupportModule.base', 'This request is closed. Only administrators and moderators can add responses.') ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
