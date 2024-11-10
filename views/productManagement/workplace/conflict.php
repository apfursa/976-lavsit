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
                <p>У вас несколько изделий,</p>
                <p>имеющих статус "в работе".</p>
                <p>Необходимо устранить конфликтную ситуацию</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-grid gap-2">
                <input type="button" class="btn btn-success" value="Выполнить повторный запрос"
                       onclick="window.location='https://sof.lavsit.ru/productManagement/workplace/start'">

                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#endWorkingDayModal">
                    Завершить рабочий день
                </button>
                <!--                <input type="button" value="Завершить рабочий день"-->
                <!--                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/working-hours/end-working-day?master_id=-->
                <?php //echo $master_id; ?><!--'">-->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="endWorkingDayModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p>Завершить рабочий день?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="endWorkingDay">Подтвердить</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        console.log('59')
        $('#endWorkingDay').click(function () {
            location.href = 'https://sof.lavsit.ru/productManagement/working-hours/end-working-day?product_id=<?php echo $product_id; ?>'
            $('#endWorkingDayModal').modal('hide');
        });
    });
</script>

