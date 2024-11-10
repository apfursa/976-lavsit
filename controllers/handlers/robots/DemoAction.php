<?php

namespace app\controllers\handlers\robots;

use yii\base\Action;
use Yii;
use wm\b24tools\b24Tools;
use Bitrix24\B24Object;
use yii\helpers\ArrayHelper;

/**
 *
 */
class DemoAction extends Action
{
    /**
     * @return string
     * @throws \Bitrix24\Exceptions\Bitrix24ApiException
     * @throws \Bitrix24\Exceptions\Bitrix24EmptyResponseException
     * @throws \Bitrix24\Exceptions\Bitrix24Exception
     * @throws \Bitrix24\Exceptions\Bitrix24IoException
     * @throws \Bitrix24\Exceptions\Bitrix24MethodNotFoundException
     * @throws \Bitrix24\Exceptions\Bitrix24PaymentRequiredException
     * @throws \Bitrix24\Exceptions\Bitrix24PortalDeletedException
     * @throws \Bitrix24\Exceptions\Bitrix24SecurityException
     * @throws \Bitrix24\Exceptions\Bitrix24TokenIsExpiredException
     * @throws \Bitrix24\Exceptions\Bitrix24TokenIsInvalidException
     * @throws \Bitrix24\Exceptions\Bitrix24WrongClientException
     * @throws \yii\db\Exception
     */
    public function run()
    {
        $request = Yii::$app->request;
        $auth = $request->post('auth');
        $event_token = $request->post('event_token');
        $properties = $request->post('properties');
        $component = new b24Tools();
        $b24App = $component->connectFromUser($auth);
        $returnValues = $this->logicRobot($properties, $b24App);
        $obB24 = new \Bitrix24\Bizproc\Event($b24App);
        $obB24->send($event_token, $returnValues);
        return '';
    }

    /**
     * @param $properties
     * @param $b24App
     * @return array
     * @throws \Bitrix24\Exceptions\Bitrix24ApiException
     * @throws \Bitrix24\Exceptions\Bitrix24EmptyResponseException
     * @throws \Bitrix24\Exceptions\Bitrix24Exception
     * @throws \Bitrix24\Exceptions\Bitrix24IoException
     * @throws \Bitrix24\Exceptions\Bitrix24MethodNotFoundException
     * @throws \Bitrix24\Exceptions\Bitrix24PaymentRequiredException
     * @throws \Bitrix24\Exceptions\Bitrix24PortalDeletedException
     * @throws \Bitrix24\Exceptions\Bitrix24PortalRenamedException
     * @throws \Bitrix24\Exceptions\Bitrix24SecurityException
     * @throws \Bitrix24\Exceptions\Bitrix24TokenIsExpiredException
     * @throws \Bitrix24\Exceptions\Bitrix24TokenIsInvalidException
     * @throws \Bitrix24\Exceptions\Bitrix24WrongClientException
     */
    protected function logicRobot($properties, $b24App)
    {
        $myProperty = ArrayHelper::getValue($properties, 'myProperty');

        $obB24 = new B24Object($b24App);
        $request = $obB24->client->call('method', ['ID' => $myProperty]);

        $ids = ArrayHelper::getValue($request, 'result');

        return [
            'ids' => $ids,
            'maxId' => $ids ? max($ids) : null,
            'minId' => $ids ? min($ids) : null,
            'count' => count($ids)
        ];
    }
}
