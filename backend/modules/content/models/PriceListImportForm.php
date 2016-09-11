<?php


namespace backend\modules\content\models;


use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\UploadedFile;
use common\models\ar\Device;
use common\models\ar\DeviceAssign;
use common\models\ar\Service;

class PriceListImportForm extends Model
{
    /** @var null|UploadedFile */
    public $file = null;

    public function init()
    {
        $this->file = UploadedFile::getInstance($this, 'file');
    }

    public function rules()
    {
        return [
            ['file', 'file', 'extensions' => 'csv', 'checkExtensionByMimeType' => false, 'skipOnEmpty' => false],
        ];
    }

    public function save()
    {
        $handle = fopen($this->file->tempName, 'r');
        fgetcsv($handle, null); // header
        $updatedPrices = [];
        $oldDeviceName = '';
        $transaction = Yii::$app->db->beginTransaction();
        $rowNumber = 1;
        $importedDeviceAssignIds = []; // Id загруженных связок услуг по устройствам
        try {
            while (($row = fgetcsv($handle, null)) !== false) {
                ++$rowNumber;
                $deviceName = (string)$row[0] ?: $oldDeviceName;
                $serviceName = (string)$row[1];
                $price = (int)$row[2];
                $priceOld = (trim($row[3]) == '0' || $row[3]) ? (integer) $row[3] : null;
                /** @var Service $serviceModel */
                $serviceModel = Service::findOneOrFail(['name' => $serviceName]);
                /** @var Device $deviceModel */
                $deviceModel = Device::findOneOrFail(['name' => $deviceName]);
                /** @var DeviceAssign $deviceAssignModel */
                $deviceAssignModel = DeviceAssign::findOrNew([
                    'device_id' => $deviceModel->id,
                    'service_id' => $serviceModel->id
                ]);
                $importedDeviceAssignIds[] = $deviceAssignModel->id;
                $deviceAssignModel->price_old = is_int($priceOld) ? $priceOld:
                    ($deviceAssignModel->price_old ?: (($deviceAssignModel->price > $price) ? $deviceAssignModel->price : null));
                $deviceAssignModel->price = (integer)$price;
                $deviceAssignModel->enabled = true;
                if ($deviceAssignModel->dirtyAttributes) {
                    $updatedPrices[] = [
                        'device' => $deviceModel->name,
                        'service' => $serviceModel->name,
                        'price' => $deviceAssignModel->price,
                        'price_old' => $deviceAssignModel->price_old,
                    ];
                }
                $deviceAssignModel->save(false);
                $oldDeviceName = $deviceModel->name;
            }

            // Все цены, которые не были загруженными сделать неактивными
            /** @var DeviceAssign[] $deviceAssigns */
            $deviceAssigns = DeviceAssign::find()
                ->where(['not', ['id' => $importedDeviceAssignIds]])
                ->all();
            foreach ($deviceAssigns as $deviceAssign) {
                $deviceAssign->enabled = false;
                $deviceAssign->save(false);
            }

            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollBack();
            $error = 'Файл ' . $this->file->name . '. Строка #' . $rowNumber . '. ' . $e->getMessage();
            return [
                'success' => false,
                'error' => $error,
            ];
        }

        return [
            'success' => true,
            'updatedItems' => $updatedPrices,
        ];
    }
}
