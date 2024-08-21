<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="container text-center">
    <div class="row">
        <div class="col-md-9">
            <p><?php echo $product_name; ?></p>
        </div>
        <div class="col-md-3">
            <div class="d-grid gap-2">
                <a>Ссылка</a>
                <input type="button" value="Продолжить"
                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/product/end-pause?product_id=<?php echo $product_id; ?>'">
                <input type="button" value="Завершить рабочий день"
                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/working-hours/end-working-day?product_id=<?php echo $product_id; ?>'">
            </div>
        </div>
    </div>
</div>


