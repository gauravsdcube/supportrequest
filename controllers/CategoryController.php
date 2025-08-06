<?php

namespace humhub\modules\requestSupport\controllers;

use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\requestSupport\models\SupportCategory;
use humhub\modules\requestSupport\permissions\ManageCategories;

class CategoryController extends ContentContainerController
{
    public function actionIndex()
    {
        if (!$this->contentContainer->can(new ManageCategories())) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to manage categories.');
        }

        $categories = SupportCategory::getCategoriesForSpace($this->contentContainer->id);

        return $this->render('index', [
            'categories' => $categories,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionCreate()
    {
        if (!$this->contentContainer->can(new ManageCategories())) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to manage categories.');
        }

        $model = new SupportCategory();
        $model->space_id = $this->contentContainer->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->success(Yii::t('RequestSupportModule.base', 'Category created successfully.'));
            return $this->redirect(['category/index', 'contentContainer' => $this->contentContainer]);
        }

        return $this->render('create', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionUpdate($id)
    {
        if (!$this->contentContainer->can(new ManageCategories())) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to manage categories.');
        }

        $model = SupportCategory::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Category not found.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->success(Yii::t('RequestSupportModule.base', 'Category updated successfully.'));
            return $this->redirect(['category/index', 'contentContainer' => $this->contentContainer]);
        }

        return $this->render('update', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionDelete($id)
    {
        if (!$this->contentContainer->can(new ManageCategories())) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to manage categories.');
        }

        $model = SupportCategory::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Category not found.');
        }

        if ($model->delete()) {
            $this->view->success(Yii::t('RequestSupportModule.base', 'Category deleted successfully.'));
        } else {
            $this->view->error(Yii::t('RequestSupportModule.base', 'Failed to delete category.'));
        }

        return $this->redirect(['category/index', 'contentContainer' => $this->contentContainer]);
    }
} 