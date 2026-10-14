<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\AvatarForm;
use app\models\Profile;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Editing your own profile and profile picture.
 */
class ProfileController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'avatar' => ['post'],
                ],
            ],
        ];
    }

    public function actionUpdate(): Response|string
    {
        $profile = $this->currentProfile();

        if ($profile->load($this->request->post()) && $profile->save()) {
            Yii::$app->session->setFlash('success', 'Profiili tallennettu.');

            return $this->refresh();
        }

        return $this->render('update', [
            'profile' => $profile,
            'avatarForm' => new AvatarForm(),
        ]);
    }

    /**
     * Profile picture upload (the original user/upload).
     */
    public function actionAvatar(): Response
    {
        $form = new AvatarForm();
        $form->avatar = UploadedFile::getInstance($form, 'avatar');

        if ($form->save($this->currentProfile())) {
            Yii::$app->session->setFlash('success', 'Profiilikuva päivitetty.');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $form->getFirstErrors()));
        }

        return $this->redirect(['profile/update']);
    }

    private function currentProfile(): Profile
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        return $user->profile;
    }
}
