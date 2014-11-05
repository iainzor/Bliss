docs.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/docs", {
		templateUrl: "./docs/index/index.html",
		controller: "IndexCtrl",
		resolve: {
			modules: ["$resource", function($resource) {
				var r = $resource("./docs/modules.json");
				
				return r.query().$promise;
			}]
		}
	}).when("/docs/:moduleId/:action?", {
		templateUrl: function(params) {
			var id = params.moduleId;
			var action = params.action || "index";
			
			return "./docs/modules/"+ id +"/"+ action +".html";
		},
		controller: "module.IndexCtrl",
		resolve: {
			module: ["$resource", "$route", function($resource, $route) {
				var id = $route.current.params.moduleId;
				var action = $route.current.params.action || "index";
				
				var r = $resource("./docs/modules/"+ id +"/"+ action +".json");
				
				return r.get().$promise;
			}]
		}
	});
}]);