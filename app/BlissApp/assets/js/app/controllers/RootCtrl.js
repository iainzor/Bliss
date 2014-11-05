blissApp.controller("RootCtrl", ["$scope", "$location", function($scope, $location) {
	$scope.$watch(function() { return $location.path(); }, function(path) {
		$scope.path = path;
	});
}]);