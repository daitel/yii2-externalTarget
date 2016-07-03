<?php
/**
 * @link https://github.com/nfedoseev/yii2-external-target
 * @copyright Copyright (c) 2016 Nikita Fedoseev
 * @license https://github.com/nfedoseev/yii2-external-target/blob/master/LICENSE.md
 */

namespace nfedoseev\yii2\ExternalTarget;

use Yii;
use yii\httpclient\Client;
use yii\log\Target;

/**
 * Class HttpTarget sends log to user API by HTTP Request
 *
 * @package nfedoseev\yii2\ExternalTarget
 */
class HttpTarget extends Target{
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
    public $user_id = 'id';
    /**
     * @var array Statuses for ignoring
     */
    public $ignore_statuses = [];

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        $data = $this->prepareData();

        if (!in_array($data['status'], $this->ignore_statuses)) {
            $this->exportMessages($data);
            $this->sent($data);
        }
    }

    /**
     * Processes the given log messages.
     * This method will filter the given messages with [[levels]] and [[categories]].
     * And if requested, it will also export the filtering result to specific medium (e.g. email).
     * @param array $messages log messages to be processed. See [[\yii\log\Logger::messages]] for the structure
     * of each message.
     * @param boolean $final whether this method is called at the end of the current application
     */
    public function collect($messages, $final)
    {
        $this->messages = array_merge($this->messages, $messages);
        if ($final) {
            $this->export();
        }
    }

    /**
     * Export messages
     * @param $data
     */
    private function exportMessages(&$data)
    {
        foreach ($this->messages as $msg) {
            $message = new Message($msg);
            $data['messages'][] = $message->getData();
        }
    }

    /**
     * Sent data
     * @param $data array
     */
    private function sent($data){
        $client = new Client(['baseUrl' => $this->baseUrl]);
        $client->post('sent', $data)->send();
    }

    /**
     * Prepare Data
     * @return array
     */
    private function prepareData()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();

        return [
            'tag' => $this->getTag(),
            'url' => $request->getAbsoluteUrl(),
            'site' => $this->site,
            'time' => date("Y-m-d H:i:s"),
            'status' => (int) $response->statusCode,
            'method' => $request->getMethod(),
            'user_ip' => $request->getUserIP(),
            'user_id' => $this->getUserId(),
        ];
    }

    /**
     * Return generated uniq tag of request
     * @return string
     */
    private function getTag(){
        return date('YmdH-').sha1($this->site.uniqid('', true));
    }

    /**
     * Return user ID from identity
     * @return int|null
     */
    private function getUserId()
    {
        $user_id = $this->user_id;

        return (Yii::$app->has('user') && ($user = Yii::$app->get('user')) && ($identity = $user->getIdentity(false))) ?
            $identity->$user_id : null;
    }
}