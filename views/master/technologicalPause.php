<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<style type="text/css">
    .con{
        position: relative;
    }
    .t1{
        position: absolute;
        margin-top: 25%;
        left: 40%;
        width:80%;
        transform: translate( -50%, -50%);
        font-size: 50px;
        text-align: center;
    }
    .k{
        position: absolute;
        height:100%;
        width:100%;
    }
    .k1{
        position: absolute;
        font-size: 40px;
        width:150px;
        left: 90%;
        margin-top: 10%;
        transform: translate( -50%, -50%);
    }
    .k3{
        position: absolute;
        font-size: 40px;
        width:150px;
        left: 90%;
        margin-top: 30%;
        transform: translate( -50%,  -50%);
    }
    .k5{
        position: absolute;
        font-size: 40px;
        width:150px;
        left: 90%;
        margin-top: 40%;
        transform: translate( -50%,  -50%);
    }
    .k6{
        position: absolute;
        font-size: 40px;
        width:150px;
        left: 90%;
        margin-top: 50%;
        transform: translate( -50%,  -50%);
    }

    .a2{
        position: fixed;
        font-size: 40px;

        left: 50%;
        top: 80%;
        transform: translate( -50%, -50%);
    }

</style>
<div class="con">
    <div class="t1">
        <p><?php echo $product_name; ?></p>
<!--        <p>Название изделия</p>-->
    </div>
    <div class="k">
        <div class="k1">
            <a>Ссылка</a>
        </div>
        <div class="k5">
            <input type="button" value="Продолжить"
                   onclick="window.location='https://test.mysmartautomation.ru/master/technological-pause-end?product_id=<?php echo $product_id; ?>'">
        </div>
        <div class="k3">
            <input type="button" value="Взять следующее изделие"
                   onclick="window.location='https://test.mysmartautomation.ru/master/start?continue=true'">
            <!--            <button>Готово</button>-->
        </div>
        <div class="k6">
            <button>Пауза</button>
        </div>
    </div>
</div>


<div class="a2">
    <input type="button" value="Завершить рабочий день"
           onclick="window.location='https://test.mysmartautomation.ru/master/end-working-day?product_id=<?php echo $product_id; ?>'">
<!--    <button>Завершить рабочий день</button>-->
</div>


