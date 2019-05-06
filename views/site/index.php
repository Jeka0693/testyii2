<?php

/* @var $this yii\web\View */

use yii\widgets\ActiveForm;

use yii\helpers\Html;

use app\models\Author;
use app\models\Book;
use app\models\Shop;


$this->title = 'Тестовое задание';
?>
<div class="site-index">   

    <div class="body-content">

        <div class="row">
            <?php $form = ActiveForm::begin(); ?>
            <div class="col-lg-4">
    <?= $form->field(new Book, 'name')->dropDownList($books,
        [
            'multiple' => 'true',
            'class'=>'form-control selSel'
        ]
        )->label('Список книг');
    ?>
            </div>
            <div class="col-lg-4">
    <?= $form->field(new Author, 'name')->dropDownList($authors,
        [
            'multiple' => 'true',
            'class'=>'form-control selSel'
        ]
        )->label('Список авторов');
    ?>
            </div>
            <div class="col-lg-4">
    <?= $form->field(new Shop, 'name')->dropDownList($shops,
        [
            'multiple' => 'true',
            'class'=>'form-control selSel'
        ]
        )->label('Список магазинов');
    ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

<div class="row"><div class="counter"></div>
    <div class="vitrina">
    </div>
</div>
    </div>
</div>
