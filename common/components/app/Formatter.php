<?php


namespace common\components\app;


use yii\base\Exception;

/**
 * Class Formatter
 *
 * @package common\components\app
 */
class Formatter extends \yii\i18n\Formatter
{
    const PHONE_FORMAT_1 = '+7 (%s%s%s) %s%s%s-%s%s-%s%s';
    const PHONE_FORMAT_PLAIN = '+7%s%s%s%s%s%s%s%s%s%s';

    public function asPhone($phone, $format = self::PHONE_FORMAT_1)
    {
        $parts = str_split($phone);

        if (count($parts) != 10) {
            throw new Exception('Номер должен быть из 10 цифр');
        } else {
            $phoneFormat = vsprintf($format, $parts);
            return $phoneFormat;
        }
    }
}
