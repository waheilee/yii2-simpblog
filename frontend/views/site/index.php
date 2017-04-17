<?php

/* @var $this yii\web\View */
 use frontend\widgets\banner\BannerWidget;
 use yii\base\Widget;
 use frontend\widgets\chat\ChatWidget;
 use frontend\widgets\post\PostWidget;
 use frontend\widgets\hot\HotWidget;
$this->title = '博客-首页';
?>
<div class="row">
    <div class="col-lg-9">
        <!--图片轮播组件-->
        <?= BannerWidget::widget()?>
        <!--文章列表-->
        <?= PostWidget::widget()?>

    </div>

    <div class="col-lg-3">
        <!--留言板组件-->
        <?=ChatWidget::widget() ?>
        <!--热门浏览-->
        <?=HotWidget::widget() ?>


    </div>

</div>

