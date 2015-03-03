bliss.service("user.Account", ["$resource", "bliss.App", function($resource, App) {
	var Account = $resource("./account/:action.json");
	
	Account.user = function(user) {
		if (typeof(user) !== "undefined") {
			App.config().user = user;
		}
		return App.config().user;
	};
	
	Account.clear = function() {
		App.config().user = null;
	};
	
	return Account;
}]);