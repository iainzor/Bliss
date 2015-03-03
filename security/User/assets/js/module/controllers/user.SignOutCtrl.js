bliss.controller("user.SignOutCtrl", ["$scope", "$timeout", "$interval", "$window", "unifiedUI.Layout", "user.Account", "user.User", function($scope, $timeout, $interval, $window, Layout, Account, User) {
	Layout.shrink();
	Layout.menu.disable();
	
	var start = new Date();
	var delay = 3000;
	var delayTimer = $timeout(function() {
		User.signOut({}, function(response) {
			Account.clear();
			$scope.cancel();
		}, function(error) {
			console.error(error.data);
		});
	}, delay);
	
	$scope.timeLeft = delay;
	var countdown = $interval(function() {
		var now = new Date();
		var diff = delay - (now.getTime() - start.getTime());
		
		$scope.timeLeft = diff;
		
		if (diff <= 0) {
			$scope.timeLeft = 0;
			$interval.cancel(countdown);
		}
	}, 1);
	
	$scope.cancel = function() {
		$window.history.back();
	};
	
	$scope.$on("$locationChangeStart", function() {
		$timeout.cancel(delayTimer);
	});
}]);