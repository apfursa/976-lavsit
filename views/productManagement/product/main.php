<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<style type="text/css">
    .t1{
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
                <input type="button" value="Ссылка"
                       onclick="window.location='<?php echo $link; ?>'">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#returnProductModal">
                    Вернуть
                </button>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#doneModal">
                    Готово
                </button>
                <button type="button" class="btn btn-warning" data-toggle="modal"
                        data-target="#startTechnologicalPauseModal">
                    Технологическая пауза
                </button>
                <!--                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#startPauseModal">-->
                <!--                    Пауза-->
                <!--                </button>-->
                <input type="button" value="Пауза"
                       onclick="window.location='https://sof.lavsit.ru/productManagement/product/start-pause?product_id=<?php echo $product_id; ?>'">

                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#endWorkingDayModal">
                    Завершить рабочий день
                </button>

                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#endWorkingDayModal1">
                    Завершить рабочий день
                </button>


                <button type="button" class="btn btn-info">
                    <?php echo 'Дата готовности: ' . $deadline; ?>
                </button>
                <!--                <input type="button" value="Завершить рабочий день"-->
                <!--                       onclick="window.location='https://test.mysmartautomation.ru/productManagement/working-hours/end-working-day?product_id=-->
                <?php //echo $product_id; ?><!--'">-->
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="returnProductModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p>Вы уверены, что хотите вернуть изделие на предыдущую стадию?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="returnProduct">Подтвердить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="doneModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p> Изделие готово? </p>
                <p>Передвинуть изделие на следующую стадию?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="done">Подтвердить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="startTechnologicalPauseModal" tabindex="-1" role="dialog"
     aria-labelledby="confirmationModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p>Поставить изделие на технологическую паузу и уведомить руководителя?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="startTechnologicalPause">Подтвердить</button>
            </div>
        </div>
    </div>
</div>

<!--<div class="modal fade" id="startPauseModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"-->
<!--     aria-hidden="true">-->
<!--    <div class="modal-dialog">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-body">-->
<!--                <p>Поставить изделие на паузу и уведомить руководителя?</p>-->
<!--            </div>-->
<!--            <div class="modal-footer">-->
<!--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>-->
<!--                <button type="button" class="btn btn-primary" id="startPause">Подтвердить</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

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
        $('#returnProduct').click(function () {
            location.href = 'https://sof.lavsit.ru/productManagement/product/return-product?product_id=<?php echo $product_id; ?>'
            $('#returnProductModal').modal('hide');
        });
    });
</script>

<script>
    $(document).ready(function () {
        console.log('59')
        $('#done').click(function () {
            location.href = 'https://sof.lavsit.ru/productManagement/product/done?product_id=<?php echo $product_id; ?>'
            $('#doneModal').modal('hide');
        });
    });
</script>

<script>
    $(document).ready(function () {
        console.log('59')
        $('#startTechnologicalPause').click(function () {
            location.href = 'https://sof.lavsit.ru/productManagement/product/start-technological-pause?product_id=<?php echo $product_id; ?>'
            $('#startTechnologicalPauseModal').modal('hide');
        });
    });
</script>

<!--<script>-->
<!--    $(document).ready(function () {-->
<!--        console.log('59')-->
<!--        $('#startPause').click(function () {-->
<!--            location.href = 'https://test.mysmartautomation.ru/productManagement/product/start-pause?product_id=--><?php //echo $product_id; ?><!--'-->
<!--//            $('#startPauseModal').modal('hide');-->
<!--//        });-->
<!--//    });-->
<!--</script>-->

<script>
    $(document).ready(function () {
        console.log('59')
        $('#endWorkingDay').click(function () {
            location.href = 'https://sof.lavsit.ru/productManagement/working-hours/end-working-day?product_id=<?php echo $product_id; ?>'
            $('#endWorkingDayModal').modal('hide');
        });
    });
</script>



