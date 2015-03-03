bliss.controller("bliss.AppCtrl", ["$rootScope", "bliss.App", function($scope, App) {
	App.init();
	
	$scope.app = App;
	$scope.pageError = false;
	$scope.pageLoading = true;
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
			App.loading(flag ? true : false);
		}
		return App.loading();
	};
	
	$scope.$watch(function() { return App.error(); }, function(error) { 
		$scope.pageError = error; 
	});
	$scope.$watch(function() { return App.config(); }, function(config) {
		$scope.$broadcast("bliss.AppUpdated", config);
	});
	
	$scope.$on("$locationChangeStart", function() {
		App.error(false);
		App.loading(true);
	});
	$scope.$on("$routeChangeSuccess", function() {
		App.loading(false);
	});
	$scope.$on("$routeChangeError", function() {
		App.loading(true);
		
		if (!App.error()) {
			App.error({
				message: "An unknown error occurred while loading the page, check the error console for details."
			});
		}
	});
}]);