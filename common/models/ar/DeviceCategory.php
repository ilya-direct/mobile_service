<?php

namespace common\models\ar;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
use common\models\CategoryQuery;

/**
 * This is the model class for table "device_category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $alias
 * @property string $description
 * @property integer $enabled
 *
 * @property Device[] $devices
 */
class DeviceCategory extends \yii\db\ActiveRecord
{
    use \kartik\tree\models\TreeTrait {
        isDisabled as parentIsDisabled; // note the alias
    }

    /**
     * @var string the classname for the TreeQuery that implements the NestedSetQueryBehavior.
     * If not set this will default to `kartik	ree\models\TreeQuery`.
     */
    public static $treeQueryClass; // change if you need to set your own TreeQuery

    /**
     * @var bool whether to HTML encode the tree node names. Defaults to `true`.
     */
    public $encodeNodeNames = true;

    /**
     * @var bool whether to HTML purify the tree node icon content before saving.
     * Defaults to `true`.
     */
    public $purifyNodeIcons = true;

    /**
     * @var array activation errors for the node
     */
    public $nodeActivationErrors = [];

    /**
     * @var array node removal errors
     */
    public $nodeRemovalErrors = [];

    /**
     * @var bool attribute to cache the `active` state before a model update. Defaults to `true`.
     */
    public $activeOrig = true;

    /**
     * Note overriding isDisabled method is slightly different when
     * using the trait. It uses the alias.
     */

    public $icon = null;

    public $icon_type = 1;

    public function isDisabled()
    {
        return false;
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth',
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'device_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'tree', 'lft', 'rgt', 'depth', 'alias'], 'safe'],
            [['tree', 'lft', 'rgt', 'depth', 'enabled'], 'integer'],
            [['description'], 'string'],
            [['name', 'alias'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['tree'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'alias' => 'Alias',
            'description' => 'Description',
            'enabled' => 'Enabled',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['device_category_id' => 'id']);
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
}
