bliss.directive("shrink", ["$timeout", function($timeout) {
		
	var shrink = function($element) {
		$element.css({
			transform: "scale(0)",
			opacity: 0,
			margin: 0,
			padding: 0,
			height: 0,
			width: 0,
			overflow: "hidden"
		});
	};
	
	var grow = function($element) {
		var el = $element[0];
		var currentHeight = el.clientHeight;
		var currentWidth = el.clientWidth;
		
		$element.css({
			height: "auto",
			width: "auto",
			padding: null,
			margin: null,
			overflow: null
		});
		
		var newHeight = el.clientHeight;
		var newWidth = el.clientWidth;
		
		$element.css({
			height: currentHeight +"px",
			width: currentWidth +"px"
		});
		$element.css({
			transform: "scale(1)",
			opacity: 1,
			height: newHeight +"px",
			width: newWidth +"px"
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
				height: open.y +"px",
				width: open.x +"px"
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