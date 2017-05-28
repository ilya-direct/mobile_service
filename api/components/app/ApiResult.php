<?php

namespace api\components\app;

use common\components\app\Helper;
use yii\base\Exception;

abstract class ApiResult
{
    protected static abstract function getResultAttributes();
    
    public static function toArray($serializable)
    {
        if (is_array($serializable) && !Helper::isArrayAssoc($serializable)) {
            $data = [];
            foreach ($serializable as $item) {
                $data[] = self::toArrayObject($item);
            }
        } else {
            $data = self::toArrayObject($serializable);
        }
        
        return $data;
    }
    
    protected static function toArrayObject($object)
    {
        $data = [];
        foreach (static::getResultAttributes() as $key => $value) {
            if (is_string($value)) {
                if (!is_scalar($object->$value) && !is_null($object->$value)) {
                    throw new Exception('ResultArray attribute ' . $value . ' must be a string');
                }
                $data[$value] = $object->$value;
            } else {
                if (is_array($value)) {
                    $data[$key] = self::toArrayInternal($object->$key, $value);
                }
            }
        }
        
        return $data;
    }
    
    private function toArrayInternal($property, $values)
    {
        if (is_array($values) && !Helper::isArrayAssoc($values) && is_array(reset($values))) {
            $data = [];
            $item = reset($values);
            
            foreach ($property as $child) {
                $data[] = $this->toArrayInternal($child, $item);
            }
            
            return $data;
        }
        
        if (!$property) {
            return null;
        }
        $data = [];
        foreach ($values as $key => $value) {
            if (is_string($value)) {
                if (!is_scalar($property->$value && !is_null($property->$value))) {
                    throw new Exception('ResultArray attribute ' . $value . ' must be a string');
                }
                $data[$value] = $property->$value;
            } else {
                if (is_array($value)) {
                    $data[$key] = $this->toArrayInternal($property->$value, $value);
                }
            }
        }
        
        return $data;
    }
}