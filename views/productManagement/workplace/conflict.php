<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="container text-center">
    <div class="row">
        <div class="col-md-9">
            <p>У вас несколько изделий,</p>
            <p>имеющих статус "в работе".</p>
            <p>Необходимо устранить конфликтную ситуацию</p>
        </div>
        <div class="col-md-3">
            <div class="d-grid gap-2">
                <input type="button" value="Выполнить повторный запрос"
                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/workplace/start'">
                <input type="button" value="Завершить рабочий день"
                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/working-hours/end-working-day?master_id=<?php echo $master_id; ?>'">
            </div>
        </div>
    </div>
</div>

