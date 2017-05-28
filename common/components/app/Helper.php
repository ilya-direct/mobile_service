<?php

namespace common\components\app;

class Helper
{
    /**
     * Checks if array is assoc
     * @param $array
     * @return bool
     */
    public static function isArrayAssoc(array $array)
    {
        // Keys of the array
        $keys = array_keys($array);
        
        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }
    
}