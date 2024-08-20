<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<style type="text/css">
    .a1{
        /*width:500px;*/
        /*height:200px;*/
        position: fixed;
        font-size: 50px;

        left: 50%;
        top: 30%;
        transform: translate( -50%, -10%);
    }
    .a2{
        /*width:500px;*/
        /*height:200px;*/
        position: fixed;
        font-size: 40px;

        left: 70%;
        top: 80%;
        transform: translate( -50%, -50%);
    }
    .a3{
        /*width:500px;*/
        /*height:200px;*/
        position: fixed;
        font-size: 40px;

        left: 30%;
        top: 80%;
        transform: translate( -50%, -50%);
    }
</style>

<div class="a1">
    <p>Для Вас нет изделий,</p>
    <p>имеющих статус "в работе", </p>
    <p>"на складе" или "в очереди".</p>
</div>
<div class="a3">
    <input type="button" value="Выполнить повторный запрос"
           onclick="window.location='https://test.mysmartautomation.ru/master/start'">
<!--    <button>Выполнить повторный запрос</button>-->
</div>
<div class="a2">
    <input type="button" value="Завершить рабочий день"
           onclick="window.location='https://test.mysmartautomation.ru/master/end-working-day?master_id=<?php echo $master_id; ?>'">
<!--    <button>Завершить рабочий день</button>-->
</div>

