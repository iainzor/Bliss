bliss.controller("user.SignUpCtrl", ["$scope", "$location", "bliss.App", "unifiedUI.Layout", "user.User", function($scope, $location, App, Layout, User) {
	Layout.shrink();
	
	$scope.user = {};
	$scope.errors = [];
	
	$scope.submit = function() {
		$scope.loading(true);
		$scope.errors = [];
		
		User.create({
			user: $scope.user
		}, function(response) {
			$scope.loading(false);
			
			if (response.result === "error") {
				$scope.errors = response.errors;
			} else {
				App.reload();
				$location.path("/");
			}
		}, function(error) {
			$scope.loading(false);
			$scope.errors = [
				error.data.message
			];
			console.error(error.data);
		});
	};
}]);