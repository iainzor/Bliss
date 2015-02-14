bliss.directive("dropdown", ["$window", "$document", function($window, $document) {
	var Dropdown = function($scope, $element) {
		var _open = $element.hasClass("open");
		var _ignoreDocumentClick = false;
		var _self = this;
		var _menu;
		
		
		var _menuClickEvent = function(e) {
			//e.stopPropagation();
			_ignoreDocumentClick = true;
			//_self.close();
		};
		var _attachMenuEvent = function() {
			var menu = $element[0].querySelector(".dropdown-menu") || false;
			if (menu !== false) {
				var el = angular.element(menu);
				el.bind("click", _menuClickEvent);
			}
		};
		
		
		this.toggle = function(e) {
			_attachMenuEvent();
			
			var stop = false;
			
			if (_open) {
				if (!_ignoreDocumentClick) {
					stop = true;
					this.close();
				} else {
					_ignoreDocumentClick = false;
				}
			} else {
				stop = true;
				this.open();
			}
			
			if (stop) {
				e.preventDefault();
				e.stopPropagation();
			}
		};
		
		this.open = function() {
			_open = true;
			$element.addClass("open");
			$document.bind("click", this.documentClickEvent);
		};
		
		this.close = function() {
			_ignoreDocumentClick = false;
			_open = false;
			$element.removeClass("open");
			$document.unbind("click", this.documentClickEvent);
		};
		
		this.documentClickEvent = function() {
			if (_ignoreDocumentClick === false) {
				_self.close();
			}
		};
	};
	
	return {
		restrict: "A",
		scope: true,
		link: function($scope, $element) {
			var dropdown = new Dropdown($scope, $element);
			
			$scope.$on("$locationChangeStart", function() {
				dropdown.close();
			});
			$element.on("click", function(e) {
				dropdown.toggle(e);
			});
		}
	};
}]);