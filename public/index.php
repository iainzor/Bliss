<?php
require_once "../bliss/app.php";

$env = getenv("APP_ENV") ? getenv("APP_ENV") : "development";
$app = BlissApp::create("Bliss", dirname(__DIR__), $env);
$app->modules()->registerModulesDirectory(dirname(__DIR__) ."/app");
$app->run();