<?php

namespace app\controllers\test\ex;

class AddexController extends \wm\admin\controllers\RestController
{
    public function actionDisk_folder_get()
    {
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'disk.folder.get',
            [
                'id' => 90
            ]
        );
        return $answerB24;
    }

    public function actionDisk_folder_uploadfile()
    {
//        $doc = 't1.txt';
//        $doc = 'W1.docx';
        $doc = 'Ex1.xlsx';
//        if(file_get_contents($doc)){
//            return 'Ok';
//        }else{
//            return 'no';
//        }

        $newfile = base64_encode(file_get_contents($doc)); //конвертируем в base64 т.к. данные можно загрузить только в таком формате
        $component = new \wm\b24tools\b24Tools();
        $b24App = $component->connectFromAdmin();
        $obB24 = new \Bitrix24\B24Object($b24App);
        $answerB24 = $obB24->client->call(
            'disk.folder.uploadfile',
            [
                'id' => '90', //ID папки
            //                'data'=>['NAME'=>'W1.docx'], // Имя файла которое добавляем
                'data' => ['NAME' => 'Ex1.xlsx'], // Имя файла которое добавляем
            //                'fileContent'=>['W1.docx',$newfile], //Файл в формате base64
                'fileContent' => $newfile, //Файл в формате base64
            ]
        );
        return $answerB24;
    }
}
