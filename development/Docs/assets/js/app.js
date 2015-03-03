bliss.config(["$routeProvider", function($routeProvider) {
	var _moduleResolve = ["$resource", "$route", function($resource, $route) {
		var id = $route.current.params.moduleId || "bliss";
		var action = $route.current.params.action;
		var path = "./docs/modules/"+ id;
		
		if (action) {
			path += "/"+ action;
		}
		
		var r = $resource(path +".json", {}, {
			get: {
				method: "GET",
				cache: true
			}
		});

		return r.get().$promise;
	}];

	var _moduleListResolve = ["$resource", function($resource) {
		var r = $resource("./docs/modules.json", {}, {
			query: {
				method: "GET",
				cache: true,
				isArray: true
			}
		});
		
		return r.query().$promise;
	}];
	
	$routeProvider.when("/docs", {
		templateUrl: "./docs/modules/bliss.html",
		controller: "docs.module.IndexCtrl",
		resolve: {
			module: _moduleResolve,
			modules: _moduleListResolve
		}
	}).when("/docs/modules/:moduleId/:action?", {
		templateUrl: function(params) {
			var id = params.moduleId;
			var action = params.action || "index";
			
			return "./docs/modules/"+ id +"/"+ action +".html";
		},
		controller: "docs.module.IndexCtrl",
		resolve: {
			module: _moduleResolve,
			modules: _moduleListResolve
		}
	});
}]);