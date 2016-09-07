<?php

namespace common\models\ar;


use Yii;
use yii\behaviors\SluggableBehavior;
use creocoder\nestedsets\NestedSetsBehavior;
use creocoder\nestedsets\NestedSetsQueryBehavior;
use kartik\tree\models\Tree;

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
 *
 * @method static \yii\db\ActiveQuery find()
 *
 */
class DeviceCategory extends Tree
{
    public $icon = null;
    public $active;
    public $selected;
    public $disabled = false;
    public $readonly = false;
    public $visible;
    public $collapsed;
    public $movable_u;
    public $movable_d;
    public $movable_r;
    public $movable_l;
    public $removable;
    public $removable_all;
    public $icon_type = 1;


    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth',
            ],
            'slug' => [
                'class' => SluggableBehavior::className(),
                'slugAttribute' => 'alias',
                'attribute' => 'name',
                'ensureUnique' => true,
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device_category}}';
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название категории',
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

    /**
     * Список корневых категорий, упорядоченных по имени
     * @param array $condition
     * @param string $attribute
     * @return array
     */
    public static function getRootList()
    {
        $list =self::find()
            ->select('name')
            ->where([
                'enabled' => true,
                'depth' => 0,
            ])
            ->indexBy('id')
            ->orderBy('name')
            ->column();

        return $list;
    }
}
