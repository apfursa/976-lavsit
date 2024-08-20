<?php
use yii\helpers\Url;

$js = <<<JS
        $(document).ready(
            function () {		 
                BX24.init(
                    function(){                    
                        BX24.installFinish();                                
                    }
                );
            }
        );
JS;
$this->registerJs($js);
?>
<script type="text/javascript" src="//api.bitrix24.com/api/v1/"></script>