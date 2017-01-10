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

    public function actionBackUpDb()
    {
        Console::output('Generating back-up ...');
        $folder = Yii::$app->params['dbBackUpFolder'];
        // TODO: get db name from DSN
        $backUpFileName = 'mobile-service.' . time() . '.backup';
        $cmd = 'pg_dump -U postgres -h 127.0.0.1 -Fc '
            . 'mobile-service' . ' > '
            . $folder . '/' . $backUpFileName;
        system($cmd , $return);
        if ($return) {
            Console::output('Error ' . $return);
        } else {
            Console::output('Successfully generated.');
        }

    }
    
    public function actionRestoreDb($path)
    {
        Console::output('Restoring database...');
        
        if (!file_exists($path)) {
            Console::output('File does not exist!');
            
            return;
        }
        // createdb -U postgres -E UTF8 -O postgres -T template0 mobile-service - creating DB
        // TODO: get db name from DSN
        $cmd = 'pg_restore -U postgres -d '
            . 'mobile-service' . ' '
            . realpath($path);
        system($cmd , $return);
        
        if ($return) {
            Console::output('Error ' . $return);
        } else {
            Console::output('Successfully restored');
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
            $user->generateAuthKey();
            $user->save(false);
            $user->sendResetPasswordMail();
            Console::output('User ' . $user->id . ' restored. Reset password link was sended to his email.');
        }
    }

    /**
     * Изменение роли пользователя через консоль
     * @param string $email email пользователя
     * @param string $role новая роль
     * @return null
     */
    public function actionChangeRole($email, $role)
    {
        if (!in_array($role, User::getRoles())) {
            Console::output('Role ' . $role . ' undefined');
            return null;
        }

        /** @var User $user */
        $user = User::findOne(['email' => $email]);
        $oldRole = $user->role;
        if ($oldRole == $role) {
            Console::output('Nothing changed, user is already in this role');
        } else {
            $user->role = $role;
            $user->save(false);
            Console::output('Role changed from ' . $oldRole . ' to ' . $role);
        }
    }

    /**
     * Log out user from all sessions
     * @param string $email email пользователя
     * @return null
     */
    public function actionLogOut($email)
    {
        /** @var User|null $user */
        $user = User::findByUsername($email);
        if (!$user) {
            Console::output('User not found');
        }
        $user->generateAuthKey();
        $user->update(false);
        Console::output('User was logged out from all sessions');
    }
}
