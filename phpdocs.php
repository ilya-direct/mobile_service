<?php

/**
 * Class Yii
 */
class Yii extends \yii\BaseYii {
    /** @var IDE_PHPDocConsoleApp|IDE_PHPDocWebApp */
    public static $app;
}

/**
 * Class IDE_PHPDocWebApp
 *
 */
class IDE_PHPDocWebApp extends \yii\web\Application {}

/**
 * Class IDE_PHPDocConsoleApp
 */
class IDE_PHPDocConsoleApp extends \yii\console\Application {}
