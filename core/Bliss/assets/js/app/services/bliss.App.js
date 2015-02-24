bliss.service("bliss.App", ["$resource", function($resource) {
	var App = $resource("./app.json");
	var _config;
	var _page;
	
	App.init = function() {
		if (bliss.app) {
			_config = angular.extend(bliss.app, {
				ready: true
			});
		} else {
			_config = App.get({}, function(response) {
				response.ready = true;
			}, function(error) {
				console.error(error.data);
			});
		}
	};
	
	App.reload = function() {
		App.get({}, function(response) {
			angular.extend(_config, response);
		});
	};
	
	App.config = function(config) {
		if (typeof(config) !== "undefined") {
			angular.extend(_config, config);
		}
		return _config;
	};
	
	App.page = function(page) {
		if (typeof(page) !== "undefined") {
			_page = page;
		}
		return _page;
	};
	
	return App;
}]);