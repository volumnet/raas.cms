<?php
namespace RAAS\CMS;

use RAAS\Application;

require __DIR__ . '/../../../../cron/cron.php';
require __DIR__ . '/resources/ConcreteVisibleRecursiveCache.php';
require __DIR__ . '/../classes/controller_frontend.class.php';
require __DIR__ . '/src/BaseTest.php';
require __DIR__ . '/src/BaseDBTest.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
Application::i()->run('cron', false);
Package::i();
