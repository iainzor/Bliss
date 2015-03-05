bliss.directive("googleRecaptcha", ["$document", "$q", function($document, $q) {
	var SCRIPT_ID = "google-recaptcha-script";
	
	var Recaptcha = function(container, params) 
	{
		var document = $document[0];
		var promise = $q(function(resolve, reject) {
			if (!document.getElementById(SCRIPT_ID)) {
				var script = document.createElement("script");
				script.id = SCRIPT_ID;
				script.src = "https://www.google.com/recaptcha/api.js?render=explicit";
				script.onload = function() { resolve(); }
				script.async = true;
				script.defer = true;

				document.head.appendChild(script);
			} else {
				resolve();
			}
		});
		
		promise.then(function() {
			grecaptcha.render(container, params);
		});
	};
		
	return {
		restrict: "E",
		link: function($scope, $element, $attrs) {
			var captcha = new Recaptcha($element[0], {
				sitekey: $attrs.key
			});
		}
	};
}]);