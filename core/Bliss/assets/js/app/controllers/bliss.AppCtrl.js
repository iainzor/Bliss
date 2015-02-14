bliss.controller("bliss.AppCtrl", ["$rootScope", "bliss.App", function($scope, App) {
	App.init();
	
	$scope.app = App.config();
	$scope.pageError = false;
	$scope.pageTitle = function() { 
		var title = App.config().name;
		var pageTitle = App.page() ? App.page().title : false;
		
		if (pageTitle) {
			title = pageTitle +" - "+ title;
		}
		
		return title;
	};
	$scope.clearPageError = function() { $scope.pageError = false; };
	
	$scope.loading = function(flag) {
		if (typeof(flag) !== "undefined") {
			$scope.app.loading = flag ? true : false;
		}
		return $scope.app.loading;
	};
	
	$scope.$on("$locationChangeStart", function() {
		$scope.pageError = false;
		$scope.app.loading = true;
	});
	$scope.$on("$routeChangeSuccess", function() {
		$scope.app.loading = false;
	});
	$scope.$on("$routeChangeError", function() {
		$scope.app.loading = false;
		$scope.pageError = {
			message: "An error occurred while loading the page"
		};
	});
}]);