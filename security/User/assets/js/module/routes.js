bliss.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/sign-in", {
		templateUrl: "./user/auth/sign-in.html",
		controller: "user.SignInCtrl"
	}).when("/sign-up", {
		templateUrl: "./user/auth/sign-up.html",
		controller: "user.SignUpCtrl"
	});
}]);