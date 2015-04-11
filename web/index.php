<?php

ini_set( 'display_errors', true );
define( 'YII_DEBUG', true );

// Including the Yii Framework itself
require( __DIR__ . '/../vendor/yiisoft/yii2/Yii.php' );

// Getting the configuration
$config = require( __DIR__ . '/../config/web.php' );

// making and lounching the application immediately
( new yii\web\Application($config) )->run();