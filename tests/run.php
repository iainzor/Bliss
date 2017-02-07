<?php
require_once dirname(__DIR__) ."/modules/CmdLine/src/Application.php";

$startTime = microtime(true);

$app = new CmdLine\Application();
$app->moduleRegistry()->registerDirectory(__DIR__);
$app->execute("tests", "runner", "run");

$endTime = microtime(true);
$totalTime = $endTime - $startTime;

echo $totalTime ."ms";