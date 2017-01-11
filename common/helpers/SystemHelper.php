<?php

namespace common\helpers;

use common\models\ar\User;

class SystemHelper
{
    private static $_isWin;
    private static $_isConsole;
    private static $_consoleUserId;
    
    public static function isWin()
    {
        if (is_null(self::$_isWin)) {
            $os_string = php_uname('s');
            self::$_isWin = strpos(strtoupper($os_string), 'WIN') !== false;
        }
        return self::$_isWin;
    }

    public static function isConsole()
    {
        if (is_null(self::$_isConsole)) {
            self::$_isConsole = PHP_SAPI == 'cli' || (!isset($_SERVER['DOCUMENT_ROOT']) && !isset($_SERVER['REQUEST_URI']));
        }
        return self::$_isConsole;
    }
    
    public static function getConsoleUserId()
    {
        if (!self::$_consoleUserId) {
            self::$_consoleUserId = User::find()
                ->select('id')
                ->where(['email' => 'console@console.ru'])
                ->scalar();
        }
        
        return self::$_consoleUserId;
    }
}
