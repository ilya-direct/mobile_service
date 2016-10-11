<?php

namespace backend\models;

use Yii;
use common\models\ar\Order;
use common\models\ar\OrderPerson;
use yii\data\ActiveDataProvider;

class OrderSearchForm extends Order
{
    public $name;
    public $phone;

    public function scenarios()
    {
        return [self::SCENARIO_DEFAULT => ['id', 'uid', 'name', 'phone', 'order_status_id']];
    }

    public function rules()
    {
        return [
            [['uid', 'name', 'phone'], 'string'],
            [['id', 'order_status_id'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = Order::findOwnOrders(Yii::$app->user->identity)
            ->innerJoinWith(['orderPerson', 'orderStatus'])
            ->notDeleted();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [1, 1000],
                'defaultPageSize' => 100,
            ],
            'sort' => [
                'attributes' => [
                    'id',
                ],
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Order::tableName() . '.id' => $this->id,
            Order::tableName() . '.order_status_id' => $this->order_status_id,
        ]);
        $query->andFilterWhere(['ilike', 'uid', $this->uid]);
        $query->andFilterWhere(['ilike', OrderPerson::tableName() . '.first_name', $this->name]);
        $query->andFilterWhere(['ilike', OrderPerson::tableName() . '.phone', $this->phone]);

        return $dataProvider;
    }
}
