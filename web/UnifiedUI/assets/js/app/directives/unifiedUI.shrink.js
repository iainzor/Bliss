bliss.directive("shrink", ["$timeout", function($timeout) {
		
	var shrink = function($element) {
		$element.css({
			transform: "scaleY(0)",
			overflow: "hidden",
			opacity: 0,
			margin: 0,
			height: 0
		});
	};
	
	var grow = function($element) {
		var el = $element[0];
		var currentHeight = el.clientHeight;
		
		$element.css("height", "auto");
		
		var newHeight = el.clientHeight;
		
		$element.css("height", currentHeight);
		$element.css({
			transform: "scaleY(1)",
			overflow: "visible",
			opacity: 1,
			margin: null,
			height: newHeight +"px"
		});
	};
	
	return {
		restrict: "A",
		scope: {
			shrink: "="
		},
		controller: ["$scope", "$element", function($scope, $element) {
			var el = $element[0];
			var open = {x:el.clientWidth, y:el.clientHeight};
			var close = {x:0, y:0};
			
			$element.css({
				height: open.y
			});
			$scope.$watch("shrink", function(flag) {
				$timeout(function() {
					if (flag) {
						shrink($element, close);
					} else {
						grow($element, open);
					}
				});
			});
		}]
	};
}]);