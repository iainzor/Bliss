<?php
require_once "../bliss/app.php";

error_reporting(-1);
ini_set("display_errors", true);
ini_set("display_startup_errors", true);

date_default_timezone_set("UTC");

$startTime = microtime(true);
$baseUrl = preg_replace("/^(.*)\/.*\.php$/i", "\\1/", filter_input(INPUT_SERVER, "SCRIPT_NAME"));
$requestUri = substr(filter_input(INPUT_SERVER, "REQUEST_URI"), strlen($baseUrl));

define("BASE_URL", $baseUrl);

$app = BlissApp::create("Bliss", dirname(__DIR__));
$app->modules()->registerModulesDirectory(dirname(__DIR__) ."/app");
$app->modules()->registerModulesDirectory(dirname(__DIR__) ."/bliss/development");

set_error_handler([$app, "handleError"]);
set_exception_handler([$app, "handleException"]);

$request = $app->request();
$request->setUri($requestUri);

$router = $app->router();
$route = $router->find($requestUri);

$app->execute($route->params());

$format = $request->getFormat();

if (in_array($format, [null, "html"])) {
	echo "\n\n\n";
	echo "<!-- Total Execution Time .............. ". number_format((microtime(true) - $startTime) * 1000, 2) ." ms -->\n";
	echo "<!-- Total Memory Usage ................ ". number_format(memory_get_usage()/1024, 2) ." kb -->\n";
	
	echo "\n\n";
	echo "<!------------------------------>\n";
	echo "<!--           Log            -->\n";
	echo "<!------------------------------>\n";
	echo "<!--\n\n";
	foreach ($app->logs() as $log) {
		echo "\t{$log["message"]}\n";
	}
	echo "-->";
}