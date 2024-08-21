<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<style type="text/css">
    .con {
        position: relative;
    }

    .t1 {
        position: absolute;
        margin-top: 25%;
        left: 40%;
        width: 80%;
        transform: translate(-50%, -50%);
        font-size: 50px;
        text-align: center;
    }

    .k {
        position: absolute;
        height: 100%;
        width: 100%;
    }

    .k1 {
        position: absolute;
        font-size: 40px;
        width: 150px;
        left: 90%;
        margin-top: 10%;
        transform: translate(-50%, -50%);
    }

    .k2 {
        position: absolute;
        font-size: 40px;
        width: 150px;
        left: 90%;
        margin-top: 20%;
        transform: translate(-50%, -50%);
    }

    .k3 {
        position: absolute;
        font-size: 40px;
        width: 150px;
        left: 90%;
        margin-top: 30%;
        transform: translate(-50%, -50%);
    }

    .k4 {
        position: absolute;
        font-size: 40px;
        width: 150px;
        left: 90%;
        margin-top: 40%;
        transform: translate(-50%, -50%);
    }

    .k5 {
        position: absolute;
        font-size: 40px;
        width: 150px;
        left: 90%;
        margin-top: 50%;
        transform: translate(-50%, -50%);
    }

    .a2 {
        position: fixed;
        font-size: 40px;

        left: 50%;
        top: 80%;
        transform: translate(-50%, -50%);
    }

</style>

<div class="container text-center">
    <div class="row">
        <div class="col-md-9">
            <p><?php echo $product_name; ?></p>
        </div>
        <div class="col-md-3">
            <div class="d-grid gap-2">
                <a>Ссылка</a>

                <a class="btn btn-primary"
                   href="https://test.mysmartautomation.ru/master/return-product?product_id=<?php echo $product_id; ?>"
                   role="button">Вернуть</a>
                <!--            <input type="button" value="Вернуть"-->
                <!--                   onclick="window.location='https://test.mysmartautomation.ru/master/return-product?product_id=--><?php //echo $product_id; ?>
                <!--            <button>Вернуть</button>-->

                <input type="button" value="Готово"
                       onclick="window.location='https://test.mysmartautomation.ru/master/done?product_id=<?php echo $product_id; ?>' ">
                <!--            <button>Готово</button>-->
                <input type="button" value="Технологическая пауза"
                       onclick="window.location='https://test.mysmartautomation.ru/master/technological-pause-start?product_id=<?php echo $product_id; ?>' ">
                <!--            <button>Технологическая пауза</button>-->
                <button>Пауза</button>
                <input type="button" value="Завершить рабочий день"
                       onclick="window.location='https://test.mysmartautomation.ru/master/end-working-day?product_id=<?php echo $product_id; ?>'">


            </div>
        </div>
    </div>
</div>



