<?php
namespace aminkt\ticket\controllers\api;


use aminkt\ticket\models\Department;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;

class DepartmentController extends ActiveController
{
    public $modelClass = Department::class;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                // restrict access to
                'Origin' => ['*'],
                'methods' => ['*']
            ],
            'actions' => [
                'login' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'login-authed' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'revoke-token' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'],
            'optional' => ['*']
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the default actions
        unset($actions['delete'], $actions['update'], $actions['create'], $actions['view']);

        return $actions;
    }
}