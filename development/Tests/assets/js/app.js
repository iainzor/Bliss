bliss.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/tests", {
		templateUrl: "./tests.html",
		controller: "tests.ResultCtrl",
		resolve: {
			result: ["tests.Result", function(Result) {
				return Result.get().$promise;
			}]
		}
	});
}]);