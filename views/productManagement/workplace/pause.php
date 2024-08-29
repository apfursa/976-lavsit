<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<style type="text/css">
    .t1 {
        font-size: 30px;
    }
</style>
<div class="container text-center">
    <div class="row">
        <div class="col-md-9">
            <div class="t1">
                <p><?php echo $product_name; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-grid gap-2">

                <a class="btn btn-primary" href="https://test.mysmartautomation.ru/productManagement/product/end-pause?product_id=<?php echo $product_id; ?>"
                   role="button">Продолжить</a>

<!--                <input type="button" value="Продолжить"-->
<!--                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/product/end-pause?product_id=--><?php //echo $product_id; ?><!--'">-->
            </div>
        </div>
    </div>
</div>


