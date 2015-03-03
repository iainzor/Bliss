bliss.service("user.User", ["$resource", "bliss.App", function($resource, App) {
	return $resource("./user/:path/:action.json", {}, {
		signIn: {
			method: "POST",
			params: {
				path: "auth",
				action: "sign-in"
			},
			interceptor: {
				response: function(response) {
					App.reload();
					return response;
				}
			}
		},
		signOut: {
			method: "POST",
			params: {
				path: "auth",
				action: "sign-out"
			},
			interceptor: {
				response: function(response) {
					App.reload();
					return response;
				}
			}
		},
		create: {
			method: "POST",
			params: {
				path: "auth",
				action: "sign-up"
			}
		}
	});
}]);