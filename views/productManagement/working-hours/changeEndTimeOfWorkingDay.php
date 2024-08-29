<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?><style type="text/css">
    .t1{
        font-size: 30px;
    }
</style>

<div class="container text-center">
    <div class="row">
        <div class="col-md-9">
            <div class="t1">
                <p>Предыдущий рабочий день завершился автоматически. Пожалуйста, установите актуальную дату и время завершения предыдущего рабочего дня.</p>
            </div>
            <form action="https://test.mysmartautomation.ru/productManagement/working-hours/change-time" method="post">
                <input type="date" id="date" name="date" size="1">
                <input type="time" id="time" name="time" size="1"><br><br>
                <input type="hidden" name="master_id" value="<?php echo $master_id ?>">
                <input type="hidden" name="entity_id" value="<?php echo $entity_id ?>">
                <input type="submit" name="submit" value="Продолжить">
            </form>

        </div>
<!--        <div class="col-md-3">-->
<!--            <div class="d-grid gap-2">-->
<!--               -->
<!--                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#continueModal">-->
<!--                    Продолжить-->
<!--                </button>-->
<!--                -->
<!--            </div>-->
<!--        </div>-->
    </div>
</div>

<script>
    // Вывод даты по умолчанию в поле <input type="date">
    let date = "<?php echo $dateEnd ?>";
    document.querySelector("#date").value = date;
    // Вывод времени по умолчанию в поле <input type="time">
    let time = "<?php echo $timeEnd ?>";
    document.querySelector("#time").value = time;
</script>





