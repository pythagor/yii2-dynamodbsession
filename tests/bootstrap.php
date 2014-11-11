<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require(__DIR__ . '/../vendor/autoload.php');
}

class Yii extends \yii\BaseYii
{

}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = include('vendor/yiisoft/yii2' . '/classes.php');
Yii::$container = new yii\di\Container;
