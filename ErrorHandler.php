<?php
/**
 * @link https://github.com/nfedoseev/yii2-external-target
 * @copyright Copyright (c) 2016 Nikita Fedoseev
 * @license https://github.com/nfedoseev/yii2-external-target/blob/master/LICENSE.md
 */

namespace nfedoseev\yii2\ExternalTarget;

use Yii;
use yii\web\Application;

/**
 * Class ErrorHandler
 * @package nfedoseev\yii2\ExternalTarget
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * Initialization
     */
    public function init()
    {
        Yii::$app->on(Application::EVENT_BEFORE_REQUEST, [$this, 'onShutdown']);
    }

    /**
     * onShutdown
     */
    public function onShutdown()
    {
        $error = error_get_last();
        if ($error !== null) {
            $errors = array(
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_CORE_WARNING,
                E_COMPILE_ERROR,
                E_COMPILE_WARNING,
                E_STRICT
            );
            if (in_array($error['type'], $errors)) {
                $this->reportException(
                    $this->createErrorException($error['message'], $error['type'], $error['file'], $error['line'])
                );
            }
        }
    }


    /**
     * Handle Error
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return bool|void
     */
    public function handleError($code, $message, $file, $line)
    {
        if (error_reporting()) {
            $this->reportException($this->createErrorException($message, $code, $file, $line));
        }

        parent::handleError($code, $message, $file, $line);
    }

    /**
     * Handle Exception
     * @param \Exception $exception
     */
    public function handleException($exception)
    {
        $this->reportException($exception);
        parent::handleException($exception);
    }

    /**
     * Create error exception
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     * @return \ErrorException
     */
    protected function createErrorException($message, $code, $file, $line)
    {
        return new \ErrorException($message, $code, 0 /* will be resolved */, $file, $line);
    }

    /**
     * Report Exception
     * @param $ex \ErrorException
     * @return mixed
     */
    private function reportException($ex)
    {
        $data = [
            'class' => get_class($ex),
            'error_group' => $ex->getMessage() . ':' . $ex->getLine(),
            'line_number' => $ex->getLine(),
            'file_name' => $ex->getFile(),
            'message' => $ex->getMessage(),
            'stack_trace' => $ex->getTraceAsString(),
        ];

        Client::get()->sent($data, LogClient::SOURCE_HANDLER);
    }
} 