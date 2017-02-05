<?php

namespace api\components\app;

use yii\base\Exception;

/**
 * Class ApiResultBase
 *
 * @property array $resultAttributes
 * @package api\components\app
 */
trait ApiResultBase
{
    protected function getResultAttributes()
    {
        return [];
    }
    
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = [];
        foreach ($this->resultAttributes as $key => $value) {
            if (is_string($value)) {
                if (!is_scalar($this->$value) && !is_null($this->$value)) {
                    throw new Exception('ResultArray attribute ' . $value . ' must be a string');
                }
                $data[$value] = $this->$value;
            } else if (is_array($value)) {
                $data[$key] = $this->toArrayInternal($this->$key, $value);
            }
        }
        
        return $data;
    }
    
    private function toArrayInternal($model, $values)
    {
        if (!$model) {
            return null;
        }
        $data = [];
        foreach ($values as $key => $value) {
            if (is_string($value)) {
                if (!is_scalar($this->$value && !is_null($this->$value))) {
                    throw new Exception('ResultArray attribute ' . $value . ' must be a string');
                }
                $data[$value] = $this->$value;
            } else if (is_array($value)) {
                $data[$key] = $this->toArrayInternal($this->$value, $value);
            }
        }
        
        return $data;
    }
}
