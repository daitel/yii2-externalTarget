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
     * @var array Debug backtrace, contains the application code call stacks
     */
    public $traces;

    /**
     * Convert Yii2 Logged message
     * @param $message array
     */
    public function __construct($message)
    {
        list($this->message, $this->level, $this->category, $this->timestamp) = $message;

        $this->prepare();
    }

    /**
     * Get Array of Data
     * @return array
     */
    public function getData(){
        return [
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