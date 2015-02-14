bliss.controller("unifiedUI.LayoutCtrl", ["$rootScope", "bliss.App", "pages.Page", "unifiedUI.Layout", "unifiedUI.Navigation", function($scope, App, Page, Layout, Nav) {
	var preventMenuClose = false;
	
	angular.element(document.body).on("context", function(e) {
		console.log(e);
	});
	
	$scope.menuOpen = false;
	$scope.page = null;
	$scope.layout = Layout;
	
	$scope.toggleMenu = function() {
		$scope.menuOpen = !$scope.menuOpen;
	};
	
	$scope.preventMenuClose = function() {
		preventMenuClose = true;
	};
	
	$scope.$watch(function() { return App.config().unifiedui; }, function(config) {
		$scope.layout.setConfig(config);
	});
	$scope.$watch(function() { return Nav.page(); }, function(page) {
		$scope.page = page;
	});
	$scope.$on("$routeChangeSuccess", function() {
		$scope.menuOpen = preventMenuClose === true ? $scope.menuOpen : false;
		$scope.layout.reset();
		
		preventMenuClose = false;
	});
}]);