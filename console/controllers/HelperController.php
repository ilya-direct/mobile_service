<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\ar\Order;
use common\models\ar\User;

class HelperController extends Controller
{

    public function actionDeleteOrder(array $ids)
    {
        $confirmed = Console::confirm('Are you sure to delete orders ?');
        if (!$confirmed) {
            return;
        }
        /** @var Order $order */
        foreach ($ids as $id) {
            $order = Order::findOne($id);
            if (!$order) {
                Console::output('OrderId = '. $id . ' not found. ');
            } else {
                $order->delete(false);
                Console::output('OrderId = '. $id . ' successfully deleted. ');
            }

        }
    }

    public function actionBackUp()
    {
        Console::output('Generating back-up ...');
        $folder = Yii::$app->params['dbBackUpFolder'];
        $cmd = 'pg_dump -U postgres -h 127.0.0.1 -Fc' . ' mobile-service'
            . ' > '  . $folder . '/mobile-service.' . time() . '.backup';
        system($cmd , $return);
        if ($return) {
            Console::output('Error ' . $return);
        } else {
            Console::output('Successfully generated.');
        }

    }

    public function actionRestoreUser($email)
    {
        /** @var User $user */
        $user = User::findOne(['email' => $email]);
        if (!$user) {
            Console::output('User not found.');
        } else if (!$user->deleted) {
            Console::output('User is not deleted.');
        } else {
            $user->deleted = false;
            $user->enabled = true;
            $user->setPassword(Yii::$app->security->generateRandomString());
            $user->generatePasswordResetToken();
            $user->generateAuthKey();
            $user->save(false);
            $user->sendResetPasswordMail();
            Console::output('User ' . $user->id . ' restored. Reset password link was sended to his email.');
        }
    }
}
