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
     * @var string Uniq tag of request
     */
    public $tag;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->tag = uniqid();
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        $data = [];
        foreach ($this->messages as $msg) {
            $message = new Message($msg, $this->tag);
            $data[] = $message->getData();
        }

        $this->sent($data);
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
     * Sent data
     * @param $data array
     */
    private function sent($data){
        $client = new Client(['baseUrl' => $this->baseUrl]);
        $client->post('sent', $data)->send();
    }
}