bliss.directive("grid", ["$window", function($window) {
	/**
	 * Find the shortest column 
	 * 
	 * @param array columns
	 * @returns array
	 */
	var findShortestColumn = function(columns) {
		var column, current;
		for (var i = 0; i < columns.length; i++) {
			current = columns[i];
			current.height = current.height || 0;
			
			if (!column || current.height < column.height) {
				column = current;
			}
		}
		return column;
	};
	
	/**
	 * Adjust the grid
	 * 
	 * @param $element
	 * @returns void
	 */
	var adjust = function($element) {
		$element.style.position = "relative";
		
		var height = 0;
		var items = [];
		var columns = [];

		// Collect all items and determine the minimum column width
		angular.forEach(angular.element($element).children(), function(el) {
			el.style.position = "relative";
			el.style.left = null;
			el.style.top = null;

			var item = {
				el: el,
				top: el.offsetTop,
				left: el.offsetLeft,
				width: el.clientWidth,
				height: el.clientHeight
			};

			if (typeof(columns.width) === "undefined" || item.width < columns.width) {
				columns.width = item.width;
				columns.count = Math.round($element.clientWidth / item.width);
			}

			items.push(item);
		});

		// Create columns
		var column;
		for (var i = 0; i < columns.count; i++) {
			column = [];
			column.offset = i * columns.width;

			columns.push(column);
		}

		if (columns.length <= 1) {
			$element.style.height = "auto";
		}

		// Group items into columns
		angular.forEach(items, function(item) {
			var column = findShortestColumn(columns);
			if (!column) {
				return;
			}

			var el = item.el;
			if (columns.length > 1) {
				el.style.position = "absolute";
				el.style.left = column.offset +"px";
				el.style.top = column.height +"px";
			}

			column.push(item);
			column.height += item.height;

			if (column.height > height) {
				height = column.height;
			}
		});

		// Adjust the container height
		if (columns.length > 1) {
			$element.style.height = height +"px";
		}
	};
	
	var run = function($element) {
		try {
			adjust($element);
		} catch (e) {
			console.error(e);
		}
	};
	
	var timer;
		
	return { 
		link: {
			pre: function($scope, $element, $attrs) {
				$scope.$watch(function() {
					if (timer) {
						clearTimeout(timer);
					}
					timer = setTimeout(function() {
						run($element[0]);
					}, 50);
				});
				angular.element($window).on("resize", function() {
					run($element[0]);
				});
			}
		}
	};
}]);