bliss.controller("docs.module.IndexCtrl", ["$scope", "modules", "module", function($scope, modules, module) {
	$scope.modules = modules;
	$scope.module = module;
}]);