<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use backend\models\Apple;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Apples';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apple-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Сгенерировать яблоки', ['generate'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'remain',
            [
                'attribute' => 'state',
                'value' => function($model) {
                    return $model->getTextState();
                },
            ],
            'created_at',
            'fell_at',
            'color',
            [
                'class' => ActionColumn::className(),
//                'urlCreator' => function ($action, Apple $model, $key, $index, $column) {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                 },
                'template' => '{eat} {fall}',
                'buttons'=>[
                    'eat'=>function ($url, $model) {
                        return
                            '<div>'.
                            Html::textInput('eat', '', ['class' => 'form-control']).' '.
                            Html::a('Съесть', ['apple/eat', 'id' => $model->id], ['class' => 'btn btn-default btn-xs eat-button form-control']).
                            '</div>';
                    },
                    'fall'=>function ($url, $model) {
                        if($model->state == Apple::STATE_ON_THE_GROUND) {
                            return '';
                        }
                        return Html::a('Упасть', ['apple/fall-to-ground', 'id' => $model->id], ['class' => 'btn btn-default btn-xs fall-button form-control']);
                    },
                ],
            ],
        ],
    ]); ?>


</div>

<?php
$js = <<<JS
$('a.eat-button').click(function(e){
    e.preventDefault();
    var val = $(this).prev('input').val();
    var url = $(this).prop('href');
    window.location.href = url + '&percent=' + val;
});
JS;
$this->registerJs($js);
?>
