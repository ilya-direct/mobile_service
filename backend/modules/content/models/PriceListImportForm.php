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
        fgetcsv($handle, null, ';', '"'); // header
        $updatedPrices = [];
        $oldDeviceName = '';
        $transaction = Yii::$app->db->beginTransaction();
        $rowNumber = 1;
        try {
            while (($row = fgetcsv($handle, null, ';', '"')) !== false) {
                ++$rowNumber;
                $deviceName = (string)$row[0] ?: $oldDeviceName;
                $serviceName = (string)$row[1];
                $price = (int)$row[2];
                $priceOld = (int)$row[3];
                /** @var Service $serviceModel */
                $serviceModel = Service::findOneOrFail(['name' => $serviceName]);
                /** @var Device $deviceModel */
                $deviceModel = Device::findOneOrFail(['name' => $deviceName]);
                /** @var DeviceAssign $deviceAssignModel */
                $deviceAssignModel = DeviceAssign::findOrNew([
                    'device_id' => $deviceModel->id,
                    'service_id' => $serviceModel->id
                ]);
                $deviceAssignModel->price_old = $priceOld ?: $deviceAssignModel->price_old ?: (($deviceAssignModel->price > $price) ? $deviceAssignModel->price : null);
                $deviceAssignModel->price = $price;
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
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollBack();
            $error = 'Файл ' . $this->file->name . '. Строка #' . $rowNumber . '. Неизвестное устройство или услуга';
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
