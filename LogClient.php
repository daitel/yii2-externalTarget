<?php
/**
 * @link https://github.com/nfedoseev/yii2-external-target
 * @copyright Copyright (c) 2016 Nikita Fedoseev
 * @license https://github.com/nfedoseev/yii2-external-target/blob/master/LICENSE.md
 */

namespace nfedoseev\yii2\ExternalTarget;

use Yii;

/**
 * Class LogClient
 * @package nfedoseev\yii2\ExternalTarget
 */
class LogClient
{
    const SOURCE_TARGET = 0;
    const SOURCE_HANDLER = 1;

    /**
     * @var string Base API url
     */
    public $baseUrl = '';
    /**
     * @var string Site Name
     */
    public $site = 'site';
    /**
     * @var string ID User
     */
    public $user_id = 0;

    /**
     * Prepare Data
     * @return array
     */
    private function prepareData()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();

        return [
            'tag' => Tag::get(),
            'url' => $request->getAbsoluteUrl(),
            'site' => $this->site,
            'time' => date("Y-m-d H:i:s"),
            'status' => (int)$response->statusCode,
            'method' => $request->getMethod(),
            'user_ip' => $request->getUserIP(),
            'user_id' => $this->getUserId(),
        ];
    }

    /**
     * Get user id
     * @return int
     */
    private function getUserId()
    {
        if (Yii::$app->has('user')) {
            $id = $this->user_id;
            return !Yii::$app->user->isGuest ? Yii::$app->user->identity->$id : 0;
        } else {
            return 0;
        }
    }

    /**
     * Sent data
     * @param array $_data
     * @param int $source
     */
    public function sent($_data, $source)
    {
        $data = $this->prepareData();
        $data['source'] = $source;
        $data['data'] = $_data;

        $client = new \yii\httpclient\Client(['baseUrl' => $this->baseUrl]);
        $client->post('sent', $data)->send();
    }
} 