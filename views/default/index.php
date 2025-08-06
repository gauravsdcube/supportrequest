<?php

use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\requestSupport\models\SupportRequest;
use humhub\modules\requestSupport\permissions\CreateSupportRequest;
use humhub\modules\requestSupport\permissions\ManageSupportRequests;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $requests SupportRequest[] */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h4><?= Yii::t('RequestSupportModule.base', 'Support Requests') ?></h4>
        </div>
        <?php if ($contentContainer->can(new \humhub\modules\requestSupport\permissions\ManageCategories())): ?>
            <div class="panel-heading-controls">
                <?= Html::a(
                    '<i class="fa fa-cog"></i> ' . Yii::t('RequestSupportModule.base', 'Manage Categories'),
                    Url::to(['/requestSupport/category/index', 'contentContainer' => $contentContainer]),
                    ['class' => 'btn btn-default btn-sm']
                ) ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="panel-body">
        <?php if ($contentContainer->can(new CreateSupportRequest())): ?>
            <div class="text-right" style="margin-bottom: 20px;">
                <?= Html::a(
                    '<i class="fa fa-plus"></i> ' . Yii::t('RequestSupportModule.base', 'Create Support Request'),
                    Url::to(['/requestSupport/request/create', 'contentContainer' => $contentContainer]),
                    ['class' => 'btn btn-primary']
                ) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($requests)): ?>
            <div class="text-center">
                <p class="text-muted">
                    <?= Yii::t('RequestSupportModule.base', 'No support requests found.') ?>
                </p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('RequestSupportModule.base', 'Subject') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Category') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Status') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Requester') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Created') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td>
                                    <strong><?= Html::encode($request->subject) ?></strong>
                                </td>
                                <td>
                                    <span class="label label-info"><?= Html::encode($request->getCategoryName()) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = 'label-default';
                                    switch ($request->status) {
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
                                        <?= Html::encode(SupportRequest::getStatusOptions()[$request->status]) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= Html::encode($request->requester->displayName) ?>
                                </td>
                                <td>
                                    <?= Yii::$app->formatter->asDatetime($request->created_at) ?>
                                </td>
                                <td>
                                    <?= Html::a(
                                        '<i class="fa fa-eye"></i>',
                                        Url::to(['/requestSupport/request/view', 'id' => $request->id, 'contentContainer' => $contentContainer]),
                                        ['class' => 'btn btn-xs btn-default', 'title' => Yii::t('RequestSupportModule.base', 'View')]
                                    ) ?>
                                    
                                    <?php if ($request->canManage()): ?>
                                        <?= Html::a(
                                            '<i class="fa fa-edit"></i>',
                                            Url::to(['/requestSupport/request/update', 'id' => $request->id, 'contentContainer' => $contentContainer]),
                                            ['class' => 'btn btn-xs btn-primary', 'title' => Yii::t('RequestSupportModule.base', 'Edit')]
                                        ) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div> 