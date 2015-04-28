bliss.service("unifiedUI.Header", [function() {
	var Header = function() {
		var isVisible = true;
		
		this.isVisible = function(flag) {
			if (typeof(flag) !== "undefined") {
				isVisible = flag;
			}
			return isVisible;
		};
	};
	var instance = instance || new Header();
	
	return instance;
}]);