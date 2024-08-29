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
<!--                <input type="button" value="Ссылка"-->
<!--                       onclick="window.location='#'">-->

                <a class="btn btn-success" href="https://test.mysmartautomation.ru/productManagement/product/end-technological-pause?product_id=<?php echo $product_id; ?>"
                   role="button">Продолжить</a>
<!--                <input type="button" value="Продолжить"-->
<!--                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/product/end-technological-pause?product_id=--><?php //echo $product_id; ?><!--'">-->

                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#takeFollowingProductModal">
                    Взять следующее изделие
                </button>
<!--                <input type="button" value="Взять следующее изделие"-->
<!--                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/workplace/take-following-product'">-->

                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#endWorkingDayModal">
                    Завершить рабочий день
                </button>
<!--                <input type="button" value="Завершить рабочий день"-->
<!--                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/working-hours/end-working-day?product_id=--><?php //echo $product_id; ?><!--'">-->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="takeFollowingProductModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p>Вы уверены, что хотите перейти к следующему изделию?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="takeFollowingProduct">Подтвердить</button>
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
        $('#takeFollowingProduct').click(function () {
            location.href = 'https://test.mysmartautomation.ru/productManagement/workplace/take-following-product'
            $('#takeFollowingProductModal').modal('hide');
        });
    });
</script>

<script>
    $(document).ready(function () {
        console.log('59')
        $('#endWorkingDay').click(function () {
            location.href = 'https://test.mysmartautomation.ru/productManagement/working-hours/end-working-day?product_id=<?php echo $product_id; ?>'
            $('#endWorkingDayModal').modal('hide');
        });
    });
</script>


