blissApp.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/", {
		templateUrl: "./bliss-app/index/index.html"
	});
}]);