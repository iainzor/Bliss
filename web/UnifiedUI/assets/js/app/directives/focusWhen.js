bliss.directive("focusWhen", ["$timeout", function($timeout) {
	var focus = function($element) {
		console.log($element);
		var el = $element[0].querySelector("input,select,textarea");
		console.log(el);
		if (el) {
			el.focus();
		}
	};
	var blur = function($element) {
		var el = $element[0].querySelector("input,select,textarea");
		if (el) {
			el.blur();
		}
	};
	
	return {
		restrict: "A",
		controller: ["$scope", "$element", "$attrs", function($scope, $element, $attrs) {
			var flag = $scope.$eval($attrs.focusWhen);
			
			$scope.$watch(function() {
				return $scope.$eval($attrs.focusWhen, $scope);
			}, function(value) {
				console.log(value);
			});
			
			/*
			$scope.$watch($attrs, function(attrs) {
				if (attrs) {
					var flag = $scope.$eval(attrs.focusWhen);

					$timeout(function() {
						console.log(flag);
						if (flag) {
							focus($element);
						} else {
							blur($element);
						}
					}, 100);
				}
			});*/
		}]
	};
}]);