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
                <a class="btn btn-primary"
                   href="https://test.mysmartautomation.ru/productManagement/product/return-product?product_id=<?php echo $product_id; ?>"
                   role="button">Вернуть</a>
                <input type="button" value="Готово"
                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/product/done?product_id=<?php echo $product_id; ?>' ">
                <input type="button" value="Технологическая пауза"
                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/product/start-technological-pause?product_id=<?php echo $product_id; ?>' ">
                <input type="button" value="Пауза"
                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/product/start-pause?product_id=<?php echo $product_id; ?>'">
                <input type="button" value="Завершить рабочий день"
                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/working-hours/end-working-day?product_id=<?php echo $product_id; ?>'">
            </div>
        </div>
    </div>
</div>



