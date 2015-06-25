var blissModules = blissModules || [];
blissModules.splice(0, 0, "ngRoute", "ngResource");

var bliss = angular.module("bliss", blissModules);

bliss.config(["$locationProvider", "$routeProvider", function($locationProvider, $routeProvider) {
	$locationProvider.html5Mode(true);
	
	$routeProvider.otherwise({
		templateUrl: "./bliss/welcome.html"
	});
}]);