var blissApp = angular.module("blissApp", ["ngRoute", "docs"]);

blissApp.config(["$locationProvider", function($locationProvider) {
	$locationProvider.html5Mode(true);
}]);