<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Specify the class of the user model used to associate reports with users.
    | By default, it uses App\Models\User.
    |
    */
    'user_model' => env('REPORTING_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Report Model
    |--------------------------------------------------------------------------
    |
    | Specify the class of the report model. You can extend the default
    | Alyakin\Reporting\Models\Report class to customize its logic.
    |
    */
    'report_model' => Alyakin\Reporting\Models\Report::class,

    /*
    |--------------------------------------------------------------------------
    | Soft Delete Retention Period
    |--------------------------------------------------------------------------
    |
    | Specify how many days deleted reports will be retained in the database.
    | Set to null for indefinite retention.
    |
    */
    'soft_delete_days' => 30, // null for indefinite retention.
];
