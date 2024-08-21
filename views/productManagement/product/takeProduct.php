<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="container text-center">
    <div class="row">
        <div class="col-md-12">
            <input type="button" value="Взять изделие в работу"
                   onclick="window.location='https://test.mysmartautomation.ru/productManagement/workplace/start'">
            <input type="button" value="Завершить рабочий день"
                   onclick="window.location='https://test.mysmartautomation.ru/productManagement/working-hours/end-working-day?master_id=<?php echo $master_id; ?>'">
        </div>
    </div>
</div>


