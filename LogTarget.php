<?php
/**
 * @link https://github.com/nfedoseev/yii2-external-target
 * @copyright Copyright (c) 2016 Nikita Fedoseev
 * @license https://github.com/nfedoseev/yii2-external-target/blob/master/LICENSE.md
 */

namespace nfedoseev\yii2\ExternalTarget;

use Yii;
use yii\log\Target;

/**
 * Class LogTarget collect and sent logs
 *
 * @package nfedoseev\yii2\ExternalTarget
 */
class LogTarget extends Target
{
    /**
     * @var array Statuses for ignoring
     */
    public $ignore_statuses = [];
    /**
     * @var bool
     */
    private static $send = false;

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        if (self::$send == false) {
            if (!in_array(Yii::$app->getResponse()->statusCode, $this->ignore_statuses)) {
                Client::get()->sent($this->exportMessages(), LogmanClient::SOURCE_TARGET);
                self::$send = true;
            }
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
     * @return array
     */
    private function exportMessages()
    {
        $data = [];

        foreach ($this->messages as $msg) {
            $message = new Message($msg);
            $data[] = $message->getData();
        }

        return $data;
    }
}