bliss.controller("user.MenuWidgetCtrl", ["$scope", "$location", "user.Account", "user.User", function($scope, $location, Account, User) {
	$scope.$watch(function() { return Account.user(); }, function(user) {
		$scope.user = user;
	}, true);
	
	$scope.signOut = function() {
		User.signOut({}, function(response) {
			Account.clear();
			window.location.reload();
		}, function(error) {
			console.error(error.data);
		});
	};
}]);