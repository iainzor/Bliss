var bliss = angular.module("bliss", [
	"ngRoute",
	"ngResource"
]);

bliss.config(["$locationProvider", "$routeProvider", function($locationProvider, $routeProvider) {
	$locationProvider.html5Mode(true);
	
	$routeProvider.otherwise({
		templateUrl: "./bliss/welcome.html"
	});
}]);