<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CatsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分类标签';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cats-model-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Cats Model', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cat_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>