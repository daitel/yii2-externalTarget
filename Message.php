<?php
/**
 * @link https://github.com/nfedoseev/yii2-external-target
 * @copyright Copyright (c) 2016 Nikita Fedoseev
 * @license https://github.com/nfedoseev/yii2-external-target/blob/master/LICENSE.md
 */

namespace nfedoseev\yii2\ExternalTarget;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class Message
 *
 * @package nfedoseev\yii2\ExternalTarget
 * @see yii\log\Logger
 */
class Message
{
    /**
     * @var string Tag
     */
    public $tag;
    /**
     * @var string Site
     */
    public $site;
    /**
     * @var string Url
     */
    public $url;
    /**
     * @var string Method
     */
    public $method;
    /**
     * @var string User IP
     */
    public $user_ip = null;
    /**
     * @var int User ID
     */
    public $user_id = null;
    /**
     * @var int Status Code
     */
    public $status;
    /**
     * @var mixed String or some complex data, such as an exception object
     */
    public $message;
    /**
     * @var null
     */
    public $messageFull = null;
    /**
     * @var null
     */
    public $messageShort = null;
    /**
     * @var null
     */
    public $messageAll = null;
    /**
     * Line
     * @var null
     */
    public $line = null;
    /**
     * File
     * @var string
     */
    public $file = null;
    /**
     * @var array
     */
    public $additional = [];
    /**
     * @var integer Level
     */
    public $level;
    /**
     * @var string
     */
    public $category;
    /**
     * @var float Obtained by microtime(true)
     */
    public $timestamp;
    /**
     * @var int Time
     */
    public $time;
    /**
     * @var array Debug backtrace, contains the application code call stacks
     */
    public $traces;

    /**
     * Convert Yii2 Logged message
     * @param $message array
     * @param $tag string
     * @param $site string
     */
    public function __construct($message, $tag, $site = '')
    {
        $this->tag = $tag;
        $this->site = $site;

        list($this->message, $this->level, $this->category, $this->timestamp) = $message;

        $request = Yii::$app->getRequest();
        $this->url = $request->getAbsoluteUrl();
        $this->method = $request->getMethod();
        $this->ip = $request->getUserIP();

        $response = Yii::$app->getResponse();
        $this->status = $response->statusCode;

        $this->time = time();

        $this->prepare();
    }

    /**
     * Get Array of Data
     * @return array
     */
    public function getData(){
        return [
            'tag' => $this->tag,
            'site' => $this->site,
            'url' => $this->url,
            'method' => $this->method,
            'user_ip' => $this->user_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'message' => $this->message,
            'message_full' => $this->messageFull,
            'message_short' => $this->messageShort,
            'message_all' => $this->messageAll,
            'line' => $this->line,
            'file' => $this->file,
            'additional' => json_encode($this->additional),
            'level' => $this->level,
            'category' => $this->category,
            'timestamp' => $this->timestamp,
            'time' => $this->time,
            'traces' => json_encode($this->traces)
        ];
    }

    /**
     * Prepare Variables
     */
    private function prepare()
    {
        $this->prepareText();
        $this->prepareTraces();
        $this->prepareUser();
    }

    /**
     * Prepare Text
     */
    private function prepareText()
    {
        if (is_string($this->message)) {
            $this->messageShort = $this->message;
        } elseif ($this->message instanceof \Exception) {
            $this->messageShort = 'Exception ' . get_class($this->message) . ': ' . $this->message->getMessage();
            $this->messageFull = (string)$this->message;
            $this->line = $this->message->getLine();
            $this->file = $this->message->getFile();
        } else {
            $short = ArrayHelper::remove($this->message, 'short');
            $full = ArrayHelper::remove($this->message, 'full');
            $add = ArrayHelper::remove($this->message, 'add');

            if ($short !== null) {
                $this->messageShort = $short;
                $this->messageFull = VarDumper::dumpAsString($this->message);
            } else {
                $this->messageShort = VarDumper::dumpAsString($this->message);
            }

            if ($full !== null) {
                $this->messageFull = VarDumper::dumpAsString($full);
            }

            if (is_array($add)) {
                foreach ($add as $key => $val) {
                    if (is_string($key)) {
                        if (!is_string($val)) {
                            $val = VarDumper::dumpAsString($val);
                        }
                        $this->additional[] = [$key, $val];
                    }
                }
            }
        }
    }

    /**
     * Prepare User
     */
    private function prepareUser(){
        if (Yii::$app->has('user') && ($user = Yii::$app->get('user')) && ($identity = $user->getIdentity(false))
        ) {
            $this->user_id = $identity->id;
        }
    }

    /**
     * Prepare Traces
     */
    private function prepareTraces()
    {
        if (isset($this->traces) && is_array($this->traces)) {
            $traces = [];
            foreach ($this->traces as $index => $trace) {
                $traces[] = "{$trace['file']}:{$trace['line']}";
                if ($index === 0) {
                    $this->file = $trace['file'];
                    $this->line = $trace['line'];
                }
            }

            $this->additional[] = ['trace', implode("\n", $traces)];
        }
    }
} 