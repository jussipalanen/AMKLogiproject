<?php

declare(strict_types=1);

namespace app\commands;

use app\models\Comment;
use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Fills an empty database with demo users and comments.
 *
 * ```
 * php yii seed
 * ```
 */
class SeedController extends Controller
{
    public const DEMO_PASSWORD = 'demo1234';

    private const USERS = [
        'matti' => ['Matti', 'Meikäläinen', 'male', 'Kouvola', User::ROLE_STUDENT],
        'liisa' => ['Liisa', 'Laaksonen', 'female', 'Kotka', User::ROLE_STUDENT],
        'admin' => ['Ylläpito', null, null, null, User::ROLE_ADMIN],
    ];

    private const COMMENTS = [
        ['liisa', 'matti', 'Moi Matti! Nähdäänkö huomenna ohjelmoinnin luennolla?', false],
        ['matti', 'liisa', 'Joo, nähdään! Otan läppärin mukaan.', false],
        ['matti', 'liisa', 'Tämä on yksityinen viesti, näkyy vain meille kahdelle.', true],
        ['admin', 'matti', 'Tervetuloa AMKLogiin!', false],
    ];

    /**
     * Creates demo users (password "demo1234") unless the database already has users.
     */
    public function actionIndex(): int
    {
        if (User::find()->exists()) {
            $this->stdout("Database already has users, nothing to seed.\n");

            return ExitCode::OK;
        }

        $users = [];

        foreach (self::USERS as $username => [$firstname, $surname, $gender, $city, $role]) {
            $user = new User([
                'username' => $username,
                'email' => "$username@amklogi.test",
                'role' => $role,
            ]);
            $user->setPassword(self::DEMO_PASSWORD);
            $user->generateAuthKey();
            $user->save(false);

            $user->profile->setAttributes([
                'firstname' => $firstname,
                'surname' => $surname,
                'gender' => $gender,
                'city' => $city,
            ]);
            $user->profile->save(false);

            $users[$username] = $user;
        }

        foreach (self::COMMENTS as [$from, $to, $body, $isPrivate]) {
            $comment = new Comment([
                'author_id' => $users[$from]->id,
                'recipient_id' => $users[$to]->id,
                'body' => $body,
                'is_private' => $isPrivate,
            ]);
            $comment->save(false);
        }

        $this->stdout(
            'Created users ' . implode(', ', array_keys($users)) . ' with password "' . self::DEMO_PASSWORD . "\".\n",
            Console::FG_GREEN,
        );

        return ExitCode::OK;
    }
}
