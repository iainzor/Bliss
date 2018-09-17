bliss.directive("mediaContainer", [function() {
	var setup = function($scope, $element) {
		var el = $element[0];
		var spinner = el.querySelector(".spinner");
		var image = el.querySelector("img");

		if (spinner && image) {
			load($scope, el, spinner, image);
			
		}
	};
	
	var load = function($scope, el, spinner, image) {
		var oldClassname = el.className;
		el.className += " loading";
		
		spinner.active = true;
		
		var height = el.clientHeight;
		el.style.overflow = "hidden";
		
		image.style.opacity = 0;
		image.style.marginTop = "-100%";
		image.onload = function() {
			spinner.active = false;
			el.className = oldClassname;
			
			var newHeight = image.clientHeight;
			
			image.style.marginTop = 0;
			image.style.opacity = 1;
			
			$scope.$apply();
		};
	};
	
	return {
		restrict: "A",
		transclude: true,
		template: "<ng-transclude></ng-transclude>",
		link: function($scope, $element) {
			setup($scope, $element);
		}
	};
}]);