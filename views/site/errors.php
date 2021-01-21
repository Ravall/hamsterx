<?php

/* @var $this yii\web\View */

$this->title = 'Errors';
/**
 * @var array $errors
 */
?>

<div class="container">

    <div class="jumbotron">
        <h1>Ошибки</h1>
    </div>

    <div class="body-content">
        <?php foreach ($errors as $key => $key_errors): ?>
            <?php foreach ($key_errors as $error): ?>
            <div class="alert alert-danger" role="alert">
                <h3><?=$key?></h3> <?=$error?>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

</div>
