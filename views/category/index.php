<?php

use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\requestSupport\models\SupportCategory;
use humhub\modules\requestSupport\permissions\ManageCategories;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $categories SupportCategory[] */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h4><?= Yii::t('RequestSupportModule.base', 'Manage Support Categories') ?></h4>
        </div>
    </div>
    
    <div class="panel-body">
        <?php if ($contentContainer->can(new ManageCategories())): ?>
            <div class="text-right" style="margin-bottom: 20px;">
                <?= Html::a(
                    '<i class="fa fa-plus"></i> ' . Yii::t('RequestSupportModule.base', 'Add Category'),
                    Url::to(['/requestSupport/category/create', 'contentContainer' => $contentContainer]),
                    ['class' => 'btn btn-primary']
                ) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($categories)): ?>
            <div class="text-center">
                <p class="text-muted">
                    <?= Yii::t('RequestSupportModule.base', 'No categories found.') ?>
                </p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('RequestSupportModule.base', 'Name') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Description') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Sort Order') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Status') ?></th>
                            <th><?= Yii::t('RequestSupportModule.base', 'Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <strong><?= Html::encode($category->name) ?></strong>
                                </td>
                                <td>
                                    <?= Html::encode($category->description) ?>
                                </td>
                                <td>
                                    <?= $category->sort_order ?>
                                </td>
                                <td>
                                    <?php if ($category->is_active): ?>
                                        <span class="label label-success"><?= Yii::t('RequestSupportModule.base', 'Active') ?></span>
                                    <?php else: ?>
                                        <span class="label label-default"><?= Yii::t('RequestSupportModule.base', 'Inactive') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= Html::a(
                                        '<i class="fa fa-edit"></i>',
                                        Url::to(['/requestSupport/category/update', 'id' => $category->id, 'contentContainer' => $contentContainer]),
                                        ['class' => 'btn btn-xs btn-primary', 'title' => Yii::t('RequestSupportModule.base', 'Edit')]
                                    ) ?>
                                    
                                    <?= Html::a(
                                        '<i class="fa fa-trash"></i>',
                                        Url::to(['/requestSupport/category/delete', 'id' => $category->id, 'contentContainer' => $contentContainer]),
                                        [
                                            'class' => 'btn btn-xs btn-danger', 
                                            'title' => Yii::t('RequestSupportModule.base', 'Delete'),
                                            'data-confirm' => Yii::t('RequestSupportModule.base', 'Are you sure you want to delete this category?')
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="text-left" style="margin-top: 20px;">
            <?= Html::a(
                '<i class="fa fa-arrow-left"></i> ' . Yii::t('RequestSupportModule.base', 'Back to Support Requests'),
                Url::to(['/requestSupport/default/index', 'contentContainer' => $contentContainer]),
                ['class' => 'btn btn-default']
            ) ?>
        </div>
    </div>
</div> 