<?php
/**
 * @link https://github.com/nfedoseev/yii2-external-target
 * @copyright Copyright (c) 2016 Nikita Fedoseev
 * @license https://github.com/nfedoseev/yii2-external-target/blob/master/LICENSE.md
 */

namespace nfedoseev\yii2\ExternalTarget;

/**
 * Class Tag for generate uniq tag
 *
 * @package nfedoseev\yii2\ExternalTarget
 */
class Tag {
    /**
     * Uniq tag
     * @var string
     */
    private static $tag;

    /**
     * Get
     * @return string
     */
    public static function get(){
        if(empty(self::$tag)){
            self::$tag = date('YmdH-').sha1(uniqid());
        }

        return self::$tag;
    }
} 