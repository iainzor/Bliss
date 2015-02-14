bliss.service("tests.Result", ["$resource", function($resource) {
	return $resource("./tests.json");
}]);