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
        margin-top: 30%;
        left: 40%;
        width:100%;
        transform: translate( -50%, -50%);
        font-size: 100px;
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
    .k2{
        position: absolute;
        font-size: 40px;
        width:150px;
        left: 90%;
        margin-top: 20%;
        transform: translate( -50%,  -50%);
    }
    .k3{
        position: absolute;
        font-size: 40px;
        width:150px;
        left: 90%;
        margin-top: 30%;
        transform: translate( -50%,  -50%);
    }
    .k4{
        position: absolute;
        font-size: 40px;
        width:150px;
        left: 90%;
        margin-top: 40%;
        transform: translate( -50%,  -50%);
    }
    .k5{
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
        <p>Название изделия</p>
    </div>
    <div class="k">
        <div class="k1">
            <a>Ссылка</a>
        </div>
<!--        <div class="k2">-->
<!--            <button>Вернуть</button>-->
<!--        </div>-->
        <div class="k3">
            <button>Продолжить</button>
        </div>
<!--        <div class="k4">-->
<!--            <button>Следующее изделие</button>-->
<!--        </div>-->
<!--        <div class="k5">-->
<!--            <button>Пауза</button>-->
<!--        </div>-->
    </div>
</div>


<div class="a2">
    <button>Завершить рабочий день - 6m</button>
</div>

<?php echo $id; ?>


