bliss.controller("tests.ResultCtrl", ["$scope", "$sce", "tests.Result", "result", function($scope, $sce, Result, result) {
	result.response = $sce.trustAsHtml(result.response);
	
	$scope.result = result;
	$scope.loading = false;
	
	$scope.reload = function() {
		$scope.loading = true;
		$scope.result = Result.get({}, function(result) {
			$scope.loading = false;
			
			result.response = $sce.trustAsHtml(result.response);
		});
	};
}]);