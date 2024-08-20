<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<style type="text/css">
    .a1{
        /*width:500px;*/
        /*height:200px;*/
        position: fixed;
        font-size: 100px;

        left: 50%;
        top: 30%;
        transform: translate( -50%, -10%);
    }
    .a2{
        /*width:500px;*/
        /*height:200px;*/
        position: fixed;
        font-size: 40px;

        left: 50%;
        top: 80%;
        transform: translate( -50%, -50%);
    }
</style>

<div class="a1">
    <input type="button" value="Продолжить"
           onclick="window.location='https://test.mysmartautomation.ru/master/start?continue=true'">
<!--    <button>Взять изделие в работу</button>-->
</div>
<div class="a2">
    <input type="button" value="Завершить рабочий день"
           onclick="window.location='https://test.mysmartautomation.ru/master/end-working-day?master_id=<?php echo $master_id; ?>'">
<!--    <button>Завершить рабочий день</button>-->
</div>

<script>

</script>


