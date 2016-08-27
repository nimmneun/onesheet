<?php

// Fix for PHPUnit_Util_Configuration (https://www.drupal.org/node/2597814)
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', __DIR__ . '/../vendor/autoload.php');
}

require __DIR__ . '/../vendor/autoload.php';
